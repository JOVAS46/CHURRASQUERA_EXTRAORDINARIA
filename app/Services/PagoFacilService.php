<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PagoFacilService
{
    protected $tokenService;
    protected $tokenSecret;
    // Base URL para MasterQR v2
    protected $baseUrl = 'https://masterqr.pagofacil.com.bo/api/services/v2';

    public function __construct()
    {
        $this->tokenService = config('services.pagofacil.token_service');
        $this->tokenSecret = config('services.pagofacil.token_secret');
    }

    /**
     * Autenticarse y obtener Token Bearer
     */
    private function login()
    {
        try {
            $response = Http::withHeaders([
                'tcTokenService' => $this->tokenService,
                'tcTokenSecret' => $this->tokenSecret,
            ])->post($this->baseUrl . '/login');

            if ($response->successful()) {
                $data = $response->json();
                // Estructura: values -> accessToken
                return $data['values']['accessToken'] ?? null;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generar QR para una venta (Versión 2 JWT)
     */
    public function generarQR(int $idVenta, float $monto, string $clienteEmail = 'cajero@restaurante.com')
    {
        try {
            // 1. Obtener Token
            $jwtToken = $this->login();
            
            if (!$jwtToken) {
                return [
                    'success' => false,
                    'message' => 'No se pudo autenticar con PagoFácil (Login fallido).',
                ];
            }

            // 2. Preparar payload para Generate QR V2
            $payload = [
                "paymentMethod" => 4, // 4 = QR
                "clientName" => "Cliente Venta " . $idVenta,
                "documentType" => 1,
                "documentId" => "0000000",
                "phoneNumber" => "00000000",
                "email" => $clienteEmail,
                "paymentNumber" => "V-" . $idVenta . "-" . time(), // Debe ser único
                "amount" => $monto,
                "currency" => 2, // 2 = Bs (Bolivianos)
                "clientCode" => "11001", // Cod Cliente fijo o variable según integración
                "callbackUrl" => config('services.pagofacil.callback_url'), // URL desde config/env
                "orderDetail" => [
                    [
                        "serial" => 1,
                        "product" => "Consumo Venta #" . $idVenta,
                        "quantity" => 1,
                        "price" => $monto,
                        "discount" => 0,
                        "total" => $monto
                    ]
                ]
            ];

            // 3. Enviar Solicitud con Bearer Token
            $response = Http::withToken($jwtToken)->post($this->baseUrl . '/generate-qr', $payload);
            $data = $response->json();

            // 4. Procesar Respuesta
            if (isset($data['error']) && $data['error'] == 0) {
                $values = $data['values'];
                // QR en base64 viene en 'qrBase64'
                $qrImage = $values['qrBase64'] ?? null; 
                $nroTransaccion = $values['transactionId'] ?? null;

                return [
                    'success' => true,
                    'qr_image' => $qrImage,
                    'nro_transaccion' => $nroTransaccion,
                    'message' => $data['message'] ?? 'QR Generado',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Error al generar QR',
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Excepción: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Consultar estado de una transacción - API v2
     * Verifica el paymentStatus: 1 = Pendiente, 2 = Pagado
     */
    public function consultarEstado(string $nroTransaccion)
    {
        try {
            $jwtToken = $this->login();
            
            if (!$jwtToken) {
                return ['success' => false, 'message' => 'Error de autenticación'];
            }

            // Consultar transacción usando pagofacilTransactionId
            $response = Http::withToken($jwtToken)->post($this->baseUrl . '/query-transaction', [
                'pagofacilTransactionId' => $nroTransaccion
            ]);

            $data = $response->json();

            // Respuesta exitosa tiene error == 0
            if (isset($data['error']) && $data['error'] == 0 && isset($data['values'])) {
                $values = $data['values'];
                $paymentStatus = $values['paymentStatus'] ?? null;

                // paymentStatus 1 = En Proceso (Pendiente)
                // paymentStatus 2 = Pagado (Completado)
                // otros valores = Error/Rechazado
                $isCompleted = ($paymentStatus == 2);

                return [
                    'success' => true,
                    'completed' => $isCompleted,
                    'paymentStatus' => $paymentStatus,
                    'statusDescription' => $values['paymentStatusDescription'] ?? null,
                    'amount' => $values['amount'] ?? null,
                    'paymentDate' => $values['paymentDate'] ?? null,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'completed' => false,
                    'message' => $data['message'] ?? 'Error al consultar',
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'completed' => false,
                'message' => 'Excepción: ' . $e->getMessage(),
            ];
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageVisit;

class RecordPageVisit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Registrar visita de forma asincrónica para no ralentizar la respuesta
        try {
            PageVisit::registrarVisita(
                pagina: basename($request->path()) ?: 'inicio',
                url: $request->path(),
                ip: $request->ip(),
                userAgent: $request->userAgent(),
                referrer: $request->header('referer')
            );
        } catch (\Exception $e) {
            // Silenciar errores para no interrumpir la solicitud
            \Log::debug('Error registrando visita: ' . $e->getMessage());
        }

        return $next($request);
    }
}

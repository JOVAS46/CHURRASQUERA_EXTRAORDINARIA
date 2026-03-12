<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminUsuariosController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\CajasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\MenuClienteController;
use App\Http\Controllers\CocinaController;
use App\Http\Controllers\ClienteDashboardController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\ClienteReservaController;
use App\Http\Controllers\CajeroController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\SearchController;

// Rutas públicas
Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/about', function () {
    return Inertia::render('About');
});

// PagoFácil Callback (sin autenticación)
Route::post('/api/pagofacil/callback', [CajeroController::class, 'callbackPagoFacil'])->withoutMiddleware(['auth']);

// Auth Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// API Routes
Route::get('/api/menu', [MenuController::class, 'getMenu']);
Route::get('/api/search/debug/info', [SearchController::class, 'debugInfo']);
Route::get('/api/test/productos', [SearchController::class, 'testProductos']);
Route::get('/api/test/buscar', [SearchController::class, 'testBuscar']);
Route::get('/api/search', [SearchController::class, 'search']);
Route::get('/api/mesas-disponibles', [ReservaController::class, 'obtenerMesasDisponibles']);
Route::get('/api/pedido-pendiente', [PagoController::class, 'obtenerPedidoPendiente']);

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Productos
    Route::resource('productos', ProductoController::class);
    
    // Pedidos
    Route::resource('pedidos', PedidoController::class);
    
    // Reservas
    Route::resource('reservas', ReservaController::class);
    
    // Mis Reservas (para clientes)
    Route::get('/cliente/reservas', [ClienteReservaController::class, 'index'])->name('cliente.reservas.index');
    
    // Usuarios
    Route::resource('usuarios', UserController::class);
    
    // Pagos
    Route::resource('pagos', PagoController::class);
    
    // Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/generar-automatico/{pedido}', [TicketController::class, 'generarAutomatico'])->name('tickets.generarAutomatico');
    Route::get('/tickets/{ticket}/exportar-pdf', [TicketController::class, 'exportarPDF'])->name('tickets.exportarPDF');
    Route::get('/tickets-reporte', [TicketController::class, 'reporte'])->name('tickets.reporte');

    // Admin - Usuarios Management
    Route::get('/admin/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/admin/usuarios/create', [UserController::class, 'create'])->name('admin.usuarios.create');
    Route::post('/admin/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
    Route::get('/admin/usuarios/{usuario}/edit', [UserController::class, 'edit'])->name('admin.usuarios.edit');
    Route::patch('/admin/usuarios/{usuario}', [UserController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{usuario}', [UserController::class, 'destroy'])->name('admin.usuarios.destroy');

    // Ventas
    Route::get('/ventas', [VentasController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/pendientes', [VentasController::class, 'pendientes'])->name('ventas.pendientes');

    // Cajas
    Route::get('/cajas', [CajasController::class, 'index'])->name('cajas.index');

    // Reportes
    Route::get('/admin/reportes', [ReportesController::class, 'index'])->name('reportes.index');

    // Menu (Mesero)
    Route::get('/menu', [MenuClienteController::class, 'index'])->name('menu.index');

    // Cocina - Pedidos
    Route::get('/cocina/pedidos', [CocinaController::class, 'pedidos'])->name('cocina.pedidos');
    Route::patch('/cocina/pedidos/{pedido}', [CocinaController::class, 'updateEstado'])->name('cocina.updateEstado');

    // Cajero - Cuentas por Cobrar (Tickets)
    Route::get('/cajero/cuentas-por-cobrar', [CajeroController::class, 'cuentasPorCobrar'])->name('cajero.cuentasPorCobrar');
    Route::post('/cajero/registrar-pago-ticket/{id}', [CajeroController::class, 'registrarPago'])->name('cajero.registrarPagoTicket');
    Route::post('/cajero/generar-qr/{id}', [CajeroController::class, 'generarQR'])->name('cajero.generarQR');
    Route::post('/cajero/generar-qr-ticket/{id}', [CajeroController::class, 'generarQR'])->name('cajero.generarQRTicket');
    Route::get('/cajero/verificar-estado/{id}', [CajeroController::class, 'verificarEstadoPedido'])->name('cajero.verificarEstado');
    Route::get('/cajero/verificar-transaccion/{nro_transaccion}', [CajeroController::class, 'verificarTransaccion'])->name('cajero.verificarTransaccion');
    Route::get('/cajero/ventas-del-dia', [CajeroController::class, 'ventasDelDia'])->name('cajero.ventasDelDia');
    Route::get('/cajero/pago/{pago}', [CajeroController::class, 'detallePago'])->name('cajero.detallePago');
    
    // DEBUG: Verificar transacción (solo para testing)
    Route::get('/cajero/debug-verificar/{nro_transaccion}', [CajeroController::class, 'debugVerificarTransaccion'])->name('cajero.debugVerificar');

    // Cliente - Dashboard
    Route::get('/cliente/dashboard', [ClienteDashboardController::class, 'index'])->name('cliente.dashboard');

    // Mesas
    Route::resource('mesas', MesaController::class);

    // Preferencias de Usuario
    Route::post('/preferences/tema', [PreferenceController::class, 'actualizarTema'])->name('preferences.tema');
    Route::post('/preferences/tamano-letra', [PreferenceController::class, 'actualizarTamanoLetra'])->name('preferences.tamanoLetra');
    Route::post('/preferences/alto-contraste', [PreferenceController::class, 'actualizarAltoContraste'])->name('preferences.altoContraste');
});


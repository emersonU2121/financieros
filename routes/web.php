<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClasificacionClienteController;
use App\Http\Controllers\PoliticaCreditoController;
use App\Http\Controllers\CuentaPorCobrarController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReporteCarteraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BitacoraController;


// ============================================
//  HOME: redirige al login
// ============================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================
//  LOGIN Y AUTENTICACIÓN
// ============================================

// Ruta extra que venía del remoto para reactivar incobrables
Route::post('/cuentas/{cuenta}/reactivar-incobrable', [CuentaPorCobrarController::class, 'reactivarIncobrable'])
    ->name('cuentas.reactivarIncobrable');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

//  LOGOUT (solo autenticados)
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


// ============================================
//  RUTAS PROTEGIDAS
// ============================================
Route::middleware('auth')->group(function () {

    // ============================================
    //  CLIENTES
    // ============================================
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // CLASIFICACIONES
    Route::resource('clasificaciones', ClasificacionClienteController::class)->except(['show']);

    // POLÍTICAS DE CRÉDITO
    Route::resource('politicas', PoliticaCreditoController::class)->except(['show']);


    // ============================================
    //  CUENTAS POR COBRAR
    // ============================================
    Route::resource('cuentas', CuentaPorCobrarController::class);

    // Acciones especiales
    Route::post('cuentas/{cuenta}/incobrable', [CuentaPorCobrarController::class, 'marcarIncobrable'])
        ->name('cuentas.incobrable');

    Route::post('cuentas/{cuenta}/reactivar', [CuentaPorCobrarController::class, 'reactivarIncobrable'])
        ->name('cuentas.reactivar');

    Route::post('cuentas/{cuenta}/refinanciar', [CuentaPorCobrarController::class, 'crearRefinanciamiento'])
        ->name('cuentas.refinanciar');

    Route::post('cuentas/{cuenta}/embargo', [CuentaPorCobrarController::class, 'marcarEmbargo'])
        ->name('cuentas.embargo');


    // ============================================
    //  PAGOS
    // ============================================
    Route::post('pagos', [PagoController::class, 'store'])->name('pagos.store');


    // ============================================
    //  REPORTES – ENTRADA PRINCIPAL
    // ============================================
    Route::get('reportes', function () {
        return redirect()->route('reportes.cartera');
    })->name('reportes.index');


    // ============================================
    //  REPORTES – VISTAS NORMALES
    // ============================================
    Route::get('reportes/cartera', [ReporteCarteraController::class, 'carteraGeneral'])
        ->name('reportes.cartera');

    Route::get('reportes/mora', [ReporteCarteraController::class, 'mora'])
        ->name('reportes.mora');

    Route::get('reportes/incobrables', [ReporteCarteraController::class, 'incobrables'])
        ->name('reportes.incobrables');

    Route::get('reportes/por-zona', [ReporteCarteraController::class, 'porZona'])
        ->name('reportes.por_zona');

    Route::get('reportes/por-tipo-cliente', [ReporteCarteraController::class, 'porTipoCliente'])
        ->name('reportes.por_tipo_cliente');


    // ============================================
    //  REPORTES – EXPORTACIONES (PDF / CSV / XLS)
    // ============================================
    Route::get('reportes/cartera/export/{formato}', [ReporteCarteraController::class, 'exportarCarteraGeneral'])
        ->name('reportes.cartera.export');

    Route::get('reportes/mora/export/{formato}', [ReporteCarteraController::class, 'exportarMora'])
        ->name('reportes.mora.export');

    Route::get('reportes/incobrables/export/{formato}', [ReporteCarteraController::class, 'exportarIncobrables'])
        ->name('reportes.incobrables.export');

    Route::get('reportes/por-zona/export/{formato}', [ReporteCarteraController::class, 'exportarPorZona'])
        ->name('reportes.por_zona.export');

    Route::get('reportes/por-tipo-cliente/export/{formato}', [ReporteCarteraController::class, 'exportarPorTipoCliente'])
        ->name('reportes.por_tipo_cliente.export');


    // ============================================
    // Usuarios 
    // ============================================
    Route::resource('usuarios', UserController::class)
        ->parameters(['usuarios' => 'usuario'])
        ->except(['show']);


    // ============================================
    // Bitácora de actividades
    // ============================================
    Route::get('/bitacora', [BitacoraController::class, 'index'])
        ->name('bitacora.index');
});

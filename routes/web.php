<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CompraProveedorController;
use App\Http\Controllers\CajaController;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

// ============================================================
// AUTENTICACIÓN PÚBLICA
// ============================================================
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================================
// RECUPERACIÓN DE CONTRASEÑA
// ============================================================
Route::get('/forgot-password', fn() => view('auth.forgot-password'))
    ->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', 'Te enviamos el enlace a tu correo.')
        : back()->withErrors(['email' => 'No encontramos ese correo registrado.']);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', fn(string $token) => view('auth.reset-password', ['token' => $token]))
    ->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Illuminate\Http\Request $request) {
    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])
                ->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        }
    );
    return $status === Password::PASSWORD_RESET
        ? redirect('/')->with('status', 'Contraseña actualizada correctamente.')
        : back()->withErrors(['email' => 'El enlace no es válido o ya expiró.']);
})->middleware('guest')->name('password.update');

// ============================================================
// RUTAS COMPARTIDAS: ADMIN + EMPLEADO
// ============================================================
Route::middleware(['auth', 'role:admin,empleado', 'prevent-back-history'])->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Datos personales
    Route::get('/admin/datospersonales', [AdminController::class, 'datosper'])->name('admin.datospersonales');
    Route::resource('datospersonales', UsuarioController::class)
        ->only(['index', 'edit', 'update', 'show'])->names('usuario');
    Route::post('/usuario/cambiar-contra', [UsuarioController::class, 'cambiarContra'])->name('usuario.cambiarContra');
    Route::post('/usuario/cambiar-avatar', [UsuarioController::class, 'cambiarAvatar'])->name('usuario.cambiarAvatar');

    // Ventas — ambos roles
    Route::get('admin/venta', [VentaController::class, 'ventas'])->name('admin.adm_venta');
    Route::get('/ventas/estadisticas', [VentaController::class, 'estadisticas'])->name('ventas.estadisticas');
    Route::get('/venta/imprimir/{id}', [VentaController::class, 'imprimir'])->name('venta.imprimir');
    Route::get('/venta/{id}/imprimir', [VentaController::class, 'imprimir']);

    // Compra / Carrito
    Route::get('/admin/compra', fn() => view('admin.adm_compra'))->name('admin.compra');
    Route::post('/admin/guardar-venta', [CompraController::class, 'store']);
    Route::get('/cliente/buscar/{ci}', [CompraController::class, 'buscarCliente']);

    // Productos — ambos pueden ver el catálogo
    Route::get('admin/adm_producto', [AdminController::class, 'gesprod'])->name('admin.adm_producto');
    Route::get('/admin/buscar-producto', [ProductoController::class, 'buscar']);

    // Lotes — ambos pueden ver y REGISTRAR nuevos lotes
    Route::get('/admin/lotes', [LoteController::class, 'loteprod'])->name('admin.lotes');
    Route::get('/admin/buscar-lotes', [LoteController::class, 'buscar']);
    Route::get('/admin/lotes-riesgo', [LoteController::class, 'lotesRiesgo']);
    Route::post('/lote/crear', [ProductoController::class, 'storeLote'])->name('admin.lote.crear');

    // Proveedores — ambos pueden consultar
    Route::get('admin/proveedor', [ProveedorController::class, 'proveed'])->name('admin.adm_proveedor');
    Route::get('/admin/listar-proveedores', [ProveedorController::class, 'listar']);

    // Atributos
    Route::get('admin/adm_atributo', [AdminController::class, 'atributo'])->name('admin.adm_atributo');
    Route::get('/admin/listar-laboratorios', [LaboratorioController::class, 'listar']);
    Route::get('/admin/listar-categorias', [CategoriaController::class, 'listar']);

    // ============================================================
    // COMPRAS A PROVEEDOR — ambos roles registran y consultan
    // ============================================================
    Route::get('/admin/compras-proveedor', [CompraProveedorController::class, 'index'])->name('admin.compras_proveedor');
    Route::post('/admin/compras-proveedor', [CompraProveedorController::class, 'store'])->name('admin.compras_proveedor.store');
    Route::post('/admin/compras-proveedor/{id}/pagar', [CompraProveedorController::class, 'pagar'])->name('admin.compras_proveedor.pagar');

    // ============================================================
    // CAJA — ambos roles operan la caja diaria
    // ============================================================
    Route::get('/admin/caja', [CajaController::class, 'index'])->name('admin.caja');
    Route::get('/admin/caja/estado', [CajaController::class, 'estado'])->name('admin.caja.estado');
    Route::post('/admin/caja/abrir', [CajaController::class, 'abrir'])->name('admin.caja.abrir');
    Route::post('/admin/caja/cerrar', [CajaController::class, 'cerrar'])->name('admin.caja.cerrar');
    Route::post('/admin/caja/movimiento', [CajaController::class, 'registrarMovimiento'])->name('admin.caja.movimiento');
    Route::get('/admin/caja/{id}/detalle', [CajaController::class, 'detalle'])->name('admin.caja.detalle');
});

// ============================================================
// RUTAS EXCLUSIVAS DEL ADMINISTRADOR
// ============================================================
Route::middleware(['auth', 'role:admin', 'prevent-back-history'])->group(function () {

    // Usuarios
    Route::get('/admin/registrar-usuario', [GestionController::class, 'registroEmp'])->name('admin.adm_usuario');
    Route::get('/admin/buscar-empleado', [AdminController::class, 'index']);
    Route::post('/admin/registrar-usuario', [GestionController::class, 'register'])->name('admin.registrar.usuario');
    Route::post('/admin/eliminar-usuario', [GestionController::class, 'eliminarUsuario'])->name('usuario.eliminar');

    // Laboratorios
    Route::post('/admin/crear-laboratorio', [LaboratorioController::class, 'crearlab'])->name('laboratorio.crear');
    Route::post('/laboratorio/{id}/logo', [LaboratorioController::class, 'cambiarLogo']);
    Route::delete('/laboratorio/{id}', [LaboratorioController::class, 'eliminar']);
    Route::put('/laboratorio/{id}', [LaboratorioController::class, 'update']);

    // Categorías
    Route::post('/admin/crear-categoria', [CategoriaController::class, 'crearCat'])->name('categoria.crear');
    Route::delete('/categoria/{id}', [CategoriaController::class, 'eliminar']);
    Route::put('/categoria/{id}', [CategoriaController::class, 'update']);

    // Productos — solo admin crea/edita/elimina
    Route::post('/admin/producto/crear', [ProductoController::class, 'crear'])->name('admin.producto.crear');
    Route::post('/producto/{id}/avatar', [ProductoController::class, 'cambiarAvatar']);
    Route::put('/producto/{id}', [ProductoController::class, 'update']);
    Route::delete('/producto/{id}', [ProductoController::class, 'eliminar']);

    // Proveedores
    Route::post('/admin/proveedor/crear', [ProveedorController::class, 'crearProv'])->name('admin.proveedor.crear');
    Route::post('/proveedor/{id}/avatar', [ProveedorController::class, 'cambiarAvatar']);
    Route::delete('/proveedor/{id}', [ProveedorController::class, 'eliminar']);
    Route::put('/proveedor/{id}', [ProveedorController::class, 'update']);

    // Lotes — solo admin edita y elimina
    Route::put('/lote/{id}', [LoteController::class, 'update']);
    Route::delete('/lote/{id}', [LoteController::class, 'eliminar']);

    // Ventas — solo admin elimina y ve reportes
    Route::delete('/venta/{id}', [VentaController::class, 'destroy'])->name('venta.destroy');
    Route::get('/ventas/reportes', [VentaController::class, 'reportes'])->name('ventas.reportes');
    Route::get('/ventas/reportes/datos', [VentaController::class, 'datoReportes'])->name('ventas.reportes.datos');

    // Cuarentena
    Route::get('/admin/cuarentena', [LoteController::class, 'cuarentena'])->name('admin.cuarentena');
    Route::get('/admin/cuarentena/datos', [LoteController::class, 'datoCuarentena']);

    // Reportes de compras
    Route::get('/compras/reportes', [CompraProveedorController::class, 'reportes'])->name('compras.reportes');
    Route::get('/compras/reportes/datos', [CompraProveedorController::class, 'datoReportes'])->name('compras.reportes.datos');
});

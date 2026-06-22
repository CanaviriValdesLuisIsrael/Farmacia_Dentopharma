<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Caja;
use App\Models\MovimientoCaja;
 
class CajaController extends Controller
{
    // =========================================================
    // VISTA PRINCIPAL DE CAJA
    // =========================================================
    public function index()
    {
        $cajaActual = Caja::with('empleado.user')
            ->abierta()
            ->first();
 
        $historial = Caja::with('empleado.user')
            ->where('estado', 'cerrada')
            ->orderByDesc('id_caja')
            ->take(15)
            ->get();
 
        $movimientos = collect();
        if ($cajaActual) {
            $movimientos = MovimientoCaja::with(['empleado', 'venta', 'compra'])
                ->where('id_caja', $cajaActual->id_caja)
                ->orderByDesc('id_movimiento')
                ->get();
        }
 
        return view('admin.adm_caja', compact('cajaActual', 'historial', 'movimientos'));
    }
 
    // =========================================================
    // ABRIR CAJA
    // =========================================================
    public function abrir(Request $request)
    {
        $request->validate([
            'saldo_inicial' => 'required|numeric|min:0',
            'observacion'   => 'nullable|string|max:255',
        ]);
 
        if (Caja::cajaActual()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una caja abierta. Debe cerrarla antes de abrir otra.',
            ], 422);
        }
 
        DB::beginTransaction();
        try {
            $empleadoId = Auth::user()->empleado->id_empleado;
 
            $caja = Caja::create([
                'fecha_apertura' => now(),
                'fecha_cierre'   => null,
                'saldo_inicial'  => $request->saldo_inicial,
                'saldo_actual'   => $request->saldo_inicial,
                'saldo_final'    => 0,
                'estado'         => 'abierta',
                'id_empleado'    => $empleadoId,
                'observacion'    => $request->observacion,
            ]);
 
            MovimientoCaja::create([
                'id_caja'     => $caja->id_caja,
                'tipo'        => MovimientoCaja::TIPO_APERTURA,
                'monto'       => $request->saldo_inicial,
                'descripcion' => 'Apertura de caja',
                'fecha'       => now()->toDateString(),
                'hora'        => now()->toTimeString(),
                'id_empleado' => $empleadoId,
            ]);
 
            DB::commit();
 
            return response()->json([
                'success' => true,
                'message' => 'Caja abierta correctamente',
                'caja'    => $caja,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al abrir caja: ' . $e->getMessage(),
            ], 500);
        }
    }
 
    // =========================================================
    // CERRAR CAJA
    // =========================================================
    public function cerrar(Request $request)
    {
        $request->validate([
            'observacion' => 'nullable|string|max:255',
        ]);
 
        $caja = Caja::cajaActual();
 
        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay ninguna caja abierta.',
            ], 422);
        }
 
        DB::beginTransaction();
        try {
            $empleadoId = Auth::user()->empleado->id_empleado;
 
            $saldoFinal = (float) $caja->saldo_actual;
 
            $caja->update([
                'fecha_cierre' => now(),
                'saldo_final'  => $saldoFinal,
                'estado'       => 'cerrada',
                'observacion'  => $request->observacion ?? $caja->observacion,
            ]);
 
            MovimientoCaja::create([
                'id_caja'     => $caja->id_caja,
                'tipo'        => MovimientoCaja::TIPO_CIERRE,
                'monto'       => $saldoFinal,
                'descripcion' => 'Cierre de caja',
                'fecha'       => now()->toDateString(),
                'hora'        => now()->toTimeString(),
                'id_empleado' => $empleadoId,
            ]);
 
            DB::commit();
 
            return response()->json([
                'success'     => true,
                'message'     => 'Caja cerrada correctamente',
                'saldo_final' => number_format($saldoFinal, 2),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar caja: ' . $e->getMessage(),
            ], 500);
        }
    }
 
    // =========================================================
    // REGISTRAR MOVIMIENTO MANUAL (ingreso/egreso extra)
    // =========================================================
    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'tipo'        => 'required|in:ingreso,egreso',
            'monto'       => 'required|numeric|min:0.01',
            'descripcion' => 'required|string|max:255',
        ]);
 
        $caja = Caja::cajaActual();
 
        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay ninguna caja abierta.',
            ], 422);
        }
 
        try {
            $empleadoId = Auth::user()->empleado->id_empleado;
 
            $movimiento = MovimientoCaja::registrar($caja, [
                'tipo'        => $request->tipo,
                'monto'       => $request->monto,
                'descripcion' => $request->descripcion,
                'id_empleado' => $empleadoId,
            ]);
 
            return response()->json([
                'success'      => true,
                'message'      => 'Movimiento registrado correctamente',
                'saldo_actual' => number_format($caja->refresh()->saldo_actual, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar movimiento: ' . $e->getMessage(),
            ], 500);
        }
    }
 
    // =========================================================
    // ESTADO ACTUAL DE CAJA (AJAX — para validaciones en compras)
    // =========================================================
    public function estado()
    {
        $caja = Caja::cajaActual();
 
        return response()->json([
            'abierta' => (bool) $caja,
            'caja'    => $caja,
        ]);
    }
 
    // =========================================================
    // DETALLE DE UNA CAJA CERRADA (historial)
    // =========================================================
    public function detalle($id)
    {
        $caja = Caja::with(['empleado.user'])->findOrFail($id);
 
        $movimientos = MovimientoCaja::with(['empleado', 'venta', 'compra'])
            ->where('id_caja', $caja->id_caja)
            ->orderBy('id_movimiento')
            ->get();
 
        return response()->json([
            'caja'        => $caja,
            'movimientos' => $movimientos,
        ]);
    }
}

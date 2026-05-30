<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\LoteProducto;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    // =========================================
    // VISTA PRINCIPAL DE VENTAS
    // Administrador: ve TODAS las ventas
    // Empleado: ve SOLO sus propias ventas
    // =========================================
    public function ventas()
    {
        $user      = Auth::user();
        $esAdmin   = $user->hasRole('admin');
        $idEmpleado = $user->id_empleado;

        $query = Venta::with([
            'cliente',
            'empleado.user',
            'detalles.producto.categoria',
            'detalles.producto.laboratorio',
        ])->orderBy('id_venta', 'desc');

        // Si es empleado, filtra solo sus ventas
        if (!$esAdmin) {
            $query->where('id_empleado', $idEmpleado);
        }

        $ventas = $query->get();

        return view('admin.adm_venta', compact('ventas', 'esAdmin'));
    }

    // =========================================
    // ESTADÍSTICAS AJAX
    // Administrador: estadísticas globales
    // Empleado: solo estadísticas propias
    // =========================================
    public function estadisticas()
    {
        $user       = Auth::user();
        $esAdmin    = $user->hasRole('admin');
        $idEmpleado = $user->id_empleado;
        $hoy        = Carbon::today();

        // Venta del día por este vendedor (ambos roles)
        $ventaDiaVendedor = Venta::whereDate('fecha_venta', $hoy)
            ->where('id_empleado', $idEmpleado)
            ->sum('total_venta');

        if ($esAdmin) {
            // Admin: estadísticas globales
            $ventaDia  = Venta::whereDate('fecha_venta', $hoy)->sum('total_venta');
            $ventaMes  = Venta::whereMonth('fecha_venta', Carbon::now()->month)
                ->whereYear('fecha_venta', Carbon::now()->year)
                ->sum('total_venta');
            $ventaAnio = Venta::whereYear('fecha_venta', Carbon::now()->year)
                ->sum('total_venta');
        } else {
            // Empleado: solo sus ventas
            $ventaDia  = Venta::whereDate('fecha_venta', $hoy)
                ->where('id_empleado', $idEmpleado)->sum('total_venta');
            $ventaMes  = Venta::whereMonth('fecha_venta', Carbon::now()->month)
                ->whereYear('fecha_venta', Carbon::now()->year)
                ->where('id_empleado', $idEmpleado)->sum('total_venta');
            $ventaAnio = Venta::whereYear('fecha_venta', Carbon::now()->year)
                ->where('id_empleado', $idEmpleado)->sum('total_venta');
        }

        return response()->json([
            'ventaDiaVendedor' => number_format($ventaDiaVendedor, 2),
            'ventaDia'         => number_format($ventaDia, 2),
            'ventaMes'         => number_format($ventaMes, 2),
            'ventaAnio'        => number_format($ventaAnio, 2),
            'esAdmin'          => $esAdmin,
        ]);
    }

    // =========================================
    // ELIMINAR VENTA — Solo administrador
    // (protegido en rutas, doble validación aquí)
    // =========================================
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        DB::beginTransaction();
        try {
            $venta = Venta::with('detalles')->findOrFail($id);

            // Restaurar stock al eliminar venta
            foreach ($venta->detalles as $detalle) {
                $lote = LoteProducto::find($detalle->id_lote);
                if ($lote) {
                    $lote->cantidad_por_caja += $detalle->cantidad;
                    $lote->save();
                }
            }

            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venta eliminada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar venta',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // =========================================
    // IMPRIMIR COMPROBANTE PDF
    // Ambos roles pueden imprimir,
    // pero el empleado solo las suyas
    // =========================================
    public function imprimir($id)
    {
        $user  = Auth::user();
        $venta = Venta::with([
            'cliente',
            'empleado',
            'detalles.producto.categoria',
            'detalles.producto.laboratorio',
        ])->findOrFail($id);

        // Empleado solo puede imprimir sus propias ventas
        if (!$user->hasRole('admin') && $venta->id_empleado !== $user->id_empleado) {
            abort(403, 'No autorizado para imprimir esta venta.');
        }

        $pdf = Pdf::loadView('admin.pdf.comprobante_venta', compact('venta'));
        $pdf->setPaper('letter');

        return $pdf->stream('venta-' . $venta->id_venta . '.pdf');
    }
}

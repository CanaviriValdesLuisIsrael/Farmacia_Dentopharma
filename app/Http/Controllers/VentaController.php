<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\LoteProducto;
use App\Models\DetalleVenta;
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
        $user       = Auth::user();
        $esAdmin    = $user->hasRole('admin');
        $idEmpleado = $user->id_empleado;

        $query = Venta::with([
            'cliente',
            'empleado.user',
            'detalles.producto.categoria',
            'detalles.producto.laboratorio',
        ])->orderBy('id_venta', 'desc');

        if (!$esAdmin) {
            $query->where('id_empleado', $idEmpleado);
        }

        $ventas = $query->get();

        return view('admin.adm_venta', compact('ventas', 'esAdmin'));
    }

    // =========================================
    // VISTA REPORTES (solo admin)
    // =========================================
    public function reportes()
    {
        return view('admin.adm_reportes_ventas');
    }

    public function reportescompra()
    {
        return view('admin.adm_reportes_compras');
    }
    // =========================================
    // DATOS AJAX PARA REPORTES
    // =========================================
    public function datoReportes(Request $request)
    {
        $periodo = $request->get('periodo', 'mes');
        $hoy     = Carbon::now();

        switch ($periodo) {
            case 'semana':
                $desde = $hoy->copy()->subDays(6)->startOfDay();
                $hasta = $hoy->copy()->endOfDay();
                break;
            case 'trimestre':
                $desde = $hoy->copy()->subMonths(3)->startOfDay();
                $hasta = $hoy->copy()->endOfDay();
                break;
            case 'anio':
                $desde = $hoy->copy()->startOfYear();
                $hasta = $hoy->copy()->endOfDay();
                break;
            case 'personalizado':
                $desde = Carbon::parse($request->desde)->startOfDay();
                $hasta  = Carbon::parse($request->hasta)->endOfDay();
                break;
            default: // mes
                $desde = $hoy->copy()->startOfMonth();
                $hasta = $hoy->copy()->endOfDay();
        }

        $ventasQ = Venta::with(['cliente', 'empleado', 'detalles.producto.categoria'])
            ->whereBetween('fecha_venta', [$desde, $hasta])
            ->orderBy('fecha_venta', 'desc');

        $ventas = $ventasQ->get();

        // KPIs
        $total    = $ventas->sum('total_venta');
        $cantidad = $ventas->count();
        $promedio = $cantidad > 0 ? $total / $cantidad : 0;

        // Mejor día
        $porDia = $ventas->groupBy(fn($v) => Carbon::parse($v->fecha_venta)->format('Y-m-d'));
        $mejorDia = $porDia->map(fn($g) => $g->sum('total_venta'))->sortDesc()->keys()->first();

        // Ventas por día (para gráfica de barras)
        $ventasPorDia = $porDia->map(fn($g) => $g->sum('total_venta'))
            ->map(fn($t, $d) => ['fecha' => $d, 'total' => round($t, 2)])
            ->values();

        // Por vendedor
        $porVendedor = $ventas->groupBy('id_empleado')
            ->map(fn($g) => [
                'nombre' => $g->first()->empleado->nombre ?? 'Sin nombre',
                'total'  => round($g->sum('total_venta'), 2),
            ])->values();

        // Top 10 productos
        $detalles = DetalleVenta::with('producto')
            ->whereHas('venta', fn($q) => $q->whereBetween('fecha_venta', [$desde, $hasta]))
            ->get();

        $topProductos = $detalles->groupBy('id_producto')
            ->map(fn($g) => [
                'nombre'   => $g->first()->producto->nombre_comercial ?? '-',
                'cantidad' => $g->sum('cantidad'),
            ])
            ->sortByDesc('cantidad')
            ->take(10)
            ->values();

        // Por categoría
        $porCategoria = $detalles->groupBy(fn($d) => $d->producto->categoria->nombre ?? 'Sin categoría')
            ->map(fn($g) => [
                'categoria' => $g->first()->producto->categoria->nombre ?? 'Sin categoría',
                'total' => round($g->sum(fn($d) => $d->cantidad * $d->precio_unitario), 2)
            ])
            ->values();

        // Lista simple ventas
        $listaVentas = $ventas->map(fn($v) => [
            'id_venta'    => $v->id_venta,
            'fecha_venta' => Carbon::parse($v->fecha_venta)->format('d/m/Y'),
            'cliente'     => $v->cliente->nombre ?? 'Sin cliente',
            'vendedor'    => $v->empleado->nombre ?? '-',
            'total_venta' => $v->total_venta,
        ]);

        return response()->json([
            'kpis' => [
                'total'     => round($total, 2),
                'cantidad'  => $cantidad,
                'promedio'  => round($promedio, 2),
                'mejor_dia' => $mejorDia,
            ],
            'por_dia'       => $ventasPorDia,
            'por_vendedor'  => $porVendedor,
            'top_productos' => $topProductos,
            'por_categoria' => $porCategoria,
            'ventas'        => $listaVentas,
        ]);
    }

    // =========================================
    // ESTADÍSTICAS AJAX (dashboard)
    // =========================================
    public function estadisticas()
    {
        $user       = Auth::user();
        $esAdmin    = $user->hasRole('admin');
        $idEmpleado = $user->id_empleado;
        $hoy        = Carbon::today();

        $ventaDiaVendedor = Venta::whereDate('fecha_venta', $hoy)
            ->where('id_empleado', $idEmpleado)
            ->sum('total_venta');

        if ($esAdmin) {
            $ventaDia  = Venta::whereDate('fecha_venta', $hoy)->sum('total_venta');
            $ventaMes  = Venta::whereMonth('fecha_venta', Carbon::now()->month)
                ->whereYear('fecha_venta', Carbon::now()->year)
                ->sum('total_venta');
            $ventaAnio = Venta::whereYear('fecha_venta', Carbon::now()->year)->sum('total_venta');
        } else {
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
    // =========================================
    public function destroy($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        DB::beginTransaction();
        try {
            $venta = Venta::with('detalles')->findOrFail($id);

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

        if (!$user->hasRole('admin') && $venta->id_empleado !== $user->id_empleado) {
            abort(403, 'No autorizado para imprimir esta venta.');
        }

        $pdf = Pdf::loadView('admin.pdf.comprobante_venta', compact('venta'));
        $pdf->setPaper('letter');

        return $pdf->stream('venta-' . $venta->id_venta . '.pdf');
    }
}

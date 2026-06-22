<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\LoteProducto;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\Proveedor;
use App\Models\Producto;
 
class CompraProveedorController extends Controller
{
    // =========================================================
    // REGISTRAR COMPRA AL PROVEEDOR
    // =========================================================
    public function store(Request $request)
    {
        $request->validate([
            'id_proveedor'              => 'required|exists:proveedor,id_proveedor',
            'fecha_vencimiento'         => 'required|date|after_or_equal:today',
            'tipo_pago'                 => 'required|in:contado,credito',
            'descuento'                 => 'nullable|numeric|min:0',
            'nota'                      => 'nullable|string|max:255',
            'items'                     => 'required|array|min:1',
            'items.*.id_producto'       => 'required|exists:producto,id_producto',
            'items.*.tipo_unidad'       => 'required|in:caja,blister,unidad',
            'items.*.cantidad'          => 'required|integer|min:1',
            'items.*.unidades_por_paquete' => 'required|integer|min:1',
            'items.*.costo_por_paquete' => 'required|numeric|min:0',
        ]);
 
        DB::beginTransaction();
        try {
            $empleadoId = Auth::user()->empleado->id_empleado;
            $descuento  = (float) ($request->descuento ?? 0);
 
            // ── Verificar caja abierta ────────────────────────
            $caja = Caja::cajaActual();
 
            if (!$caja && $request->tipo_pago === 'contado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una caja abierta. Debe abrir la caja antes de registrar una compra al contado.',
                ], 422);
            }
 
            // ── Calcular total ────────────────────────────────
            $totalCompra = 0;
            foreach ($request->items as $item) {
                $totalCompra += (float) $item['costo_por_paquete'] * (int) $item['cantidad'];
            }
            $totalCompra -= $descuento;
 
            // ── Crear cabecera de compra ──────────────────────
            $compra = Compra::create([
                'fecha_compra' => now()->toDateString(),
                'total_compra' => $totalCompra,
                'descuento'    => $descuento,
                'tipo_pago'    => $request->tipo_pago,
                'estado'       => 'registrada',
                'estado_pago'  => $request->tipo_pago === 'credito' ? 'pendiente' : 'pagado',
                'fecha_pago'   => $request->tipo_pago === 'credito' ? null : now()->toDateString(),
                'nota'         => $request->nota,
                'id_empleado'  => $empleadoId,
                'id_caja'      => $caja?->id_caja,
            ]);
 
            // ── Procesar cada ítem ────────────────────────────
            foreach ($request->items as $item) {
                $cantidad           = (int)   $item['cantidad'];
                $unidadesPorPaquete = (int)   $item['unidades_por_paquete'];
                $costoPorPaquete    = (float) $item['costo_por_paquete'];
                $tipoUnidad         = $item['tipo_unidad'];
                $totalUnidades      = $cantidad * $unidadesPorPaquete;
                $precioUnitario     = $tipoUnidad === 'unidad'
                    ? $costoPorPaquete
                    : round($costoPorPaquete / $unidadesPorPaquete, 4);
 
                // Guardar detalle
                DetalleCompra::create([
                    'id_compra'            => $compra->id_compra,
                    'id_producto'          => $item['id_producto'],
                    'tipo_unidad'          => $tipoUnidad,
                    'cantidad'             => $cantidad,
                    'unidades_por_paquete' => $unidadesPorPaquete,
                    'costo_por_paquete'    => $costoPorPaquete,
                    'precio_unitario'      => $precioUnitario,
                    'subtotal'             => $costoPorPaquete * $cantidad,
                ]);
 
                // Crear lote e incrementar stock
                LoteProducto::create([
                    'numero_lote'       => 'L' . time() . rand(10, 99),
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'cantidad_por_caja' => $totalUnidades,
                    'id_producto'       => $item['id_producto'],
                    'id_compra'         => $compra->id_compra,
                    'id_proveedor'      => $request->id_proveedor,
                ]);
            }
 
            // ── Movimiento de caja (egreso) ───────────────────
            if ($caja && $request->tipo_pago === 'contado') {
                MovimientoCaja::registrar($caja, [
                    'tipo'        => MovimientoCaja::TIPO_EGRESO,
                    'monto'       => $totalCompra,
                    'descripcion' => 'Compra a proveedor #' . $compra->id_compra,
                    'id_empleado' => $empleadoId,
                    'id_compra'   => $compra->id_compra,
                ]);
            }
 
            DB::commit();
 
            return response()->json([
                'success'    => true,
                'message'    => 'Compra registrada correctamente',
                'id_compra'  => $compra->id_compra,
                'total'      => number_format($totalCompra, 2),
            ]);
 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage(),
            ], 500);
        }
    }
 
    // =========================================================
    // VISTA PRINCIPAL — listado de compras
    // =========================================================
    public function index()
    {
        $compras = Compra::with(['empleado', 'detalles.producto', 'detalles'])
            ->where('estado', 'registrada')
            ->orderByDesc('id_compra')
            ->get();
 
        $proveedores = Proveedor::orderBy('nombre')->get();
        $productos   = Producto::with('laboratorio')->orderBy('nombre_comercial')->get();
 
        return view('admin.adm_comprasproveedor', compact('compras', 'proveedores', 'productos'));
    }
 
    // =========================================================
    // DATOS AJAX PARA REPORTE DE COMPRAS
    // =========================================================
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
                $hasta = Carbon::parse($request->hasta)->endOfDay();
                break;
            default: // mes
                $desde = $hoy->copy()->startOfMonth();
                $hasta = $hoy->copy()->endOfDay();
        }
 
        $compras = Compra::with(['empleado', 'detalles.producto'])
            ->where('estado', 'registrada')
            ->whereBetween('fecha_compra', [$desde, $hasta])
            ->orderByDesc('fecha_compra')
            ->get();
 
        // ── KPIs ─────────────────────────────────────────────
        $totalGastado = $compras->sum('total_compra');
        $cantCompras  = $compras->count();
        $promedio     = $cantCompras > 0 ? $totalGastado / $cantCompras : 0;
 
        // ── Compras por día ───────────────────────────────────
        $porDia = $compras
            ->groupBy(fn($c) => Carbon::parse($c->fecha_compra)->format('Y-m-d'))
            ->map(fn($g) => ['fecha' => $g->first()->fecha_compra, 'total' => round($g->sum('total_compra'), 2)])
            ->values();
 
        // ── Top proveedores ───────────────────────────────────
        $detalles = DetalleCompra::with(['producto', 'compra.empleado'])
            ->whereHas('compra', fn($q) => $q->where('estado', 'registrada')
                ->whereBetween('fecha_compra', [$desde, $hasta]))
            ->get();
 
        // Agrupar por proveedor usando los lotes creados
        $porProveedor = LoteProducto::with('proveedor')
            ->whereHas('compra', fn($q) => $q->where('estado', 'registrada')
                ->whereBetween('fecha_compra', [$desde, $hasta]))
            ->get()
            ->groupBy('id_proveedor')
            ->map(fn($g) => [
                'proveedor' => $g->first()->proveedor->nombre ?? 'Sin nombre',
                'total'     => round($g->first()->proveedor
                    ? Compra::where('estado', 'registrada')
                        ->whereBetween('fecha_compra', [$desde, $hasta])
                        ->whereHas('lotes', fn($q) => $q->where('id_proveedor', $g->first()->id_proveedor))
                        ->sum('total_compra') : 0, 2),
                'cantidad'  => $g->count(),
            ])
            ->sortByDesc('total')
            ->values();
 
        // ── Top productos más comprados ───────────────────────
        $topProductos = $detalles
            ->groupBy('id_producto')
            ->map(fn($g) => [
                'nombre'   => $g->first()->producto->nombre_comercial ?? '-',
                'cantidad' => $g->sum('total_unidades'),
                'gasto'    => round($g->sum('subtotal'), 2),
            ])
            ->sortByDesc('cantidad')
            ->take(10)
            ->values();
 
        // ── Lista de compras ──────────────────────────────────
        $listaCompras = $compras->map(fn($c) => [
            'id_compra'    => $c->id_compra,
            'fecha_compra' => Carbon::parse($c->fecha_compra)->format('d/m/Y'),
            'empleado'     => $c->empleado->nombre ?? '-',
            'items'        => $c->detalles->count(),
            'tipo_pago'    => $c->tipo_pago,
            'descuento'    => $c->descuento,
            'total_compra' => $c->total_compra,
        ]);
 
        return response()->json([
            'kpis' => [
                'total_gastado' => round($totalGastado, 2),
                'cantidad'      => $cantCompras,
                'promedio'      => round($promedio, 2),
            ],
            'por_dia'       => $porDia,
            'por_proveedor' => $porProveedor,
            'top_productos' => $topProductos,
            'compras'       => $listaCompras,
        ]);
    }
 
    // =========================================================
    // MARCAR COMPRA A CRÉDITO COMO PAGADA
    // =========================================================
    public function pagar(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);
 
        if ($compra->tipo_pago !== 'credito' || $compra->estado_pago !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Esta compra no está pendiente de pago.',
            ], 422);
        }
 
        $caja = Caja::cajaActual();
 
        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay una caja abierta. Debe abrir la caja para registrar el pago.',
            ], 422);
        }
 
        DB::beginTransaction();
        try {
            $empleadoId = Auth::user()->empleado->id_empleado;
 
            $compra->update([
                'estado_pago' => 'pagado',
                'fecha_pago'  => now()->toDateString(),
                'id_caja'     => $compra->id_caja ?? $caja->id_caja,
            ]);
 
            MovimientoCaja::registrar($caja, [
                'tipo'        => MovimientoCaja::TIPO_EGRESO,
                'monto'       => $compra->total_compra,
                'descripcion' => 'Pago de compra a crédito #' . $compra->id_compra,
                'id_empleado' => $empleadoId,
                'id_compra'   => $compra->id_compra,
            ]);
 
            DB::commit();
 
            return response()->json([
                'success' => true,
                'message' => 'Compra marcada como pagada y descontada de caja',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }
 
    // =========================================================
    // VISTA DE REPORTE
    // =========================================================
    public function reportes()
    {
        return view('admin.adm_reportes_compras');
    }
}
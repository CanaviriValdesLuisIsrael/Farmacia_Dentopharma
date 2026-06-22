<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoteProducto;
use App\Models\Proveedor;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoteController extends Controller
{
    // =========================================
    // VISTA PRINCIPAL DE LOTES
    // =========================================
    public function loteprod()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $esAdmin     = Auth::user()->hasRole('admin');

        return view('admin.adm_lote', compact('proveedores', 'esAdmin'));
    }

    // =========================================
    // BUSCAR LOTES (AJAX) — Solo lotes con stock > 0 y no vencidos
    // =========================================
    public function buscar(Request $request)
    {
        $buscar  = $request->get('buscar', '');
        $esAdmin = Auth::user()->hasRole('admin');

        $lotes = LoteProducto::with(['producto.categoria', 'producto.laboratorio', 'proveedor'])
            ->whereHas('producto', function ($q) use ($buscar) {
                $q->where('nombre_comercial', 'LIKE', "%$buscar%");
            })
            ->where('cantidad_por_caja', '>', 0)
            ->where('fecha_vencimiento', '>=', Carbon::today())
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json([
            'lotes'   => $lotes,
            'esAdmin' => $esAdmin,
        ]);
    }

    // =========================================
    // LOTES EN RIESGO (DASHBOARD)
    // Clasifica en 3 categorías:
    //  - vencidos:   lotes con stock cuya fecha ya pasó (deben retirarse)
    //  - cuarentena: lotes con stock que vencen en los próximos 90 días
    //                (prioridad de venta FEFO)
    //  - sin_stock:  PRODUCTOS sin ningún lote vigente con stock > 0
    //                (no por lote suelto, para no generar ruido si el
    //                 producto aún tiene stock en otro lote)
    // =========================================
    public function lotesRiesgo()
    {
        $hoy              = Carbon::today();
        $limiteCuarentena = Carbon::today()->addDays(90);

        // VENCIDOS
        $vencidos = LoteProducto::with(['producto.laboratorio', 'producto.categoria', 'proveedor'])
            ->where('cantidad_por_caja', '>', 0)
            ->where('fecha_vencimiento', '<', $hoy)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // CUARENTENA (≤ 90 días)
        $cuarentena = LoteProducto::with(['producto.laboratorio', 'producto.categoria', 'proveedor'])
            ->where('cantidad_por_caja', '>', 0)
            ->whereBetween('fecha_vencimiento', [$hoy, $limiteCuarentena])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // SIN STOCK (por producto: ningún lote vigente con cantidad > 0)
        $sinStock = Producto::with(['laboratorio', 'categoria', 'lotes'])
            ->get()
            ->filter(function ($producto) use ($hoy) {
                $stockVigente = $producto->lotes
                    ->filter(fn($l) => $l->cantidad_por_caja > 0 && Carbon::parse($l->fecha_vencimiento) >= $hoy)
                    ->sum('cantidad_por_caja');

                return $stockVigente <= 0;
            })
            ->values();

        return response()->json([
            'vencidos'   => $vencidos,
            'cuarentena' => $cuarentena,
            'sin_stock'  => $sinStock,
            'conteos'    => [
                'vencidos'   => $vencidos->count(),
                'cuarentena' => $cuarentena->count(),
                'sin_stock'  => $sinStock->count(),
            ],
        ]);
    }

    // =========================================
    // CUARENTENA: vencidos + sin stock (solo admin)
    // =========================================
    public function cuarentena()
    {
        return view('admin.adm_cuarentena');
    }

    public function datoCuarentena()
    {
        $hoy = Carbon::today();

        // Vencidos con stock
        $vencidos = LoteProducto::with(['producto.laboratorio', 'proveedor'])
            ->where('fecha_vencimiento', '<', $hoy)
            ->where('cantidad_por_caja', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // Todos los sin stock
        $sinStock = LoteProducto::with(['producto.laboratorio', 'proveedor'])
            ->where('cantidad_por_caja', '<=', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json([
            'vencidos'  => $vencidos,
            'sin_stock' => $sinStock,
        ]);
    }

    // =========================================
    // EDITAR LOTE — Solo administrador
    // =========================================
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado'], 403);
        }

        $request->validate([
            'cantidad_por_caja' => 'required|numeric|min:0',
        ]);

        $lote = LoteProducto::findOrFail($id);
        $lote->update(['cantidad_por_caja' => $request->cantidad_por_caja]);

        return response()->json(['success' => true, 'mensaje' => 'Stock actualizado correctamente']);
    }

    // =========================================
    // ELIMINAR LOTE — Solo administrador
    // =========================================
    public function eliminar($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado'], 403);
        }

        $lote = LoteProducto::findOrFail($id);
        $lote->delete();

        return response()->json(['success' => true, 'mensaje' => 'Lote eliminado correctamente']);
    }
}
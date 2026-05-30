<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoteProducto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;

class LoteController extends Controller
{
    // =========================================
    // VISTA PRINCIPAL DE LOTES
    // Ambos roles pueden acceder
    // =========================================
    public function loteprod()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $esAdmin     = Auth::user()->hasRole('admin');

        return view('admin.adm_lote', compact('proveedores', 'esAdmin'));
    }

    // =========================================
    // BUSCAR LOTES (AJAX)
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
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        // Añadir el rol al response para controlar botones en JS
        return response()->json([
            'lotes'   => $lotes,
            'esAdmin' => $esAdmin,
        ]);
    }

    // =========================================
    // LOTES EN RIESGO (DASHBOARD)
    // =========================================
    public function lotesRiesgo()
    {
        $lotes = LoteProducto::with(['producto.laboratorio', 'producto.categoria', 'proveedor'])
            ->where('cantidad_por_caja', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return response()->json($lotes);
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
            'cantidad_por_caja' => 'required|numeric|min:1',
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

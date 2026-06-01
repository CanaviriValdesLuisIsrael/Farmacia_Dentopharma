<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\LoteProducto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    // VISTA PRINCIPAL
    public function index()
    {
        $categorias   = \App\Models\Categoria::all();
        $laboratorios = \App\Models\Laboratorio::all();

        return view('admin.producto', compact('categorias', 'laboratorios'));
    }

    // =========================================
    // CREAR PRODUCTO — Solo admin
    // =========================================
    public function crear(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado'], 403);
        }

        try {
            $request->validate([
                'nombre_comercial' => 'required|string|max:255',
                'descripcion'      => 'required|string',
                'concentracion'    => 'required|string',
                'precio_referencia'=> 'required|numeric|min:0',
                'id_categoria'     => 'required|exists:categoria,id_categoria',
                'id_laboratorio'   => 'required|exists:laboratorio,id_laboratorio',
            ]);

            Producto::create([
                'nombre_comercial'  => $request->nombre_comercial,
                'descripcion'       => $request->descripcion,
                'concentracion'     => $request->concentracion,
                'precio_referencia' => $request->precio_referencia,
                'id_categoria'      => $request->id_categoria,
                'id_laboratorio'    => $request->id_laboratorio,
            ]);

            return response()->json(['success' => true, 'mensaje' => 'Producto creado correctamente']);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->validator->errors()->first()
            ], 422);
        }
    }

    // =========================================
    // BUSCAR PRODUCTOS (AJAX)
    // - Para gestión (admin/empleado): devuelve todos
    // - dashboard.js filtra vencidos y sin stock en el front
    //   pero el catálogo de ventas solo muestra disponibles
    // =========================================
    public function buscar(Request $request)
    {
        $buscar = $request->get('buscar', '');
        // Contexto: si viene de catálogo (solo disponibles) o gestión (todos)
        $soloDisponibles = $request->boolean('solo_disponibles', false);

        $hoy = Carbon::today();

        $query = Producto::with(['categoria', 'laboratorio', 'lotes'])
            ->where('nombre_comercial', 'LIKE', "%$buscar%");

        $productos = $query->get();

        $productos = $productos->map(function ($prod) use ($soloDisponibles, $hoy) {
            $lotes = $prod->lotes;

            // Stock total considerando solo lotes vigentes y con stock
            $prod->stock_total = $lotes
                ->filter(fn($l) => $l->cantidad_por_caja > 0 && Carbon::parse($l->fecha_vencimiento) >= $hoy)
                ->sum('cantidad_por_caja');

            $prod->tiene_lote_vencido = $lotes
                ->filter(fn($l) => Carbon::parse($l->fecha_vencimiento) < $hoy)
                ->isNotEmpty();

            $prod->tiene_sin_stock = $lotes
                ->filter(fn($l) => $l->cantidad_por_caja <= 0)
                ->isNotEmpty();

            return $prod;
        });

        // El catálogo solo muestra productos con stock disponible y sin vencer
        if ($soloDisponibles) {
            $productos = $productos->filter(fn($p) => $p->stock_total > 0);
        }

        return response()->json($productos->values());
    }

    public function cambiarAvatar(Request $request, $id)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $producto = Producto::findOrFail($id);

        if ($request->hasFile('avatar')) {
            $file   = $request->file('avatar');
            $nombre = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('productos', $nombre, 'public');

            if ($producto->avatar && file_exists(storage_path('app/public/productos/' . $producto->avatar))) {
                unlink(storage_path('app/public/productos/' . $producto->avatar));
            }

            $producto->avatar = $nombre;
            $producto->save();
        }

        return response()->json(['success' => true, 'mensaje' => 'Imagen actualizada correctamente']);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado'], 403);
        }

        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre_comercial'  => 'required|unique:producto,nombre_comercial,' . $id . ',id_producto',
            'descripcion'       => 'required',
            'concentracion'     => 'required',
            'precio_referencia' => 'required|numeric|min:0',
            'id_categoria'      => 'required',
            'id_laboratorio'    => 'required',
        ]);

        $producto->update([
            'nombre_comercial'  => $request->nombre_comercial,
            'descripcion'       => $request->descripcion,
            'concentracion'     => $request->concentracion,
            'precio_referencia' => $request->precio_referencia,
            'id_categoria'      => $request->id_categoria,
            'id_laboratorio'    => $request->id_laboratorio,
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Producto actualizado correctamente']);
    }

    public function eliminar($id)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado'], 403);
        }

        $producto = Producto::findOrFail($id);

        if ($producto->avatar && file_exists(storage_path('app/public/productos/' . $producto->avatar))) {
            unlink(storage_path('app/public/productos/' . $producto->avatar));
        }

        $producto->delete();

        return response()->json(['success' => true, 'mensaje' => 'Producto eliminado correctamente']);
    }

    // =========================================
    // CREAR LOTE — Ambos roles (admin y empleado)
    // =========================================
    public function storeLote(Request $request)
    {
        $request->validate([
            'id_producto'       => 'required|exists:producto,id_producto',
            'id_proveedor'      => 'required|exists:proveedor,id_proveedor',
            'cantidad_por_caja' => 'required|numeric|min:1',
            'fecha_vencimiento' => 'required|date|after_or_equal:today',
        ]);

        LoteProducto::create([
            'numero_lote'       => 'L' . time(),
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'cantidad_por_caja' => $request->cantidad_por_caja,
            'id_producto'       => $request->id_producto,
            'id_proveedor'      => $request->id_proveedor,
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Lote agregado correctamente']);
    }
}

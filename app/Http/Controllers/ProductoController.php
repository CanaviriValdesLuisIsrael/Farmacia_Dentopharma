<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\LoteProducto;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    // VISTA PRINCIPAL
    public function index()
    {
        $categorias = \App\Models\Categoria::all();
        $laboratorios = \App\Models\Laboratorio::all();

        return view('admin.producto', compact('categorias', 'laboratorios'));
    }


    // CREAR PRODUCTO
    public function crear(Request $request)
    {
        try {
            $request->validate([
                'nombre_comercial' => 'required',
                'descripcion' => 'required',
                'concentracion' => 'required',
                'precio_referencia' => 'required|numeric',
                'id_categoria' => 'required',
                'id_laboratorio' => 'required',
            ], [
                'nombre_comercial.unique' => 'El producto ya existe'
            ]);

            Producto::create([
                'nombre_comercial' => $request->nombre_comercial,
                'descripcion' => $request->descripcion,
                'concentracion' => $request->concentracion,
                'precio_referencia' => $request->precio_referencia,
                'id_categoria' => $request->id_categoria,
                'id_laboratorio' => $request->id_laboratorio,
            ]);

            return response()->json([
                'success' => true,
                'mensaje' => 'Producto creado correctamente'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->validator->errors()->first()
            ], 422);
        }
    }

    // 
    public function buscar(Request $request)
    {
        $buscar = $request->get('buscar');

        $productos = Producto::with(['categoria', 'laboratorio', 'lotes'])
            ->where('nombre_comercial', 'LIKE', "%$buscar%")
            ->get();

        $productos->map(function ($prod) {
            $prod->stock_total = $prod->lotes->sum('cantidad_por_caja');
            return $prod;
        });

        return response()->json($productos);
    }

    public function cambiarAvatar(Request $request, $id)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $producto = Producto::findOrFail($id);

        if ($request->hasFile('avatar')) {

            $file = $request->file('avatar');
            $nombre = time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('productos', $nombre, 'public');

            if ($producto->avatar && file_exists(storage_path('app/public/productos/' . $producto->avatar))) {
                unlink(storage_path('app/public/productos/' . $producto->avatar));
            }

            $producto->avatar = $nombre;
            $producto->save();
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Imagen actualizada correctamente'
        ]);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre_comercial' => 'required|unique:producto,nombre_comercial,' . $id . ',id_producto',
            'descripcion' => 'required',
            'concentracion' => 'required',
            'precio_referencia' => 'required|numeric',
            'id_categoria' => 'required',
            'id_laboratorio' => 'required',
        ]);

        $producto->update([
            'nombre_comercial' => $request->nombre_comercial,
            'descripcion' => $request->descripcion,
            'concentracion' => $request->concentracion,
            'precio_referencia' => $request->precio_referencia,
            'id_categoria' => $request->id_categoria,
            'id_laboratorio' => $request->id_laboratorio,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Producto actualizado correctamente'
        ]);
    }
    //  ELIMINAR PRODUCTO
    public function eliminar($id)
    {
        $producto = Producto::findOrFail($id);

        // eliminar imagen si existe
        if ($producto->avatar && file_exists(storage_path('app/public/productos/' . $producto->avatar))) {
            unlink(storage_path('app/public/productos/' . $producto->avatar));
        }

        $producto->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Producto eliminado correctamente'
        ]);
    }

    public function storeLote(Request $request)
    {
        $request->validate([
            'id_producto' => 'required',
            'id_proveedor' => 'required',
            'cantidad_por_caja' => 'required|numeric|min:1',
            'fecha_vencimiento' => 'required|date|after_or_equal:today'
        ]);

        LoteProducto::create([
            'numero_lote' => 'L' . time(),
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'cantidad_por_caja' => $request->cantidad_por_caja,
            'id_producto' => $request->id_producto,
            'id_proveedor' => $request->id_proveedor,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Se agrego correctamente'
        ]);
    }
}

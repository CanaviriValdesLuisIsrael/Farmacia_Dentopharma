<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    //
    public function crearCat(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:categoria,nombre'
        ], [
            'nombre.required' => 'El nombre de la presentacion es obligatorio.',
            'nombre.unique' => 'El nombre ya existe'
        ]);

        $lab = Categoria::create([
            'nombre' => $request->nombre
        ]);
        return response()->json([
            'success' => true,
            'mensaje' => 'Presentacion creada correctamente'
        ]);
    }
    public function listar(Request $request)
    {
        $buscar = $request->buscar;

        $categorias = Categoria::when($buscar, function ($query) use ($buscar) {
            $query->where('nombre', 'like', '%' . $buscar . '%');
        })->orderBy('nombre', 'asc')->get();

        return response()->json($categorias);
    }

    public function eliminar($id)
    {
        $cat = Categoria::findOrFail($id);

        // validar relación (opcional pero recomendable)
        if ($cat->productos()->count() > 0) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se puede eliminar'
            ]);
        }

        $cat->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Presentación eliminada correctamente'
        ]);
    }

    public function update(Request $request, $id)
    {
        $cat = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|unique:categoria,nombre,' . $id . ',id_categoria'
        ]);

        $cat->update([
            'nombre' => $request->nombre
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Presentación actualizada correctamente'
        ]);
    }
}

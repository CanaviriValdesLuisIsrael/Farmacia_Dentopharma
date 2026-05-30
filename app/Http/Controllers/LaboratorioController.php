<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratorio;

class LaboratorioController extends Controller
{
    
    public function crearlab(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:laboratorio,nombre',
            'telefono' => 'required|unique:laboratorio,telefono',
            'direccion' => 'nullable'
        ], [
            'nombre.required' => 'El nombre del laboratorio es obligatorio.',
            'nombre.unique' => 'El laboratorio ya existe.',
            'telefono.unique' => 'El nro de telefono ya existe'
        ]);

        $lab = Laboratorio::create([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Laboratorio creado correctamente'
        ]);
    }
    public function listar(Request $request)
    {
        $buscar = $request->buscar;

        $laboratorios = Laboratorio::when($buscar, function ($query) use ($buscar) {
            $query->where('nombre', 'like', '%' . $buscar . '%');
        })->orderBy('nombre', 'asc')->get();

        return response()->json($laboratorios);
    }
    public function cambiarLogo(Request $request, $id)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'logo.required' => 'Debe seleccionar una imagen.',
            'logo.image' => 'El archivo debe ser una imagen.',
        ]);

        $lab = Laboratorio::findOrFail($id);

        if ($request->hasFile('logo')) {

            $file = $request->file('logo');
            $nombre = time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('laboratorios', $nombre, 'public');

            // eliminar anterior
            if ($lab->avatar && file_exists(storage_path('app/public/laboratorios/' . $lab->avatar))) {
                unlink(storage_path('app/public/laboratorios/' . $lab->avatar));
            }

            // GUARDAR EN avatar (NO logo)
            $lab->avatar = $nombre;
            $lab->save();
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Logo actualizado correctamente'
        ]);
    }
    public function eliminar($id)
    {
        $lab = Laboratorio::findOrFail($id);

        // VALIDAR RELACIÓN
        if ($lab->productos()->count() > 0) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se puede eliminar'
            ]);
        }

        // eliminar logo si existe
        if ($lab->avatar && file_exists(storage_path('app/public/laboratorios/' . $lab->avatar))) {
            unlink(storage_path('app/public/laboratorios/' . $lab->avatar));
        }

        $lab->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Laboratorio eliminado correctamente'
        ]);
    }
    public function update(Request $request, $id)
    {
        $lab = Laboratorio::findOrFail($id);

        $request->validate([
            'nombre' => 'required|unique:laboratorio,nombre,' . $id . ',id_laboratorio',
            'telefono' => 'required|unique:laboratorio,telefono,' . $id . ',id_laboratorio',
            'direccion' => 'nullable'
        ]);

        $lab->update([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Laboratorio actualizado correctamente'
        ]);
    }
}

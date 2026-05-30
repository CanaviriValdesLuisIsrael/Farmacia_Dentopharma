<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function proveed()
    {
        return view('admin.adm_proveedor');
    }
    public function crearProv(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:proveedor,nombre',
            'nro_contacto' => 'required|unique:proveedor,nro_contacto',
            'correo' => 'required|email|unique:proveedor,correo',
            'direccion' => 'nullable'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.unique' => 'El proveedor ya existe.',
            'nro_contacto.unique' => 'El nro de telefono ya existe',
            'correo.unique' => 'El correo ya existe'
        ]);

        $lab = Proveedor::create([
            'nombre' => $request->nombre,
            'nro_contacto' => $request->nro_contacto,
            'correo' => $request->correo,
            'direccion' => $request->direccion
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Proveedor creado correctamente'
        ]);
    }
    public function listar(Request $request)
    {
        $buscar = $request->buscar;

        $proveedores = Proveedor::when($buscar, function ($query) use ($buscar) {
            $query->where('nombre', 'like', '%' . $buscar . '%');
        })->orderBy('nombre', 'asc')->get();

        return response()->json($proveedores);
    }
    public function cambiarAvatar(Request $request, $id)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'logo.required' => 'Debe seleccionar una imagen.',
            'logo.image' => 'El archivo debe ser una imagen.',
        ]);

        $prov = Proveedor::findOrFail($id);

        if ($request->hasFile('logo')) {

            $file = $request->file('logo');
            $nombre = time() . '.' . $file->getClientOriginalExtension();

            $file->storeAs('proveedores', $nombre, 'public');

            // eliminar anterior
            if ($prov->avatar && file_exists(storage_path('app/public/proveedores/' . $prov->avatar))) {
                unlink(storage_path('app/public/proveedores/' . $prov->avatar));
            }

            $prov->avatar = $nombre;
            $prov->save();
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Avatar actualizado correctamente'
        ]);
    }

    public function eliminar($id)
    {
        $prov = Proveedor::findOrFail($id);

        if ($prov->lotes()->count() > 0) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No se puede eliminar'
            ]);
        }

        if ($prov->avatar && file_exists(storage_path('app/public/proveedores/' . $prov->avatar))) {
            unlink(storage_path('app/public/proveedores/' . $prov->avatar));
        }

        $prov->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Proveedor eliminado correctamente'
        ]);
    }
    public function update(Request $request, $id)
    {
        $prov = Proveedor::findOrFail($id);

        $request->validate([
            'nombre' => 'required|unique:proveedor,nombre,' . $id . ',id_proveedor',
            'nro_contacto' => 'required|unique:proveedor,nro_contacto,' . $id . ',id_proveedor',
            'correo' => 'required|email|unique:proveedor,correo,' . $id . ',id_proveedor',
            'direccion' => 'nullable'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.unique' => 'El proveedor ya existe.',
            'nro_contacto.unique' => 'El nro de telefono ya existe',
            'correo.unique' => 'El correo ya existe'
        ]);

        $prov->update($request->all());

        return response()->json([
            'success' => true,
            'mensaje' => 'Proveedor actualizado correctamente'
        ]);
    }
}

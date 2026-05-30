<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empleado;


class UsuarioController extends Controller
{
    // Mostrar información de un usuario y su empleado
    public function show($id)
    {
        $user = User::with('empleado')->findOrFail($id);
        
        return view('usuarios.show', compact('user'));
    }

    // Actualizar datos del empleado relacionado al usuario
    public function update(Request $request, $id)
    {
        $request->validateWithBag('datosErrors',[
            'nro_contacto' => 'required|numeric|min:8',
            'correo' => 'required|email',
            'direccion' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:100',
        ]);

        $user = User::findOrFail($id);

        $empleado = $user->empleado;

        if (!$empleado) {
            // Crear empleado automáticamente si no existe
            $empleado = new Empleado();
            $empleado->id_empleado = $user->id_empleado; // asignar PK según usuario
        }

        $empleado->nro_contacto = $request->input('nro_contacto');
        $empleado->direccion = $request->input('direccion');
        $user->email = $request->input('correo');
        $empleado->cargo = $request->input('cargo');

        $empleado->save();

        return back()->with('success_datos', 'Datos actualizados correctamente.');
    }
//cambiar contrasenia
    public function cambiarContra(Request $request)
    {
        $user = auth()->user();

        $request->validateWithBag('passwordErrors', [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.'],'passwordErrors')
                ->with('showModal', true); // Mantener el modal abierto
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success_password', 'Contraseña cambiada correctamente.')
            ->with('showModal', true); // Mantener abierto el modal
    }
//cambiar avatar
    public function cambiarAvatar(Request $request)
    {
        $request->validateWithBag('avatarErrors', [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('avatars', $filename, 'public');
            
            // Eliminar el avatar anterior si existía
            if ($user->avatar && file_exists(storage_path('app/public/avatars/' . $user->avatar))) {
                unlink(storage_path('app/public/avatars/' . $user->avatar));
            }

            $user->avatar = $filename;
            $user->save();
        }

        return back()->with('success_avatar', 'Avatar actualizado correctamente.')->with('showAvatarModal', true);;
    }
}

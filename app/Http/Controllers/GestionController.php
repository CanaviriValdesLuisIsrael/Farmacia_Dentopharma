<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Empleado;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Mail;
use App\Mail\BienvenidaEmpleado;

class GestionController extends Controller
{
    /* public function showLoginForm()
    {
        return view('auth.login');
    }

*/
    public function registroEmp()
    {
        return view('admin.adm_usuario');
    }


    public function register(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required',
                'apellido' => 'required',
                'fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'ci' => 'required|unique:empleado,ci',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8'
            ], [
                'ci.unique' => 'El CI ya está registrado.',
                'email.unique' => 'El correo ya existe.',
                'fecha_nacimiento.before_or_equal' => 'El empleado debe ser mayor de 18 años.',
            ]);

            $role = Role::where('name', 'empleado')->first();

            $empleado = Empleado::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'ci' => $request->ci,
            ]);

            $user = User::create([
                'name' => $request->nombre, // 🔥 corregido
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $role->id,
                'id_empleado' => $empleado->id_empleado,
            ]);

            // Enviar correo de bienvenida con credenciales
            try {
                Mail::to($request->email)->send(
                    new BienvenidaEmpleado($request->nombre, $request->email, $request->password)
                );
            } catch (\Exception $e) {
                \Log::error('Error enviando correo al empleado: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'mensaje' => 'Empleado creado correctamente. Se envió un correo con sus credenciales.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->validator->errors()->first()
            ], 422);
        }
    }
    public function eliminarUsuario(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'user_id' => 'required|exists:users,id',
            ]);

            $admin = Auth::user();

            if ($admin->role->name !== 'admin') {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No autorizado'
                ], 403);
            }

            if (!Hash::check($request->current_password, $admin->password)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'Contraseña incorrecta'
                ], 422);
            }

            $user = User::findOrFail($request->user_id);

            if ($user->id == $admin->id) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No puedes eliminarte a ti mismo'
                ], 422);
            }

            if ($user->empleado) {
                $user->empleado->delete();
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'mensaje' => 'Usuario eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al eliminar usuario'
            ], 500);
        }
    }

    protected function redirectToRole()
    {
        $role = Auth::user()->role->name;
        if ($role === 'admin') {
            return redirect('/admin/dashboard');
        } elseif ($role === 'empleado') {
            return redirect('/empleado/dashboard');
        } else {
            return redirect('/dashboard');
        }
    }
}

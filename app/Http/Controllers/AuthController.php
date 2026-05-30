<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class AuthController extends Controller
{
    // Mostrar un formulario
    public function showLoginForm(){
        return view('auth.login');
    }

// registro para siguiente modulo
/*
    public function showRegisterForm(){
        return view('auth.register');
    }

*/
    //verificar las credenciales del usuario que se quiere autenticar 
    public function login(Request $request){
        $credentials=$request->only('email','password');
        //verificamos las credenciales con lo que hay en la base de datos
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return $this->redirectToRole ();
        }
        return back()->withErrors(['email'=>'credenciales incorectas']);
    }


//registro al inicio del login 
/*
    public function register(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required |min:8|confirmed'
        ]);

        //que rol
        $role=Role::where('name','empleado')->first(); //busca un rol bajo la condicion de que el nombre coincida con admin

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role_id'=> $role->id
        ]);

        Auth::login($user);
        return $this->redirectToRole ();
    }
*/
    protected function redirectToRole(){
        $role=Auth::user()->role->name;
        if($role==='admin'){
            return redirect('/admin/dashboard');
        }elseif($role==='empleado'){
            return redirect('/admin/dashboard');
        }else{
            return redirect('/');
        }
    }

    //cerrar sesion
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

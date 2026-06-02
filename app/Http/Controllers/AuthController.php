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

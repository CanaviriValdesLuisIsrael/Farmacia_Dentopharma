<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Role;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Laboratorio;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function datosper()
    {
        return view('admin.datospersonales');
    }

    public function atributo()
    {
        $esAdmin = Auth::user()->hasRole('admin');
        return view('admin.adm_atributo', compact('esAdmin'));
    }

    public function gesprod()
    {
        $categorias   = Categoria::orderBy('nombre')->get();
        $laboratorios = Laboratorio::orderBy('nombre')->get();
        $proveedores  = Proveedor::orderBy('nombre')->get();
        $esAdmin      = Auth::user()->hasRole('admin');

        return view('admin.adm_producto', compact('categorias', 'laboratorios', 'proveedores', 'esAdmin'));
    }

    // Buscar empleados (AJAX)
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $empleados = Empleado::with('user.role')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', '%' . $buscar . '%');
            })
            ->get();

        return response()->json($empleados);
    }
}

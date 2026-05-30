@include('layouts.header')

<link rel="stylesheet" href="{{ asset('css/registro.css') }}">
<title>Admin | Editar Datos</title>

<!-- Tell the browser to be responsive to screen width -->

@include('layouts.nav')

<!-- crear usuario -->
<div class="modal fade" id="crearusuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">crear usuario</h3>
                    <button data-dismiss="modal" aria-label="close" class="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    
                    <div id="mensaje-global"></div>
                    <form action="{{ route('admin.registrar.usuario') }}" method="POST" id="form-crear">
                        @csrf
                        <div class="form-group">
                            <label for="nombre">Nombres</label>
                            <input id="nombre" type="text" name="nombre" class="form-control"
                                placeholder="ingrese nombre" value="{{ old('nombre') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellidos</label>
                            <input id="apellido" type="text" name="apellido" class="form-control"
                                placeholder="ingrese apellidos" value="{{ old('apellido') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="edad">Nacimiento</label>
                            <input id="edad" type="date" name="fecha_nacimiento" class="form-control"
                                max="{{ date('Y-m-d', strtotime('-18 years')) }}" value="{{ old('fecha_nacimiento') }}"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="ci">CI</label>
                            <input id="ci" type="text" name="ci" class="form-control"
                                placeholder="ingrese ci" value="{{ old('ci') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="ci">Email</label>
                            <input id="email" type="email" name="email" class="form-control"
                                placeholder="ingrese correo" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" class="form-control"
                                placeholder="ingrese password" required>
                        </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right m-1">Guardar</button>
                    <button type="button" data-dismiss="modal"
                        class="btn btn-outline-secondary float-right m-1">Cerrar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gestion usuarios
                        @if (auth()->user()->hasRole('admin'))
                            <button type="button" data-toggle="modal" data-target="#crearusuario"
                                class="btn bg-gradient-primary">
                                Crear usuario
                            </button>
                        @endif
                    </h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Gestion usuario</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>


    <section>
        <div class="container-fluid">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Buscar usuario</h3>
                    <div class="input-group">
                        <input type="text" id="buscarusuario" class="form-control float-left"
                            placeholder="Ingrese nombre de usuario">
                        <div class="input-group-append"><button class="btn btn-default"><i
                                    class="fas fa-search"></i></button></div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="empleados" class="row d-flex align-items-stretch"></div>

                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </section>


</div>


{{-- eliminar usuario--}}

<div class="modal fade" id="eliminar_usuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirmar Accion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="mensaje-eliminar"></div>
                <div class="text-center">
                    <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}"
                        class="profile-user-img img-fluid img-circle">


                </div>
                <div class="text-center">
                    <b>{{ auth()->user()->empleado->nombre }}</b>
                </div>

                {{-- FORMULARIO --}}
                
                <form id="eliminaempleado" action="{{ route('usuario.eliminar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id_eliminar">
                    <label for="current_password">Necesitamos su password para continuar</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                        </div>
                        <input type="password" name="current_password" class="form-control"
                            placeholder="Ingrese contraseña actual" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="btnGuardarempleado" class="btn bg-gradient-primary">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
<script>
    const usuarioAuth = {
        id: {{ auth()->id() }},
        role: "{{ auth()->user()->role->name }}"
    };
</script>
<script src="{{ asset('js/gestion_usuario.js') }}"></script>
<script src="{{ asset('js/Usuario.js') }}"></script>

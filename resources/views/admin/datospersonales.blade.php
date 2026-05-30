@include('layouts.header')
<title>Admin | Editar Datos</title>

<!-- Tell the browser to be responsive to screen width 
aqui colocas tu navegador

-->

@include('layouts.nav') 





<!-- Modelo Cambiar Contraseña -->
<div class="modal fade" id="cambiocontra" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cambiar contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}"
                        class="profile-user-img img-fluid img-circle">


                </div>
                <div class="text-center">
                    <b>{{ auth()->user()->empleado->nombre }}</b>
                </div>
                {{-- MENSAJES DE ALERTA --}}
                @if (session('success_password'))
                    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                        {{ session('success_password') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->passwordErrors->any())
                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->passwordErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Animación automática de las alertas --}}
                <style>
                    .alert-slide {
                        transition: all 0.8s ease;
                        opacity: 1;
                        transform: translateY(0);
                    }

                    .alert-slide.hide {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                </style>


                {{-- FORMULARIO --}}
                <form id="formCambioPassword" action="{{ route('usuario.cambiarContra') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                        </div>
                        <input type="password" name="current_password" class="form-control"
                            placeholder="Ingrese contraseña actual" required>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="new_password" class="form-control"
                            placeholder="Ingrese nueva contraseña" required>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="new_password_confirmation" class="form-control"
                            placeholder="Confirme nueva contraseña" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="btnGuardarPassword" class="btn bg-gradient-primary">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modelo para cambiar avatar -->
<div class="modal fade" id="cambioavatar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cambiar perfil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}"
                        class="profile-user-img img-fluid img-circle" alt="Avatar" width="120" height="120">

                </div>
                <div class="text-center">
                    <b>{{ auth()->user()->empleado->nombre }}</b>
                </div>



                {{-- MENSAJES DE ALERTA --}}
                @if (session('success_avatar'))
                    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                        {{ session('success_avatar') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->avatarErrors->any())
                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->avatarErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif


                {{-- Animación automática de las alertas --}}
                <style>
                    .alert-slide {
                        transition: all 0.8s ease;
                        opacity: 1;
                        transform: translateY(0);
                    }

                    .alert-slide.hide {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                </style>


                {{-- FORMULARIO --}}
                <form id="formCambioavatar" enctype="multipart/form-data"
                    action="{{ route('usuario.cambiarAvatar') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3 ml-5 mt-2">
                        <input type="file" name="avatar" class="input-group">
                        <input type="hidden" name="funcion" value="cambiar_avatar">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="btnGuardarPassword" class="btn bg-gradient-primary">
                            Guardar
                        </button>
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
                    <h1>Datos personales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Datos Personales</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!--se empieza a modificar-->

    <section>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-success card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img src="{{ auth()->user()->avatar ? asset('storage/avatars/' . auth()->user()->avatar) : asset('img/avatar5.png') }}"
                                        class="profile-user-img img-fluid img-circle" alt="Avatar">


                                </div>
                                <div class="text-center mt-2">
                                    <button type="button" data-toggle="modal" data-target="#cambioavatar"
                                        class="btn btn-primary btn-sm">Cambiar avatar</button>
                                </div>

                                <h3 class="profile-username text-center text-primary">
                                    {{ucwords( auth()->user()->empleado->nombre) }}</h3>
                                <p class="text-muted text-center">{{ucwords( auth()->user()->empleado->apellido )}}</p>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b style="color: #1f31b8">Edad</b><a
                                            class="float-right">{{ auth()->user()->empleado->edad }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b style="color: #1f31b8">DNI</b><a
                                            class="float-right">{{ auth()->user()->empleado->ci }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b style="color: #1f31b8">Tipo Usuario</b>
                                        <span
                                            class="float-right badge badge-primary">{{ auth()->user()->role->name }}</span>
                                    </li>
                                    <button type="button" class="btn bg-gradient-primary" data-toggle="modal"
                                        data-target="#cambiocontra"> Cambiar contraseña </button>


                                </ul>
                            </div>
                        </div>
                        <div class="card card-success">
                            <div class="card-header bg-gradient-danger">
                                <h3 class="card-title ">Sobre mi</h3>
                            </div>
                            <div class="card-body">
                                <strong style="color: #1f31b8">
                                    <i class="fas fa-phone mr-1"></i>Telefono
                                </strong>
                                <p class="text-muted">{{ auth()->user()->empleado->nro_contacto }}</p>
                                <strong style="color: #1f31b8">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Residencia
                                </strong>
                                <p class="text-muted">{{ auth()->user()->empleado->direccion }}</p>
                                <strong style="color: #1f31b8">
                                    <i class="fas fa-at mr-1"></i>Correo
                                </strong>
                                <p class="text-muted">{{ auth()->user()->email }}</p>
                                <strong style="color: #1f31b8">
                                    <i class="fas fa-smile-wink mr-1"></i>sexo
                                </strong>
                                <p class="text-muted">{{ auth()->user()->empleado->sexo }}</p>
                                <strong style="color: #1f31b8">
                                    <i class="fas fa-pencil mr-1"></i>Informacion adicional
                                </strong>
                                <p class="text-muted">{{ auth()->user()->empleado->cargo }}</p>
                                <button id="btnEditar" class="btn btn-block bg-gradient-primary">Editar</button>
                            </div>
                            <div class="card-footer"></div>
                            <p class="text-muted">click en boton si desea editar</p>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card card-success">
                            <div class="card-header bg-gradient-danger">
                                <h3 class="card-title">Editar datos personales</h3>
                            </div>
                            <div class="card-body">
                                {{-- MENSAJES DE ALERTA --}}
                                @if (session('success_datos'))
                                    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                                        {{ session('success_datos') }}
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if ($errors->datosErrors->any())
                                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->datosErrors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <form action="{{ route('usuario.update', auth()->user()->id) }}" id="formGuardar"
                                    method="POST" class="form-horizontal">

                                    @csrf
                                    @method('PUT')
                                    <div class="form-group row">
                                        <label for="telefono" class="col-sm-2 col-form-label">Telefono</label>
                                        <div class="col-sm-10">
                                            <input type="number" id="telefono" class="form-control"
                                                name="nro_contacto"
                                                value="{{ auth()->user()->empleado->nro_contacto }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="residencia" class="col-sm-2 col-form-label">Residencia</label>
                                        <div class="col-sm-10">
                                            <input type="text" id="residencia" class="form-control"
                                                name="direccion" value="{{ auth()->user()->empleado->direccion }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="correo" class="col-sm-2 col-form-label">Correo</label>
                                        <div class="col-sm-10">
                                            <input type="text" id="correo" class="form-control" name="correo"
                                                value="{{ auth()->user()->email }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="adicional" class="col-sm-2 col-form-label">Informacion
                                            adicional</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" id="adicional" cols="30" rows="10" name="cargo">{{ auth()->user()->empleado->cargo }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10 float-right">
                                            <button type="submit" id="btnGuardardatos"
                                                class="btn btn-block btn-outline-primary">Guardar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer">
                                <p class="text-muted">Cuidado con ingresar datos erroneos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->

    <!-- /.content -->
</div>


{{-- contrasenia --}}

@if ($errors->passwordErrors->any() || session('success_password') || session('showPasswordModal'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#cambiocontra').modal('show');
    });
</script>
@endif




{{-- Avatar --}}
@if ($errors->avatarErrors->any() || session('success_avatar') || session('showAvatarModal'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#cambioavatar').modal('show');
    });
</script>
@endif



<script src="{{ asset('js/Usuario.js') }}"></script>

@include('layouts.footer')

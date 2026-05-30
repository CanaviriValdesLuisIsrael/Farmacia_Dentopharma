@php $esAdmin = auth()->user()->hasRole("admin"); @endphp
@include('layouts.header')

<link rel="stylesheet" href="{{ asset('css/laboratorio.css') }}">

<title>Admin | Catálogo</title>

@include('layouts.nav')



<!-- ===================== MODAL LABORATORIO ===================== -->
<div class="modal fade" id="crearlaboratorio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Crear laboratorio</h3>
                    <button data-dismiss="modal" class="close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    <!-- MENSAJE DINÁMICO -->
                    <div id="mensaje-laboratorio"></div>

                    <form action="{{ route('laboratorio.crear') }}" method="POST" id="form-crear-laboratorio">
                        @csrf

                        <div class="form-group">
                            <label for="nombre_laboratorio">Nombre</label>
                            <input id="nombre_laboratorio" type="text" name="nombre" class="form-control"
                                placeholder="Ingrese nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono_laboratorio">Teléfono</label>
                            <input id="telefono_laboratorio" type="text" name="telefono" class="form-control"
                                placeholder="Ingrese número" required>
                        </div>

                        <div class="form-group">
                            <label for="direccion_laboratorio">Dirección (opcional)</label>
                            <input id="direccion_laboratorio" type="text" name="direccion" class="form-control"
                                placeholder="Ingrese la dirección">
                        </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right m-1">Crear</button>
                    <button type="button" data-dismiss="modal"
                        class="btn btn-outline-secondary float-right m-1">Cerrar</button>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- ===================== MODAL EDITAR LABORATORIO ===================== -->
<div class="modal fade" id="editarLaboratorio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card card-success">

                <div class="card-header">
                    <h3 class="card-title">Editar laboratorio</h3>
                    <button data-dismiss="modal" class="close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="card-body">

                    <div id="mensaje-editar"></div>

                    <form id="form-editar-laboratorio">
                        @csrf
                        <input type="hidden" id="edit_id">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" id="edit_telefono" name="telefono" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" id="edit_direccion" name="direccion" class="form-control">
                        </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-success float-right m-1">
                        Guardar cambios
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary float-right m-1">
                        Cancelar
                    </button>
                </div>

                </form>

            </div>
        </div>
    </div>
</div>
<!-- ===================== MODAL TIPO ===================== -->
<div class="modal fade" id="creartipo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card card-danger">

                <div class="card-header">
                    <h3 class="card-title">Crear tipo</h3>
                    <button data-dismiss="modal" class="close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="card-body">
                    <div id="mensaje-tipo"></div>

                    <form id="form-crear-tipo">
                        <div class="form-group">
                            <label for="nombre_tipo">Nombre</label>
                            <input id="nombre_tipo" type="text" class="form-control" placeholder="Ingrese nombre"
                                required>
                        </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right m-1">Crear</button>
                    <button type="button" data-dismiss="modal"
                        class="btn btn-outline-secondary float-right m-1">Cerrar</button>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===================== MODAL PRESENTACION ===================== -->
<div class="modal fade" id="crearpresentacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card card-danger">

                <div class="card-header">
                    <h3 class="card-title">Crear presentación</h3>
                    <button data-dismiss="modal" class="close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="card-body">
                    <div id="mensaje-presentacion"></div>

                    <form action="{{ route('categoria.crear') }}" method="POST" id="form-crear-presentacion">
                        @csrf
                        <div class="form-group">
                            <label for="nombre_presentacion">Nombre</label>
                            <input id="nombre_presentacion" type="text" name="nombre" class="form-control"
                                placeholder="Ingrese nombre" required>
                        </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right m-1">Crear</button>
                    <button type="button" data-dismiss="modal"
                        class="btn btn-outline-secondary float-right m-1">Cerrar</button>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- ===================== MODAL EDITAR PRESENTACION ===================== -->
<div class="modal fade" id="editarPresentacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card card-success">

                <div class="card-header">
                    <h3 class="card-title">Editar presentación</h3>
                    <button data-dismiss="modal" class="close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="card-body">
                    <div id="mensaje-editar-presentacion"></div>

                    <form id="form-editar-presentacion">
                        @csrf
                        <input type="hidden" id="edit_id_cat">

                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" id="edit_nombre_cat" class="form-control" required>
                        </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-success float-right m-1">
                        Guardar
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary float-right m-1">
                        Cancelar
                    </button>
                </div>

                </form>

            </div>
        </div>
    </div>
</div>

<!-- ===================== CONTENIDO ===================== -->
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gestión de atributos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Gestión atributos</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills">

                        <li class="nav-item">
                            <a href="#laboratorio" class="nav-link active" data-toggle="tab">Laboratorio</a>
                        </li>

                        <!-- Aqui esta la seccion tipo
                        <li class="nav-item">
                            <a href="#tipo" class="nav-link" data-toggle="tab">Tipo</a>
                        </li>
                        -->
                        <li class="nav-item">
                            <a href="#presentacion" class="nav-link" data-toggle="tab">Presentación</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- LABORATORIO -->
                        <div class="tab-pane active" id="laboratorio">
                            <div class="card-danger">

                                <div class="card-header">
                                    <div class="card-title">
                                        Buscar laboratorio
                                        <button type="button" data-toggle="modal" data-target="#crearlaboratorio"
                                            class="btn bg-gradient-primary btn-sm m-2">
                                            Crear laboratorio
                                        </button>
                                    </div>

                                    <div class="input-group">
                                        <input id="buscar-laboratorio" type="text" class="form-control"
                                            placeholder="Ingrese nombre">
                                        <div class="input-group-append">
                                            <button class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-hover text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Acción</th>
                                                <th>Logo</th>
                                                <th>Laboratorio</th>
                                                <th>Teléfono</th>
                                                <th>Dirección</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-laboratorios">
                                            <!-- AQUÍ SE INSERTA TODO -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer"></div>

                            </div>
                        </div>

                        <!-- TIPO -->
                        <div class="tab-pane" id="tipo">
                            <div class="card-danger">

                                <div class="card-header">
                                    <div class="card-title">
                                        Buscar tipo
                                        <button type="button" data-toggle="modal" data-target="#creartipo"
                                            class="btn bg-gradient-primary btn-sm m-2">
                                            Crear tipo
                                        </button>
                                    </div>

                                    <div class="input-group">
                                        <input id="buscar-tipo" type="text" class="form-control"
                                            placeholder="Ingrese nombre">
                                        <div class="input-group-append">
                                            <button class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body"></div>
                                <div class="card-footer"></div>

                            </div>
                        </div>

                        <!-- PRESENTACION -->
                        <div class="tab-pane" id="presentacion">
                            <div class="card-danger">

                                <div class="card-header">
                                    <div class="card-title">
                                        Buscar presentación
                                        <button type="button" data-toggle="modal" data-target="#crearpresentacion"
                                            class="btn bg-gradient-primary btn-sm m-2">
                                            Crear presentación
                                        </button>
                                    </div>

                                    <div class="input-group">
                                        <input id="buscar-presentacion" type="text" class="form-control"
                                            placeholder="Ingrese nombre">
                                        <div class="input-group-append">
                                            <button class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-hover text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Acción</th>
                                                <th>Nombre</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-categorias">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer"></div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer"></div>
            </div>

        </div>
    </section>

</div>

<!-- modelo para cambiar logo -->
<div class="modal fade" id="modifavatar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Cambiar logo del laboratorio</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!--  MENSAJE DINÁMICO -->
                <div id="mensaje-logo"></div>
                <!--  PREVIEW LOGO -->
                <div class="text-center mb-3">
                    <img id="preview-logo" src="" class="img-fluid rounded"
                        style="max-width:120px; max-height:120px;">
                </div>
                <!--  NOMBRE LAB -->
                <div class="text-center mb-2">
                    <b id="nombre-lab"></b>
                </div>
                <!-- FORM -->
                <form id="form-logo" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <input type="hidden" id="lab_id_logo">
                    <div class="form-group">
                        <input type="file" name="logo" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn bg-gradient-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/Laboratorio.js') }}"></script>
<script src="{{ asset('js/Presentacion.js') }}"></script>

@include('layouts.header')
<link rel="stylesheet" href="{{ asset('css/proveedor.css') }}">
<title>Admin | Gestión de Proveedores</title>
@include('layouts.nav')

@php $esAdmin = auth()->user()->hasRole('admin'); @endphp

{{-- ===================== MODAL CREAR PROVEEDOR (solo admin) ===================== --}}
@if($esAdmin)
<div class="modal fade" id="crearproveedor" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Crear proveedor</h3>
                    <button data-dismiss="modal" aria-label="close" class="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    <div id="mensaje-global"></div>
                    <form action="{{ route('admin.proveedor.crear') }}" method="POST" id="form-crear">
                        @csrf
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ingrese nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="nro_contacto" class="form-control" placeholder="Ingrese número" required>
                        </div>
                        <div class="form-group">
                            <label>Correo</label>
                            <input type="email" name="correo" class="form-control" placeholder="Ingrese correo">
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Ingrese dirección">
                        </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right m-1">Guardar</button>
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary float-right m-1">Cerrar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL AVATAR (solo admin) ===================== --}}
<div class="modal fade" id="modalAvatar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar imagen del proveedor</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="mensaje-avatar"></div>
                <div class="text-center mb-3">
                    <img id="preview-avatar" src="/img/default.png" class="img-fluid rounded" style="max-width:120px;">
                </div>
                <div class="text-center mb-2"><b id="nombre-prov"></b></div>
                <form id="form-avatar" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="prov_id_avatar">
                    <div class="form-group">
                        <input type="file" id="input-avatar" name="logo" class="form-control" required>
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

{{-- ===================== MODAL EDITAR (solo admin) ===================== --}}
<div class="modal fade" id="modalEditarProveedor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Editar proveedor</h3>
                    <button data-dismiss="modal" class="close"><span>&times;</span></button>
                </div>
                <div class="card-body">
                    <div id="mensaje-editar-proveedor"></div>
                    <form id="form-editar-proveedor">
                        @csrf
                        <input type="hidden" id="edit_id_prov">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" id="edit_nombre_prov" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" id="edit_nro_prov" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Correo</label>
                            <input type="email" id="edit_correo_prov" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" id="edit_direccion_prov" class="form-control">
                        </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-success float-right m-1">Guardar cambios</button>
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary float-right m-1">Cancelar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ===================== CONTENIDO PRINCIPAL ===================== --}}
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Gestión de Proveedores
                        @if($esAdmin)
                            <button type="button" data-toggle="modal" data-target="#crearproveedor"
                                class="btn bg-gradient-primary btn-sm ml-2">
                                <i class="fas fa-plus"></i> Nuevo proveedor
                            </button>
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Gestión proveedor</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container-fluid">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $esAdmin ? 'Administrar proveedores' : 'Consulta de proveedores' }}
                    </h3>
                    <div class="input-group">
                        <input type="text" id="buscarproveedor" class="form-control"
                            placeholder="Ingrese nombre de proveedor">
                        <div class="input-group-append">
                            <button class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover text-center">
                        <thead class="thead-light">
                            <tr>
                                @if($esAdmin)<th>Acciones</th>@endif
                                <th>Avatar</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Dirección</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-proveedores"></tbody>
                    </table>
                </div>
                <div class="card-footer"></div>
            </div>
        </div>
    </section>
</div>

@include('layouts.footer')
<script>
    const proveedorConfig = {
        esAdmin: {{ $esAdmin ? 'true' : 'false' }}
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/Proveedor.js') }}"></script>

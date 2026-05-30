@include('layouts.header')
<title>Admin | Gestión de Lotes</title>
@include('layouts.nav')

<link rel="stylesheet" href="{{ asset('css/lote.css') }}">

{{-- ===================== MODAL REGISTRAR LOTE ===================== --}}
{{-- Ambos roles pueden registrar nuevos lotes (HU08) --}}
<div class="modal fade" id="crearLote" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Registrar nuevo lote</h3>
                    <button data-dismiss="modal" aria-label="close" class="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    <div id="mensaje-lote"></div>
                    <form action="{{ route('admin.lote.crear') }}" method="POST" id="form-crear-lote">
                        @csrf
                        <div class="form-group">
                            <label>Producto</label>
                            <select name="id_producto" id="sel_producto" class="form-control" required>
                                <option value="">Seleccione producto</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Proveedor</label>
                            <select name="id_proveedor" class="form-control" required>
                                <option value="">Seleccione proveedor</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id_proveedor }}">{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Cantidad</label>
                            <input type="number" name="cantidad_por_caja" class="form-control"
                                placeholder="Cantidad" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha de vencimiento</label>
                            <input type="date" name="fecha_vencimiento" class="form-control"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn bg-gradient-primary float-right m-1">Registrar</button>
                        <button type="button" data-dismiss="modal"
                            class="btn btn-outline-secondary float-right m-1">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===================== CONTENIDO PRINCIPAL ===================== --}}
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Gestión de Lotes
                        <button type="button" data-toggle="modal" data-target="#crearLote"
                            class="btn bg-gradient-primary btn-sm ml-2">
                            <i class="fas fa-plus"></i> Nuevo lote
                        </button>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Lotes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container-fluid">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Buscar lotes</h3>
                    <div class="input-group">
                        <input type="text" id="buscarLote" class="form-control"
                            placeholder="Ingrese nombre de producto">
                        <div class="input-group-append">
                            <button class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="lotes" class="row"></div>
                </div>
            </div>
        </div>
    </section>

</div>

@include('layouts.footer')

{{-- Pasar rol al JS --}}
<script>
    const loteConfig = {
        esAdmin: {{ $esAdmin ? 'true' : 'false' }}
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/Lote.js') }}"></script>

@include('layouts.header')
<title>Admin | Gestión de Lotes</title>
@include('layouts.nav')

<link rel="stylesheet" href="{{ asset('css/lote.css') }}">

{{-- ===================== CONTENIDO PRINCIPAL ===================== --}}
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Gestión de Lotes
                     {{-- 
                        <a href="{{ route('admin.compras_proveedor') }}"
                            class="btn bg-gradient-primary btn-sm ml-2">
                            <i class="fas fa-plus"></i> Nuevo lote (Compra a proveedor)
                        </a>
--}} 

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
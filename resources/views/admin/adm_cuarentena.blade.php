@include('layouts.header')
<title>Admin | Área de Cuarentena</title>
@include('layouts.nav')

<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
<link rel="stylesheet" href="{{ asset('css/cuarentena.css') }}">
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-biohazard text-warning mr-2"></i>
                        Área de Cuarentena
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Cuarentena</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- Aviso informativo --}}
    <section>
        <div class="container-fluid">
            <div class="callout callout-warning">
                <h5><i class="fas fa-exclamation-triangle mr-1"></i> ¿Qué es el Área de Cuarentena?</h5>
                <p class="mb-0">
                    Aquí se listan los <strong>productos vencidos</strong> y los <strong>sin stock (0
                        unidades)</strong>.
                    Los lotes vencidos deben ser entregados al laboratorio correspondiente para su disposición final.
                    Este módulo no aparece en el catálogo de ventas.
                </p>
            </div>
        </div>
    </section>

    {{-- Tabs --}}
    <section>
        <div class="container-fluid">
            <div class="card card-warning card-outline">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs" id="cuarentena-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab_vencidos">
                                <i class="fas fa-skull-crossbones text-danger mr-1"></i> Lotes Vencidos
                                <span class="badge badge-danger ml-1" id="badge_vencidos">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab_sin_stock">
                                <i class="fas fa-box-open text-secondary mr-1"></i> Sin Stock
                                <span class="badge badge-secondary ml-1" id="badge_sin_stock">0</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">

                        {{-- LOTES VENCIDOS --}}
                        <div class="tab-pane fade show active" id="tab_vencidos">
                            <div class="table-responsive">
                                <table id="tabla_vencidos" class="table table-hover table-striped table-sm">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Lote</th>
                                            <th>Producto</th>
                                            <th>Laboratorio</th>
                                            <th>Proveedor</th>
                                            <th>Stock</th>
                                            <th>Vencimiento</th>
                                            <th>Días Vencido</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_vencidos">
                                        <tr>
                                            <td colspan="8" class="text-center"><i
                                                    class="fas fa-spinner fa-spin"></i> Cargando...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- SIN STOCK --}}
                        <div class="tab-pane fade" id="tab_sin_stock">
                            <div class="table-responsive">
                                <table id="tabla_sin_stock" class="table table-hover table-striped table-sm">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Lote</th>
                                            <th>Producto</th>
                                            <th>Laboratorio</th>
                                            <th>Proveedor</th>
                                            <th>Vencimiento</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_sin_stock">
                                        <tr>
                                            <td colspan="7" class="text-center"><i
                                                    class="fas fa-spinner fa-spin"></i> Cargando...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

@include('layouts.footer')

<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/cuarentena.js') }}"></script>

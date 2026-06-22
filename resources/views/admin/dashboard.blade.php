@include('layouts.header')
<title>Admin| catalogo</title>

<!-- Tell the browser to be responsive to screen width -->

@include('layouts.nav')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Catálogo</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Catálogo</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>


        <section>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-danger filtro-alerta" data-filtro="vencidos" role="button">
                        <div class="inner">
                            <h3 id="conteo-vencidos">0</h3>
                            <p>Lotes Vencidos</p>
                        </div>
                        <div class="icon"><i class="fas fa-skull-crossbones"></i></div>
                        <span class="small-box-footer">Retirar del inventario <i class="fas fa-arrow-circle-right"></i></span>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning filtro-alerta" data-filtro="cuarentena" role="button">
                        <div class="inner">
                            <h3 id="conteo-cuarentena">0</h3>
                            <p>En Cuarentena (≤ 90 días)</p>
                        </div>
                        <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                        <span class="small-box-footer">Prioridad de venta (FEFO) <i class="fas fa-arrow-circle-right"></i></span>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-secondary filtro-alerta" data-filtro="sin_stock" role="button">
                        <div class="inner">
                            <h3 id="conteo-sin_stock">0</h3>
                            <p>Productos Sin Stock</p>
                        </div>
                        <div class="icon"><i class="fas fa-box-open"></i></div>
                        <span class="small-box-footer">Reabastecer <i class="fas fa-arrow-circle-right"></i></span>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title">Alertas de Inventario</h3>

                    <ul class="nav nav-pills ml-auto" id="tabs-alertas">
                        <li class="nav-item">
                            <a class="nav-link active filtro-alerta" href="#" data-filtro="vencidos">
                                Vencidos <span class="badge badge-light" id="badge-vencidos">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filtro-alerta" href="#" data-filtro="cuarentena">
                                Cuarentena <span class="badge badge-light" id="badge-cuarentena">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link filtro-alerta" href="#" data-filtro="sin_stock">
                                Sin Stock <span class="badge badge-light" id="badge-sin_stock">0</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0 table-responsive">
                  <table class="table table-hover text-center mb-0">
                    <thead class="thead-suave">
                      <tr>
                        <th>Codigo</th>
                        <th>Productos</th>
                        <th>Stock</th>
                        <th>Laboratorio</th>
                        <th>Presentacion</th>
                        <th>Proveedor</th>
                        <th>Vence en</th>
                      </tr>
                    </thead>
                    <tbody id="lotes" class="table-active"></tbody>
                  </table>
                </div>
                <div class="card-footer text-center" id="footer-ver-todos" style="display:none;">
                    <button class="btn btn-sm btn-outline-secondary" id="btn-ver-todos">
                        Ver todos (<span id="restantes">0</span> más)
                    </button>
                </div>
            </div>

        </div>
    </section>

    <!-- Main content -->
    <section>
        <div class="container-fluid">

            <div class="card card-green">

                <div class="card-header">
                    <h3 class="card-title">Buscar producto</h3>

                    <div class="input-group">
                        <input type="text" id="buscarproducto" class="form-control"
                            placeholder="Ingrese nombre del producto">

                        <div class="input-group-append">
                            <button class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- RESULTADOS DINÁMICOS -->
                <div class="card-body">
                    <div id="productos" class="row"></div>
                </div>

            </div>

        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@include('layouts.footer')

<script src="{{ asset('js/dashboard.js') }}"></script>
<script src="{{ asset('js/Carrito.js') }}"></script>
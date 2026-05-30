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
                    <h1>Catalogo</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Catalogo</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>


        <section>
        <div class="container-fluid">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Lotes en riesgos</h3>

                </div>
                <div class="card-body p-0 table-responsive">
                  <table class="table table-hover text-center">
                    <thead class="thead-suave">
                      <tr>
                        <th>Codigo</th>
                        <th>Productos</th>
                        <th>Stock</th>
                        <th>Laboratorio</th>
                        <th>Presentacion</th>
                        <th>Proveedor</th>
                        <th>Mes</th>
                        <th>dia</th>
                        
                      </tr>
                    </thead>
                    <tbody id="lotes" class="table-active">

                    </tbody>
                  </table>
                </div>
                <div class="card-footer">

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

@include('layouts.header')
<title>Admin | Reportes de Compras</title>
@include('layouts.nav')
 
<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
 
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
 
<div class="content-wrapper">
 
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-truck-loading mr-2"></i>Reportes de Compras</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Reportes de Compras</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
 
    {{-- ===== FILTROS ===== --}}
    <section>
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filtros de Reporte</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Período</label>
                            <select id="filtro_periodo" class="form-control">
                                <option value="semana">Últimos 7 días</option>
                                <option value="mes" selected>Este mes</option>
                                <option value="trimestre">Último trimestre</option>
                                <option value="anio">Este año</option>
                                <option value="personalizado">Personalizado</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="rango_personalizado" style="display:none">
                            <label>Desde</label>
                            <input type="date" id="fecha_desde" class="form-control">
                        </div>
                        <div class="col-md-3" id="rango_hasta" style="display:none">
                            <label>Hasta</label>
                            <input type="date" id="fecha_hasta" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="btn_generar" class="btn btn-primary btn-block">
                                <i class="fas fa-sync-alt mr-1"></i> Generar Reporte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    {{-- ===== TARJETAS KPI ===== --}}
    <section>
        <div class="container-fluid">
            <div class="row" id="kpi_cards">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="kpi_total_gastado">-</h3>
                            <p>Total Gastado (Bs.)</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="kpi_num_compras">-</h3>
                            <p>Número de Compras</p>
                        </div>
                        <div class="icon"><i class="fas fa-receipt"></i></div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="kpi_promedio">-</h3>
                            <p>Promedio por Compra (Bs.)</p>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    {{-- ===== GRÁFICAS ===== --}}
    <section>
        <div class="container-fluid">
            <div class="row">
 
                {{-- Compras por día --}}
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Compras por Día</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chart_compras_dia" height="120"></canvas>
                        </div>
                    </div>
                </div>
 
                {{-- Top proveedores --}}
                <div class="col-md-4">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-truck mr-1"></i> Top Proveedores</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chart_proveedores" height="200"></canvas>
                        </div>
                    </div>
                </div>
 
            </div>
 
            <div class="row">
                {{-- Top productos comprados --}}
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-pills mr-1"></i> Top 10 Productos Más Comprados</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chart_productos_compra" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    {{-- ===== TABLA DETALLE ===== --}}
    <section>
        <div class="container-fluid mb-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-table mr-1"></i> Detalle de Compras del Período</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="tabla_reporte_compras" class="table table-hover table-striped table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Empleado</th>
                                <th>Items</th>
                                <th>Tipo Pago</th>
                                <th>Descuento (Bs.)</th>
                                <th>Total (Bs.)</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_reporte_compras">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Genera un reporte para ver los datos</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
 
</div>
 
@include('layouts.footer')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/Reportescompras.js') }}"></script>
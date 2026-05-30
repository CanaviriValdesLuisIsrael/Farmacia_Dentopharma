@include('layouts.header')
<title>Admin | Gestión Ventas</title>
@include('layouts.nav')

<link rel="stylesheet" href="{{ asset('css/datatables.css') }}">

{{-- ===================== MODAL VER DETALLE VENTA ===================== --}}
<div class="modal fade" id="vista_venta" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="card card-green">
                <div class="card-header">
                    <h3 class="card-title">Detalle de venta</h3>
                    <button data-dismiss="modal" class="close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="card-body" id="contenido_modal_venta"></div>
                <div class="card-footer">
                    <button type="button" data-dismiss="modal"
                        class="btn btn-outline-secondary float-right m-1">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== CONTENIDO ===================== --}}
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        @if($esAdmin)
                            Gestión de Ventas
                        @else
                            Mis Ventas
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">
                            {{ $esAdmin ? 'Gestión Ventas' : 'Mis Ventas' }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== ESTADÍSTICAS ===================== --}}
    <section>
        <div class="container-fluid">
            <div class="card card-green">
                <div class="card-header">
                    <h3 class="card-title">Estadísticas</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 id="venta_dia_vendedor">Bs 0.00</h3>
                                    <p>Venta del día por vendedor</p>
                                </div>
                                <div class="icon"><i class="fas fa-user"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3 id="venta_diaria">Bs 0.00</h3>
                                    <p>Venta por día</p>
                                </div>
                                <div class="icon"><i class="fas fa-shopping-bag"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3 id="venta_mensual">Bs 0.00</h3>
                                    <p>Venta por mes</p>
                                </div>
                                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3 id="venta_anual">Bs 0.00</h3>
                                    <p>Venta por año</p>
                                </div>
                                <div class="icon"><i class="fas fa-signal"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TABLA DE VENTAS ===================== --}}
    <section>
        <div class="container-fluid">
            <div class="card card-green">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $esAdmin ? 'Listado de todas las ventas' : 'Mis ventas registradas' }}
                    </h3>
                </div>
                <div class="card-body">
                    <table id="example" class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>CI</th>
                                <th>Total</th>
                                <th>Vendedor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ventas as $venta)
                                <tr id="fila-venta-{{ $venta->id_venta }}">
                                    <td>{{ $venta->id_venta }}</td>
                                    <td>{{ $venta->fecha_venta }}</td>
                                    <td>{{ $venta->cliente->nombre ?? 'Sin cliente' }}</td>
                                    <td>{{ $venta->cliente->ci ?? '-' }}</td>
                                    <td>Bs {{ number_format($venta->total_venta, 2) }}</td>
                                    <td>{{ $venta->empleado->nombre ?? 'Sin vendedor' }}</td>
                                    <td>
                                        {{-- Imprimir: ambos roles --}}
                                        <a href="{{ route('venta.imprimir', $venta->id_venta) }}"
                                            target="_blank" class="btn btn-secondary btn-sm"
                                            title="Imprimir comprobante">
                                            <i class="fas fa-print"></i>
                                        </a>

                                        {{-- Ver detalle: ambos roles --}}
                                        <button type="button"
                                            class="btn btn-success btn-sm btn-ver-venta"
                                            data-toggle="modal" data-target="#vista_venta"
                                            data-codigo="{{ $venta->id_venta }}"
                                            data-fecha="{{ $venta->fecha_venta }}"
                                            data-cliente="{{ $venta->cliente->nombre ?? 'Sin cliente' }}"
                                            data-ci="{{ $venta->cliente->ci ?? '-' }}"
                                            data-vendedor="{{ $venta->empleado->nombre ?? 'Sin vendedor' }}"
                                            data-total="{{ $venta->total_venta }}"
                                            data-detalles='@json($venta->detalles)'
                                            title="Ver detalle">
                                            <i class="fas fa-search"></i>
                                        </button>

                                        {{-- Eliminar: SOLO administrador --}}
                                        @if($esAdmin)
                                            <button class="btn btn-danger btn-sm btn-eliminar-venta"
                                                data-id="{{ $venta->id_venta }}"
                                                title="Eliminar venta">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

@include('layouts.footer')

{{-- Pasar el rol al JS --}}
<script>
    const ventaConfig = {
        esAdmin: {{ $esAdmin ? 'true' : 'false' }}
    };
</script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/Venta.js') }}"></script>

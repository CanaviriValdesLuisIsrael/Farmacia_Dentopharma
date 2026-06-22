@include('layouts.header')
<title>Admin | Compras a Proveedor</title>
@include('layouts.nav')

<link rel="stylesheet" href="{{ asset('css/compras.css') }}">

{{-- ===================== MODAL REGISTRAR COMPRA ===================== --}}
<div class="modal fade" id="modalRegistrarCompra" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content modal-compra">
            <div class="modal-header bg-gradient-danger">
                <h5 class="modal-title"><i class="fas fa-truck-loading mr-2"></i>Registrar Compra a Proveedor</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-compra">
                @csrf
                <div class="modal-body">

                    {{-- ALERTA ESTADO DE CAJA --}}
                    <div id="alerta-caja" class="alert alert-warning d-none">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <span id="alerta-caja-texto"></span>
                    </div>

                    {{-- DATOS GENERALES --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Proveedor</label>
                                <select name="id_proveedor" id="sel_proveedor" class="form-control" required>
                                    <option value="">Seleccione proveedor</option>
                                    @foreach($proveedores as $prov)
                                        <option value="{{ $prov->id_proveedor }}">{{ $prov->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">Fecha de Vencimiento (lote)</label>
                                <input type="date" name="fecha_vencimiento" class="form-control"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">Tipo de Pago</label>
                                <select name="tipo_pago" id="sel_tipo_pago" class="form-control" required>
                                    <option value="contado">Contado (afecta caja)</option>
                                    <option value="credito">Crédito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold">Descuento (Bs.)</label>
                                <input type="number" step="0.01" min="0" name="descuento"
                                    id="inp_descuento" class="form-control" value="0">
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- TABLA DE PRODUCTOS A COMPRAR --}}
                    <h6 class="font-weight-bold mb-2"><i class="fas fa-boxes mr-1"></i> Productos</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="tabla-items-compra">
                            <thead>
                                <tr>
                                    <th style="min-width:210px">Producto</th>
                                    <th style="width:110px">Tipo unidad</th>
                                    <th style="width:90px">Cantidad</th>
                                    <th style="width:135px">
                                        Unid. / paquete
                                        <i class="fas fa-question-circle text-white-50 ml-1"
                                           title="Cuántas unidades trae cada paquete/blister/caja"
                                           data-toggle="tooltip"></i>
                                    </th>
                                    <th style="width:130px">Costo paquete (Bs.)</th>
                                    <th style="width:115px">Precio unitario</th>
                                    <th style="width:110px">Total unid.</th>
                                    <th style="width:115px">Subtotal (Bs.)</th>
                                    <th style="width:46px"></th>
                                </tr>
                            </thead>
                            <tbody id="items-compra-body">
                                {{-- filas dinámicas --}}
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-item">
                        <i class="fas fa-plus mr-1"></i> Agregar producto
                    </button>

                    <hr>

                    {{-- NOTA Y TOTAL --}}
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <div class="form-group mb-0">
                                <label>Nota (opcional)</label>
                                <input type="text" name="nota" class="form-control" maxlength="255"
                                    placeholder="Ej: Compra mensual de antibióticos">
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <h5 class="mb-0">Total a pagar:</h5>
                            <h2 class="font-weight-bold text-danger mb-0" id="total-compra-display">Bs. 0.00</h2>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4" id="btn-guardar-compra">
                        <i class="fas fa-save mr-1"></i> Registrar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== TEMPLATE FILA ITEM (oculto) ===================== --}}
<template id="template-item-compra">
    <tr class="fila-item-compra">
        <td>
            <select name="items[][id_producto]" class="form-control form-control-sm sel-producto" required>
                <option value="">Seleccione producto</option>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id_producto }}">
                        {{ $prod->nombre_comercial }} — {{ $prod->concentracion }} ({{ $prod->laboratorio->nombre ?? 'Sin laboratorio' }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[][tipo_unidad]" class="form-control form-control-sm sel-tipo-unidad" required>
                <option value="unidad">Unidad</option>
                <option value="blister">Blister</option>
                <option value="caja">Caja</option>
            </select>
        </td>
        <td>
            <input type="number" name="items[][cantidad]" class="form-control form-control-sm inp-cantidad"
                min="1" value="1" required>
        </td>
        <td>
            <input type="number" name="items[][unidades_por_paquete]" class="form-control form-control-sm inp-upp"
                min="1" value="1" required>
            <small class="text-muted upp-hint d-none">Unidades por paquete</small>
        </td>
        <td>
            <input type="number" step="0.01" name="items[][costo_por_paquete]" class="form-control form-control-sm inp-costo"
                min="0" value="0" required>
        </td>
        <td class="text-right precio-unitario-display">Bs. 0.00</td>
        <td class="text-right total-unidades-display font-weight-bold">0</td>
        <td class="text-right subtotal-display font-weight-bold">Bs. 0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btn-quitar-item">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

{{-- ===================== CONTENIDO PRINCIPAL ===================== --}}
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-truck-loading mr-2"></i>Compras a Proveedor
                        <button type="button" data-toggle="modal" data-target="#modalRegistrarCompra"
                            class="btn bg-gradient-danger btn-sm ml-2">
                            <i class="fas fa-plus"></i> Nueva compra
                        </button>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Compras</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container-fluid">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-1"></i> Historial de Compras</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Empleado</th>
                                    <th>Productos</th>
                                    <th>Tipo Pago</th>
                                    <th>Estado</th>
                                    <th class="text-right">Descuento</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($compras as $compra)
                                    <tr>
                                        <td>{{ $compra->id_compra }}</td>
                                        <td>{{ \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y') }}</td>
                                        <td>{{ $compra->empleado->nombre ?? '-' }}</td>
                                        <td>
                                            @foreach($compra->detalles as $det)
                                                <span class="badge badge-light border">
                                                    {{ $det->producto->nombre_comercial ?? '-' }}
                                                    @if($det->producto)
                                                        <small class="text-muted">({{ $det->producto->concentracion }})</small>
                                                    @endif
                                                    — {{ $det->cantidad }} {{ $det->tipo_unidad }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($compra->tipo_pago === 'contado')
                                                <span class="badge badge-success">Contado</span>
                                            @else
                                                <span class="badge badge-warning">Crédito</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($compra->tipo_pago === 'credito')
                                                @if($compra->estado_pago === 'pendiente')
                                                    <span class="badge badge-danger mb-1 d-block">
                                                        <i class="fas fa-clock"></i> Pendiente
                                                    </span>
                                                    <button class="btn btn-xs btn-outline-success btn-marcar-pagado"
                                                        data-id="{{ $compra->id_compra }}">
                                                        <i class="fas fa-check"></i> Marcar pagado
                                                    </button>
                                                @else
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Pagado
                                                        @if($compra->fecha_pago)
                                                            <br><small>{{ \Carbon\Carbon::parse($compra->fecha_pago)->format('d/m/Y') }}</small>
                                                        @endif
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge badge-light text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-right">Bs. {{ number_format($compra->descuento, 2) }}</td>
                                        <td class="text-right font-weight-bold">Bs. {{ number_format($compra->total_compra, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Aún no hay compras registradas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('layouts.footer')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/compras.js') }}"></script>
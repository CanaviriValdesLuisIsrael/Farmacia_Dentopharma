@include('layouts.header')
<title>Admin | Caja</title>
@include('layouts.nav')
 
<link rel="stylesheet" href="{{ asset('css/caja.css') }}">
 
{{-- ===================== MODAL ABRIR CAJA ===================== --}}
<div class="modal fade" id="modalAbrirCaja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-caja">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title"><i class="fas fa-cash-register mr-2"></i>Apertura de Caja</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-abrir-caja">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Registra el monto con el que inicia la caja hoy.</p>
                    <div class="form-group">
                        <label class="font-weight-bold">Saldo inicial (Bs.)</label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Bs.</span>
                            </div>
                            <input type="number" step="0.01" min="0" name="saldo_inicial"
                                class="form-control" placeholder="0.00" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Observación (opcional)</label>
                        <textarea name="observacion" class="form-control" rows="2"
                            placeholder="Ej: Turno mañana - cajero Juan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-door-open mr-1"></i> Abrir Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
{{-- ===================== MODAL CERRAR CAJA ===================== --}}
<div class="modal fade" id="modalCerrarCaja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-caja">
            <div class="modal-header bg-gradient-danger">
                <h5 class="modal-title"><i class="fas fa-door-closed mr-2"></i>Cierre de Caja</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-cerrar-caja">
                @csrf
                <div class="modal-body">
                    <div class="resumen-cierre mb-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Saldo Inicial</small>
                                <strong id="cierre_saldo_inicial">Bs. 0.00</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Ingresos</small>
                                <strong id="cierre_ingresos" class="text-success">Bs. 0.00</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Egresos</small>
                                <strong id="cierre_egresos" class="text-danger">Bs. 0.00</strong>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <small class="text-muted d-block">Saldo Final (en caja)</small>
                            <h2 class="text-dark font-weight-bold" id="cierre_saldo_final">Bs. 0.00</h2>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Observación de cierre (opcional)</label>
                        <textarea name="observacion" class="form-control" rows="2"
                            placeholder="Ej: Cuadre correcto, sin novedades"></textarea>
                    </div>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Esta acción cerrará la caja del día. No podrás registrar más movimientos hasta abrir una nueva.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-lock mr-1"></i> Cerrar Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
{{-- ===================== MODAL MOVIMIENTO MANUAL ===================== --}}
<div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-caja">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title"><i class="fas fa-exchange-alt mr-2"></i>Registrar Movimiento</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-movimiento">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tipo de movimiento</label>
                        <div class="btn-group btn-block" role="group">
                            <input type="radio" class="btn-check" name="tipo" id="tipo_ingreso" value="ingreso" autocomplete="off" checked>
                            <label class="btn btn-outline-success" for="tipo_ingreso">
                                <i class="fas fa-arrow-down mr-1"></i> Ingreso
                            </label>
 
                            <input type="radio" class="btn-check" name="tipo" id="tipo_egreso" value="egreso" autocomplete="off">
                            <label class="btn btn-outline-danger" for="tipo_egreso">
                                <i class="fas fa-arrow-up mr-1"></i> Egreso
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Monto (Bs.)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Bs.</span>
                            </div>
                            <input type="number" step="0.01" min="0.01" name="monto" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" class="form-control"
                            placeholder="Ej: Pago de servicio de luz" required maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
{{-- ===================== MODAL DETALLE CAJA HISTÓRICA ===================== --}}
<div class="modal fade" id="modalDetalleCaja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-caja">
            <div class="modal-header bg-gradient-secondary">
                <h5 class="modal-title"><i class="fas fa-history mr-2"></i>Detalle de Caja</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detalle-caja-resumen" class="row text-center mb-3"></div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Hora</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Empleado</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody id="detalle-caja-movimientos"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
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
                    <h1><i class="fas fa-cash-register mr-2"></i>Gestión de Caja</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Caja</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
 
    <section>
        <div class="container-fluid">
 
            @if($cajaActual)
                {{-- ===== CAJA ABIERTA ===== --}}
                <div class="card card-success card-outline caja-estado-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-circle text-success mr-2" style="font-size:10px;"></i>
                            Caja Abierta
                        </h3>
                        <div class="card-tools">
                            <small class="text-muted">
                                Abierta el {{ $cajaActual->fecha_apertura->format('d/m/Y H:i') }}
                                por <strong>{{ $cajaActual->empleado->nombre ?? '-' }}</strong>
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-secondary"><i class="fas fa-coins"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Saldo Inicial</span>
                                        <span class="info-box-number">Bs. {{ number_format($cajaActual->saldo_inicial, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Ingresos del día</span>
                                        <span class="info-box-number">Bs. {{ number_format($cajaActual->total_ingresos, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-arrow-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Egresos del día</span>
                                        <span class="info-box-number">Bs. {{ number_format($cajaActual->total_egresos, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-wallet"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Saldo Actual</span>
                                        <span class="info-box-number font-weight-bold" id="saldo-actual-display">
                                            Bs. {{ number_format($cajaActual->saldo_actual, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
 
                        <div class="text-right mt-2">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalMovimiento">
                                <i class="fas fa-exchange-alt mr-1"></i> Nuevo Movimiento
                            </button>
                            <button class="btn btn-danger" id="btn-abrir-cierre">
                                <i class="fas fa-lock mr-1"></i> Cerrar Caja
                            </button>
                        </div>
                    </div>
                </div>
 
                {{-- ===== MOVIMIENTOS DEL DÍA ===== --}}
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Movimientos de hoy</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Hora</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Empleado</th>
                                        <th class="text-right">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($movimientos as $mov)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($mov->hora)->format('H:i') }}</td>
                                            <td>
                                                @switch($mov->tipo)
                                                    @case('ingreso')
                                                        <span class="badge badge-success"><i class="fas fa-arrow-down"></i> Ingreso</span>
                                                        @break
                                                    @case('egreso')
                                                        <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Egreso</span>
                                                        @break
                                                    @case('apertura')
                                                        <span class="badge badge-secondary"><i class="fas fa-door-open"></i> Apertura</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-dark"><i class="fas fa-door-closed"></i> Cierre</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                {{ $mov->descripcion }}
                                                @if($mov->id_venta)
                                                    <span class="text-muted small">(Venta #{{ $mov->id_venta }})</span>
                                                @endif
                                                @if($mov->id_compra)
                                                    <span class="text-muted small">(Compra #{{ $mov->id_compra }})</span>
                                                @endif
                                            </td>
                                            <td>{{ $mov->empleado->nombre ?? '-' }}</td>
                                            <td class="text-right font-weight-bold {{ $mov->tipo === 'egreso' ? 'text-danger' : 'text-success' }}">
                                                {{ in_array($mov->tipo, ['egreso']) ? '-' : '+' }} Bs. {{ number_format($mov->monto, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">Aún no hay movimientos registrados hoy</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
 
            @else
                {{-- ===== CAJA CERRADA — INVITAR A ABRIR ===== --}}
                <div class="card card-outline card-secondary">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-store-slash fa-4x text-muted mb-3"></i>
                        <h3>No hay una caja abierta</h3>
                        <p class="text-muted">Debes abrir la caja para registrar ventas en efectivo y compras al contado.</p>
                        <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#modalAbrirCaja">
                            <i class="fas fa-door-open mr-1"></i> Abrir Caja
                        </button>
                    </div>
                </div>
            @endif
 
            {{-- ===== HISTORIAL DE CAJAS CERRADAS ===== --}}
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history mr-1"></i> Historial de Cajas</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Apertura</th>
                                    <th>Fecha Cierre</th>
                                    <th>Empleado</th>
                                    <th class="text-right">Saldo Inicial</th>
                                    <th class="text-right">Saldo Final</th>
                                    <th class="text-center">Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historial as $h)
                                    <tr>
                                        <td>{{ $h->id_caja }}</td>
                                        <td>{{ $h->fecha_apertura->format('d/m/Y H:i') }}</td>
                                        <td>{{ $h->fecha_cierre ? \Carbon\Carbon::parse($h->fecha_cierre)->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $h->empleado->nombre ?? '-' }}</td>
                                        <td class="text-right">Bs. {{ number_format($h->saldo_inicial, 2) }}</td>
                                        <td class="text-right font-weight-bold">Bs. {{ number_format($h->saldo_final, 2) }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary btn-ver-detalle" data-id="{{ $h->id_caja }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Sin historial de cajas cerradas</td></tr>
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
<script src="{{ asset('js/caja.js') }}"></script>
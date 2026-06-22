<link rel="stylesheet" href="{{ asset('css/nav.css') }}">
<link rel="icon" href="{{ asset('img/logofar.png') }}" class="mb-3" alt="Logo">
<body class="hold-transition sidebar-mini">

<div class="wrapper">

    {{-- ===================== NAVBAR SUPERIOR ===================== --}}
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>

            {{-- Carrito de compras (visible para ambos roles) --}}
            <li class="nav-item dropdown" id="cat-carrito" style="display: none">
                <a class="nav-link dropdown-toggle p-0" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-expanded="false"
                    style="position: relative; display: inline-block;">
                    <img src="{{ asset('img/carrito.png') }}" style="width:30px;">
                    <span id="contador-carrito" class="badge badge-danger contador-carrito">0</span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <table class="carro table table-hover text-nowrap p-0">
                        <thead class="table-success">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Concentración</th>
                                <th>Presentación</th>
                                <th>Precio</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody id="carrito-body"></tbody>
                    </table>
                    <a href="#" class="btn btn-danger btn-block btn-procesar">Procesar Compra</a>
                    <a href="#" class="btn btn-primary btn-block btn-vaciar">Vaciar Carrito</a>
                </div>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            @include('partials.menu')
        </ul>
    </nav>

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="main-sidebar elevation-4">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <img src="{{ asset('img/logofar.png') }}" class="brand-image img-circle elevation-3" style="opacity:.8">
            <span class="brand-text font-weight-light">Dentopharma</span>
        </a>

        <div class="sidebar">
            {{-- Información del usuario --}}
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}"
                        class="img-circle elevation-2" alt="Avatar">
                </div>
                <div class="info">
                    <a href="{{ route('admin.datospersonales') }}" class="d-block">
                        {{ ucwords(auth()->user()->empleado->nombre) }}
                    </a>
                    <small class="text-muted">
                        {{--
                            PUNTO DE COLOR:
                            - Verde (success) = Empleado → usuario normal con acceso restringido
                            - Rojo (danger)   = Admin    → acceso total al sistema
                            Es un indicador visual del nivel de privilegios del usuario activo.
                        --}}
                        <i class="fas fa-circle text-{{ auth()->user()->hasRole('admin') ? 'danger' : 'success' }} mr-1"
                           style="font-size:8px;"></i>
                        {{ ucfirst(auth()->user()->role->name) }}
                    </small>
                </div>
            </div>

            {{-- Menú de navegación --}}
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview"
                    role="menu" data-accordion="false">

                    {{-- ===== PERFIL ===== --}}
                    <li class="nav-header">Mi Cuenta</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.datospersonales') }}"
                           class="nav-link {{ request()->routeIs('admin.datospersonales') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>Datos Personales</p>
                        </a>
                    </li>

                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a href="{{ route('admin.adm_usuario') }}"
                           class="nav-link {{ request()->routeIs('admin.adm_usuario', 'admin.registrar.usuario') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Gestión Usuarios</p>
                        </a>
                    </li>
                    @endif

                    {{-- ===== VENTAS ===== --}}
                    <li class="nav-header">Ventas</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.adm_venta') }}"
                           class="nav-link {{ request()->routeIs('admin.adm_venta') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-notes-medical"></i>
                            <p>
                                @if(auth()->user()->hasRole('admin'))
                                    Listado de Ventas
                                @else
                                    Mis Ventas
                                @endif
                            </p>
                        </a>
                    </li>

                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a href="{{ route('ventas.reportes') }}"
                           class="nav-link {{ request()->routeIs('ventas.reportes') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reportes de Ventas</p>
                        </a>
                    </li>
                    @endif

                    {{-- ===== ALMACÉN ===== --}}
                    <li class="nav-header">Almacén</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.adm_producto') }}"
                           class="nav-link {{ request()->routeIs('admin.adm_producto') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-pills"></i>
                            <p>
                                @if(auth()->user()->hasRole('admin'))
                                    Gestión Productos
                                @else
                                    Catálogo Productos
                                @endif
                            </p>
                        </a>
                    </li>

                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a href="{{ route('admin.adm_atributo') }}"
                           class="nav-link {{ request()->routeIs('admin.adm_atributo') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-vials"></i>
                            <p>Gestión Atributos</p>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('admin.lotes') }}"
                           class="nav-link {{ request()->routeIs('admin.lotes') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cubes"></i>
                            <p>Gestión Lotes</p>
                        </a>
                    </li>

                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a href="{{ route('admin.cuarentena') }}"
                           class="nav-link {{ request()->routeIs('admin.cuarentena') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-biohazard"></i>
                            <p>Área de Cuarentena</p>
                        </a>
                    </li>
                    @endif

                    {{-- ===== PROVEEDORES ===== --}}
                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-header">Proveedores</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.adm_proveedor') }}"
                           class="nav-link {{ request()->routeIs('admin.adm_proveedor') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Gestión Proveedores</p>
                        </a>
                    </li>
                    @endif

                    {{-- ===== COMPRAS Y CAJA ===== --}}
                    <li class="nav-header">Compras y Caja</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.compras_proveedor') }}"
                           class="nav-link {{ request()->routeIs('admin.compras_proveedor') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-truck-loading"></i>
                            <p>Compras a Proveedor</p>
                        </a>
                    </li>

                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a href="{{ route('compras.reportes') }}"
                           class="nav-link {{ request()->routeIs('compras.reportes') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>Reportes de Compras</p>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('admin.caja') }}"
                           class="nav-link {{ request()->routeIs('admin.caja') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>Caja</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>
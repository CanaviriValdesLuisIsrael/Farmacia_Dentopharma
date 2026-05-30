@include('layouts.header')

<title>Admin | Producto</title>

@include('layouts.nav')

<link rel="stylesheet" href="{{ asset('css/producto.css') }}">

<!-- ===================== MODAL CREAR LOTE ===================== -->
<div class="modal fade" id="crearlote" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Crear lote</h3>
                    <button data-dismiss="modal" class="close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="card-body">

                    <div id="mensaje-lote"></div>

                    <form action="{{ route('admin.lote.crear') }}" method="POST" id="form-crear-lote">
                        @csrf
                        <input type="hidden" name="id_producto" id="producto_id_lote">
                        <label>Producto: </label>
                        <label id="nombre_producto_lote"></label>
                        <div class="form-group">
                            <label>Proveedor: </label>
                            <select name="id_proveedor" class="form-control" required>
                                <option value="">Seleccione</option>
                                @foreach ($proveedores as $prov)
                                    <option value="{{ $prov->id_proveedor }}">{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Stock: </label>
                            <input id="cantidad_por_caja" type="number" name="cantidad_por_caja" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Vencimiento: </label>
                            <input id="fecha_vencimiento" type="date" name="fecha_vencimiento" class="form-control"
                                required>
                        </div>


                </div>

                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right m-1">
                        Guardar
                    </button>

                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary float-right m-1">
                        Cerrar
                    </button>
                </div>

                </form>
            </div>

        </div>
    </div>
</div>

<!-- ===================== MODAL CREAR PRODUCTO ===================== -->
<div class="modal fade" id="crearproducto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Crear producto</h3>
                    <button data-dismiss="modal" class="close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="card-body">

                    <div id="mensaje-producto"></div>

                    <form action="{{ route('admin.producto.crear') }}" method="POST" id="form-crear-producto">
                        @csrf

                        <div class="form-group">
                            <label>Nombre comercial</label>
                            <input type="text" name="nombre_comercial" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" name="descripcion" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Concentración</label>
                            <input type="text" name="concentracion" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Precio</label>
                            <input type="number" step="0.01" name="precio_referencia" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Categoría</label>
                            <select name="id_categoria" class="form-control" required>
                                <option value="">Seleccione</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Laboratorio</label>
                            <select name="id_laboratorio" class="form-control" required>
                                <option value="">Seleccione</option>
                                @foreach ($laboratorios as $lab)
                                    <option value="{{ $lab->id_laboratorio }}">{{ $lab->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right m-1">
                        Guardar
                    </button>

                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary float-right m-1">
                        Cerrar
                    </button>
                </div>

                </form>
            </div>

        </div>
    </div>
</div>

<!-- ===================== MODAL VER PRODUCTOS ===================== -->
<div class="modal fade" id="verProductos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Lista de Productos</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    &times;
                </button>
            </div>

            <div class="modal-body">

                <!-- 🔍 BUSCADOR -->
                <div class="form-group">
                    <input type="text" id="buscarProductoModal" class="form-control"
                        placeholder="Buscar producto...">
                </div>

                <!-- RESULTADOS -->
                <div id="listaProductosModal" class="row"></div>

            </div>
        </div>
    </div>
</div>


<!-- ===================== MODAL AVATAR PRODUCTO ===================== -->
<div class="modal fade" id="modifavatarProducto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Cambiar imagen del producto</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- MENSAJE -->
                <div id="mensaje-avatar"></div>

                <!-- PREVIEW -->
                <div class="text-center mb-3">
                    <img id="preview-avatar" src="" class="img-fluid rounded" style="max-width:120px;">
                </div>

                <!-- NOMBRE -->
                <div class="text-center mb-2">
                    <b id="nombre-producto"></b>
                </div>

                <!-- FORM -->
                <form id="form-avatar" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" id="producto_id">

                    <div class="form-group">
                        <input type="file" id="input-avatar" name="avatar" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn bg-gradient-primary">Guardar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- ===================== MODAL EDITAR CONTENIDO ===================== -->
<div class="modal fade" id="editarProducto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="card card-success">

                <div class="card-header">
                    <h3 class="card-title">Editar producto</h3>
                    <button data-dismiss="modal" class="close">&times;</button>
                </div>

                <div class="card-body">

                    <form id="form-editar-producto">
                        @csrf
                        <input type="hidden" id="edit_id_producto">

                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre_comercial" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <input type="text" id="edit_descripcion" name="descripcion" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Concentración</label>
                            <input type="text" id="edit_concentracion" name="concentracion" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Precio</label>
                            <input type="number" step="0.1" id="edit_precio" name="precio_referencia"
                                class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Categoría</label>
                            <select id="edit_categoria" class="form-control">
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Laboratorio</label>
                            <select id="edit_laboratorio" class="form-control">
                                @foreach ($laboratorios as $lab)
                                    <option value="{{ $lab->id_laboratorio }}">{{ $lab->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-success float-right m-1">
                        Guardar cambios
                    </button>
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary float-right m-1">
                        Cancelar
                    </button>

                </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===================== CONTENIDO PRINCIPAL ===================== -->
<div class="content-wrapper">

    <!-- HEADER -->
    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>
                        Gestión de productos
                        <!-- PARA QUE SOLO ADMIN CREE PRODUCTOS
                        @if (auth()->user()->hasRole('admin'))
<button type="button"
                                data-toggle="modal"
                                data-target="#crearproducto"
                                class="btn bg-gradient-primary ml-2">
                                Crear producto
                            </button>
@endif
-->
                        <button type="button" data-toggle="modal" data-target="#crearproducto"
                            class="btn bg-gradient-primary ml-2">
                            Crear producto
                        </button>
                    </h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Productos</li>
                    </ol>
                </div>

            </div>
        </div>
    </section>

    <!-- BUSCADOR -->
    <section>
        <div class="container-fluid">

            <div class="card card-danger">

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

</div>

@include('layouts.footer')

<script>
    const usuarioAuth = {
        id: {{ auth()->id() }},
        role: "{{ auth()->user()->role->name }}"
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const productoConfig = { esAdmin: {{ $esAdmin ? 'true' : 'false' }} };
</script>
<script src="{{ asset('js/Producto.js') }}"></script>

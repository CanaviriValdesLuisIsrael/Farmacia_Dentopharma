$(document).ready(function () {
    $("#cat-carrito").show();
    // CARGA INICIAL
    buscar_producto();
    cargar_lotes_riesgo();

    // BUSCADOR
    $("#buscarproducto").keyup(function () {
        let valor = $(this).val();
        buscar_producto(valor);
    });
});
// ==============================
// LISTAR PRODUCTOS
// ==============================
function buscar_producto(valor = "") {
    $.get("/admin/buscar-producto", { buscar: valor, solo_disponibles: 1 }, function (response) {
        let template = "";

        response.forEach((prod) => {
            // obtener lote más próximo a vencer (FIFO)
            let loteValido = null;

            if (prod.lotes && prod.lotes.length > 0) {
                let hoy = new Date();

                loteValido = prod.lotes
                    .filter(
                        (l) =>
                            new Date(l.fecha_vencimiento) > hoy &&
                            l.cantidad_por_caja > 0,
                    )
                    .sort(
                        (a, b) =>
                            new Date(a.fecha_vencimiento) -
                            new Date(b.fecha_vencimiento),
                    )[0];
            }
            template += `
            <div class="col-md-4 mb-3">
                <div class="card producto-card">

                    <div class="producto-header">
                    
                       <i class="fas fa-lg fa-cubes mr-1"></i>Stock ${prod.stock_total ?? 0}
                    </div>

                    <div class="card-body">
                        <div class="row align-items-center">

                            <div class="col-4 text-center">
                                ${
                                    prod.avatar
                                        ? `<img src="/storage/productos/${prod.avatar}" class="producto-avatar-img">`
                                        : `<div class="producto-avatar"></div>`
                                }
                            </div>

                            <div class="col-8">
                                <h5>${prod.nombre_comercial}</h5>
                                <h4 class="precio">Bs. ${prod.precio_referencia}</h4>

                                <p class="detalle">
                                    <b>Concentración:</b> ${prod.concentracion}<br>
                                    <b>Descripción:</b> ${prod.descripcion}<br>
                                    <b>Laboratorio:</b> ${prod.laboratorio?.nombre || ""}<br>
                                    <b>Categoría:</b> ${prod.categoria?.nombre || ""}
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="producto-footer text-center">

                    <button 
                        class="agregar-carrito btn btn-sm btn-primary"
                        data-idproducto="${prod.id_producto}"
                        data-idlote="${loteValido ? loteValido.id_lote : ""}"
                        data-stock="${loteValido ? loteValido.cantidad_por_caja : 0}"
                        data-stocktotal="${prod.stock_total ?? 0}"
                        data-precio="${prod.precio_referencia}"
                        data-nombre="${prod.nombre_comercial}"
                        data-concentracion="${prod.concentracion}"
                        data-presentacion="${prod.categoria?.nombre || ""}"
                        data-laboratorio="${prod.laboratorio?.nombre || "Sin lab"}">

                        <i class="fas fa-plus-square mr-2"></i> Agregar al carrito
                    </button>
                
                    </div>

                </div>
            </div>
            `;
        });

        $("#productos").html(template);
    });
}
// ==============================
// ALERTAS DE INVENTARIO (vencidos / cuarentena / sin stock)
// ==============================
let alertasData = { vencidos: [], cuarentena: [], sin_stock: [] };
let filtroActivo = "vencidos";
let mostrarTodos = false;
const TOP_N = 10;

function cargar_lotes_riesgo() {
    $.get("/admin/lotes-riesgo", function (response) {
        alertasData = response;

        $("#conteo-vencidos").text(response.conteos.vencidos);
        $("#conteo-cuarentena").text(response.conteos.cuarentena);
        $("#conteo-sin_stock").text(response.conteos.sin_stock);
        $("#badge-vencidos").text(response.conteos.vencidos);
        $("#badge-cuarentena").text(response.conteos.cuarentena);
        $("#badge-sin_stock").text(response.conteos.sin_stock);

        renderizarAlertas();
    });
}

function renderizarAlertas() {
    let datos = alertasData[filtroActivo] || [];
    let hoy = new Date();
    let filasAMostrar = mostrarTodos ? datos : datos.slice(0, TOP_N);

    let template = "";

    if (filasAMostrar.length === 0) {
        template = `<tr><td colspan="7" class="text-center text-muted py-3">No hay registros en esta categoría </td></tr>`;
    }

    filasAMostrar.forEach((item) => {
        if (filtroActivo === "sin_stock") {
            // item = PRODUCTO (sin lote ni fecha de vencimiento asociada)
            template += `
                <tr class="fila-sinstock">
                    <td>—</td>
                    <td>${item.nombre_comercial}</td>
                    <td>0</td>
                    <td>${item.laboratorio?.nombre || ""}</td>
                    <td>${item.categoria?.nombre || ""}</td>
                    <td>—</td>
                    <td>Sin lotes vigentes</td>
                </tr>
            `;
        } else {
            // item = LOTE (vencido o en cuarentena)
            let vencimiento = new Date(item.fecha_vencimiento);
            let dias = Math.floor((vencimiento - hoy) / (1000 * 60 * 60 * 24));
            let clase = filtroActivo === "vencidos" ? "fila-vencido" : "fila-proximo";
            let textoVence = dias < 0
                ? `Vencido hace ${Math.abs(dias)} día(s)`
                : `En ${dias} día(s)`;

            template += `
                <tr class="${clase}">
                    <td>${item.id_lote}</td>
                    <td>${item.producto.nombre_comercial}</td>
                    <td>${item.cantidad_por_caja}</td>
                    <td>${item.producto.laboratorio?.nombre || ""}</td>
                    <td>${item.producto.categoria?.nombre || ""}</td>
                    <td>${item.proveedor?.nombre || ""}</td>
                    <td>${textoVence}</td>
                </tr>
            `;
        }
    });

    $("#lotes").html(template);

    let restantes = datos.length - filasAMostrar.length;
    if (!mostrarTodos && restantes > 0) {
        $("#restantes").text(restantes);
        $("#footer-ver-todos").show();
    } else {
        $("#footer-ver-todos").hide();
    }
}

// Cambiar de pestaña (tarjeta KPI o pill) — reinicia a Top 10
$(document).on("click", ".filtro-alerta", function (e) {
    e.preventDefault();
    filtroActivo = $(this).data("filtro");
    mostrarTodos = false;

    $("#tabs-alertas .nav-link").removeClass("active");
    $(`#tabs-alertas .nav-link[data-filtro="${filtroActivo}"]`).addClass("active");

    renderizarAlertas();
});

// Expandir a la lista completa de la pestaña activa
$(document).on("click", "#btn-ver-todos", function () {
    mostrarTodos = true;
    renderizarAlertas();
});
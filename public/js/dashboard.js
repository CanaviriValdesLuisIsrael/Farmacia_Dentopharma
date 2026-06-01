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
                    <h6>  Codigo ${prod.id_producto}</h6>
                       <i class="fas fa-lg fa-cubes mr-1"></i> ${prod.stock_total ?? 0}
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
function cargar_lotes_riesgo() {
    $.get("/admin/lotes-riesgo", function (response) {
        let template = "";

        response.forEach((lote) => {
            let hoy = new Date();
            let vencimiento = new Date(lote.fecha_vencimiento);

            let diferencia = vencimiento - hoy;
            let dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
            let meses = Math.floor(dias / 30);

            let clase = "";

            if (dias < 0) {
                clase = "fila-vencido";
            } else if (meses <= 3) {
                clase = "fila-proximo";
            }

            template += `
                <tr class="${clase}">
                    <td>${lote.id_lote}</td>
                    <td>${lote.producto.nombre_comercial}</td>
                    <td>${lote.cantidad_por_caja}</td>
                    <td>${lote.producto.laboratorio?.nombre || ""}</td>
                    <td>${lote.producto.categoria?.nombre || ""}</td>
                    <td>${lote.proveedor?.nombre || ""}</td>
                    <td>${meses}</td>
                    <td>${dias}</td>
                </tr>
            `;
        });

        $("#lotes").html(template);
    });
}

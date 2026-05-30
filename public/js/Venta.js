$(document).ready(function () {
    // ======================================
    // CARGAR ESTADISTICAS
    // ======================================
    cargarEstadisticas();

    // ======================================
    // DATATABLE
    // ======================================
    $("#example").DataTable({
        responsive: true,
        autoWidth: false,

        language: {
            lengthMenu: "Mostrar _MENU_ registros",

            zeroRecords: "No se encontraron resultados",

            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",

            infoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",

            infoFiltered: "(filtrado de un total de _MAX_ registros)",

            sSearch: "Buscar:",

            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior",
            },

            sProcessing: "Procesando...",
        },
    });
});

// ======================================
// CARGAR ESTADISTICAS AJAX
// ======================================
function cargarEstadisticas() {
    $.ajax({
        url: "/ventas/estadisticas",
        type: "GET",

        success: function (response) {
            $("#venta_dia_vendedor").html(`Bs ${response.ventaDiaVendedor}`);

            $("#venta_diaria").html(`Bs ${response.ventaDia}`);

            $("#venta_mensual").html(`Bs ${response.ventaMes}`);

            $("#venta_anual").html(`Bs ${response.ventaAnio}`);
        },

        error: function (xhr) {
            console.log(xhr.responseText);
        },
    });
}

// ======================================
// MOSTRAR DETALLE DE VENTA
// ======================================
$(document).on("click", ".btn-ver-venta", function () {
    let codigo = $(this).data("codigo");
    let fecha = $(this).data("fecha");
    let cliente = $(this).data("cliente");
    let ci = $(this).data("ci");
    let vendedor = $(this).data("vendedor");
    let total = $(this).data("total");

    let detalles = $(this).data("detalles");

    let filas = "";

    detalles.forEach((detalle) => {
        filas += `
            <tr>
                <td>${detalle.cantidad}</td>
                <td>${detalle.precio_unitario}</td>
                <td>${detalle.producto.nombre_comercial}</td>
                <td>${detalle.producto.concentracion ?? "-"}</td>

                <td>
                    ${
                        detalle.producto.categoria
                            ? detalle.producto.categoria.nombre
                            : "-"
                    }
                </td>

                <td>
                    ${
                        detalle.producto.laboratorio
                            ? detalle.producto.laboratorio.nombre
                            : "-"
                    }
                </td>

                <td>${detalle.subtotal}</td>
            </tr>
        `;
    });

    let html = `
    
        <div class="row mb-3">
        
            <div class="col-md-12">
                <strong>Codigo Venta:</strong> ${codigo}
            </div>

            <div class="col-md-12 mt-2">
                <strong>Fecha:</strong> ${fecha}
            </div>

            <div class="col-md-12 mt-2">
                <strong>Cliente:</strong> ${cliente}
            </div>

            <div class="col-md-12 mt-2">
                <strong>CI:</strong> ${ci}
            </div>

            <div class="col-md-12 mt-2">
                <strong>Vendedor:</strong> ${vendedor}
            </div>

        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="bg-success text-white">

                    <tr>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Producto</th>
                        <th>Concentración</th>
                        <th>Presentación</th>
                        <th>Laboratorio</th>
                        <th>Subtotal</th>
                    </tr>

                </thead>

                <tbody>
                    ${filas}
                </tbody>

            </table>

        </div>

        <div class="text-right mt-3">
            <h3>
                <strong>Total: Bs ${parseFloat(total).toFixed(2)}</strong>
            </h3>
        </div>
    `;

    $("#contenido_modal_venta").html(html);
});

// ======================================
// ELIMINAR VENTA
// ======================================
$(document).on("click", ".btn-eliminar-venta", function () {
    let idVenta = $(this).data("id");

    Swal.fire({
        title: "¿Eliminar venta?",
        text: "El stock será restaurado al lote correspondiente",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/venta/" + idVenta,
                type: "DELETE",

                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },

                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "Correcto",
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                    });

                  

                   
                    // ELIMINAR FILA DE LA TABLA
                    $("#fila-venta-" + idVenta).remove();

                    // ACTUALIZAR ESTADISTICAS
                    cargarEstadisticas();
                },

                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo eliminar la venta",
                    });

                    console.log(xhr.responseText);
                },
            });
        }
    });
});

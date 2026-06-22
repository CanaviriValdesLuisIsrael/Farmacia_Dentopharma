// ============================================================
// COMPRAS A PROVEEDOR — Lógica del modal de registro
// ============================================================

$(document).ready(function () {
    inicializarEventos();
    // Activar tooltips de Bootstrap (usados en las cabeceras de la tabla)
    $('[data-toggle="tooltip"]').tooltip();
});

function inicializarEventos() {
    // Al abrir el modal: verificar caja y agregar la primera fila
    $("#modalRegistrarCompra").on("show.bs.modal", function () {
        verificarEstadoCaja();
        if ($("#items-compra-body tr").length === 0) {
            agregarFilaItem();
        }
    });

    // Al cerrar el modal: limpiar todo para la próxima vez
    $("#modalRegistrarCompra").on("hidden.bs.modal", function () {
        $("#form-compra")[0].reset();
        $("#items-compra-body").empty();
        actualizarTotalGeneral();
    });

    $("#btn-add-item").on("click", () => agregarFilaItem());

    // Delegación de eventos para filas dinámicas
    $("#items-compra-body").on("click", ".btn-quitar-item", function () {
        if ($("#items-compra-body tr").length === 1) {
            Swal.fire("Atención", "Debe haber al menos un producto en la compra", "info");
            return;
        }
        $(this).closest("tr").remove();
        actualizarTotalGeneral();
    });

    $("#items-compra-body").on("change", ".sel-tipo-unidad", function () {
        actualizarFila($(this).closest("tr"));
    });

    $("#items-compra-body").on("input", ".inp-cantidad, .inp-upp, .inp-costo", function () {
        actualizarFila($(this).closest("tr"));
    });

    $("#inp_descuento").on("input", actualizarTotalGeneral);
    $("#sel_tipo_pago").on("change", verificarEstadoCaja);

    $("#form-compra").on("submit", registrarCompra);

    $(document).on("click", ".btn-marcar-pagado", marcarComoPagado);
}

// ============================================================
// MARCAR COMPRA A CRÉDITO COMO PAGADA
// ============================================================
function marcarComoPagado() {
    let id = $(this).data("id");

    Swal.fire({
        title: "¿Marcar esta compra como pagada?",
        text: "Se registrará un egreso en la caja abierta por el total de la compra.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, marcar como pagada",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#28a745",
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.post(`/admin/compras-proveedor/${id}/pagar`, {
            _token: $('meta[name="csrf-token"]').attr("content"),
        })
            .done(function (res) {
                Swal.fire({
                    icon: "success",
                    title: "Pago registrado",
                    text: res.message,
                    timer: 1800,
                    showConfirmButton: false,
                }).then(() => location.reload());
            })
            .fail(function (xhr) {
                Swal.fire("Error", xhr.responseJSON?.message || "No se pudo registrar el pago", "error");
            });
    });
}

// ============================================================
// VERIFICAR ESTADO DE CAJA (solo importa si es "contado")
// ============================================================
function verificarEstadoCaja() {
    let tipoPago = $("#sel_tipo_pago").val();

    if (tipoPago !== "contado") {
        $("#alerta-caja").addClass("d-none");
        return;
    }

    $.get("/admin/caja/estado")
        .done(function (res) {
            if (!res.abierta) {
                $("#alerta-caja-texto").text(
                    "No hay una caja abierta. Si registras esta compra al contado, fallará. " +
                    "Abre la caja primero o selecciona 'Crédito'."
                );
                $("#alerta-caja").removeClass("d-none alert-info").addClass("alert-warning");
            } else {
                $("#alerta-caja-texto").html(
                    `Caja abierta — saldo actual: <strong>Bs. ${parseFloat(res.caja.saldo_actual).toFixed(2)}</strong>. ` +
                    `Esta compra se descontará de la caja.`
                );
                $("#alerta-caja").removeClass("d-none alert-warning").addClass("alert-info");
            }
        });
}

// ============================================================
// AGREGAR FILA DE PRODUCTO (clona el <template>)
// ============================================================
function agregarFilaItem() {
    let template = document.getElementById("template-item-compra");
    let clone = template.content.cloneNode(true);

    $("#items-compra-body").append(clone);

    // Inicializar la fila recién agregada según su tipo_unidad por defecto
    let fila = $("#items-compra-body tr").last();
    actualizarFila(fila);
}

// ============================================================
// ACTUALIZAR UNA FILA: lógica caja/blister/unidad + cálculos
// ============================================================
function actualizarFila(fila) {
    let tipoUnidad = fila.find(".sel-tipo-unidad").val();
    let cantidad   = parseFloat(fila.find(".inp-cantidad").val()) || 0;
    let upp        = parseFloat(fila.find(".inp-upp").val()) || 1;
    let costo      = parseFloat(fila.find(".inp-costo").val()) || 0;

    let inputUpp = fila.find(".inp-upp");
    let hintUpp  = fila.find(".upp-hint");

    // Si es "unidad", forzamos unidades_por_paquete = 1 y bloqueamos el campo
    if (tipoUnidad === "unidad") {
        inputUpp.val(1).prop("readonly", true).addClass("bg-light");
        hintUpp.addClass("d-none");
        upp = 1;
    } else {
        inputUpp.prop("readonly", false).removeClass("bg-light");
        hintUpp.removeClass("d-none").text(
            tipoUnidad === "caja" ? "Unidades por caja" : "Unidades por blister"
        );
        if (upp < 1) upp = 1;
    }

    let totalUnidades = cantidad * upp;
    let precioUnitario = tipoUnidad === "unidad" ? costo : (upp > 0 ? costo / upp : 0);
    let subtotal = cantidad * costo;

    fila.find(".total-unidades-display").text(totalUnidades);
    fila.find(".precio-unitario-display").text("Bs. " + precioUnitario.toFixed(4));
    fila.find(".subtotal-display").text("Bs. " + subtotal.toFixed(2));

    actualizarTotalGeneral();
}

// ============================================================
// TOTAL GENERAL DE LA COMPRA
// ============================================================
function actualizarTotalGeneral() {
    let total = 0;

    $("#items-compra-body tr").each(function () {
        let cantidad = parseFloat($(this).find(".inp-cantidad").val()) || 0;
        let costo    = parseFloat($(this).find(".inp-costo").val()) || 0;
        total += cantidad * costo;
    });

    let descuento = parseFloat($("#inp_descuento").val()) || 0;
    total -= descuento;
    if (total < 0) total = 0;

    $("#total-compra-display").text("Bs. " + total.toFixed(2));
}

// ============================================================
// ENVIAR COMPRA AL SERVIDOR
// ============================================================
function registrarCompra(e) {
    e.preventDefault();

    // Construir payload manualmente (los inputs tienen name="items[][...]")
    let items = [];
    let valido = true;

    $("#items-compra-body tr").each(function () {
        let idProducto = $(this).find(".sel-producto").val();
        let tipoUnidad = $(this).find(".sel-tipo-unidad").val();
        let cantidad   = parseInt($(this).find(".inp-cantidad").val()) || 0;
        let upp        = parseInt($(this).find(".inp-upp").val()) || 1;
        let costo      = parseFloat($(this).find(".inp-costo").val()) || 0;

        if (!idProducto || cantidad <= 0 || costo < 0) {
            valido = false;
        }

        items.push({
            id_producto: idProducto,
            tipo_unidad: tipoUnidad,
            cantidad: cantidad,
            unidades_por_paquete: upp,
            costo_por_paquete: costo,
        });
    });

    if (!valido) {
        Swal.fire("Atención", "Verifica que todos los productos tengan datos válidos", "warning");
        return;
    }

    let payload = {
        _token: $('meta[name="csrf-token"]').attr("content"),
        id_proveedor: $("#sel_proveedor").val(),
        fecha_vencimiento: $('input[name="fecha_vencimiento"]').val(),
        tipo_pago: $("#sel_tipo_pago").val(),
        descuento: $("#inp_descuento").val() || 0,
        nota: $('input[name="nota"]').val(),
        items: items,
    };

    let btn = $("#btn-guardar-compra");
    btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...').prop("disabled", true);

    $.post("/admin/compras-proveedor", payload)
        .done(function (res) {
            Swal.fire({
                icon: "success",
                title: "Compra registrada",
                text: "Total: Bs. " + res.total,
                timer: 1800,
                showConfirmButton: false,
            }).then(() => location.reload());
        })
        .fail(function (xhr) {
            let msg = xhr.responseJSON?.message || "No se pudo registrar la compra";
            Swal.fire("Error", msg, "error");
        })
        .always(function () {
            btn.html('<i class="fas fa-save mr-1"></i> Registrar Compra').prop("disabled", false);
        });
}
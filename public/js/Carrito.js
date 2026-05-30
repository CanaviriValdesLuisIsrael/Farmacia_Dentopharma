// Evita que el carrito se cierre al hacer click dentro
$(document).on("click", "#cat-carrito .dropdown-menu", function (e) {
    e.stopPropagation();
});

let carrito = [];

// ==========================
// INICIAR
// ==========================
$(document).ready(function () {
// ==========================
// BUSCAR CLIENTE POR CI
// ==========================
let tiempoBusqueda;

$("#dni").on("keyup", function () {

    clearTimeout(tiempoBusqueda);

    let ci = $(this).val();

    // limpiar si vacío
    if (ci.length === 0) {

        $("#cliente").val("");

        return;
    }

    // esperar 500ms antes de buscar
    tiempoBusqueda = setTimeout(() => {

        $.ajax({

            url: "/cliente/buscar/" + ci,
            method: "GET",

            success: function (res) {

                if (res.success) {

                    $("#cliente").val(res.cliente.nombre);

                } else {

                    $("#cliente").val("");
                }
            },

            error: function () {

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudo buscar el cliente",
                });
            }
        });

    }, 500);
});
    cargarCarrito();
    renderCarrito();

    // si existe tabla de compra, cargarla
    if ($("#lista-compra").length) {
        renderCompra();
    }
});

// ==========================
// AGREGAR AL CARRITO
// ==========================
$(document).on("click", ".agregar-carrito", function () {
    let btn = $(this);

    let id_lote = btn.data("idlote");
    let id_producto = btn.data("idproducto");
    let nombre = btn.data("nombre");
    let concentracion = btn.data("concentracion");
    let presentacion = btn.data("presentacion");
    let precio = parseFloat(btn.data("precio"));
    let stock = parseInt(btn.data("stock"));
    let laboratorio = btn.data("laboratorio");
    let stocktotal = parseInt(btn.data("stocktotal"));

    // VALIDACIONES
    if (!id_lote) {
        Swal.fire({
            icon: "error",
            text: `${nombre} sin stock o está vencido`,
            timer: 2000,
            showConfirmButton: false,
        });
        return;
    } else if (stock <= 0) {
        Swal.fire({
            icon: "error",
            text: `Sin stock en ${nombre}`,
            timer: 2000,
            showConfirmButton: false,
        });
        return;
    }

    // evitar duplicados
    let item = carrito.find((p) => p.id_lote == id_lote);

    if (item) {
        Swal.fire({
            icon: "warning",
            text: `${nombre} ya fue agregado al carrito`,
            timer: 1500,
            showConfirmButton: false,
        });
        return;
    }

    carrito.push({
        id_lote,
        id_producto,
        nombre,
        concentracion,
        presentacion,
        laboratorio,
        stock: stocktotal,
        precio,
        cantidad: 1,
    });

    // bajar stock visual
    stock--;
    btn.data("stock", stock);

    guardarCarrito();
    renderCarrito();
});

// ==========================
// CONTADOR
// ==========================
function actualizarContador() {
    let total = carrito.length;
    $("#contador-carrito").text(total);
}

// ==========================
// RENDER CARRITO (NAV)
// ==========================
function renderCarrito() {
    let template = "";

    carrito.forEach((item, index) => {
        template += `
        <tr>
            <td>${item.id_lote}</td>
            <td>${item.nombre}</td>
            <td>${item.concentracion}</td>
            <td>${item.presentacion}</td>
            <td>${item.precio}</td>
            <td>
                <button class="btn btn-danger btn-sm eliminar-item" data-index="${index}">
                    X
                </button>
            </td>
        </tr>
        `;
    });

    $("#carrito-body").html(template);
    actualizarContador();
}

// ==========================
// ELIMINAR ITEM NAV
// ==========================
$(document).on("click", ".eliminar-item", function () {
    let index = $(this).data("index");
    carrito.splice(index, 1);

    guardarCarrito();
    renderCarrito();
});

// ==========================
// GUARDAR / CARGAR
// ==========================
function guardarCarrito() {
    localStorage.setItem("carrito", JSON.stringify(carrito));
}

function cargarCarrito() {
    let data = localStorage.getItem("carrito");
    carrito = data ? JSON.parse(data) : [];
}

// ==========================
// VACIAR CARRITO
// ==========================
$(document).on("click", ".btn-vaciar", function (e) {
    e.preventDefault();

    carrito = [];
    localStorage.removeItem("carrito");

    renderCarrito();
});

// ==========================
// PROCESAR (NAV)
// ==========================
$(document).on("click", ".btn-procesar", function (e) {
    e.preventDefault();

    if (carrito.length === 0) {
        Swal.fire({
            icon: "warning",
            text: "El carrito está vacío",
            timer: 1500,
            showConfirmButton: false,
        });
        return;
    }

    window.location.href = "/admin/compra";
});

// ==========================
// RENDER COMPRA
// ==========================
function renderCompra() {
    let template = "";
    let subtotal = 0;

    carrito.forEach((item, index) => {
        let sub = item.precio * item.cantidad;
        subtotal += sub;

        template += `
        <tr>
            <td>${item.nombre}</td>
            <td>${item.stock}</td>
            <td>${item.precio}</td>
            <td>${item.concentracion}</td>
            <td>${item.laboratorio}</td>
            <td>${item.presentacion}</td>
            <td>
                <input 
                    type="number" 
                    min="1" 
                    max="${item.stock}"
                    value="${item.cantidad}"
                    class="form-control cantidad-input"
                    data-index="${index}">
            </td>
            <td>${sub.toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm eliminar-compra" data-index="${index}">
                    X
                </button>
            </td>
        </tr>
        `;
    });

    $("#lista-compra").html(template);
    calcularTotales(subtotal);
}

$(document).on("change keyup", ".cantidad-input", function () {
    let index = $(this).data("index");
    let cantidad = parseInt($(this).val());

    if (cantidad < 1) cantidad = 1;

    let stock = carrito[index].stock;

    if (cantidad > stock) {
        Swal.fire({
            icon: "warning",
            text: "Cantidad supera el stock disponible",
            timer: 1500,
            showConfirmButton: false,
        });
        cantidad = stock;
    }

    carrito[index].cantidad = cantidad;

    guardarCarrito();
    renderCompra();
});

// ==========================
// CALCULOS con credito fiscal iva
// ==========================
/*
function calcularTotales(subtotal) {
    let igv = subtotal * 0.13;
    let totalSin = subtotal + igv;

    let descuento = parseFloat($("#descuento").val()) || 0;
    let total = totalSin - descuento;

    let pago = parseFloat($("#pago").val()) || 0;
    let vuelto = pago - total;

    $("#subtotal").text(subtotal.toFixed(2));
    $("#con_igv").text(igv.toFixed(2));
    $("#total_sin_descuento").text(totalSin.toFixed(2));
    $("#total").text(total.toFixed(2));
    $("#vuelto").text(vuelto > 0 ? vuelto.toFixed(2) : 0);
}
*/
// ==========================
// CALCULOS sin credito fiscal iva
// ==========================
function calcularTotales(subtotal) {
    // ❌ ELIMINAMOS IGV
    let totalSin = subtotal;

    let descuento = parseFloat($("#descuento").val()) || 0;
    let total = totalSin - descuento;

    if (total < 0) total = 0;

    let pago = parseFloat($("#pago").val()) || 0;
    let vuelto = pago - total;

    $("#subtotal").text(subtotal.toFixed(2));

    // 🔴 IGV en 0 o puedes ocultarlo en la vista
    $("#con_igv").text("0.00");

    $("#total_sin_descuento").text(totalSin.toFixed(2));
    $("#total").text(total.toFixed(2));
    $("#vuelto").text(vuelto > 0 ? vuelto.toFixed(2) : 0);
}
// ==========================
// ACTUALIZAR CALCULOS
// ==========================
$(document).on("keyup change", "#descuento, #pago", function () {
    let subtotal = 0;

    carrito.forEach((item) => {
        subtotal += item.precio * item.cantidad;
    });

    calcularTotales(subtotal);
});

// ==========================
// ELIMINAR EN COMPRA
// ==========================
$(document).on("click", ".eliminar-compra", function () {
    let index = $(this).data("index");

    carrito.splice(index, 1);

    guardarCarrito();
    renderCompra();
});

// ==========================
// FINALIZAR COMPRA
// ==========================
$(document).on("click", "#procesar-compra", function (e) {
    e.preventDefault();

    let cliente = $("#cliente").val();
    let dni = $("#dni").val();

    if (!cliente) {
        Swal.fire({
            icon: "warning",
            text: "El nombre del cliente es obligatorio",
        });
        return;
    }

    if (carrito.length === 0) {
        Swal.fire({
            icon: "warning",
            text: "No hay productos en la compra",
        });
        return;
    }

    $.ajax({
        url: "/admin/guardar-venta",
        method: "POST",
        data: {
            _token: $("meta[name='csrf-token']").attr("content"),
            cliente: cliente,
            dni: dni,
            carrito: carrito,
        },
        success: function (res) {
            Swal.fire({
                icon: "success",
                title: "Compra realizada",
                text: res.message,
                confirmButtonText: "Ver comprobante",
            }).then(() => {
                // =========================
                // ABRIR PDF
                // =========================
                window.open("/venta/" + res.id_venta + "/imprimir", "_blank");

                // =========================
                // LIMPIAR CARRITO
                // =========================
                carrito = [];

                localStorage.removeItem("carrito");

                renderCompra();
                renderCarrito();

                // =========================
                // LIMPIAR CAMPOS
                // =========================
                $("#cliente").val("");
                $("#dni").val("");
                $("#descuento").val("");
                $("#pago").val("");

                $("#subtotal").text("0.00");
                $("#total").text("0.00");
                $("#vuelto").text("0.00");
            });
        },
        error: function (err) {
            Swal.fire({
                icon: "error",
                text: err.responseJSON.message,
            });
        },
    });
});

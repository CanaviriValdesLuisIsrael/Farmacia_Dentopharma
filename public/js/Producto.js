$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).ready(function () {
    // CARGA INICIAL
    buscar_producto();

    // BUSCADOR
    $("#buscarproducto").keyup(function () {
        let valor = $(this).val();
        buscar_producto(valor);
    });
});

// ==============================
// CREAR PRODUCTO
// ==============================
$(document).on("submit", "#form-crear-producto", function (e) {
    e.preventDefault();

    let form = $(this);

    $.ajax({
        url: form.attr("action"),
        method: "POST",
        data: form.serialize(),

        success: function (response) {
            mostrarMensaje(response.mensaje, "success", "#mensaje-producto");
            form[0].reset();
            buscar_producto();
        },

        error: function (xhr) {
            let mensaje =
                xhr.responseJSON?.mensaje || "Error al crear producto";
            mostrarMensaje(mensaje, "error", "#mensaje-producto");
        },
    });
});

// ==============================
// LISTAR PRODUCTOS
// ==============================
function buscar_producto(valor = "") {
    $.get("/admin/buscar-producto", { buscar: valor }, function (response) {
        let template = "";

        response.forEach((prod) => {
            template += `
            <div class="col-md-4 mb-3">
                <div class="card producto-card">

                    <div class="producto-header">
                       <i class="fas fa-capsules"></i> ${prod.stock_total ?? 0}
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

                        ${productoConfig.esAdmin ? `
                        <button class="btn btn-info btn-avatar" data-id="${prod.id_producto}" title="Cambiar imagen">
                            <i class="fas fa-image"></i>
                        </button>
                        <button class="btn btn-success btn-editar" data-id="${prod.id_producto}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>` : ''}

                        <button class="btn btn-primary btn-agregar" data-id="${prod.id_producto}" title="A\xf1adir lote">
                            <i class="fas fa-plus"></i> Lote
                        </button>

                        ${productoConfig.esAdmin ? `
                        <button class="btn btn-danger btn-eliminar" data-id="${prod.id_producto}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>` : ''}

                    </div>

                </div>
            </div>
            `;
        });

        $("#productos").html(template);
    });
}

// ==============================
// ABRIR MODAL AVATAR
// ==============================
$(document).on("click", ".btn-avatar", function () {
    let id = $(this).data("id");
    let card = $(this).closest(".producto-card");

    let nombre = card.find("h5").text();
    let img = card.find("img").attr("src");

    $("#producto_id").val(id);
    $("#nombre-producto").text(nombre);

    if (img) {
        $("#preview-avatar").attr("src", img);
    } else {
        $("#preview-avatar").attr("src", "/img/default.png");
    }

    $("#modifavatarProducto").modal("show");
});

// ==============================
// PREVIEW IMAGEN
// ==============================
$(document).on("change", "#input-avatar", function () {
    let file = this.files[0];

    if (file) {
        if (!file.type.startsWith("image/")) {
            mostrarMensajeAvatar("El archivo debe ser una imagen", "error");
            $(this).val("");
            return;
        }

        let reader = new FileReader();

        reader.onload = function (e) {
            $("#preview-avatar").attr("src", e.target.result);
        };

        reader.readAsDataURL(file);
    }
});

// ==============================
// SUBIR AVATAR
// ==============================
$(document).on("submit", "#form-avatar", function (e) {
    e.preventDefault();

    let id = $("#producto_id").val();
    let formData = new FormData(this);

    $.ajax({
        url: `/producto/${id}/avatar`,
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (response) {
            mostrarMensajeAvatar(response.mensaje, "success");

            $("#input-avatar").val("");

            buscar_producto();
        },

        error: function (xhr) {
            let mensaje = "Error al subir imagen";

            if (xhr.responseJSON?.errors) {
                mensaje = Object.values(xhr.responseJSON.errors)
                    .flat()
                    .join("<br>");
            }

            mostrarMensajeAvatar(mensaje, "error");

            $("#input-avatar").val("");
        },
    });
});

// ==============================
// MENSAJE AVATAR
// ==============================
function mostrarMensajeAvatar(mensaje, tipo = "success") {
    let clase = tipo === "success" ? "alert-success" : "alert-danger";

    let html = `
        <div class="alert ${clase} fade-custom text-center">
            ${mensaje}
        </div>
    `;

    $("#mensaje-avatar").html(html);

    let alerta = $("#mensaje-avatar .fade-custom");

    setTimeout(() => alerta.addClass("show"), 50);

    setTimeout(() => {
        alerta.removeClass("show").addClass("hide");

        setTimeout(() => {
            $("#mensaje-avatar").html("");
        }, 800);
    }, 3000);
}

// ==============================
// MENSAJE GLOBAL
// ==============================
function mostrarMensaje(
    texto,
    tipo = "success",
    contenedor = "#mensaje-producto",
) {
    let clase = tipo === "success" ? "alert-success" : "alert-danger";

    let mensaje = `
        <div class="alert ${clase} fade-custom text-center">
            ${texto}
        </div>
    `;

    $(contenedor).html(mensaje);

    let alerta = $(contenedor).find(".fade-custom");

    setTimeout(() => alerta.addClass("show"), 50);

    setTimeout(() => {
        alerta.removeClass("show").addClass("hide");

        setTimeout(() => {
            $(contenedor).html("");
        }, 800);
    }, 2000);
}
//mostrar editar
$(document).on("click", ".btn-editar", function () {
    let card = $(this).closest(".producto-card");

    let id = $(this).data("id");

    $("#edit_id_producto").val(id);
    $("#edit_nombre").val(card.find("h5").text());
    $("#edit_precio").val(card.find(".precio").text().replace("Bs. ", ""));

    let detalle = card.find(".detalle").text();

    // extraer valores correctamente
    let concentracion = detalle.match(/Concentración:\s*(.*?)\s*Descripción:/);
    let descripcion = detalle.match(/Descripción:\s*(.*)/);

    $("#edit_concentracion").val(concentracion ? concentracion[1].trim() : "");
    $("#edit_descripcion").val(descripcion ? descripcion[1].trim() : "");

    $("#editarProducto").modal("show");
});
//editar
$(document).on("submit", "#form-editar-producto", function (e) {
    e.preventDefault();

    let id = $("#edit_id_producto").val();

    $.ajax({
        url: `/producto/${id}`,
        method: "POST",
        data: {
            _method: "PUT",
            _token: $('meta[name="csrf-token"]').attr("content"),
            nombre_comercial: $("#edit_nombre").val(),
            descripcion: $("#edit_descripcion").val(),
            concentracion: $("#edit_concentracion").val(),
            precio_referencia: $("#edit_precio").val(),
            id_categoria: $("#edit_categoria").val(),
            id_laboratorio: $("#edit_laboratorio").val(),
        },

        success: function (response) {
            Swal.fire({
                title: "Actualizado",
                text: response.mensaje,
                icon: "success",
            });

            $("#editarProducto").modal("hide");
            buscar_producto();
        },

        error: function (xhr) {
            let mensaje = "Error al actualizar";

            if (xhr.responseJSON?.errors) {
                mensaje = Object.values(xhr.responseJSON.errors)
                    .flat()
                    .join("\n");
            }

            Swal.fire("Error", mensaje, "error");
        },
    });
});

// ==============================
// CREAR LOTE
// ==============================
$(document).on("submit", "#form-crear-lote", function (e) {
    e.preventDefault();

    let form = $(this);

    $.ajax({
        url: "/lote/crear",
        method: "POST",
        data: form.serialize(),

        success: function (response) {
            $("#crearlote").modal("hide");

            Swal.fire("Correcto", response.mensaje, "success");

            buscar_producto(); //  actualiza stock
        },

        error: function (xhr) {
            let mensaje = "Error al crear lote";

            if (xhr.responseJSON?.errors) {
                mensaje = Object.values(xhr.responseJSON.errors)
                    .flat()
                    .join("\n");
            }

            Swal.fire("Error", mensaje, "error");
        },
    });
});
// ==============================
// ELIMINAR PRODUCTO
// ==============================
$(document).on("click", ".btn-eliminar", function () {
    let id = $(this).data("id");

    Swal.fire({
        title: "¿Eliminar producto?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/producto/${id}`,
                method: "POST",
                data: {
                    _method: "DELETE",
                },

                success: function (response) {
                    Swal.fire("Eliminado", response.mensaje, "success");

                    buscar_producto();
                },

                error: function () {
                    Swal.fire("Error", "No se pudo eliminar", "error");
                },
            });
        }
    });
});

// ==============================
// ABRIR MODAL CREAR LOTE
// ==============================
$(document).on("click", ".btn-agregar", function () {
    let id = $(this).data("id");

    let card = $(this).closest(".producto-card");
    let nombre = card.find("h5").text();

    // colocar datos en modal
    $("#producto_id_lote").val(id);
    $("#nombre_producto_lote").text(nombre);

    // limpiar form
    $("#form-crear-lote")[0].reset();

    // fecha mínima = hoy
    let hoy = new Date().toISOString().split("T")[0];
    $("#fecha_vencimiento").attr("min", hoy);

    $("#crearlote").modal("show");
});

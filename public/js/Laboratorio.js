// ==============================
// INICIALIZACIÓN
// ==============================
$(document).ready(function () {
    // listar al cargar
    listar_laboratorios();

    // buscador
    $("#buscar-laboratorio").keyup(function () {
        let valor = $(this).val().trim();
        listar_laboratorios(valor);
    });

    // ==========================
    // CREAR LABORATORIO
    // ==========================
    $("#form-crear-laboratorio").submit(function (e) {
        e.preventDefault();

        let form = $(this);

        $.ajax({
            url: form.attr("action"),
            method: "POST",
            data: form.serialize(),
            dataType: "json",

            success: function (response) {
                mostrarMensajeLaboratorio(response.mensaje, "success");

                form[0].reset();
                listar_laboratorios();
            },

            error: function (xhr) {
                let mensaje = "Error al crear laboratorio";

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    mensaje = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("<br>");
                }

                mostrarMensajeLaboratorio(mensaje, "error");
            },
        });
    });

    // ==========================
    // CAMBIAR LOGO (SUBMIT)
    // ==========================
    $("#form-logo").submit(function (e) {
        e.preventDefault();

        let id = $("#lab_id_logo").val();
        let formData = new FormData(this);

        $.ajax({
            url: `/laboratorio/${id}/logo`,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,

            success: function (response) {
                mostrarMensajeLogo(response.mensaje, "success");

                // limpiar input file
                $("#input-logo").val("");

                // refrescar tabla
                listar_laboratorios();
            },

            error: function (xhr) {
                let mensaje = "Error al subir logo";

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    mensaje = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("<br>");
                }

                mostrarMensajeLogo(mensaje, "error");

                // limpiar input SI falla
                $("#input-logo").val("");
            },
        });
    });
});

// ==============================
// LISTAR LABORATORIOS
// ==============================
function listar_laboratorios(valor = "") {
    $.ajax({
        url: "/admin/listar-laboratorios",
        method: "GET",
        data: { buscar: valor },

        success: function (response) {
            let template = "";

            if (response.length === 0) {
                template = `
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No se encontraron laboratorios
                        </td>
                    </tr>
                `;
            } else {
                response.forEach((lab) => {
                    // 🔥 USAR avatar (CORRECTO)
                    let logo = lab.avatar
                        ? `<img src="/storage/laboratorios/${lab.avatar}" class="img-logo">`
                        : `<div class="logo-vacio"></div>`;

                    template += `
                        <tr>
                            <td>
                                <!--  CAMBIAR LOGO -->
                                <button class="btn btn-sm btn-info btn-logo"
                                    data-id="${lab.id_laboratorio}">
                                    <i class="fas fa-image"></i>
                                </button>

                                <!-- 🔥 EDITAR -->
                                <button class="btn btn-sm btn-success btn-editar"
                                    data-id="${lab.id_laboratorio}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- 🔥 ELIMINAR -->
                                <button class="btn btn-sm btn-danger btn-eliminar"
                                  data-id="${lab.id_laboratorio}">
                                 <i class="fas fa-trash"></i>
                                </button>
                            </td>

                            <td>${logo}</td>
                            <td>${lab.nombre}</td>
                            <td>${lab.telefono ? lab.telefono : "-"}</td>
                            <td>${lab.direccion ? lab.direccion : "-"}</td>
                        </tr>
                    `;
                });
            }

            $("#tabla-laboratorios").html(template);
        },

        error: function () {
            console.log("Error al listar laboratorios");
        },
    });
}

// ==============================
// ABRIR MODAL LOGO
// ==============================
$(document).on("click", ".btn-logo", function () {
    let id = $(this).data("id");

    let fila = $(this).closest("tr");

    let nombre = fila.find("td:eq(2)").text();
    let logo = fila.find("img").attr("src");

    $("#lab_id_logo").val(id);
    $("#nombre-lab").text(nombre);

    if (logo) {
        $("#preview-logo").attr("src", logo);
    } else {
        $("#preview-logo").attr("src", "/img/default.png"); // opcional
    }

    // 🔥 abrir modal manualmente
    $("#modifavatar").modal("show");
});

// ==============================
// PREVIEW IMAGEN
// ==============================
$(document).on("change", "#input-logo", function () {
    let file = this.files[0];

    if (file) {
        // 🔥 validar tipo antes (extra pro)
        if (!file.type.startsWith("image/")) {
            mostrarMensajeLogo("El archivo debe ser una imagen", "error");
            $(this).val("");
            return;
        }

        let reader = new FileReader();

        reader.onload = function (e) {
            $("#preview-logo").attr("src", e.target.result);
        };

        reader.readAsDataURL(file);
    }
});

// ==============================
//  MENSAJE CREAR
// ==============================
function mostrarMensajeLaboratorio(mensaje, tipo = "success") {
    const contenedor = $("#mensaje-laboratorio");

    let clase = tipo === "success" ? "alert-success" : "alert-danger";

    let html = `
        <div class="alert ${clase} fade-custom text-center">
            ${mensaje}
        </div>
    `;

    contenedor.html(html);

    let alerta = contenedor.find(".fade-custom");

    setTimeout(() => {
        alerta.addClass("show");
    }, 50);

    setTimeout(() => {
        alerta.removeClass("show").addClass("hide");

        setTimeout(() => {
            contenedor.html("");
        }, 800);
    }, 3000);
}

// ==============================
//  MENSAJE LOGO
// ==============================
function mostrarMensajeLogo(mensaje, tipo = "success") {
    let clase = tipo === "success" ? "alert-success" : "alert-danger";

    let html = `
        <div class="alert ${clase} fade-custom text-center">
            ${mensaje}
        </div>
    `;

    $("#mensaje-logo").html(html);

    let alerta = $("#mensaje-logo .fade-custom");

    setTimeout(() => {
        alerta.addClass("show");
    }, 50);

    setTimeout(() => {
        alerta.removeClass("show").addClass("hide");

        setTimeout(() => {
            $("#mensaje-logo").html("");
        }, 800);
    }, 3000);
}

// ==============================
//  ELIMINAR LABORATORIO
// ==============================
$(document).on("click", ".btn-eliminar", function () {
    let id = $(this).data("id");

    let fila = $(this).closest("tr");

    let nombre = fila.find("td:eq(2)").text();

    let logo = fila.find("img").attr("src");

    Swal.fire({
        title: `¿Desea eliminar ${nombre}?`,
        text: "Esta acción no se puede deshacer",

        // 🔥 AQUÍ CAMBIAS EL ICONO POR IMAGEN
        imageUrl: logo ? logo : "/img/default.png",
        imageWidth: 80,
        imageHeight: 80,
        imageAlt: "Logo laboratorio",

        showCancelButton: true,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",

        // 🎨 COLORES
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        showClass: {
            popup: "animate__animated animate__zoomIn",
        },
        hideClass: {
            popup: "animate__animated animate__zoomOut",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/laboratorio/${id}`,
                method: "POST",
                data: {
                    _method: "DELETE",
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },

                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Eliminado",
                            text: response.mensaje,
                            icon: "success",
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: "#28a745",
                        });

                        listar_laboratorios();
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.mensaje,
                            icon: "error",
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: "#dc3545", // rojo
                        });
                    }
                },

                error: function () {
                    Swal.fire("Error", "No se pudo eliminar", "error");
                },
            });
        }
    });
});
// ==============================
//ABRIR MODAL EDITAR
// ==============================
$(document).on("click", ".btn-editar", function () {

    let fila = $(this).closest("tr");

    let id = $(this).data("id");
    let nombre = fila.find("td:eq(2)").text();
    let telefono = fila.find("td:eq(3)").text();
    let direccion = fila.find("td:eq(4)").text();

    $("#edit_id").val(id);
    $("#edit_nombre").val(nombre);
    $("#edit_telefono").val(telefono === "-" ? "" : telefono);
    $("#edit_direccion").val(direccion === "-" ? "" : direccion);

    $("#editarLaboratorio").modal("show");
});
// ==============================
// 🔄 ACTUALIZAR LABORATORIO
// ==============================
$(document).on("submit", "#form-editar-laboratorio", function (e) {
    e.preventDefault();

    let id = $("#edit_id").val();

    $.ajax({
        url: `/laboratorio/${id}`,
        method: "POST",
        data: {
            _method: "PUT",
            _token: $('meta[name="csrf-token"]').attr("content"),
            nombre: $("#edit_nombre").val(),
            telefono: $("#edit_telefono").val(),
            direccion: $("#edit_direccion").val(),
        },

        success: function (response) {
            if (response.success) {

                Swal.fire({
                    title: "Actualizado",
                    text: response.mensaje,
                    icon: "success",
                    confirmButtonColor: "#28a745"
                });

                $("#editarLaboratorio").modal("hide");
                listar_laboratorios();

            } else {
                Swal.fire("Error", response.mensaje, "error");
            }
        },

        error: function (xhr) {
            let mensaje = "Error al actualizar";

            if (xhr.responseJSON?.errors) {
                mensaje = Object.values(xhr.responseJSON.errors).flat().join("\n");
            }

            Swal.fire({
                title: "Error",
                text: mensaje,
                icon: "error",
                confirmButtonColor: "#dc3545"
            });
        }
    });
});
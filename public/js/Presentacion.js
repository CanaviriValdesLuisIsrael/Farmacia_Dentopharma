$(document).ready(function () {
    // listar al cargar
    listar_categorias();

    // buscador
    $("#buscar-presentacion").keyup(function () {
        let valor = $(this).val().trim();
        listar_categorias(valor);
    });

    // ==========================
    // CREAR categoria
    // ==========================
    $("#form-crear-presentacion").submit(function (e) {
        e.preventDefault();

        let form = $(this);

        $.ajax({
            url: form.attr("action"),
            method: "POST",
            data: form.serialize(),
            dataType: "json",

            success: function (response) {
                mostrarMensaje(response.mensaje, "success");

                form[0].reset();
                listar_categorias();
            },

            error: function (xhr) {
                let mensaje = "Error al crear categoria";

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    mensaje = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("<br>");
                }

                mostrarMensaje(mensaje, "error");
            },
        });
    });



});

function mostrarMensaje(mensaje, tipo = "success") {
    const contenedor = $("#mensaje-presentacion");

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

function listar_categorias(valor = "") {
    $.ajax({
        url: "/admin/listar-categorias",
        method: "GET",
        data: { buscar: valor },

        success: function (response) {
            let template = "";

            if (response.length === 0) {
                template = `
                    <tr>
                        <td colspan="2">No hay datos</td>
                    </tr>
                `;
            } else {
                response.forEach(cat => {
                    template += `
                        <tr>
                            <td>
                                <button class="btn btn-sm btn-success btn-editar-cat"
                                    data-id="${cat.id_categoria}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-sm btn-danger btn-eliminar-cat"
                                    data-id="${cat.id_categoria}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>

                            <td>${cat.nombre}</td>
                        </tr>
                    `;
                });
            }

            $("#tabla-categorias").html(template);
        }
    });
}$(document).on("click", ".btn-editar-cat", function () {

    let fila = $(this).closest("tr");

    let id = $(this).data("id");
    let nombre = fila.find("td:eq(1)").text();

    $("#edit_id_cat").val(id);
    $("#edit_nombre_cat").val(nombre);

    $("#editarPresentacion").modal("show");
});

//actualizar
$(document).on("submit", "#form-editar-presentacion", function (e) {
    e.preventDefault();

    let id = $("#edit_id_cat").val();

    $.ajax({
        url: `/categoria/${id}`,
        method: "POST",
        data: {
            _method: "PUT",
            _token: $('meta[name="csrf-token"]').attr("content"),
            nombre: $("#edit_nombre_cat").val(),
        },

        success: function (response) {
            Swal.fire("Actualizado", response.mensaje, "success");

            $("#editarPresentacion").modal("hide");
            listar_categorias();
        },

        error: function (xhr) {
            let mensaje = "Error";

            if (xhr.responseJSON?.errors) {
                mensaje = Object.values(xhr.responseJSON.errors).flat().join("\n");
            }

            Swal.fire("Error", mensaje, "error");
        }
    });
});

//eliminar
$(document).on("click", ".btn-eliminar-cat", function () {

    let id = $(this).data("id");
    let fila = $(this).closest("tr");
    let nombre = fila.find("td:eq(1)").text();

    Swal.fire({
        title: `¿Eliminar ${nombre}?`,
        text: "No se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Eliminar"
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: `/categoria/${id}`,
                method: "POST",
                data: {
                    _method: "DELETE",
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },

                success: function (response) {
                    if (response.success) {
                        Swal.fire("Eliminado", response.mensaje, "success");
                        listar_categorias();
                    } else {
                        Swal.fire("Error", response.mensaje, "error");
                    }
                }
            });

        }
    });
});


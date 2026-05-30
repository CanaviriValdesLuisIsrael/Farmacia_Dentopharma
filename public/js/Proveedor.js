/**
 * Proveedor.js
 * Control de gestión de proveedores con validación de roles.
 * - Administrador: crear, editar, eliminar, cambiar imagen.
 * - Empleado: solo consultar (solo lectura).
 */

$(document).ready(function () {
    listarProveedores();

    $("#buscarproveedor").on("keyup", function () {
        listarProveedores($(this).val());
    });
});

// ============================================================
// LISTAR PROVEEDORES
// ============================================================
function listarProveedores(valor = "") {
    $.ajax({
        url: "/admin/listar-proveedores",
        method: "GET",
        data: { buscar: valor },
        success: function (response) {
            let template = "";

            if (response.length === 0) {
                template = `<tr><td colspan="${proveedorConfig.esAdmin ? 6 : 5}" class="text-center py-3 text-muted">
                    <i class="fas fa-truck fa-2x mb-2"></i><br>No hay proveedores registrados.
                </td></tr>`;
                $("#tabla-proveedores").html(template);
                return;
            }

            response.forEach((prov) => {
                const avatar = prov.avatar
                    ? `<img src="/storage/proveedores/${prov.avatar}" class="img-logo">`
                    : `<div class="logo-vacio"></div>`;

                // Botones de acción: solo visibles para admin
                const accionesAdmin = proveedorConfig.esAdmin
                    ? `<td>
                        <button class="btn btn-info btn-sm btn-avatar" data-id="${prov.id_proveedor}" title="Cambiar imagen">
                            <i class="fas fa-image"></i>
                        </button>
                        <button class="btn btn-success btn-sm btn-editar" data-id="${prov.id_proveedor}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${prov.id_proveedor}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                      </td>`
                    : "";

                template += `
                <tr>
                    ${accionesAdmin}
                    <td>${avatar}</td>
                    <td>${prov.nombre}</td>
                    <td>${prov.nro_contacto}</td>
                    <td>${prov.correo}</td>
                    <td>${prov.direccion ?? "—"}</td>
                </tr>`;
            });

            $("#tabla-proveedores").html(template);
        },
    });
}

// ============================================================
// CREAR PROVEEDOR (solo admin)
// ============================================================
$(document).on("submit", "#form-crear", function (e) {
    e.preventDefault();
    if (!proveedorConfig.esAdmin) return;

    $.ajax({
        url: $(this).attr("action"),
        method: "POST",
        data: $(this).serialize(),
        success: function (response) {
            mostrarMensaje("#mensaje-global", response.mensaje, "success");
            $("#form-crear")[0].reset();
            listarProveedores();
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.mensaje
                || Object.values(xhr.responseJSON?.errors || {}).flat().join("<br>")
                || "Error al crear proveedor.";
            mostrarMensaje("#mensaje-global", msg, "danger");
        },
    });
});

// ============================================================
// ABRIR MODAL AVATAR (solo admin)
// ============================================================
$(document).on("click", ".btn-avatar", function () {
    if (!proveedorConfig.esAdmin) return;
    const id     = $(this).data("id");
    const fila   = $(this).closest("tr");
    const nombre = fila.find("td:eq(1)").text();
    const avatar = fila.find("img").attr("src");

    $("#prov_id_avatar").val(id);
    $("#nombre-prov").text(nombre);
    $("#preview-avatar").attr("src", avatar || "/img/default.png");
    $("#mensaje-avatar").html("");
    $("#modalAvatar").modal("show");
});

$(document).on("change", "#input-avatar", function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => $("#preview-avatar").attr("src", e.target.result);
        reader.readAsDataURL(file);
    }
});

$("#form-avatar").on("submit", function (e) {
    e.preventDefault();
    if (!proveedorConfig.esAdmin) return;
    const id       = $("#prov_id_avatar").val();
    const formData = new FormData(this);

    $.ajax({
        url: `/proveedor/${id}/avatar`,
        method: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            mostrarMensaje("#mensaje-avatar", response.mensaje, "success");
            listarProveedores();
        },
        error: function (xhr) {
            const msg = Object.values(xhr.responseJSON?.errors || {}).flat().join("<br>") || "Error al subir imagen.";
            mostrarMensaje("#mensaje-avatar", msg, "danger");
        },
    });
});

// ============================================================
// ABRIR MODAL EDITAR (solo admin)
// ============================================================
$(document).on("click", ".btn-editar", function () {
    if (!proveedorConfig.esAdmin) return;
    const fila = $(this).closest("tr");
    const id   = $(this).data("id");

    $("#edit_id_prov").val(id);
    // Con columna acciones el índice cambia
    $("#edit_nombre_prov").val(fila.find("td:eq(2)").text());
    $("#edit_nro_prov").val(fila.find("td:eq(3)").text());
    $("#edit_correo_prov").val(fila.find("td:eq(4)").text());
    const dir = fila.find("td:eq(5)").text();
    $("#edit_direccion_prov").val(dir === "—" ? "" : dir);
    $("#mensaje-editar-proveedor").html("");
    $("#modalEditarProveedor").modal("show");
});

$("#form-editar-proveedor").on("submit", function (e) {
    e.preventDefault();
    if (!proveedorConfig.esAdmin) return;
    const id = $("#edit_id_prov").val();

    $.ajax({
        url: `/proveedor/${id}`,
        method: "POST",
        data: {
            _method: "PUT",
            _token: $('meta[name="csrf-token"]').attr("content"),
            nombre: $("#edit_nombre_prov").val(),
            nro_contacto: $("#edit_nro_prov").val(),
            correo: $("#edit_correo_prov").val(),
            direccion: $("#edit_direccion_prov").val(),
        },
        success: function (response) {
            mostrarMensaje("#mensaje-editar-proveedor", response.mensaje, "success");
            listarProveedores();
        },
        error: function (xhr) {
            const msg = Object.values(xhr.responseJSON?.errors || {}).flat().join("<br>") || "Error al actualizar.";
            mostrarMensaje("#mensaje-editar-proveedor", msg, "danger");
        },
    });
});

// ============================================================
// ELIMINAR (solo admin)
// ============================================================
$(document).on("click", ".btn-eliminar", function () {
    if (!proveedorConfig.esAdmin) return;
    const id = $(this).data("id");

    Swal.fire({
        title: "¿Eliminar proveedor?",
        text: "Esta acción no se puede revertir.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `/proveedor/${id}`,
            method: "POST",
            data: {
                _method: "DELETE",
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: (response) => {
                Swal.fire("Eliminado", response.mensaje, "success");
                listarProveedores();
            },
            error: (xhr) => {
                Swal.fire("Error", xhr.responseJSON?.mensaje || "No se puede eliminar.", "error");
            },
        });
    });
});

// ============================================================
// UTILIDAD: Mostrar mensajes
// ============================================================
function mostrarMensaje(selector, texto, tipo = "success") {
    const html = `<div class="alert alert-${tipo} text-center">${texto}</div>`;
    $(selector).html(html);
    setTimeout(() => $(selector).fadeOut(500, () => $(selector).html("").show()), 3000);
}

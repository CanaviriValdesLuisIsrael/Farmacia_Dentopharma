/**
 * Lote.js
 * Control de gestión de lotes con validación de roles.
 * - Administrador: puede ver, editar y eliminar lotes.
 * - Empleado: puede ver y registrar nuevos lotes. No puede editar ni eliminar.
 */

$(document).ready(function () {
    buscarLotes();
    cargarProductosSelect();

    $("#buscarLote").on("keyup", function () {
        buscarLotes($(this).val());
    });

    // Formulario crear lote (ambos roles)
    $("#form-crear-lote").on("submit", function (e) {
        e.preventDefault();
        crearLote(this);
    });
});

// ============================================================
// LISTAR LOTES
// ============================================================
function buscarLotes(valor = "") {
    $.get("/admin/buscar-lotes", { buscar: valor }, function (response) {
        const lotes   = response.lotes;
        const esAdmin = response.esAdmin;
        let template  = "";

        if (!lotes || lotes.length === 0) {
            template = `<div class="col-12 text-center py-4 text-muted">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p>No se encontraron lotes.</p>
                        </div>`;
            $("#lotes").html(template);
            return;
        }

        lotes.forEach((lote) => {
            const hoy        = new Date();
            const vencimiento = new Date(lote.fecha_vencimiento);
            const dias        = Math.floor((vencimiento - hoy) / (1000 * 60 * 60 * 24));
            const meses       = Math.floor(dias / 30);

            let colorClase = "lote-normal";
            if (dias < 0)        colorClase = "lote-vencido";
            else if (meses <= 3) colorClase = "lote-proximo";

            // Botones de acción: editar y eliminar solo para admin
            const botonesAdmin = esAdmin
                ? `<button class="btn btn-success btn-sm btn-editar-lote mr-1"
                       data-id="${lote.id_lote}" data-stock="${lote.cantidad_por_caja}"
                       title="Editar stock">
                       <i class="fas fa-edit"></i>
                   </button>
                   <button class="btn btn-danger btn-sm btn-eliminar-lote"
                       data-id="${lote.id_lote}"
                       title="Eliminar lote">
                       <i class="fas fa-trash"></i>
                   </button>`
                : `<span class="badge badge-secondary">Solo consulta</span>`;

            template += `
            <div class="col-md-4 mb-3">
                <div class="card lote-card ${colorClase}">
                    <div class="lote-header">
                        <h6>Código ${lote.id_lote}</h6>
                        <span><i class="fas fa-cubes mr-1"></i>${lote.cantidad_por_caja}</span>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-4 text-center">
                                ${lote.producto.avatar
                                    ? `<img src="/storage/productos/${lote.producto.avatar}" class="lote-avatar-img">`
                                    : `<div class="lote-avatar"></div>`}
                            </div>
                            <div class="col-8">
                                <h5>${lote.producto.nombre_comercial}</h5>
                                <p class="detalle">
                                    <b>Concentración:</b> ${lote.producto.concentracion}<br>
                                    <b>Laboratorio:</b> ${lote.producto.laboratorio?.nombre || "—"}<br>
                                    <b>Categoría:</b> ${lote.producto.categoria?.nombre || "—"}<br>
                                    <b>Proveedor:</b> ${lote.proveedor?.nombre || "—"}<br>
                                    <b>Vencimiento:</b> ${lote.fecha_vencimiento}<br>
                                    <b>Meses:</b> ${meses < 0 ? "Vencido" : meses}<br>
                                    <b>Días:</b> ${dias < 0 ? "Vencido" : dias}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="lote-footer text-center pb-2">
                        ${botonesAdmin}
                    </div>
                </div>
            </div>`;
        });

        $("#lotes").html(template);
    });
}

// ============================================================
// CARGAR PRODUCTOS EN SELECT DEL MODAL
// ============================================================
function cargarProductosSelect() {
    $.get("/admin/buscar-producto", { buscar: "" }, function (response) {
        let options = `<option value="">Seleccione producto</option>`;
        response.forEach((p) => {
            options += `<option value="${p.id_producto}">${p.nombre_comercial} — ${p.concentracion}</option>`;
        });
        $("#sel_producto").html(options);
    });
}

// ============================================================
// CREAR LOTE (Ambos roles)
// ============================================================
function crearLote(form) {
    const data = $(form).serialize();
    $.ajax({
        url: $(form).attr("action"),
        method: "POST",
        data: data,
        success: function (response) {
            if (response.success) {
                $("#mensaje-lote").html(
                    `<div class="alert alert-success">${response.mensaje}</div>`
                );
                buscarLotes();
                setTimeout(() => $("#crearLote").modal("hide"), 1200);
            } else {
                $("#mensaje-lote").html(
                    `<div class="alert alert-danger">${response.mensaje}</div>`
                );
            }
        },
        error: function (xhr) {
            const err = xhr.responseJSON?.mensaje || "Error al registrar lote.";
            $("#mensaje-lote").html(`<div class="alert alert-danger">${err}</div>`);
        },
    });
}

// ============================================================
// EDITAR LOTE — Solo administrador
// ============================================================
$(document).on("click", ".btn-editar-lote", function () {
    if (!loteConfig.esAdmin) return;

    const id          = $(this).data("id");
    const stockActual = $(this).data("stock");

    Swal.fire({
        title: "Editar stock del lote",
        input: "number",
        inputValue: stockActual,
        inputAttributes: { min: 1 },
        showCancelButton: true,
        confirmButtonText: "Guardar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `/lote/${id}`,
            method: "POST",
            data: {
                _method: "PUT",
                _token: $('meta[name="csrf-token"]').attr("content"),
                cantidad_por_caja: result.value,
            },
            success: function (response) {
                Swal.fire("Correcto", response.mensaje, "success");
                buscarLotes();
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.mensaje || "No se pudo actualizar.";
                Swal.fire("Error", msg, "error");
            },
        });
    });
});

// ============================================================
// ELIMINAR LOTE — Solo administrador
// ============================================================
$(document).on("click", ".btn-eliminar-lote", function () {
    if (!loteConfig.esAdmin) return;

    const id = $(this).data("id");

    Swal.fire({
        title: "¿Eliminar este lote?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `/lote/${id}`,
            method: "POST",
            data: {
                _method: "DELETE",
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                Swal.fire("Eliminado", response.mensaje, "success");
                buscarLotes();
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.mensaje || "No se pudo eliminar.";
                Swal.fire("Error", msg, "error");
            },
        });
    });
});

// ============================================================
// CAJA — Lógica del módulo
// ============================================================
 
$(document).ready(function () {
    inicializarEventos();
});
 
function inicializarEventos() {
    $("#form-abrir-caja").on("submit", abrirCaja);
    $("#form-cerrar-caja").on("submit", cerrarCaja);
    $("#form-movimiento").on("submit", registrarMovimiento);
    $("#btn-abrir-cierre").on("click", abrirModalCierre);
    $(document).on("click", ".btn-ver-detalle", verDetalleCaja);
}
 
// ============================================================
// ABRIR CAJA
// ============================================================
function abrirCaja(e) {
    e.preventDefault();
 
    let form = $(this);
    let datos = form.serialize();
 
    Swal.fire({
        title: "Procesando...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });
 
    $.post("/admin/caja/abrir", datos)
        .done(function (res) {
            Swal.fire({
                icon: "success",
                title: "Caja abierta",
                text: res.message,
                timer: 1500,
                showConfirmButton: false,
            }).then(() => location.reload());
        })
        .fail(function (xhr) {
            Swal.fire("Error", xhr.responseJSON?.message || "No se pudo abrir la caja", "error");
        });
}
 
// ============================================================
// CIERRE — abrir modal con resumen calculado
// ============================================================
function abrirModalCierre() {
    let inicial = parseFloat($("#saldo-actual-display").data("inicial")) ||
                  parseFloat($("[data-saldo-inicial]").first().text()) || 0;
 
    // Tomamos los valores directamente de los info-box visibles
    let textos = $(".info-box-number").map(function () {
        return $(this).text().replace("Bs.", "").trim();
    });
 
    let saldoInicial = parseFloat(textos[0]) || 0;
    let ingresos     = parseFloat(textos[1]) || 0;
    let egresos      = parseFloat(textos[2]) || 0;
    let saldoActual  = parseFloat(textos[3]) || 0;
 
    $("#cierre_saldo_inicial").text("Bs. " + saldoInicial.toFixed(2));
    $("#cierre_ingresos").text("Bs. " + ingresos.toFixed(2));
    $("#cierre_egresos").text("Bs. " + egresos.toFixed(2));
    $("#cierre_saldo_final").text("Bs. " + saldoActual.toFixed(2));
 
    $("#modalCerrarCaja").modal("show");
}
 
// ============================================================
// CERRAR CAJA
// ============================================================
function cerrarCaja(e) {
    e.preventDefault();
 
    let form = $(this);
    let datos = form.serialize();
 
    Swal.fire({
        title: "¿Confirmar cierre de caja?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cerrar caja",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#dc3545",
    }).then((result) => {
        if (!result.isConfirmed) return;
 
        $.post("/admin/caja/cerrar", datos)
            .done(function (res) {
                Swal.fire({
                    icon: "success",
                    title: "Caja cerrada",
                    text: "Saldo final: Bs. " + res.saldo_final,
                    timer: 2000,
                    showConfirmButton: false,
                }).then(() => location.reload());
            })
            .fail(function (xhr) {
                Swal.fire("Error", xhr.responseJSON?.message || "No se pudo cerrar la caja", "error");
            });
    });
}
 
// ============================================================
// REGISTRAR MOVIMIENTO MANUAL
// ============================================================
function registrarMovimiento(e) {
    e.preventDefault();
 
    let form = $(this);
    let datos = form.serialize();
 
    $.post("/admin/caja/movimiento", datos)
        .done(function (res) {
            Swal.fire({
                icon: "success",
                title: "Movimiento registrado",
                text: "Nuevo saldo: Bs. " + res.saldo_actual,
                timer: 1500,
                showConfirmButton: false,
            }).then(() => location.reload());
        })
        .fail(function (xhr) {
            Swal.fire("Error", xhr.responseJSON?.message || "No se pudo registrar el movimiento", "error");
        });
}
 
// ============================================================
// VER DETALLE DE CAJA HISTÓRICA
// ============================================================
function verDetalleCaja() {
    let id = $(this).data("id");
 
    $.get("/admin/caja/" + id + "/detalle")
        .done(function (res) {
            let caja = res.caja;
 
            let resumenHtml = `
                <div class="col-3 resumen-item">
                    <small>Saldo Inicial</small>
                    <strong>Bs. ${parseFloat(caja.saldo_inicial).toFixed(2)}</strong>
                </div>
                <div class="col-3 resumen-item">
                    <small>Ingresos</small>
                    <strong class="text-success">Bs. ${sumarPorTipo(res.movimientos, 'ingreso').toFixed(2)}</strong>
                </div>
                <div class="col-3 resumen-item">
                    <small>Egresos</small>
                    <strong class="text-danger">Bs. ${sumarPorTipo(res.movimientos, 'egreso').toFixed(2)}</strong>
                </div>
                <div class="col-3 resumen-item">
                    <small>Saldo Final</small>
                    <strong>Bs. ${parseFloat(caja.saldo_final).toFixed(2)}</strong>
                </div>
            `;
 
            $("#detalle-caja-resumen").html(resumenHtml);
 
            let filas = "";
            res.movimientos.forEach(function (mov) {
                let badge = obtenerBadgeTipo(mov.tipo);
                let signo = mov.tipo === "egreso" ? "-" : "+";
                let colorClase = mov.tipo === "egreso" ? "text-danger" : "text-success";
 
                filas += `
                    <tr>
                        <td>${(mov.hora || "").substring(0, 5)}</td>
                        <td>${badge}</td>
                        <td>${mov.descripcion}</td>
                        <td>${mov.empleado ? mov.empleado.nombre : "-"}</td>
                        <td class="text-right font-weight-bold ${colorClase}">
                            ${signo} Bs. ${parseFloat(mov.monto).toFixed(2)}
                        </td>
                    </tr>
                `;
            });
 
            $("#detalle-caja-movimientos").html(
                filas || '<tr><td colspan="5" class="text-center text-muted">Sin movimientos</td></tr>'
            );
 
            $("#modalDetalleCaja").modal("show");
        })
        .fail(function () {
            Swal.fire("Error", "No se pudo cargar el detalle de la caja", "error");
        });
}
 
function sumarPorTipo(movimientos, tipo) {
    return movimientos
        .filter((m) => m.tipo === tipo)
        .reduce((sum, m) => sum + parseFloat(m.monto), 0);
}
 
function obtenerBadgeTipo(tipo) {
    switch (tipo) {
        case "ingreso":
            return '<span class="badge badge-success"><i class="fas fa-arrow-down"></i> Ingreso</span>';
        case "egreso":
            return '<span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Egreso</span>';
        case "apertura":
            return '<span class="badge badge-secondary"><i class="fas fa-door-open"></i> Apertura</span>';
        default:
            return '<span class="badge badge-dark"><i class="fas fa-door-closed"></i> Cierre</span>';
    }
}
// ============================================================
// REPORTES DE COMPRAS — Lógica y gráficas
// ============================================================
 
let chartComprasDia = null;
let chartProveedores = null;
let chartProductosCompra = null;
 
$(document).ready(function () {
    inicializarEventos();
    generarReporte();
});
 
function inicializarEventos() {
    $("#filtro_periodo").on("change", manejarPeriodo);
    $("#btn_generar").on("click", generarReporte);
}
 
function manejarPeriodo() {
    if ($("#filtro_periodo").val() === "personalizado") {
        $("#rango_personalizado").show();
        $("#rango_hasta").show();
    } else {
        $("#rango_personalizado").hide();
        $("#rango_hasta").hide();
    }
}
 
// ============================================================
// GENERAR REPORTE
// ============================================================
function generarReporte() {
    let params = obtenerParametros();
    if (!params) return;
 
    bloquearBoton();
 
    $.get("/compras/reportes/datos", params)
        .done(function (data) {
            actualizarKPIs(data.kpis);
            renderChartComprasDia(data.por_dia);
            renderChartProveedores(data.por_proveedor);
            renderChartProductosCompra(data.top_productos);
            renderTabla(data.compras);
        })
        .fail(function () {
            Swal.fire("Error", "No se pudo cargar el reporte", "error");
        })
        .always(function () {
            desbloquearBoton();
        });
}
 
function obtenerParametros() {
    let periodo = $("#filtro_periodo").val();
    let params = { periodo: periodo };
 
    if (periodo === "personalizado") {
        params.desde = $("#fecha_desde").val();
        params.hasta = $("#fecha_hasta").val();
 
        if (!params.desde || !params.hasta) {
            Swal.fire("Atención", "Seleccione fecha desde y hasta", "warning");
            return null;
        }
    }
 
    return params;
}
 
function bloquearBoton() {
    $("#btn_generar")
        .html('<i class="fas fa-spinner fa-spin mr-1"></i> Cargando...')
        .prop("disabled", true);
}
 
function desbloquearBoton() {
    $("#btn_generar")
        .html('<i class="fas fa-sync-alt mr-1"></i> Generar Reporte')
        .prop("disabled", false);
}
 
// ============================================================
// KPIs
// ============================================================
function actualizarKPIs(kpis) {
    $("#kpi_total_gastado").text("Bs. " + parseFloat(kpis.total_gastado).toFixed(2));
    $("#kpi_num_compras").text(kpis.cantidad);
    $("#kpi_promedio").text("Bs. " + parseFloat(kpis.promedio).toFixed(2));
}
 
// ============================================================
// GRÁFICO COMPRAS POR DÍA
// ============================================================
function renderChartComprasDia(data) {
    let ctx = document.getElementById("chart_compras_dia").getContext("2d");
 
    if (chartComprasDia) chartComprasDia.destroy();
 
    chartComprasDia = new Chart(ctx, {
        type: "bar",
        data: {
            labels: data.map((d) => d.fecha),
            datasets: [
                {
                    label: "Gasto en Compras (Bs.)",
                    data: data.map((d) => d.total),
                    backgroundColor: "rgba(220,53,69,0.7)",
                    borderColor: "rgba(220,53,69,1)",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });
}
 
// ============================================================
// GRÁFICO TOP PROVEEDORES
// ============================================================
function renderChartProveedores(data) {
    let ctx = document.getElementById("chart_proveedores").getContext("2d");
 
    if (chartProveedores) chartProveedores.destroy();
 
    const colors = ["#007bff", "#28a745", "#ffc107", "#dc3545", "#6f42c1", "#20c997", "#fd7e14"];
 
    chartProveedores = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: data.map((d) => d.proveedor),
            datasets: [
                {
                    data: data.map((d) => d.total),
                    backgroundColor: colors.slice(0, data.length),
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: "bottom" } },
        },
    });
}
 
// ============================================================
// GRÁFICO TOP PRODUCTOS COMPRADOS
// ============================================================
function renderChartProductosCompra(data) {
    let ctx = document.getElementById("chart_productos_compra").getContext("2d");
 
    if (chartProductosCompra) chartProductosCompra.destroy();
 
    chartProductosCompra = new Chart(ctx, {
        type: "bar",
        data: {
            labels: data.map((d) => d.nombre),
            datasets: [
                {
                    label: "Unidades compradas",
                    data: data.map((d) => d.cantidad),
                    backgroundColor: "rgba(255,193,7,0.7)",
                    borderColor: "rgba(255,193,7,1)",
                    borderWidth: 1,
                    yAxisID: "y",
                },
                {
                    label: "Gasto (Bs.)",
                    data: data.map((d) => d.gasto),
                    backgroundColor: "rgba(13,110,253,0.5)",
                    borderColor: "rgba(13,110,253,1)",
                    borderWidth: 1,
                    yAxisID: "y1",
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: "top" } },
            scales: {
                y: {
                    beginAtZero: true,
                    position: "left",
                    title: { display: true, text: "Unidades" },
                },
                y1: {
                    beginAtZero: true,
                    position: "right",
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: "Bs." },
                },
            },
        },
    });
}
 
// ============================================================
// TABLA DETALLE
// ============================================================
function renderTabla(compras) {
    let tbody = $("#tbody_reporte_compras");
    tbody.empty();
 
    if (!compras || compras.length === 0) {
        tbody.html('<tr><td colspan="7" class="text-center text-muted">Sin compras en este período</td></tr>');
        return;
    }
 
    compras.forEach(function (c) {
        let badgePago = c.tipo_pago === "contado"
            ? '<span class="badge badge-success">Contado</span>'
            : '<span class="badge badge-warning">Crédito</span>';
 
        tbody.append(`
            <tr>
                <td>${c.id_compra}</td>
                <td>${c.fecha_compra}</td>
                <td>${c.empleado}</td>
                <td>${c.items}</td>
                <td>${badgePago}</td>
                <td>Bs. ${parseFloat(c.descuento).toFixed(2)}</td>
                <td class="font-weight-bold">Bs. ${parseFloat(c.total_compra).toFixed(2)}</td>
            </tr>
        `);
    });
}
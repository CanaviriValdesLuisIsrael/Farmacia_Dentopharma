// ======================================================
// VARIABLES GLOBALES
// ======================================================

let chartDia = null;
let chartVendedores = null;
let chartProductos = null;
let chartCategorias = null;

// ======================================================
// INICIALIZACIÓN
// ======================================================

$(document).ready(function () {
    inicializarEventos();

    generarReporte();
});

// ======================================================
// EVENTOS
// ======================================================

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

// ======================================================
// REPORTE
// ======================================================

function generarReporte() {
    let params = obtenerParametros();

    if (!params) {
        return;
    }

    bloquearBoton();

    $.get("/ventas/reportes/datos", params)

        .done(function (data) {
            actualizarKPIs(data.kpis);

            renderChartDia(data.por_dia);

            renderChartVendedores(data.por_vendedor);

            renderChartProductos(data.top_productos);

            renderChartCategorias(data.por_categoria);

            renderTabla(data.ventas);
        })

        .fail(function () {
            Swal.fire("Error", "No se pudo cargar el reporte", "error");
        })

        .always(function () {
            desbloquearBoton();
        });
}

// ======================================================
// PARÁMETROS
// ======================================================

function obtenerParametros() {
    let periodo = $("#filtro_periodo").val();

    let params = {
        periodo: periodo,
    };

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

// ======================================================
// BOTÓN CARGA
// ======================================================

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

// ======================================================
// KPI
// ======================================================

function actualizarKPIs(kpis) {
    $("#kpi_total_ventas").text("Bs. " + parseFloat(kpis.total).toFixed(2));

    $("#kpi_num_ventas").text(kpis.cantidad);

    $("#kpi_promedio").text("Bs. " + parseFloat(kpis.promedio).toFixed(2));

    $("#kpi_mejor_dia").text(kpis.mejor_dia || "-");
}

// ======================================================
// GRÁFICO VENTAS POR DÍA
// ======================================================

function renderChartDia(data) {
    let ctx = document.getElementById("chart_ventas_dia").getContext("2d");

    if (chartDia) {
        chartDia.destroy();
    }

    chartDia = new Chart(ctx, {
        type: "bar",

        data: {
            labels: data.map((d) => d.fecha),

            datasets: [
                {
                    label: "Ventas (Bs.)",
                    data: data.map((d) => d.total),
                    backgroundColor: "rgba(54,162,235,0.7)",
                    borderColor: "rgba(54,162,235,1)",
                    borderWidth: 1,
                },
            ],
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    display: false,
                },
            },

            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
}

// ======================================================
// GRÁFICO VENDEDORES
// ======================================================

function renderChartVendedores(data) {
    let ctx = document.getElementById("chart_vendedores").getContext("2d");

    if (chartVendedores) {
        chartVendedores.destroy();
    }

    const colors = [
        "#007bff",
        "#28a745",
        "#ffc107",
        "#dc3545",
        "#6f42c1",
        "#20c997",
    ];

    chartVendedores = new Chart(ctx, {
        type: "doughnut",

        data: {
            labels: data.map((d) => d.nombre),

            datasets: [
                {
                    data: data.map((d) => d.total),
                    backgroundColor: colors.slice(0, data.length),
                },
            ],
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: "bottom",
                },
            },
        },
    });
}

// ======================================================
// GRÁFICO PRODUCTOS
// ======================================================

function renderChartProductos(data) {
    let ctx = document.getElementById("chart_productos").getContext("2d");

    if (chartProductos) {
        chartProductos.destroy();
    }

    chartProductos = new Chart(ctx, {
        type: "bar",

        data: {
            labels: data.map((d) => d.nombre),

            datasets: [
                {
                    label: "Unidades vendidas",
                    data: data.map((d) => d.cantidad),
                    backgroundColor: "rgba(255,159,64,0.7)",
                    borderColor: "rgba(255,159,64,1)",
                    borderWidth: 1,
                },
            ],
        },

        options: {
            indexAxis: "y",

            responsive: true,

            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
}

// ======================================================
// GRÁFICO CATEGORÍAS
// ======================================================

function renderChartCategorias(data) {
    let ctx = document.getElementById("chart_categorias").getContext("2d");

    if (chartCategorias) {
        chartCategorias.destroy();
    }

    const colors = [
        "#28a745",
        "#dc3545",
        "#ffc107",
        "#17a2b8",
        "#6f42c1",
        "#fd7e14",
    ];

    chartCategorias = new Chart(ctx, {
        type: "pie",

        data: {
            labels: data.map((d) => d.categoria),

            datasets: [
                {
                    data: data.map((d) => d.total),
                    backgroundColor: colors.slice(0, data.length),
                },
            ],
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: "bottom",
                },
            },
        },
    });
}

// ======================================================
// TABLA
// ======================================================

function renderTabla(ventas) {
    let html = "";

    if (!ventas.length) {
        html = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Sin ventas en este período
                </td>
            </tr>
        `;
    } else {
        ventas.forEach((v, i) => {
            html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${v.fecha_venta}</td>
                    <td>${v.cliente ?? "Sin cliente"}</td>
                    <td>${v.vendedor ?? "-"}</td>
                    <td>Bs. ${parseFloat(v.total_venta).toFixed(2)}</td>
                    <td>
                        <a
                            href="/venta/${v.id_venta}/imprimir"
                            target="_blank"
                            class="btn btn-sm btn-secondary">
                            <i class="fas fa-print"></i>
                        </a>
                    </td>
                </tr>
            `;
        });
    }

    $("#tbody_reporte").html(html);

    if ($.fn.DataTable.isDataTable("#tabla_reporte")) {
        $("#tabla_reporte").DataTable().destroy();
    }

    $("#tabla_reporte").DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
        },

        order: [[1, "desc"]],

        pageLength: 10,

        responsive: true,
    });
}

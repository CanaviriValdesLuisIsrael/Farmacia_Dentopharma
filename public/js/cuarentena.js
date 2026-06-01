$(document).ready(function() {
        cargarCuarentena();
    });

    function cargarCuarentena() {
        $.get('/admin/cuarentena/datos', function(data) {
            renderVencidos(data.vencidos);
            renderSinStock(data.sin_stock);
            $('#badge_vencidos').text(data.vencidos.length);
            $('#badge_sin_stock').text(data.sin_stock.length);
        }).fail(function() {
            $('#tbody_vencidos, #tbody_sin_stock').html(
                '<tr><td colspan="8" class="text-center text-danger">Error al cargar datos</td></tr>'
            );
        });
    }

    function renderVencidos(lotes) {
        let html = '';
        let hoy = new Date();

        if (!lotes.length) {
            html =
                '<tr><td colspan="8" class="text-center text-success"><i class="fas fa-check-circle mr-1"></i>No hay lotes vencidos</td></tr>';
        } else {
            lotes.forEach(l => {
                let venc = new Date(l.fecha_vencimiento);
                let diasVencido = Math.floor((hoy - venc) / (1000 * 60 * 60 * 24));
                html += `<tr class="table-danger">
                <td><span class="badge badge-dark">${l.numero_lote}</span></td>
                <td>${l.producto?.nombre_comercial ?? '-'}</td>
                <td>${l.producto?.laboratorio?.nombre ?? '-'}</td>
                <td>${l.proveedor?.nombre ?? '-'}</td>
                <td>${l.cantidad_por_caja}</td>
                <td><span class="text-danger font-weight-bold">${l.fecha_vencimiento}</span></td>
                <td><span class="badge badge-danger">${diasVencido} días</span></td>
                <td>
                    <button class="btn btn-sm btn-danger btn-eliminar-lote" data-id="${l.id_lote}"
                        title="Dar de baja este lote">
                        <i class="fas fa-trash-alt"></i> Dar de baja
                    </button>
                </td>
            </tr>`;
            });
        }
        $('#tbody_vencidos').html(html);

        if ($.fn.DataTable.isDataTable('#tabla_vencidos')) $('#tabla_vencidos').DataTable().destroy();
        $('#tabla_vencidos').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            order: [
                [5, 'asc']
            ]
        });
    }

    function renderSinStock(lotes) {
        let html = '';
        let hoy = new Date();

        if (!lotes.length) {

            // No agregamos fila manual
            // Dejamos que DataTables muestre su mensaje de tabla vacía
            html = '';

        } else {

            lotes.forEach(l => {

                let venc = new Date(l.fecha_vencimiento);

                let estado = venc < hoy ?
                    '<span class="badge badge-danger">Vencido</span>' :
                    '<span class="badge badge-secondary">Sin stock</span>';

                html += `
            <tr class="table-secondary">
                <td>
                    <span class="badge badge-dark">
                        ${l.numero_lote}
                    </span>
                </td>

                <td>
                    ${l.producto?.nombre_comercial ?? '-'}
                </td>

                <td>
                    ${l.producto?.laboratorio?.nombre ?? '-'}
                </td>

                <td>
                    ${l.proveedor?.nombre ?? '-'}
                </td>

                <td>
                    ${l.fecha_vencimiento}
                </td>

                <td>
                    ${estado}
                </td>

                <td>
                    <button
                        class="btn btn-sm btn-outline-danger btn-eliminar-lote"
                        data-id="${l.id_lote}"
                        title="Eliminar este lote">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
            });
        }

        $('#tbody_sin_stock').html(html);

        // Destruir DataTable si ya existe
        if ($.fn.DataTable.isDataTable('#tabla_sin_stock')) {
            $('#tabla_sin_stock').DataTable().destroy();
        }

        // Inicializar DataTable
        $('#tabla_sin_stock').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            pageLength: 10,
            responsive: true,
            ordering: true,
            searching: true
        });
    }

    // Eliminar / dar de baja lote
    $(document).on('click', '.btn-eliminar-lote', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: '¿Dar de baja este lote?',
            text: 'Esta acción eliminará el lote del sistema. Se recomienda solo para lotes entregados al laboratorio.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, dar de baja',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/lote/${id}`,
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(r) {
                        Swal.fire('Eliminado', r.mensaje, 'success');
                        cargarCuarentena();
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar el lote', 'error');
                    }
                });
            }
        });
    });
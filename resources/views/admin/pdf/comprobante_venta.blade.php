<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        {!! file_get_contents(public_path('css/comprobante.css')) !!}
    </style>

</head>

<body>

    <!-- ================= HEADER ================= -->

    <table class="header-table">
        <tr>
            <td class="logo-td">
                <img src="{{ public_path('img/logofar.png') }}" class="logo">
            </td>
            <td class="empresa-td">
                <h1>Farmacia Dentopharma</h1>
                <p>Av. Litoral</p>
                <p>Tel: 4185435</p>
            </td>
        </tr>
    </table>
    <div class="linea"></div>

    <!-- ================= TITULO ================= -->

    <h2 class="titulo">
        COMPROBANTE DE PAGO
    </h2>
    <!-- ================= DATOS ================= -->
    <table class="info-table">
        <tr>
            <td><strong>Código Venta:</strong></td>
            <td>{{ $venta->id_venta }}</td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong></td>
            <td>{{ $venta->cliente->nombre ?? 'Sin cliente' }}</td>
        </tr>

        <tr>
            <td><strong>CI:</strong></td>
            <td>{{ $venta->cliente->ci ?? '-' }}</td>
        </tr>

        <tr>
            <td><strong>Fecha:</strong></td>
            <td>{{ $venta->fecha_venta }}</td>
        </tr>

        <tr>
            <td><strong>Vendedor:</strong></td>
            <td>{{ $venta->empleado->nombre . ' ' . $venta->empleado->apellido ?? '-' }}</td>
        </tr>

    </table>

    <!-- ================= TABLA ================= -->

    <table class="tabla-productos">
        <thead>
            <tr>
                <th>Cant.</th>
                <th>Producto</th>
                <th>Concentración</th>
                <th>Presentación</th>
                <th>Laboratorio</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>

            @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>
                        {{ $detalle->producto->nombre_comercial }}
                    </td>
                    <td>
                        {{ $detalle->producto->concentracion }}
                    </td>
                    <td>
                        {{ $detalle->producto->categoria->nombre ?? '-' }}
                    </td>
                    <td>
                        {{ $detalle->producto->laboratorio->nombre ?? '-' }}
                    </td>
                    <td>
                        Bs {{ number_format($detalle->precio_unitario, 2) }}
                    </td>
                    <td>
                        Bs {{ number_format($detalle->subtotal, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ================= TOTAL ================= -->

    <div class="contenedor-total">
        <table class="tabla-total">
            <tr>
                <td class="label-total">TOTAL:</td>
                <td class="valor-total">
                    Bs {{ number_format($venta->total_venta, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= FOOTER ================= -->

    <div class="footer">
        <p>
            Gracias por su compra.
        </p>
    </div>
</body>
</html>
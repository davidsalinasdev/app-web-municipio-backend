<style>
    .titulo-recibo {
        text-align: center;
        font-weight: bold;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    th,
    td {
        padding: 8px;
        text-align: center;
    }

    th {
        font-size: 12px;
        text-transform: uppercase;
    }

    .footer {
        margin-top: 5px;
        text-align: left;
    }

    .verificado {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -150%);
        opacity: 0.1;
        font-size: 45px;
        color: #9c9c9c;
        z-index: -1;
    }

    .img-escudo {
        width: 50px;
        text-align: center;
    }

    .img-logo {
        width: 118px;
        text-align: center;
    }

    .titulo-main span {
        text-align: center;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .seccion-alquiler {
        text-align: center;
        text-transform: uppercase;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .datos-persona {
        font-size: 12px;
        text-align: left;
        text-transform: uppercase;

    }

    .datos-persona__titulo {
        font-weight: bold;
    }

    .datos-persona__datos {
        font-weight: normal !important;
    }

    .seccion-descripcion td {
        border: 1px solid #b4b3b3;
    }

    .precio-total {
        font-weight: bold;
        text-transform: uppercase;
        font-size: 14px;
    }

    .total-nro {
        font-weight: bold;
        font-size: 16px;
    }
</style>
</head>

<body>
    <!-- Tamaño de ancho de la hoja es 660px -->
    <!-- Encabezado titulo -->
    <table>
        <tr>
            <th style="width: 220px;">

                @php
                $imagePath = public_path('img/escudo_dos.png');
                $imageData = base64_encode(file_get_contents($imagePath));
                $imageSrc = 'data:image/png;base64,' . $imageData;
                @endphp
                <img class="img-escudo" src="{{ $imageSrc }}" alt="Escudo gamdc">
            <th style="width: 220px;">
                @php
                $imagePath = public_path('img/logo.png');
                $imageData = base64_encode(file_get_contents($imagePath));
                $imageSrc = 'data:image/png;base64,' . $imageData;
                @endphp
                <img class="img-logo" src="{{ $imageSrc }}" alt="Logo gamdc">
            </th>
            <th style="width: 220px;">
                <h3 class="titulo-recibo">RECIBO Nro. 123</h3>
            </th>
        </tr>
        <tr>
            <td colspan="3" class="titulo-main">
                <span>Dirección de ingresos municipales</span><br>
                <span>Unidad de recaudaciones</span><br>
                <span class="seccion-alquiler">Sección alquiler de puestos de venta</span>
            </td>
        </tr>
        <!-- Datos del arrendatario -->
        <tr>
            <td colspan="3" class="datos-persona">
                <span class="datos-persona__titulo">Arrendatario: <span class="datos-persona__datos">ASHLY FRANCIS SILES MAMANI</span></span><br><br>
                <span class="datos-persona__titulo">Nro. puesto: <span class="datos-persona__datos">589</span> </span><br><br>
                <span class="datos-persona__titulo">Monto de alquiler: <span class="datos-persona__datos">100</span></span> Bs.
            </td>

        </tr>
    </table>

    <!-- Tabla de datos descripcion -->
    <table>
        <tr style="background-color: #f2f2f2;">
            <th>Cantidad</th>
            <th>Concepto</th>
            <th>Importe</th>
            <th>Total</th>
        </tr>
        <!-- Para iterar -->
        <tr class="seccion-descripcion">
            <td>1</td>
            <td>Enero, Febrero y Multa</td>
            <td>485</td>
            <td>485</td>
        </tr>
        <tr class="seccion-descripcion">
            <td>1</td>
            <td>Agosto</td>
            <td>63</td>
            <td>63</td>
        </tr>

        <!-- Total a pagar -->
        <tr class="seccion-descripcion">
            <td class="precio-total" colspan="3">Total (Bs.)</td>
            <td class="total-nro">95</td>
        </tr>

    </table>



    <div class="footer">
        <p style="text-transform: uppercase; float: left; font-size: 12px;">SÓN: treinta y cinco 00/100 Bolivianos.</p>
        <p style="text-transform: uppercase; float:right; font-size: 12px;">Fecha de emision: 16 de OCTUBRE del 2024</p>
    </div>
    <div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <div>
        <p style="text-transform: uppercase; text-align: center; font-size: 13px;">Firma y sello cajero</p>
    </div>

    <div class="verificado" style="text-align: center;">
        VERIFICADO POR SISTEMA
        GAMDC
    </div>
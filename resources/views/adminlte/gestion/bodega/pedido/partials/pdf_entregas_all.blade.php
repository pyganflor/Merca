    <div style="position: relative; left: -30px; width: 705px">
        <table class="text-center" style="width: 100%">
            <tr>
                <th style="text-align: center" colspan="2">
                    RECIBO DE ENTREGA
                </th>
            </tr>
            <tr>
                <th style="font-size: 0.7em; text-align: left">
                    BENCHMARKET S.A.S.
                </th>
                <th style="font-size: 0.7em; text-align: right">
                    RUC <b>1793209142001</b>
                </th>
            </tr>
        </table>

        <table>
            <tr style="font-size: 0.8em">
                <th class="border-1px text-center">
                    Cliente
                </th>
                <th class="border-1px text-center" style="width: 80px">
                    CI
                </th>
                <th class="border-1px text-center" style="width: 60px">
                    Subtotal
                </th>
                <th class="border-1px text-center" style="width: 40px">
                    Iva
                </th>
                <th class="border-1px text-center" style="width: 50px">
                    Total
                </th>
                <th class="border-1px text-center" style="width: 130px">
                    Firma
                </th>
            </tr>
            @foreach ($datos['pedidos'] as $pos_ped => $pedido)
                @php
                    $usuario = $pedido->usuario;
                    $monto_total = 0;
                    $monto_subtotal = 0;
                    $monto_total_iva = 0;
                @endphp
                @php
                    $monto_subtotal = 0;
                    $monto_total_iva = 0;
                    $monto_total = 0;
                @endphp
                @foreach ($pedido->detalles as $det)
                    @php
                        $producto = $det->producto;
                        $precio_prod = $det->cantidad * $det->precio;
                        if ($det->iva == true) {
                            $monto_subtotal += $precio_prod / 1.12;
                            $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                        } else {
                            $monto_subtotal += $precio_prod;
                        }
                        $monto_total += $precio_prod;
                    @endphp
                @endforeach
                <tr style="font-size: 0.7em">
                    <th class="border-1px" style="text-align: left">
                        {{ $usuario->nombre_completo }}
                    </th>
                    <th class="border-1px">
                        {{ $usuario->username }}
                    </th>
                    <th class="border-1px">
                        ${{ number_format($monto_subtotal, 2) }}
                    </th>
                    <th class="border-1px">
                        ${{ number_format($monto_total_iva, 2) }}
                    </th>
                    <th class="border-1px">
                        ${{ number_format($monto_total, 2) }}
                    </th>
                    <th class="border-1px" style="height: 35px">
                    </th>
                </tr>
            @endforeach
        </table>
    </div>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        .border-1px {
            border: 1px solid black;
        }

        .text-center {
            text-align: center;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        td,
        th {
            padding: 0;
            margin: 0;
        }

        .hidden {
            display: none;
        }
    </style>

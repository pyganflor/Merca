    <div style="position: relative; left: -20px; top: -30px; width: 695px">
        <table class="text-center" style="width: 100%">
            <tr>
                <th style="text-align: center" colspan="2">
                    RECIBO DE ENTREGA para "{{ $datos['finca']->nombre }}"
                    <br>
                    <small style="font-size: 0.8em">
                        {{ convertDateToText($datos['fecha']) }}
                    </small>
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
                <th class="border-1px text-center" style="width: 80px">
                    Telf.
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
                <th class="border-1px text-center" style="width: 250px">
                    Firma
                </th>
            </tr>
            @foreach ($datos['pedidos'] as $pos_ped => $pedido)
                @php
                    $fecha = $pedido->getFechaEntrega();
                    $usuario = $pedido->usuario;
                    $monto_subtotal = 0;
                    $monto_total_iva = 0;
                    $monto_total = 0;
                    $monto_diferido = 0;
                    $diferido_selected = 0;
                @endphp
                @foreach ($pedido->detalles as $det)
                    @php
                        $producto = $det->producto;
                        if ($producto->peso == 0) {
                            $precio_prod = $det->cantidad * $det->precio;
                        } else {
                            $precio_prod = 0;
                            foreach ($det->etiquetas_peso as $e) {
                                $precio_prod += $e->peso * $e->precio_venta;
                            }
                        }
                        if ($det->iva == true) {
                            $monto_subtotal += $precio_prod / 1.12;
                            $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                        } else {
                            $monto_subtotal += $precio_prod;
                        }
                        $monto_total += $precio_prod;

                        if ($det->diferido > 0) {
                            $monto_diferido += $precio_prod / $det->diferido;
                            if ($diferido_selected == 0) {
                                $diferido_selected = $det->diferido;
                            }
                        }
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
                        {{ $usuario->telefono }}
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
                    <th class="border-1px" style="height: 35px"
                        rowspan="{{ $monto_diferido > 0 ? $diferido_selected + 1 : 1 }}">
                    </th>
                </tr>
                @php
                    $diferido_mes_inicial = $pedido->diferido_mes_actual ? 0 : 1;
                    $diferido_mes_final = $pedido->diferido_mes_actual ? $diferido_selected - 1 : $diferido_selected;
                @endphp
                @if ($monto_diferido > 0)
                    @for ($m = $diferido_mes_inicial; $m <= $diferido_mes_final; $m++)
                        @php
                            $fechaObj = new DateTime($fecha);
                            $fechaObj->modify('+' . $m . ' month');
                            $fecha_next = $fechaObj->format('Y-m-d');
                        @endphp
                        <tr style="font-size: 0.6em">
                            @if ($m == $diferido_mes_inicial)
                                <th class="border-1px" style="text-align: center;" colspan="2"
                                    rowspan="{{ $diferido_selected }}">
                                    DESCUENTO
                                </th>
                            @endif
                            <th style="text-align: left; padding-left: 2px" colspan="3">
                                {{ explode('de ', convertDateToText($fecha_next))[1] }}
                            </th>
                            <th style="text-align: right; padding-right: 2px">
                                ${{ number_format($monto_diferido, 2) }}
                            </th>
                        </tr>
                    @endfor
                @endif
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

@php
    $usuario = $datos['pedido']->usuario;
    $monto_total = 0;
    $monto_subtotal = 0;
    $monto_total_iva = 0;
    $pedido = $datos['pedido'];
    $fecha = $pedido->getFechaEntrega();
@endphp
<div style="position: relative; top: -40px; left: -40px; width: 245px">
    <table class="text-center" style="width: 100%">
        <tr>
            <th style="vertical-align: top; text-align: center">
                {!! $barCode->getBarcode(str_pad($pedido->id_pedido_bodega, 8, '0', STR_PAD_LEFT), $barCode::TYPE_CODE_128, 2) !!}
                <span style="font-size: 0.8em">
                    {{ str_pad($pedido->id_pedido_bodega, 8, '0', STR_PAD_LEFT) }}
                </span>
            </th>
        </tr>
    </table>
    <table class="text-center" style="width: 100%">
        <tr>
            <th style="text-align: center" colspan="2">
                NOTA DE ENTREGA
                <br>
                <span style="font-size: 0.8em">
                    {{ $pedido->empresa->nombre }}
                </span>
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left" colspan="2">
                BENCHMARKET S.A.S.
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left" colspan="2">
                RUC <b>1793209142001</b>
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left" colspan="2">
                Entrega: {{ $fecha != '' ? $fecha : $pedido->fecha . '*' }}
            </th>
        </tr>
        <tr>
            <th style="text-align: left" colspan="2">
                {{ $usuario->nombre_completo }}
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left">
                CI: {{ $usuario->username }}
            </th>
            <th style="font-size: 0.7em; text-align: right">
                Saldo: ${{ number_format($pedido->saldo_usuario, 2) }}
            </th>
        </tr>
    </table>

    <table>
        <tr style="font-size: 0.7em">
            <th class="border-1px text-center">
                Item
            </th>
            <th class="border-1px text-center" style="width: 40px">
                Precio
            </th>
            <th class="border-1px text-center" style="width: 40px">
                Cant
            </th>
            <th class="border-1px text-center" style="width: 40px">
                Total
            </th>
        </tr>
        @php
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
            <tr style="font-size: 0.6em">
                <th class="border-1px" style="text-align: left; padding-left: 2px">
                    {{ $producto->nombre }} {{ $det->iva ? '*' : '' }}
                </th>
                <th class="border-1px text-center">
                    ${{ number_format($det->precio, 2) }}
                </th>
                <th class="border-1px text-center">
                    {{ $det->cantidad }}
                </th>
                <th class="border-1px" style="text-align: right; padding-right: 2px">
                    ${{ number_format($precio_prod, 2) }}
                </th>
            </tr>
            @foreach ($det->etiquetas_peso as $pos_etiqueta => $e)
                <tr style="font-size: 0.6em">
                    <th class="border-1px" style="text-align: left; padding-left: 2px">
                        {{ $pos_etiqueta + 1 }}°
                    </th>
                    <th class="border-1px text-center">
                        ${{ number_format($e->precio_venta, 2) }}
                    </th>
                    <th class="border-1px text-center">
                        {{ $e->peso }}{{ $producto->unidad_medida }}
                    </th>
                    <th class="border-1px" style="text-align: right; padding-right: 2px">
                        ${{ number_format($e->peso * $e->precio_venta, 2) }}
                    </th>
                </tr>
            @endforeach
        @endforeach
        <tr style="font-size: 0.6em">
            <th style="text-align: left; padding-right: 2px" colspan="2">
                * Tiene IVA
            </th>
            <th style="text-align: right; padding-right: 2px">
                Subtotal
            </th>
            <th style="text-align: right; padding-right: 2px">
                ${{ number_format($monto_subtotal, 2) }}
            </th>
        </tr>
        <tr style="font-size: 0.6em">
            <th style="text-align: right; padding-right: 2px" colspan="3">
                Total IVA
            </th>
            <th style="text-align: right; padding-right: 2px">
                ${{ number_format($monto_total_iva, 2) }}
            </th>
        </tr>
        <tr style="font-size: 0.6em">
            <th style="text-align: right; padding-right: 2px" colspan="3">
                TOTAL
            </th>
            <th style="text-align: right; padding-right: 2px">
                ${{ number_format($monto_total, 2) }}
            </th>
        </tr>
        @if ($monto_diferido > 0)
            <tr style="font-size: 0.6em;">
                <th style="text-align: left; padding-left: 2px; border-top: 1px solid black" colspan="4">
                    DESCUENTO
                </th>
            </tr>
            @php
                $diferido_mes_inicial = $pedido->diferido_mes_actual ? 0 : 1;
                $diferido_mes_final = $pedido->diferido_mes_actual ? $diferido_selected - 1 : $diferido_selected;
            @endphp
            @for ($m = $diferido_mes_inicial; $m <= $diferido_mes_final; $m++)
                @php
                    $dia_entrega = date('d', strtotime($fecha));
                    $f = new DateTime($fecha);
                    $f->modify('first day of +' . $m . ' month');
                    $f = $f->format('Y-m-d');

                    $f = date('Y', strtotime($f)) . '-' . date('m', strtotime($f)) . '-' . $dia_entrega;
                    [$ano, $mes, $dia] = explode('-', $f);
                    $d = 1;
                    while (!checkdate($mes, $dia, $ano)) {
                        $f = new DateTime($f);
                        $f->modify('-' . $d . ' day');
                        $f = $f->format('Y-m-d');
                        [$ano, $mes, $dia] = explode('-', $f);
                        $d++;
                    }

                    $fecha_next = $f;
                @endphp
                <tr style="font-size: 0.6em">
                    <th style="text-align: right; padding-right: 2px" colspan="3">
                        {{ explode('de ', convertDateToText($fecha_next))[1] }}
                    </th>
                    <th style="text-align: right; padding-right: 2px">
                        ${{ number_format($monto_diferido, 2) }}
                    </th>
                </tr>
            @endfor
        @endif
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
</style>

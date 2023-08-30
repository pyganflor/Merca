@php
    $usuario = $datos['pedido']->usuario;
    $monto_total = 0;
    $monto_subtotal = 0;
    $monto_total_iva = 0;
    $pedido = $datos['pedido'];
@endphp
<div style="position: relative; top: -40px; left: -40px; width: 255px">
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
                NOTA DE VENTA
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left" colspan="2">
                BENCHMARKET S.A.S.
                <br>
                RUC <b>1793209142001</b>
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left" colspan="2">
                Entrega: {{ $pedido->getFechaEntrega() }}
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
            <th class="border-1px text-center">
                Precio
            </th>
            <th class="border-1px text-center">
                Cant
            </th>
            <th class="border-1px text-center">
                Total
            </th>
        </tr>
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
        @endforeach
        <tr style="font-size: 0.6em">
            <th style="text-align: right; padding-right: 2px" colspan="3">
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

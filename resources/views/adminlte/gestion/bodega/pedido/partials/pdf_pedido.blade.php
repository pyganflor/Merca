@php
    $usuario = $datos['pedido']->usuario;
    $monto_total = 0;
    $monto_subtotal = 0;
    $monto_total_iva = 0;
@endphp
<div style="position: relative; top: -40px; left: -40px; width: 320px">
    <table class="text-center" style="font-size: 0.9em; width: 100%">
        <tr>
            <td style="vertical-align: top; text-align: left">
                {!! $barCode->getBarcode(
                    str_pad($datos['pedido']->id_pedido_bodega, 8, '0', STR_PAD_LEFT),
                    $barCode::TYPE_CODE_128,
                    1,
                ) !!}
                <span style="font-size: 0.8em">
                    {{ str_pad($datos['pedido']->id_pedido_bodega, 8, '0', STR_PAD_LEFT) }}
                </span>
            </td>
            <th style="font-size: 0.8em; text-align: left">
                NOTA DE VENTA
            </th>
            <td style="text-align: right">
                <img src="{{ public_path('/images/logo_1Toque.png') }}" width="60px" alt="Logo" style="padding: 0;">
            </td>
        </tr>
    </table>
    <table class="text-center" style="font-size: 0.9em; width: 100%">
        <tr>
            <th style="font-size: 0.8em; text-align: left" colspan="3">
                BENCHMARKET S.A.S.
            </th>
            <th style="font-size: 0.8em; text-align: right" colspan="3">
                <em>RUC <b>1793209142001</b></em>
            </th>
        </tr>
    </table>

    <table style="width: 100%">
        <tr>
            <td style="font-size: 0.6em; text-align: left">
            </td>
            <td style="font-size: 0.6em; text-align: right; width: 80px">
                <em>{{ $datos['pedido']->fecha }}</em>
            </td>
        </tr>
        <tr>
            <th style="font-size: 0.9em; text-align: left">
                {{ $usuario->nombre_completo }}
                <br>
                <em style="font-size: 10px">
                    CI:{{ $usuario->username }}
                </em>
            </th>
            <td style="font-size: 0.6em; text-align: right">
                {{ $datos['pedido']->empresa->nombre }}
                <br>
                Saldo: ${{ number_format($datos['pedido']->saldo_usuario, 2) }}
            </td>
        </tr>
    </table>

    <table>
        <tr style="font-size: 0.6em">
            <th class="border-1px text-center">
                PRODUCTO
            </th>
            <th class="border-1px text-center" style="width: 60px">
                PRECIO
            </th>
            <th class="border-1px text-center" style="width: 60px">
                CANTIDAD
            </th>
            <th class="border-1px text-center" style="width: 60px">
                SUBTOTAL
            </th>
        </tr>
        @foreach ($datos['pedido']->detalles as $det)
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
                <td class="border-1px" style="text-align: left; padding-left: 2px">
                    {{ $producto->nombre }}{{ $det->iva == 1 ? '*' : '' }}
                </td>
                <td class="border-1px text-center">
                    ${{ number_format($det->precio, 2) }}
                </td>
                <td class="border-1px text-center">
                    {{ $det->cantidad }}
                </td>
                <td class="border-1px" style="text-align: right; padding-right: 2px">
                    ${{ number_format($precio_prod, 2) }}
                </td>
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

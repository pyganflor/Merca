<div style="position: relative; top: -40px; left: -40px; width: 255px">
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
            <td style="text-align: center">
                <img src="{{ public_path('/images/logo_1Toque.png') }}" width="50px" alt="Logo"
                    style="padding: 0;">
            </td>
        </tr>
        <tr>
            <th colspan="2" class="text-center" style="font-size: 0.8em">
                BENCHMARKET S.A.S.
            </th>
        </tr>
    </table>

    <table style="width: 100%">
        <tr>
            <td style="font-size: 0.6em; text-align: left">
                <em>RUC <b>1793209142001</b></em>
            </td>
            <td style="font-size: 0.6em; text-align: right; width: 60px">
                <em>{{ $datos['pedido']->fecha }}</em>
            </td>
        </tr>
        <tr>
            <th style="font-size: 0.9em; text-align: left">
                {{ $datos['pedido']->usuario->nombre_completo }}
            </th>
            <td style="font-size: 0.6em; text-align: right">
                {{ $datos['pedido']->empresa->nombre }}
                <br>
                Saldo: ${{ number_format($datos['pedido']->saldo_usuario, 2) }}
            </td>
        </tr>
    </table>

    <table class="border-1px" style="font-size: 10px">
        <tr>
            <td class="border-1px text-center">
                PRODUCTO
            </td>
            <td class="border-1px text-center">
                PRECIO
            </td>
            <td class="border-1px text-center">
                CANTIDAD
            </td>
            <td class="border-1px text-center">
                SUBTOTAL
            </td>
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

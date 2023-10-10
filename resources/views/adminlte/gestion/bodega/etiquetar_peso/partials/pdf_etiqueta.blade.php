@php
    $etiqueta = $datos['model'];
    $inventario = $etiqueta->inventario_bodega;
    $det_ped = $etiqueta->detalle_pedido_bodega;
    $producto = $det_ped->producto;
    $pedido = $det_ped->pedido_bodega;
    $fecha_entrega = $pedido->getFechaEntrega();
@endphp
<div style="position: relative; top: -30px; left: 0px; width: 100%">
    <table class="text-center" style="width: 100%">
        <tr>
            <th style="vertical-align: top; text-align: center">
                {!! $barCode->getBarcode(str_pad($etiqueta->id_etiqueta_peso, 8, '0', STR_PAD_LEFT), $barCode::TYPE_CODE_128, 2) !!}
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.8em; vertical-align: top; text-align: center">
                {{ str_pad($etiqueta->id_etiqueta_peso, 8, '0', STR_PAD_LEFT) }}
            </th>
        </tr>
    </table>
</div>

<div style="position: relative; top: -36px; left: -30px; width: 225px">
    <table class="text-center" style="width: 100%">
        <tr>
            <th style="font-size: 1em; text-align: center" colspan="3">
                {{ $producto->nombre }}
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left">
                PESO
            </th>
            <th style="font-size: 0.7em; text-align: center">
                $ Unit.
            </th>
            <th style="font-size: 0.7em; text-align: right">
                $ Total
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left">
                {{ $etiqueta->peso }}<small>{{ $producto->unidad_medida }}</small>
            </th>
            <th style="font-size: 0.7em; text-align: center">
                ${{ $etiqueta->precio_venta }}
            </th>
            <th style="font-size: 0.7em; text-align: right">
                ${{ round($etiqueta->peso * $etiqueta->precio_venta, 2) }}
            </th>
        </tr>
    </table>
    <table class="text-center" style="width: 100%">
        <tr>
            <th style="font-size: 1em; text-align: center" colspan="3">
                {{ $pedido->usuario->nombre_completo }}
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: left">
                Ped: #{{ $pedido->id_pedido_bodega }}
            </th>
            <th style="font-size: 0.7em; text-align: center">
                {{ explode(' del ', convertDateToText($fecha_entrega))[0] }}
            </th>
            <th style="font-size: 0.7em; text-align: right">
                {{ $pedido->empresa->nombre }}
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

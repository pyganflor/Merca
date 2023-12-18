<table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_ingresos">
    <thead>
        <tr id="tr_fija_top_0">
            <th class="padding_lateral_5 bg-yura_dark">
                FECHA y HORA
            </th>
            <th class="padding_lateral_5 bg-yura_dark" style="width: 50% !important">
                PRODUCTO
            </th>
            <th class="text-center bg-yura_dark" style="width: 60px">
                CANTIDAD
            </th>
            <th class="text-center bg-yura_dark" style="width: 60px">
                PRECIO
            </th>
            <th class="text-center bg-yura_dark" style="width: 60px">
                VALOR
            </th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_valor = 0;
        @endphp
        @foreach ($ingresos as $item)
            @php
                $producto = $item->producto;
                $total_valor += $item->cantidad * $item->precio;
            @endphp
            <tr id="tr_ingreso_{{ $item->id_ingreso_bodega }}">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ substr($item->fecha_registro, 0, 16) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $producto->nombre }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0" readonly
                        id="cantidad_ingreso_{{ $item->id_ingreso_bodega }}" value="{{ $item->cantidad }}">
                    <span class="hidden">
                        {{ $item->cantidad }}
                    </span>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0" readonly
                        id="precio_ingreso_{{ $item->id_ingreso_bodega }}" value="{{ $item->precio }}">
                    <span class="hidden">
                        {{ $item->precio }}
                    </span>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    ${{ number_format($item->cantidad * $item->precio, 2) }}
                </th>
            </tr>
        @endforeach
    </tbody>
    <tr class="tr_fija_bottom_0">
        <th class="padding_lateral_5 bg-yura_dark" colspan="4">
            TOTALES
        </th>
        <th class="text-center bg-yura_dark">
            ${{ number_format($total_valor, 2) }}
        </th>
    </tr>
</table>

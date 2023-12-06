<table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr id="tr_fija_top_0">
        <th class="padding_lateral_5 bg-yura_dark" style="width: 230px">
            FECHA y HORA
        </th>
        <th class="padding_lateral_5 bg-yura_dark">
            PRODUCTO
        </th>
        <th class="text-center bg-yura_dark" style="width: 90px">
            CANTIDAD
        </th>
        <th class="text-center bg-yura_dark" style="width: 90px">
            PRECIO
        </th>
        <th class="text-center bg-yura_dark" style="width: 90px">
            VALOR
        </th>
    </tr>
    @foreach ($ingresos as $item)
        @php
            $producto = $item->producto;
        @endphp
        <tr id="tr_ingreso_{{ $item->id_ingreso_bodega }}">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ convertDateTimeToText($item->fecha_registro) }}
            </th>
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $producto->nombre }}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" style="width: 100%" class="text-center" required min="0" readonly
                    id="cantidad_ingreso_{{ $item->id_ingreso_bodega }}" value="{{ $item->cantidad }}">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" style="width: 100%" class="text-center" required min="0" readonly
                    id="precio_ingreso_{{ $item->id_ingreso_bodega }}" value="{{ $item->precio }}">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                ${{ number_format($item->cantidad * $item->precio, 2) }}
            </th>
        </tr>
    @endforeach
</table>

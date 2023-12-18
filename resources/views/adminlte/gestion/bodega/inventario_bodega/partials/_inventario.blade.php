<table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_inventario">
    <thead>
        <tr id="tr_fija_top_0">
            <th class="padding_lateral_5 bg-yura_dark">
                FECHA y HORA
            </th>
            <th class="padding_lateral_5 bg-yura_dark">
                PRODUCTO
            </th>
            <th class="text-center bg-yura_dark" style="width: 90px">
                INGRESO
            </th>
            <th class="text-center bg-yura_dark" style="width: 90px">
                DISPONIBLES
            </th>
            <th class="text-center bg-yura_dark" style="width: 90px">
                PRECIO
            </th>
            <th class="text-center bg-yura_dark" style="width: 90px">
                VALOR INICIAL
            </th>
            <th class="text-center bg-yura_dark" style="width: 90px">
                VALOR ACTUAL
            </th>
            <th class="text-center bg-yura_dark" style="width: 60px">
            </th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_inicial = 0;
            $total_actual = 0;
        @endphp
        @foreach ($inventarios as $item)
            @php
                $producto = $item->producto;
                $total_inicial += $item->cantidad * $item->precio;
                $total_actual += $item->disponibles * $item->precio;
            @endphp
            <tr id="tr_inventario_{{ $item->id_inventario_bodega }}">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ substr($item->fecha_registro, 0, 16) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $producto->nombre }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="cantidad_{{ $item->id_inventario_bodega }}" value="{{ $item->cantidad }}">
                    <span class="hidden">
                        {{ $item->cantidad }}
                    </span>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="disponibles_{{ $item->id_inventario_bodega }}" value="{{ $item->disponibles }}">
                    <span class="hidden">
                        {{ $item->disponibles }}
                    </span>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="precio_{{ $item->id_inventario_bodega }}" value="{{ $item->precio }}">
                    <span class="hidden">
                        {{ $item->precio }}
                    </span>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    ${{ number_format($item->cantidad * $item->precio, 2) }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    ${{ number_format($item->disponibles * $item->precio, 2) }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-yura_warning"
                            onclick="update_inventario('{{ $item->id_inventario_bodega }}')">
                            <i class="fa fa-fw fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-yura_danger"
                            onclick="delete_inventario('{{ $item->id_inventario_bodega }}')">
                            <i class="fa fa-fw fa-trash"></i>
                        </button>
                    </div>
                </th>
            </tr>
        @endforeach
    </tbody>
    <tr class="tr_fija_bottom_0">
        <th class="padding_lateral_5 bg-yura_dark" colspan="5">
            TOTALES
        </th>
        <th class="text-center bg-yura_dark">
            ${{ number_format($total_inicial, 2) }}
        </th>
        <th class="text-center bg-yura_dark">
            ${{ number_format($total_actual, 2) }}
        </th>
        <th class="text-center bg-yura_dark">
        </th>
    </tr>
</table>

<table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_inventario">
    <thead>
        <tr id="tr_fija_top_0">
            <th class="padding_lateral_5 bg-yura_dark" style="width: 230px">
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
        @foreach ($inventarios as $item)
            @php
                $producto = $item->producto;
            @endphp
            <tr id="tr_inventario_{{ $item->id_inventario_bodega }}">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ convertDateTimeToText($item->fecha_registro) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $producto->nombre }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="cantidad_{{ $item->id_inventario_bodega }}" value="{{ $item->cantidad }}">
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="disponibles_{{ $item->id_inventario_bodega }}" value="{{ $item->disponibles }}">
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="precio_{{ $item->id_inventario_bodega }}" value="{{ $item->precio }}">
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
</table>

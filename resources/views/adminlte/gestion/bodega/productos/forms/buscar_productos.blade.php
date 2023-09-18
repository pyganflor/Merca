<div style="overflow-y: scroll; overflow-x: scroll; max-height: 700px;">
    <table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr class="tr_fija_top_0">
            <th class="padding_lateral_5 th_yura_green" style="width: 80px">
                CODIGO
            </th>
            <th class="padding_lateral_5 th_yura_green">
                CATEGORIA
            </th>
            <th class="padding_lateral_5 th_yura_green">
                NOMBRE
            </th>
            <th class="padding_lateral_5 th_yura_green" style="width: 60px">
                UNIDADES
            </th>
        </tr>
        @foreach ($listado as $pos => $item)
            @php
                $categoria = $item->categoria_producto;
            @endphp
            <tr id="tr_producto_{{ $item->id_producto }}" class="{{ $item->estado == 0 ? 'error' : '' }}">
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="hidden" class="productos_listados" value="{{ $item->id_producto }}">
                    <input type="text" id="codigo_producto_{{ $item->id_producto }}" value="{{ $item->codigo }}"
                        style="width: 100%" class="text-center">
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $categoria->nombre }}
                    <input type="hidden" readonly id="categoria_producto_{{ $item->id_producto }}" style="width: 100%"
                        class="text-center" value="{{ $categoria->nombre }}">
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item->nombre }}
                    <input type="hidden" readonly id="nombre_producto_{{ $item->id_producto }}" style="width: 100%"
                        class="text-center" value="{{ $item->nombre }}">
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="cantidad_{{ $item->id_producto }}">
                </th>
            </tr>
        @endforeach
    </table>
</div>

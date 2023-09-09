<div style="overflow-y: scroll; overflow-x: scroll; height: 500px;">
    <table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr class="tr_fija_top_0">
            <th class="text-center th_yura_green" style="width: 60px">
                IMAGEN
            </th>
            <th class="text-center th_yura_green" style="width: 100px">
                CATEGORIA
            </th>
            <th class="text-center th_yura_green">
                CODIGO
            </th>
            <th class="text-center th_yura_green">
                NOMBRE
            </th>
            <th class="text-center th_yura_green" style="width: 60px">
                UM
            </th>
            <th class="text-center th_yura_green" style="width: 120px">
                Unidades Fisicas
            </th>
        </tr>
        @foreach ($listado as $item)
            @php
                $url_imagen = 'images\productos\*' . $item->imagen;
                $url_imagen = str_replace('*', '', $url_imagen);
            @endphp
            <tr id="tr_producto_{{ $item->id_producto }}" class="{{ $item->estado == 0 ? 'error' : '' }}"
                onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    <img src="{{ url($url_imagen) }}" alt="..."
                        class="img-fluid img-thumbnail mouse-hand imagen_{{ $item->id_producto }}"
                        style="border-radius: 16px; width: 60px"
                        onclick="$('.imagen_{{ $item->id_producto }}').toggleClass('hidden')">
                </th>
                <th class="text-center padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item->categoria_producto->nombre }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item->codigo }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item->nombre }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item->unidad_medida }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item->disponibles != floor($item->disponibles) ? number_format($item->disponibles, 2) : number_format($item->disponibles) }}
                </th>
            </tr>
        @endforeach
    </table>
</div>

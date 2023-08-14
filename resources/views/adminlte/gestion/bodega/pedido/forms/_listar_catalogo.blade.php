    <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr class="tr_fija_top_0">
            <th class="text-center th_yura_green" style="width: 80px">
                IMAGEN
            </th>
            <th class="text-center th_yura_green">
                PRODUCTO
            </th>
            <th class="text-center th_yura_green" style="width: 60px">
                PRECIO
            </th>
            <th class="text-center th_yura_green" style="width: 110px">
                CANTIDAD
            </th>
        </tr>
        @foreach ($listado as $item)
            @php
                $url_imagen = 'images\productos\*' . $item->imagen;
                $url_imagen = str_replace('*', '', $url_imagen);
            @endphp
            <tr>
                <td style="border-color: #9d9d9d">
                    <img src="{{ url($url_imagen) }}" alt="..." class="img-fluid img-thumbnail"
                        style="border-radius: 16px; width: 100px; height: auto;" data-url="{{ url($url_imagen) }}"
                        id="imagen_catalogo_{{ $item->id_producto }}">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <span id="span_nombre_producto_{{ $item->id_producto }}" data-nombre="{{ $item->nombre }}">
                        <b>{{ $item->nombre }}</b>
                    </span>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <b>${{ number_format($item->precio_venta, 2) }}</b><sup>{{ $item->tiene_iva ? '+IVA' : '' }}</sup>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <span class="text-left span_descriptivo_{{ $item->id_producto }}">
                        <div class="btn-group" style="margin-top: 0">
                            <button type="button" class="btn btn-sm btn-yura_default"
                                onclick="quitar_producto('{{ $item->id_producto }}')">
                                <i class="fa fa-fw fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-yura_dark"
                                data-precio_venta="{{ $item->precio_venta }}" data-iva="{{ $item->tiene_iva }}"
                                id="btn_catalogo_{{ $item->id_producto }}">
                                0
                            </button>
                            <button type="button" class="btn btn-sm btn-yura_default"
                                onclick="agregar_producto('{{ $item->id_producto }}')">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                    </span>
                </td>
            </tr>
            <script>
                selected = $('#span_contador_selected_{{ $item->id_producto }}');
                if (selected.length > 0) {
                    valor = selected.html();
                    $('#btn_catalogo_{{ $item->id_producto }}').html(valor);
                }
            </script>
        @endforeach
    </table>

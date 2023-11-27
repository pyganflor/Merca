<div class="box-group" id="accordion_cat_{{ $item['categoria']->id_categoria_producto }}">
    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
    <div class="panel box box-success" style="margin-bottom: 0">
        <div class="box-header with-border">
            <h4 class="box-title">
                <a data-toggle="collapse" data-parent="#accordion_cat_{{ $item['categoria']->id_categoria_producto }}"
                    href="#collapse_combos_cat_{{ $item['categoria']->id_categoria_producto }}" aria-expanded="false"
                    class="collapsed text-color_yura">
                    Combos de <b>{{ $item['categoria']->nombre }}</b>
                </a>
            </h4>
            <span class="pull-right badge">
                <b style="font-size: 1.1em">{{ count($item['combos']) }}</b> combos totales
            </span>
        </div>
        <div id="collapse_combos_cat_{{ $item['categoria']->id_categoria_producto }}" class="panel-collapse collapse"
            aria-expanded="false" style="height: 0px;">
            <div class="box-body">
                <div class="row">
                    @foreach ($item['combos'] as $pos_c => $combo)
                        @php
                            $url_imagen = 'images\productos\*' . $combo->imagen;
                            $url_imagen = str_replace('*', '', $url_imagen);
                        @endphp
                        <div class="col-md-3">
                            <img src="{{ url($url_imagen) }}" alt="..."
                                class="img-fluid img-thumbnail imagen_{{ $combo->id_producto }}"
                                style="border-radius: 16px; width: 190px">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="panel box box-success" style="margin-bottom: 0">
        <div class="box-header with-border">
            <h4 class="box-title">
                <a data-toggle="collapse" data-parent="#accordion_cat_{{ $item['categoria']->id_categoria_producto }}"
                    href="#collapse_productos_cat_{{ $item['categoria']->id_categoria_producto }}"
                    class="collapsed text-color_yura" aria-expanded="false">
                    Productos de <b>{{ $item['categoria']->nombre }}</b>
                </a>
            </h4>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <b style="font-size: 1.1em">{{ count($item['productos']) }}</b> productos totales
                </button>
                <input type="text" class="text-center"
                    onkeydown="filtrar_nombre_prod('{{ $item['categoria']->id_categoria_producto }}')"
                    id="filtro_nombre_prod_{{ $item['categoria']->id_categoria_producto }}" placeholder="Busqueda">
                <div class="btn-group">
                    <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-filter"></i> Filtrar
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="javascript:void(0)"
                                onclick="ordenar_menor_precio('{{ $item['categoria']->id_categoria_producto }}')">
                                Ordenar por <b>Menor Precio</b>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)"
                                onclick="ordenar_mayor_precio('{{ $item['categoria']->id_categoria_producto }}')">
                                Ordenar por <b>Mayor Precio</b>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)"
                                onclick="ordenar_menor_nombre('{{ $item['categoria']->id_categoria_producto }}')">
                                Ordenar por <b>Nombre A-Z</b>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)"
                                onclick="ordenar_mayor_nombre('{{ $item['categoria']->id_categoria_producto }}')">
                                Ordenar por <b>Nombre Z-A</b>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="collapse_productos_cat_{{ $item['categoria']->id_categoria_producto }}"
            class="panel-collapse collapse" aria-expanded="false">
            <div class="box-body" style="overflow-x: scroll; overflow-y: scroll; max-height: 700px">
                <table>
                    <tr id="tr_productos_{{ $item['categoria']->id_categoria_producto }}">
                        @foreach ($item['productos'] as $pos_c => $producto)
                            @php
                                $url_imagen = 'images\productos\*' . $producto->imagen;
                                $url_imagen = str_replace('*', '', $url_imagen);
                            @endphp
                            <td class="padding_lateral_20 text-center td_productos_{{ $item['categoria']->id_categoria_producto }}"
                                style="width: 150px; vertical-align: top" data-precio="{{ $producto->precio_venta }}"
                                data-nombre="{{ $producto->nombre }}">
                                <div style="width: 150px" class="text-center">
                                    <img src="{{ url($url_imagen) }}" alt="..."
                                        class="img-fluid img-thumbnail imagen_{{ $producto->id_producto }} sombra_pequeña"
                                        style="border-radius: 16px; max-width: 150px; max-height: 150px">
                                </div>
                                <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px">
                                    {{ $producto->nombre }}
                                </legend>
                                <b>
                                    ${{ $producto->precio_venta }}
                                    @if ($producto->tiene_iva == 1)
                                        <sup><em>incluye IVA</em></sup>
                                    @endif
                                </b>
                                <div class="input-group">
                                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                        <i class="fa fa-fw fa-minus"></i>
                                    </span>
                                    <input type="number" id="input_catalogo_prod_{{ $producto->id_producto }}"
                                        style="width: 100%" class="text-center form-control input_cantidad">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-yura_dark" onclick="listar_reporte()">
                                            <i class="fa fa-fw fa-plus"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .input_cantidad::-webkit-inner-spin-button,
    .input_cantidad::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .input_cantidad {
        -moz-appearance: textfield;
    }
</style>

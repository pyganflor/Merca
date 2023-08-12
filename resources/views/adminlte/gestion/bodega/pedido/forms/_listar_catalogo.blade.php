<div style="padding-left: 15px; padding-right: 15px">
    <div class="row">
        @foreach ($listado as $item)
            @php
                $url_imagen = 'images\productos\*' . $item->imagen;
                $url_imagen = str_replace('*', '', $url_imagen);
            @endphp
            <div class="col-md-1 mouse-hand text-center padding_lateral_5" style="position: relative; margin-top: 10px"
                onmouseover="$('.span_descriptivo_{{ $item->id_producto }}').removeClass('hidden')"
                onmouseleave="$('.span_descriptivo_{{ $item->id_producto }}').addClass('hidden')">
                <div style="position: relative">
                    <span
                        class="text-left span_descriptivo_{{ $item->id_producto }} span_descriptivo sombra_pequeña hidden"
                        style="background-image: linear-gradient(to right, #00B388 ,#7effdf8a)">
                        <div class="btn-group" style="margin-top: 0">
                            <button type="button" class="btn btn-xs btn-yura_default"
                                onclick="quitar_producto('{{ $item->id_producto }}')">
                                <i class="fa fa-fw fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-yura_dark"
                                data-precio_venta="{{ $item->precio_venta }}" data-iva="{{ $item->tiene_iva }}"
                                id="btn_catalogo_{{ $item->id_producto }}">
                                0
                            </button>
                            <button type="button" class="btn btn-xs btn-yura_default"
                                onclick="agregar_producto('{{ $item->id_producto }}')">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                    </span>
                    <img src="{{ url($url_imagen) }}" alt="..." class="img-fluid img-thumbnail"
                        style="border-radius: 16px; width: 100px; height: auto;" data-url="{{ url($url_imagen) }}"
                        id="imagen_catalogo_{{ $item->id_producto }}"
                        onclick="$('.imagen_{{ $item->id_producto }}').toggleClass('hidden')">
                    <span class="text-right span_contador hidden" id="span_contador_{{ $item->id_producto }}"
                        style="background-image: linear-gradient(to right, #00B388 ,#7effdf8a)">
                        0
                    </span>
                </div>
                <span id="span_nombre_producto_{{ $item->id_producto }}" data-nombre="{{ $item->nombre }}">
                    <b>{{ $item->nombre }}</b>
                </span>
            </div>
            <script>
                selected = $('#span_contador_selected_{{ $item->id_producto }}');
                if (selected.length > 0) {
                    valor = selected.html();
                    $('#btn_catalogo_{{ $item->id_producto }}').html(valor);
                    $('#span_contador_{{ $item->id_producto }}').html(valor);
                    $('#span_contador_{{ $item->id_producto }}').removeClass('hidden');
                }
            </script>
        @endforeach
    </div>
</div>

<style>
    .span_descriptivo {
        position: absolute;
        top: 0;
        padding: 5px;
        color: #242424;
        font-size: 0.9em;
        font-weight: bold;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        width: 100%;
    }

    .span_contador {
        position: absolute;
        bottom: 0;
        right: 0;
        padding: 8px;
        color: #242424;
        font-size: 0.9em;
        font-weight: bold;
        border-bottom-right-radius: 16px;
        border-top-left-radius: 16px;
        height: 30px;
    }
</style>

<script>
    function agregar_producto(prod) {
        valor = parseInt($('#btn_catalogo_' + prod).html());
        valor++;
        $('#btn_catalogo_' + prod).html(valor);
        $('#span_contador_' + prod).html(valor);
        $('#span_contador_' + prod).removeClass('hidden');

        if ($('#span_contador_selected_' + prod).length == 0) {
            url_imagen = $('#imagen_catalogo_' + prod).attr('data-url');
            nombre_producto = $('#span_nombre_producto_' + prod).attr('data-nombre');
            precio_venta = $('#btn_catalogo_' + prod).attr('data-precio_venta');
            iva = $('#btn_catalogo_' + prod).attr('data-iva');
            $('#row_contenido_pedido').append(
                '<div class="col-md-1 mouse-hand text-center padding_lateral_5" style="position: relative; margin-top: 10px" id="div_col_selected_' +
                prod + '">' +
                '<div style="position: relative">' +
                '<span class="text-left span_descriptivo sombra_pequeña"' +
                'style="background-image: linear-gradient(to right, #00B388 ,#7effdf8a)">' +
                '<div class="btn-group" style="margin-top: 0">' +
                '<button type="button" class="btn btn-xs btn-yura_default" ' +
                'onclick="quitar_producto(' + prod + ')">' +
                '<i class="fa fa-fw fa-minus"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-xs btn-yura_dark" ' +
                'id="btn_catalogo_selected_' + prod + '">' +
                valor +
                '</button>' +
                '<button type="button" class="btn btn-xs btn-yura_default" ' +
                'onclick="agregar_producto(' + prod + ')">' +
                '<i class="fa fa-fw fa-plus"></i>' +
                '</button>' +
                '</div>' +
                '</span>' +
                '<img src="' + url_imagen +
                '" class="img-fluid img-thumbnail" style="border-radius: 16px; width: 100px; height: auto;">' +
                '<span class="text-right span_contador span_contador_selected" id="span_contador_selected_' + prod +
                '" data-precio_venta="' + precio_venta + '" data-iva="' + iva + '"' +
                'style="background-image: linear-gradient(to right, #00B388 ,#7effdf8a)" data-id_producto="' +
                prod + '">' +
                valor +
                '</span>' +
                '</div>' +
                '<b>' + nombre_producto + '</b>' +
                '</div>');
        } else {
            $('#span_contador_selected_' + prod).html(valor);
            $('#btn_catalogo_selected_' + prod).html(valor);
        }
        calcular_totales_pedido();
    }

    function quitar_producto(prod) {
        valor = parseInt($('#btn_catalogo_' + prod).html());
        valor--;
        if (valor > 0) {
            $('#btn_catalogo_' + prod).html(valor);
            $('#span_contador_' + prod).html(valor);
            $('#btn_catalogo_selected_' + prod).html(valor);
            $('#span_contador_selected_' + prod).html(valor);
        } else { // quitar seleccion del producto
            $('#btn_catalogo_' + prod).html(0);
            $('#btn_catalogo_selected_' + prod).html(0);
            $('#span_contador_' + prod).html(0);
            $('#span_contador_selected_' + prod).html(0);
            $('#span_contador_' + prod).addClass('hidden');
            $('#span_contador_selected_' + prod).addClass('hidden');
            $('#div_col_selected_' + prod).remove();
        }
        calcular_totales_pedido();
    }

    function calcular_totales_pedido() {
        monto_total = 0;
        span_contador_selected = $('.span_contador_selected');
        for (i = 0; i < span_contador_selected.length; i++) {
            id_span = span_contador_selected[i].id;
            cantidad = parseInt($('#' + id_span).html());
            precio_venta = parseFloat($('#' + id_span).attr('data-precio_venta'));
            iva = $('#' + id_span).attr('data-iva');
            precio_prod = cantidad * precio_venta;
            if (iva == 1) {
                precio_prod += (12 * precio_prod) / 100;
            }
            monto_total += precio_prod;
        }
        monto_total = Math.round(monto_total * 100) / 100;
        $('#span_total_monto_pedido').html('$' + monto_total);
    }
</script>

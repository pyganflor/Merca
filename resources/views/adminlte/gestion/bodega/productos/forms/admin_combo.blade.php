<legend class="text-center" style="font-size: 1em; margin-bottom: 5px">
    Productos del combo: "<strong>{{ $combo->nombre }}</strong>"
</legend>
<input type="hidden" id="id_combo_seleccionado" value="{{ $combo->id_producto }}">
<table style="width: 100%;">
    <tr>
        <td id="listado_productos" style="vertical-align: top; width: 45%">
            <table style="width: 100%">
                <tr>
                    <td class="text-center padding_lateral_5" style="border-color: #9d9d9d" id="td_cargar_longitudes">
                        <div class="input-group">
                            <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                Categorias
                            </span>
                            <select id="combo_categoria_filtro" class="form-control" style="width: 100%"
                                onchange="buscar_productos()">
                                <option value="T">Todas las Categorias</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id_categoria_producto }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-yura_dark" onclick="buscar_productos()">
                                    <i class="fa fa-fw fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </td>
                </tr>
            </table>

            <div id="div_listado_productos" style="margin-top: 5px">
            </div>
        </td>

        <td>
            <button type="button" class="btn btn-block btn-yura_dark" onclick="agregar_productos()">
                <i class="fa fa-fw fa-arrow-right"></i> Agregar
            </button>
            <button type="button" class="btn btn-block btn-yura_primary" onclick="store_agregar_productos()">
                <i class="fa fa-fw fa-save"></i> Grabar
            </button>
        </td>

        <td id="listado_seleccionados" style="vertical-align: top; width: 45%">
            <div style="overflow-y: scroll; overflow-x: scroll; max-height: 500px;">
                <table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d"
                    id="table_productos_seleccionados">
                    <tr class="tr_fija_top_0">
                        <th class="text-center th_yura_green">
                            CODIGO
                        </th>
                        <th class="text-center th_yura_green">
                            NOMBRE
                        </th>
                        <th class="text-center th_yura_green" style="width: 60px">
                            UNIDADES
                        </th>
                        <th class="text-center th_yura_green" style="width: 80px">
                        </th>
                    </tr>
                    @php
                        $pos = 0;
                    @endphp
                    @foreach ($combo->detalles_combo as $pos => $item)
                        <tr id="tr_producto_seleccionado_{{ $pos + 1 }}">
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{ $item->item->codigo }}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{ $item->item->nombre }}
                                <input type="hidden" class="cant_producto_seleccionado" value="{{ $pos + 1 }}">
                                <input type="hidden" id="id_producto_seleccionado_{{ $pos + 1 }}"
                                    value="{{ $item->id_item }}">
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                <input type="number" class="text-center" style="width: 100%"
                                    id="cantidad_producto_seleccionado_{{ $pos + 1 }}" value="{{ $item->unidades }}">
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                <button type="button" class="btn btn-xs btn-yura_danger" title="Quitar"
                                    onclick="quitar_producto_seleccionado('{{ $pos + 1 }}')">
                                    <i class="fa fa-fw fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </td>
    </tr>
</table>

<script>
    cant_producto_seleccionado = {{ $pos + 1 }};
    buscar_productos();

    function buscar_productos() {
        datos = {
            categoria: $('#combo_categoria_filtro').val(),
        };
        get_jquery('{{ url('bodega_productos/buscar_productos') }}', datos, function(retorno) {
            $('#div_listado_productos').html(retorno);
        }, 'div_listado_productos');
    }

    function agregar_productos() {
        productos_listados = $('.productos_listados');
        for (i = 0; i < productos_listados.length; i++) {
            id = productos_listados[i].value;
            if ($('#cantidad_' + id).val() > 0) {
                cant_producto_seleccionado++;
                nombre = $('#nombre_producto_' + id).val();
                codigo = $('#codigo_producto_' + id).val();
                cantidad = $('#cantidad_' + id).val();
                $('#table_productos_seleccionados').append('<tr id="tr_producto_seleccionado_' +
                    cant_producto_seleccionado + '">' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    codigo +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    nombre +
                    '<input type="hidden" class="cant_producto_seleccionado" value="' + cant_producto_seleccionado +
                    '">' +
                    '<input type="hidden" id="id_producto_seleccionado_' + cant_producto_seleccionado +
                    '" value="' + id + '">' +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="number" class="text-center" style="width: 100%" id="cantidad_producto_seleccionado_' +
                    cant_producto_seleccionado + '" value="' + cantidad + '">' +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<button type="button" class="btn btn-xs btn-yura_danger" title="Quitar" onclick="quitar_producto_seleccionado(' +
                    cant_producto_seleccionado + ')">' +
                    '<i class="fa fa-fw fa-trash"></i>' +
                    '</button>' +
                    '</td>' +
                    '</tr>');
            }
        }
    }

    function quitar_producto_seleccionado(cant_producto_seleccionado) {
        $('#tr_producto_seleccionado_' + cant_producto_seleccionado).remove();
    }

    function store_agregar_productos() {
        cant_producto_seleccionado = $('.cant_producto_seleccionado');
        data = [];
        for (i = 0; i < cant_producto_seleccionado.length; i++) {
            pos = cant_producto_seleccionado[i].value;
            unidades = $('#cantidad_producto_seleccionado_' + pos).val();
            id_item = $('#id_producto_seleccionado_' + pos).val();
            id_combo = $('#id_combo_seleccionado').val();
            if (unidades > 0)
                data.push({
                    id_item: id_item,
                    unidades: unidades,
                })
        }
        if (data.length > 0) {
            datos = {
                _token: '{{ csrf_token() }}',
                id_combo: id_combo,
                data: JSON.stringify(data)
            };
            post_jquery_m('{{ url('bodega_productos/store_agregar_productos') }}', datos, function(retorno) {
                cerrar_modals();
                admin_combo(id_combo);
            });
        }
    }
</script>

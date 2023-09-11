<div style="overflow-x: scroll">
    <table style="width: 100%">
        <tr>
            <td class="text-center">
                <legend style="font-size: 1em; margin-bottom: 2px">
                    Listado de Productos
                </legend>
            </td>
            <td class="text-center">
                <legend style="font-size: 1em; margin-bottom: 2px">
                    Opciones
                </legend>
            </td>
            <td class="text-center">
                <div class="input-group" style="font-size: 1em; margin-bottom: 2px">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        Productos a Ingresar
                    </span>
                    <input type="date" id="fecha_ingreso" style="width: 100%; height: 28px;" class="text-center"
                        value="{{ hoy() }}" max="{{ hoy() }}">
                </div>
            </td>
        </tr>
        <tr>
            <td style="min-width: 350px; width: 550px; vertical-align: top;">
                <div style="overflow-y: scroll; max-height: 600px;">
                    <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d"
                        id="table_listado_add_ingresos">
                        <thead>
                            <tr class="tr_fija_top_0">
                                <th class="text-center th_yura_green" style="width: 60px">
                                    IMAGEN
                                </th>
                                <th class="text-center th_yura_green">
                                    CODIGO
                                </th>
                                <th class="text-center th_yura_green">
                                    NOMBRE
                                </th>
                                <th class="text-center th_yura_green" style="width: 60px !important">
                                    UNIDADES
                                </th>
                                <th class="text-center th_yura_green" style="width: 60px !important">
                                    PRECIO
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($listado as $item)
                                @php
                                    $url_imagen = 'images\productos\*' . $item->imagen;
                                    $url_imagen = str_replace('*', '', $url_imagen);
                                @endphp
                                <tr>
                                    <th class="padding_lateral_5" style="border-color: #9d9d9d">
                                        <img src="{{ url($url_imagen) }}" alt="..."
                                            class="img-fluid img-thumbnail mouse-hand imagen_{{ $item->id_producto }}"
                                            style="border-radius: 16px; width: 60px"
                                            onclick="$('.imagen_{{ $item->id_producto }}').toggleClass('hidden')">
                                    </th>
                                    <th class="padding_lateral_5" style="border-color: #9d9d9d">
                                        {{ $item->codigo }}
                                        <input type="hidden" id="codigo_{{ $item->id_producto }}"
                                            value="{{ $item->codigo }}">
                                        {{--<select id="proveedor_{{ $item->id_producto }}" style="display: none">
                                            <option value="">Ninguno</option>
                                            @foreach ($proveedores as $prov)
                                                <option value="{{ $prov->id_proveedor }}"
                                                    {{ $prov->id_proveedor == $item->id_proveedor ? 'selected' : '' }}>
                                                    {{ $prov->nombre }}
                                                </option>
                                            @endforeach
                                        </select>--}}
                                        <input type="hidden" class="tr_productos" value="{{ $item->id_producto }}">
                                    </th>
                                    <th class="padding_lateral_5" style="border-color: #9d9d9d">
                                        {{ $item->nombre }}
                                        <input type="hidden" id="nombre_{{ $item->id_producto }}"
                                            value="{{ $item->nombre }}">
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        <input type="number" style="width: 100%" class="text-center"
                                            id="unidades_{{ $item->id_producto }}" min="0">
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        <input type="number" style="width: 100%" class="text-center"
                                            id="precio_{{ $item->id_producto }}" min="0"
                                            value="{{ $item->precio }}">
                                    </th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
            <td class="text-center padding_lateral_5" style="vertical-align: top">
                <button type="button" class="btn btn-block btn-yura_dark" onclick="agregar_productos()">
                    <i class="fa fa-fw fa-arrow-right"></i> Agregar
                </button>
                <button type="button" class="btn btn-block btn-yura_primary" onclick="store_ingresos()">
                    <i class="fa fa-fw fa-save"></i> Grabar
                </button>
            </td>
            <td style="min-width: 350px; width: 550px; vertical-align: top; overflow-y: scroll">
                <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_add_ingresos">
                    <tr class="tr_fija_top_0">
                        <th class="text-center bg-yura_dark">
                            CODIGO
                        </th>
                        <th class="text-center bg-yura_dark">
                            NOMBRE
                        </th>
                        <th class="text-center bg-yura_dark" style="width: 60px !important">
                            UNIDADES
                        </th>
                        <th class="text-center bg-yura_dark" style="width: 60px !important">
                            PRECIO
                        </th>
                        <th class="text-center bg-yura_dark" style="width: 60px !important">
                        </th>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<script>
    estructura_tabla('table_listado_add_ingresos');

    function agregar_productos() {
        tr_productos = $('.tr_productos');
        for (i = 0; i < tr_productos.length; i++) {
            id_prod = tr_productos[i].value;
            unidades = $('#unidades_' + id_prod).val();
            precio = $('#precio_' + id_prod).val();
            if (unidades != '' && unidades > 0) {
                codigo = $('#codigo_' + id_prod).val();
                nombre = $('#nombre_' + id_prod).val();
                if ($('#unidades_seleccionado_' + id_prod).length == 0)
                    $('#table_add_ingresos').append('<tr id="tr_add_ingreso_' + id_prod + '">' +
                        '<th class="padding_lateral_5" style="border-color: #9d9d9d">' +
                        codigo +
                        '<input type="hidden" class="id_producto_seleccionado" value="' + id_prod + '">' +
                        '</th>' +
                        '<th class="padding_lateral_5" style="border-color: #9d9d9d">' +
                        nombre +
                        '</th>' +
                        '<td class="text-center" style="border-color: #9d9d9d">' +
                        '<input type="number" style="width: 100%" class="text-center"' +
                        'id="unidades_seleccionado_' + id_prod + '" min="0" value="' + unidades + '">' +
                        '</td>' +
                        '<td class="text-center" style="border-color: #9d9d9d">' +
                        '<input type="number" style="width: 100%" class="text-center"' +
                        'id="precio_seleccionado_' + id_prod + '" min="0" value="' + precio + '">' +
                        '</td>' +
                        '<td class="text-center" style="border-color: #9d9d9d">' +
                        '<button type="button" class="btn btn-xs btn-yura_danger" onclick="quitar_fila(' + id_prod +
                        ')">' +
                        '<i class="fa fa-fw fa-trash"></i>' +
                        '</button>' +
                        '</td>' +
                        '</tr>');

            }
        }
    }

    function quitar_fila(id_prod) {
        $('#tr_add_ingreso_' + id_prod).remove();
    }

    function store_ingresos() {
        id_producto_seleccionado = $('.id_producto_seleccionado');
        data = [];
        for (i = 0; i < id_producto_seleccionado.length; i++) {
            id_prod = id_producto_seleccionado[i].value;
            unidades = $('#unidades_seleccionado_' + id_prod).val();
            if (unidades > 0) {
                precio = $('#precio_seleccionado_' + id_prod).val();
                data.push({
                    id_prod: id_prod,
                    unidades: unidades,
                    precio: precio,
                })
            }
        }

        if (data.length > 0) {
            mensaje = {
                title: '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de Confirmacion',
                mensaje: '<div class="alert alert-info text-center" style="font-size: 16px">¿Está seguro de <b>GRABAR</b> los ingresos?</div>',
            };
            modal_quest('modal_store_ingresos', mensaje['mensaje'], mensaje['title'], true, false,
                '{{ isPC() ? '50%' : '' }}',
                function() {
                    datos = {
                        _token: '{{ csrf_token() }}',
                        data: JSON.stringify(data),
                        fecha: $('#fecha_ingreso').val()
                    };
                    post_jquery_m('{{ url('movimientos_bodega/store_ingresos') }}', datos, function() {
                        cerrar_modals();
                        listar_reporte();
                    });
                });
        }
    }
</script>

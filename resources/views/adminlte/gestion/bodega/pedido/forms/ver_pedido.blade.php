<div style="overflow-x: scroll">
    <table style="width: 100%">
        <tr>
            <td class="text-center" style="border-color: #9d9d9d; min-width: 220px">
                <div class="input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        Fecha
                    </span>
                    <input type="date" id="form_fecha" style="width: 100%" class="text-center form-control"
                        value="{{ $pedido->fecha }}" min="{{ hoy() }}">
                </div>
            </td>
            <td class="text-center" style="border-color: #9d9d9d; min-width: 260px">
                <div class="input-group">
                    <span class="input-group-addon bg-yura_dark">
                        Finca
                    </span>
                    <select id="form_finca" style="width: 100%" class="form-control" onchange="seleccionar_finca()">
                        @foreach ($fincas as $f)
                            <option value="{{ $f->id_empresa }}"
                                {{ $f->id_empresa == $pedido->id_empresa ? 'selected' : '' }}>
                                {{ $f->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="text-center" style="border-color: #9d9d9d; min-width: 290px; width: 50%">
                <div class="input-group">
                    <span class="input-group-addon bg-yura_dark mouse-hand" onclick="seleccionar_finca()">
                        Usuario <i class="fa fa-fw fa-refresh"></i>
                    </span>
                    @if (in_array(session('id_usuario'), [1, 2]))
                        <select id="form_usuario" style="width: 100%" class="form-control input-yura_default">
                            <option value="{{ $pedido->id_usuario }}">
                                {{ $pedido->usuario->nombre_completo }} CI:{{ $pedido->usuario->username }}
                                saldo:${{ $pedido->usuario->saldo }}
                            </option>
                        </select>
                    @else
                        <input type="text" style="width: 100%" class="form-control input-yura_default" readonly
                            value="{{ $pedido->usuario->nombre_completo }}-{{ $pedido->usuario->username }}">
                        <input type="hidden" id="form_usuario" value="{{ $pedido->id_usuario }}">
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>

<ul class="nav nav-pills nav-justified">
    <li class="">
        <a data-toggle="tab" href="#tab-catalogo" aria-expanded="false">
            <i class="fa fa-fw fa-list"></i> Catalogo
        </a>
    </li>
    <li class="active">
        <a data-toggle="tab" href="#tab-contenido_pedido" aria-expanded="true">
            <i class="fa fa-fw fa-shopping-cart"></i> Contenido del Pedido
            <sup><span class="badge" id="span_total_monto_pedido">$0.00</span></sup>
        </a>
    </li>
</ul>
<div class="tab-content" style="margin-top: 2px;">
    <div id="tab-catalogo" class="tab-pane fade">
        <div class="input-group">
            <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                Buscar
            </span>
            <input type="text" id="buscar_catalogo" style="width: 100%" onchange="listar_catalogo()"
                onkeyup="listar_catalogo()" class="text-center form-control" placeholder="Busqueda de productos">
            <span class="input-group-addon bg-yura_dark">
                Categoria
            </span>
            <select id="categoria_catalogo" style="width: 100%" onchange="listar_catalogo()" onkeyup="listar_catalogo()"
                class="form-control input-yura_default">
                <option value="T">Todas</option>
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->id_categoria_producto }}">
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="div_catalogo" style="overflow-x: scroll; overflow-y: scroll; margin-top: 3px; max-height: 550px"></div>
    </div>
    <div id="tab-contenido_pedido" class="tab-pane fade active in"
        style="overflow-x: scroll; overflow-y: scroll; max-height: 550px">
        <table class="table-bordered" style="border: 1px solid #9d9d9d; width: 100%" id="table_contenido_pedido">
            <tr class="tr_fija_top_0">
                <th class="text-center th_yura_green" style="width: 80px">
                    Imagen
                </th>
                <th class="text-center th_yura_green">
                    Producto
                </th>
                <th class="text-center th_yura_green {{ !in_array(session('id_usuario'), [1, 2]) ? 'hidden' : '' }}"
                    style="width: 80px">
                    Precio
                </th>
                <th class="text-center th_yura_green" style="width: 110px">
                    Cantidad
                </th>
            </tr>
            @foreach ($pedido->detalles as $det)
                @php
                    $producto = $det->producto;
                    $url_imagen = 'images\productos\*' . $producto->imagen;
                    $url_imagen = str_replace('*', '', $url_imagen);
                @endphp
                <tr id="tr_producto_selected_{{ $producto->id_producto }}">
                    <td class="text-center" style="border-color: #9d9d9d">
                        <img src="{{ $url_imagen }}" alt="..." class="img-fluid img-thumbnail"
                            style="border-radius: 0px; width: 100%; height: auto;" data-url="{{ $url_imagen }}"
                            id="imagen_catalogo_selected_{{ $det->id_producto }}">
                    </td>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{ $producto->nombre }}
                    </th>
                    <th class="text-center {{ !in_array(session('id_usuario'), [1, 2]) ? 'hidden' : '' }}"
                        style="border-color: #9d9d9d">
                        <input type="number" id="input_precio_producto_selected_{{ $producto->id_producto }}"
                            style="width: 100%" value="{{ $det->precio }}" class="text-center">
                        <label for="tiene_iva_selected_{{ $producto->id_producto }}" class="mouse-hand">IVA</label>
                        <input type="checkbox" id="tiene_iva_selected_{{ $producto->id_producto }}"
                            {{ $det->iva == 1 ? 'checked' : '' }} onchange="calcular_totales_pedido()">
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        <div class="btn-group" style="margin-top: 0">
                            <button type="button" class="btn btn-sm btn-yura_default"
                                onclick="quitar_producto('{{ $producto->id_producto }}')">
                                <i class="fa fa-fw fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-yura_dark span_contador_selected"
                                id="span_contador_selected_{{ $producto->id_producto }}"
                                data-id_producto="{{ $producto->id_producto }}">
                                {{ $det->cantidad }}
                            </button>
                            <button type="button" class="btn btn-sm btn-yura_default"
                                onclick="agregar_producto('{{ $producto->id_producto }}')">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </div>
                    </th>
                </tr>
            @endforeach
        </table>
    </div>
</div>

<div style="overflow-x: scroll">
    <table style="margin-top: 0; width: 100%">
        <tbody>
            <tr>
                <td rowspan="4" style="text-align: right; padding-right: 20px; min-width: 320px">
                    <div class="btn-group">
                        <button type="button" class="btn btn-yura_primary"
                            @if ($pedido->armado == 0 && substr($pedido->fecha, 0, 7) == substr(hoy(), 0, 7)) onclick="update_pedido('{{ $pedido->id_pedido_bodega }}')"@else disabled @endif>
                            <i class="fa fa-fw fa-save"></i> ACTUALIZAR
                        </button>
                        <button type="button" class="btn btn-yura_default"
                            onclick="cerrar_modals(); ver_pedido('{{ $pedido->id_pedido_bodega }}')">
                            <i class="fa fa-fw fa-refresh"></i> Reiniciar
                        </button>
                        @if (in_array(session('id_usuario'), [1, 2]))
                            @if ($pedido->armado == 0)
                                <button type="button" class="btn btn-yura_dark"
                                    onclick="armar_pedido('{{ $pedido->id_pedido_bodega }}')">
                                    <i class="fa fa-fw fa-gift"></i> ARMAR
                                </button>
                            @endif
                            <button type="button" class="btn btn-yura_default"
                                onclick="imprimir_pedido('{{ $pedido->id_pedido_bodega }}')">
                                <i class="fa fa-fw fa-print"></i> IMPRIMIR
                            </button>
                        @endif
                    </div>
                </td>
                <th style="width: 25%; text-align: right; min-width: 120px">
                    Subtotal:
                </th>
                <th id="th_total_subtotal_pedido" style="text-align: right; padding-right: 5px; width: 10%">
                    0
                </th>
            </tr>
            <tr>
                <th style="width: 25%; text-align: right; min-width: 120px">
                    Total IVA:
                </th>
                <th id="th_total_iva_pedido" style="text-align: right; padding-right: 5px; width: 10%">
                    0
                </th>
            </tr>
            <tr>
                <th style="width: 25%; text-align: right; min-width: 120px">
                    MONTO TOTAL:
                    <input type="hidden" id="input_monto_total" value="0">
                </th>
                <th id="th_total_monto_pedido" style="text-align: right; padding-right: 5px; width: 10%">
                    $0
                </th>
            </tr>
        </tbody>
    </table>
</div>

<input type="hidden" id="user_admin" value="{{ in_array(session('id_usuario'), [1, 2]) ? 1 : 0 }}">

<script>
    listar_catalogo();
    calcular_totales_pedido();
    @if (in_array(session('id_usuario'), [1, 2]))
        //seleccionar_finca();
        setTimeout(() => {
            $('#form_usuario').select2({
                dropdownParent: $('#div_modal-modal_ver_pedido')
            })
            $('.select2-selection').css('height', '34px');
            $('.select2-selection').css('border-radius', '0');
        }, 500);
    @endif

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
            iva = parseInt($('#btn_catalogo_' + prod).attr('data-iva'));
            user_admin = $('#user_admin').val();
            $('#table_contenido_pedido').append(
                '<tr id="tr_producto_selected_' + prod + '">' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<img src="' + url_imagen + '" alt="..." class="img-fluid img-thumbnail" ' +
                'style="border-radius: 0px; width: 100%; height: auto;" data-url="' + url_imagen + '" ' +
                'id="imagen_catalogo_selected_' + prod + '">' +
                '</td>' +
                '<th class="text-center" style="border-color: #9d9d9d">' +
                nombre_producto +
                '</th>' +
                '<th class="text-center elemento_precio" style="border-color: #9d9d9d">' +
                '<input type="number" id="input_precio_producto_selected_' + prod + '" ' +
                'style="width: 100%" value="' + precio_venta + '" class="text-center">' +
                '<label for="tiene_iva_selected_' + prod + '" class="mouse-hand">IVA</label>' +
                '<input type="checkbox" id="tiene_iva_selected_' + prod + '">' +
                '</th>' +
                '<th class="text-center" style="border-color: #9d9d9d">' +
                '<div class="btn-group" style="margin-top: 0">' +
                '<button type="button" class="btn btn-sm btn-yura_default" ' +
                'onclick="quitar_producto(' + prod + ')">' +
                '<i class="fa fa-fw fa-minus"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-sm btn-yura_dark span_contador_selected" ' +
                'id="span_contador_selected_' + prod + '" data-id_producto="' + prod +
                '">' +
                valor +
                '</button>' +
                '<button type="button" class="btn btn-sm btn-yura_default" ' +
                'onclick="agregar_producto(' + prod + ')">' +
                '<i class="fa fa-fw fa-plus"></i>' +
                '</button>' +
                '</div>' +
                '</th>' +
                '</tr>');
            if (iva == 1)
                $('#tiene_iva_selected_' + prod).prop('checked', true);
            if (user_admin == 0)
                $('.elemento_precio').addClass('hidden');
        } else {
            $('#span_contador_selected_' + prod).html(valor);
        }
        calcular_totales_pedido();
    }

    function quitar_producto(prod) {
        valor = parseInt($('#btn_catalogo_' + prod).html());
        valor--;
        if (valor > 0) {
            $('#btn_catalogo_' + prod).html(valor);
            $('#span_contador_' + prod).html(valor);
            $('#span_contador_selected_' + prod).html(valor);
        } else { // quitar seleccion del producto
            $('#btn_catalogo_' + prod).html(0);
            $('#span_contador_' + prod).html(0);
            $('#span_contador_selected_' + prod).html(0);
            $('#span_contador_' + prod).addClass('hidden');
            $('#span_contador_selected_' + prod).addClass('hidden');
            $('#tr_producto_selected_' + prod).remove();
        }
        calcular_totales_pedido();
    }

    function listar_catalogo() {
        datos = {
            busqueda: $('#buscar_catalogo').val(),
            categoria: $('#categoria_catalogo').val(),
        }
        get_jquery('{{ url('pedido_bodega/listar_catalogo') }}', datos, function(retorno) {
            $('#div_catalogo').html(retorno)
        }, 'div_catalogo');
    }

    function seleccionar_finca() {
        datos = {
            _token: '{{ csrf_token() }}',
            finca: $('#form_finca').val()
        }
        $('#form_usuario').LoadingOverlay('show');
        $.post('{{ url('pedido_bodega/seleccionar_finca') }}', datos, function(retorno) {
            $('#form_usuario').html(retorno.options_usuarios);
        }, 'json').fail(function(retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function() {
            $('#form_usuario').LoadingOverlay('hide');
        });
    }

    function calcular_totales_pedido() {
        monto_total = 0;
        monto_subtotal = 0;
        monto_total_iva = 0;
        span_contador_selected = $('.span_contador_selected');
        for (i = 0; i < span_contador_selected.length; i++) {
            id_span = span_contador_selected[i].id;
            prod = parseInt($('#' + id_span).attr('data-id_producto'));
            cantidad = parseInt($('#span_contador_selected_' + prod).html());
            precio_venta = $('#input_precio_producto_selected_' + prod).val();
            iva = $('#tiene_iva_selected_' + prod).prop('checked');
            precio_prod = cantidad * precio_venta;
            if (iva == true) {
                monto_subtotal += precio_prod / 1.12;
                monto_total_iva += (precio_prod / 1.12) * 0.12;
            } else {
                monto_subtotal += precio_prod;
            }
            monto_total += precio_prod;
        }
        monto_total = Math.round(monto_total * 100) / 100;
        monto_subtotal = Math.round(monto_subtotal * 100) / 100;
        monto_total_iva = Math.round(monto_total_iva * 100) / 100;
        $('#span_total_monto_pedido').html('$' + monto_total);
        $('#th_total_monto_pedido').html('$' + monto_total);
        $('#th_total_iva_pedido').html('$' + monto_total_iva);
        $('#th_total_subtotal_pedido').html('$' + monto_subtotal);
        $('#input_monto_total').val(monto_total);
    }

    function update_pedido(ped) {
        detalles = [];
        span_contador_selected = $('.span_contador_selected');
        for (i = 0; i < span_contador_selected.length; i++) {
            id_span = span_contador_selected[i].id;
            prod = parseInt($('#' + id_span).attr('data-id_producto'));
            cantidad = parseInt($('#span_contador_selected_' + prod).html());
            precio_venta = $('#input_precio_producto_selected_' + prod).val();
            iva = $('#tiene_iva_selected_' + prod).prop('checked');
            detalles.push({
                producto: prod,
                cantidad: cantidad,
                precio_venta: precio_venta,
                iva: iva,
            });
        }
        datos = {
            _token: '{{ csrf_token() }}',
            ped: ped,
            fecha: $('#form_fecha').val(),
            finca: $('#form_finca').val(),
            usuario: $('#form_usuario').val(),
            monto_total: $('#input_monto_total').val(),
            detalles: JSON.stringify(detalles),
        }
        if (datos['fecha'] != '' && datos['finca'] != '' && datos['usuario'] != '' && detalles.length > 0) {
            post_jquery_m('{{ url('pedido_bodega/update_pedido') }}', datos, function() {
                cerrar_modals();
                listar_reporte();
            });
        } else {
            alerta('<div class="alert alert-warning text-center">Faltan datos necesarios</div>');
        }
    }

    function armar_pedido(ped) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 16px'>¿Desea <b>ARMAR</b> el pedido?</div>" +
            "</div>";

        modal_quest('modal_armar_pedido', texto, 'ARMAR pedido', true, false, '40%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                ped: ped,
            };
            post_jquery_m('pedido_bodega/armar_pedido', datos, function() {
                cerrar_modals();
                listar_reporte();
            });
        })
    }
</script>

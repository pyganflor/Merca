<input type="hidden" id="pedido_selected" value="{{ $pedido->id_pedido }}">
<table width="100%">
    <tr>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Fecha de Entrega
                </div>
                <input type="date" id="edit_fecha" required class="form-control text-center"
                    style="width: 100% !important;" value="{{ $pedido->fecha_pedido }}">
            </div>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark">
                    Cliente
                </div>
                <select id="edit_cliente" class="form-control" style="width: 100%" onchange="seleccionar_cliente()">
                    <option value="">Seleccione</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id_cliente }}"
                            {{ $c->id_cliente == $pedido->id_cliente ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark">
                    Carguera
                </div>
                <select id="edit_agencia" class="form-control input-yura_default input_seleccionar_cliente"
                    style="width: 100%">
                    @foreach ($agencias_cliente as $a)
                        <option value="{{ $a->id_agencia_carga }}"
                            {{ $a->id_agencia_carga == $pedido->id_agencia_carga ? 'selected' : '' }}>
                            {{ $a->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Consignatario
                </div>
                <select id="edit_consignatario" class="form-control input_seleccionar_cliente" style="width: 100%">
                    @foreach ($consignatarios_cliente as $c)
                        <option value="{{ $c->id_consignatario }}"
                            {{ $c->id_consignatario == $pedido->id_consignatario ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark">
                    Marcacion
                </div>
                <input type="text" id="edit_marcacion" required class="form-control text-center"
                    value="{{ $pedido->marcacion }}" style="width: 100% !important;" maxlength="250">
            </div>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark">
                    Exportador
                </div>
                <select id="edit_finca" class="form-control input-yura_default" style="width: 100%">
                    @foreach ($fincas as $f)
                        <option value="{{ $f->id_configuracion_empresa }}">{{ $f->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
</table>

<table style="width:100%">
    <tr>
        <td style="vertical-align: top; padding-right: 5px" id="td_seleccionados">
            <div class="panel panel-success" style="margin-bottom:0px" id="panel_seleccionados">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <div id="titulo_seleccionados">
                        <b> <i class="fa fa-th"></i> CONTENIDO DEL PEDIDO</b>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-yura_default btn-xs dropdown-toggle"
                            data-toggle="dropdown" aria-expanded="false">
                            Acciones <span class="fa fa-caret-down"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left sombra_pequeña">
                            <li>
                                <a href="javascript:void(0)" onclick="add_caja()">
                                    <i class="fa fa-fw fa-gift"></i> Agregar Caja
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel-body" style="height: 500px;" id="body_seleccionados">
                    <div style="overflow-y: scroll; height: 460px">
                        <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
                            <tr class="tr_fija_top_0">
                                <th class="text-center th_yura_green">
                                    <div style="width: 180px">
                                        Cajas
                                    </div>
                                </th>
                                <th class="text-center th_yura_green">
                                    Variedad
                                </th>
                                <th class="text-center th_yura_green">
                                    Longitud
                                </th>
                                <th class="text-center th_yura_green">
                                    Tallos
                                </th>
                                <th class="text-center th_yura_green">
                                    Ramos
                                </th>
                                <th class="text-center th_yura_green" style="width: 80px">
                                    Precio
                                </th>
                                <th class="text-center th_yura_green">
                                </th>
                            </tr>
                            @php
                                $monto_pedido = 0;
                            @endphp
                            @foreach ($pedido->detalles as $pos_d => $det)
                                @php
                                    $caja_frio = $det->caja_frio;
                                @endphp
                                @foreach ($caja_frio->detalles as $pos_i => $item)
                                    @php
                                        $variedad = $item->variedad;
                                        $monto_pedido += $item->precio * $item->ramos * $item->tallos_x_ramo;
                                    @endphp
                                    <tr>
                                        @if ($pos_i == 0)
                                            <th class="text-center" style="border-color: #9d9d9d"
                                                rowspan="{{ count($caja_frio->detalles) }}">
                                                <input type="hidden" class="ids_detalles"
                                                    value="{{ $det->id_detalle_pedido }}">
                                                {{ $caja_frio->nombre }}
                                                @if (count($pedido->detalles) > 1)
                                                    <br>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-yura_danger btn-xs"
                                                            onclick="eliminar_detalle_pedido('{{ $det->id_detalle_pedido }}')">
                                                            <i class="fa fa-fw fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </th>
                                        @endif
                                        <th class="text-center" style="border-color: #9d9d9d">
                                            {{ $item->variedad->nombre }}
                                        </th>
                                        <th class="text-center" style="border-color: #9d9d9d">
                                            {{ $item->longitud }}<sup>cm</sup>
                                        </th>
                                        <th class="text-center" style="border-color: #9d9d9d">
                                            {{ $item->tallos_x_ramo * $item->ramos }}
                                        </th>
                                        <th class="text-center" style="border-color: #9d9d9d">
                                            {{ $item->ramos }} <sup>x{{ $item->tallos_x_ramo }}</sup>
                                        </th>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <input type="number" style="width: 100%" value="{{ $item->precio }}"
                                                class="text-center"
                                                id="precio_det_caja_{{ $item->id_detalle_caja_frio }}"
                                                onchange="update_precio('{{ $item->id_detalle_caja_frio }}', $(this).val())">
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-yura_dark btn-xs dropdown-toggle"
                                                    data-toggle="dropdown">
                                                    <i class="fa fa-fw fa-gears"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right sombra_pequeña"
                                                    role="menu" style="z-index: 10 !important">
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            onclick="cambiar_caja('{{ $item->id_detalle_caja_frio }}')">
                                                            <i class="fa fa-fw fa-pencil"></i> Cambiar a otra Caja
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)"
                                                            onclick="eliminar_detalle('{{ $item->id_detalle_caja_frio }}')">
                                                            <i class="fa fa-fw fa-trash"></i> Quitar de la Caja
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="panel-footer" id="footer_seleccionados">
                    <div class="text-center" style="margin-top: 5px">
                        <div class="btn-group">
                            <button type="button" class="btn btn-yura_primary"
                                onclick="update_pedido('{{ $pedido->id_pedido }}')">
                                <i class="fa fa-fw fa-save"></i> Grabar Pedido
                            </button>
                            <button type="button" class="btn btn-yura_dark">
                                ${{ number_format($monto_pedido, 2) }}
                            </button>
                            <button type="button" class="btn btn-yura_default"
                                onclick="cerrar_modals(); editar_pedido('{{ $pedido->id_pedido }}')">
                                <i class="fa fa-fw fa-refresh"></i> Refrescar Formulario
                            </button>
                            <button type="button" class="btn btn-yura_danger"
                                onclick="eliminar_pedido('{{ $pedido->id_pedido }}')">
                                <i class="fa fa-fw fa-trash"></i> Eliminar Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </td>

        <td style="vertical-align: top; width: 50%; padding-left: 5px" id="td_inventarios">
            <div class="panel panel-success" style="margin-bottom: 0px" id="panel_inventarios">
                <div class="panel-heading"
                    style="display: flex; justify-content: space-between; align-items: center;">
                    <div id="titulo_inventarios">
                        <b> <i class="fa fa-gears"></i> OPCIONES </b>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-yura_default" onclick="modificar_div_inv('left')">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                            {{-- 
                            <button class="btn btn-xs btn-yura_primary" onclick="modificar_div_inv('center')">
                                <i class="fa fa-compress"></i>
                            </button>
                             --}}
                            <button class="btn btn-xs btn-yura_default" onclick="modificar_div_inv('right')">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="panel-body" id="body_inventarios">
                </div>
            </div>
        </td>
    </tr>
</table>

<script>
    setTimeout(() => {
        modificar_div_inv('left');
    }, 500);

    function update_precio(det_caja, precio) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 1.2em'>Esta a punto de <b>CAMBIAR</b> el precio a '" +
            precio + "'</div>";

        modal_quest('modal_update_precio', texto, 'Eliminar pedido', true, false, '40%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                det_caja: det_caja,
                precio: precio,
            };
            post_jquery_m('pedidos/update_precio', datos, function() {}, 'precio_det_caja_' + det_caja);
        })
    }

    function cambiar_caja(det) {
        datos = {
            det: det
        }
        get_jquery('{{ url('pedidos/cambiar_caja') }}', datos, function(retorno) {
            $('#body_inventarios').html(retorno);
            modificar_div_inv('right')
        });
    }

    function eliminar_detalle(det) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 1.5em'>Esta a punto de <b>ELIMINAR</b> la FLOR de la CAJA</div>" +
            "<div class='alert alert-info text-center' style='font-size: 1.5em'>¿Desea devolver el contenido al inventario de cuarto frio?" +
            "<div class='row'>" +
            "<div class='col-md-6'>" +
            "<label class='mouse-hand' for='radio_devolver_detalle0'>No</label>" +
            "<input type='radio' name='radio_devolver_detalle' id='radio_devolver_detalle0' value='0' style='width: 20px; height: 20px'>" +
            "</div>" +
            "<div class='col-md-6'>" +
            "<input type='radio' name='radio_devolver_detalle' id='radio_devolver_detalle1' value='1' style='width: 20px; height: 20px' checked>" +
            "<label class='mouse-hand' for='radio_devolver_detalle1'>Si</label>" +
            "</div>" +
            "</div>" +
            "</div>";

        modal_quest('modal_eliminar_detalle', texto, 'Eliminar flor de la caja', true, false, '50%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                det: det,
                devolver: $('#radio_devolver_detalle1').prop('checked') == true ? 1 : 0,
            };
            post_jquery_m('inventario_cajas/eliminar_detalle', datos, function() {
                cerrar_modals();
                editar_pedido($('#pedido_selected').val());
                listar_reporte();
            });

        })
    }

    function eliminar_detalle_pedido(det_ped) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 1.5em'>Esta a punto de <b>ELIMINAR</b> la Caja</div>" +
            "<div class='alert alert-info text-center' style='font-size: 1.5em'>¿Desea devolver la caja al inventario?" +
            "<div class='row'>" +
            "<div class='col-md-6'>" +
            "<label class='mouse-hand' for='radio_devolver0'>No</label>" +
            "<input type='radio' name='radio_devolver' id='radio_devolver0' value='0' style='width: 20px; height: 20px'>" +
            "</div>" +
            "<div class='col-md-6'>" +
            "<input type='radio' name='radio_devolver' id='radio_devolver1' value='1' style='width: 20px; height: 20px' checked>" +
            "<label class='mouse-hand' for='radio_devolver1'>Si</label>" +
            "</div>" +
            "</div>" +
            "</div>";

        modal_quest('modal_eliminar_pedido', texto, 'Eliminar caja del Pedido', true, false, '50%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                det_ped: det_ped,
                devolver: $('#radio_devolver1').prop('checked') == true ? 1 : 0,
            };
            post_jquery_m('pedidos/eliminar_detalle_pedido', datos, function() {
                cerrar_modals();
                editar_pedido($('#pedido_selected').val());
                listar_reporte();
            });

        })
    }

    function add_caja() {
        datos = {}
        get_jquery('{{ url('pedidos/add_caja') }}', datos, function(retorno) {
            $('#body_inventarios').html(retorno);
            modificar_div_inv('right')
        });
    }

    function update_pedido(ped) {
        if ($('#add_cliente').val() != '') {
            datos = {
                _token: '{{ csrf_token() }}',
                ped: ped,
                fecha: $('#edit_fecha').val(),
                cliente: $('#edit_cliente').val(),
                finca: $('#edit_finca').val(),
                agencia: $('#edit_agencia').val(),
                consignatario: $('#edit_consignatario').val(),
                marcacion: $('#edit_marcacion').val(),
            };
            post_jquery_m('{{ url('pedidos/update_pedido') }}', datos, function() {
                cerrar_modals();
                editar_pedido(ped);
                listar_reporte();
            });
        }
    }
</script>

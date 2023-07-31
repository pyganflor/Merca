<table width="100%">
    <tr>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Fecha de Entrega
                </div>
                <input type="date" id="add_fecha" required class="form-control text-center"
                    style="width: 100% !important;" value="{{ hoy() }}">
            </div>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark">
                    Cliente
                </div>
                <select id="add_cliente" class="form-control" style="width: 100%" onchange="seleccionar_cliente()">
                    <option value="">Seleccione</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id_cliente }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark">
                    Carguera
                </div>
                <select id="add_agencia" class="form-control input-yura_default input_seleccionar_cliente"
                    style="width: 100%">
                    <option value="">Seleccione</option>
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
                <select id="add_consignatario" class="form-control input_seleccionar_cliente" style="width: 100%">
                    <option value="">Seleccione</option>
                </select>
            </div>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark">
                    Marcacion
                </div>
                <input type="text" id="add_marcacion" required class="form-control text-center"
                    style="width: 100% !important;" maxlength="250">
            </div>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark">
                    Exportador
                </div>
                <select id="add_finca" class="form-control input-yura_default" style="width: 100%">
                    @foreach ($fincas as $f)
                        <option value="{{ $f->id_configuracion_empresa }}">{{ $f->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </td>
    </tr>
</table>

<table style="width:100%; margin-top: 5px">
    <tr>
        <td style="vertical-align: top; width: 50%; padding-right: 5px" id="td_inventarios">
            <div class="panel panel-success" style="margin-bottom: 0px" id="panel_inventarios">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <div id="titulo_inventarios">
                        <b> <i class="fa fa-leaf"></i> INVENTARIO DISPONIBLE </b>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-yura_default" onclick="modificar_div_inv('left')">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                            <button class="btn btn-xs btn-yura_primary" onclick="modificar_div_inv('center')">
                                <i class="fa fa-compress"></i>
                            </button>
                            <button class="btn btn-xs btn-yura_default" onclick="modificar_div_inv('right')">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="panel-body" id="body_inventarios" style="max-height: 500px">
                    <div class="input-group div-compress" style="margin-bottom:10px">
                        <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                            Empresa
                        </div>
                        <select id="finca_inventario" class="form-control input-yura_default"
                            onchange="buscar_inventario()">
                            <option value="">Todas</option>
                            @foreach ($fincas as $f)
                                <option value="{{ $f->id_configuracion_empresa }}">{{ $f->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-addon bg-yura_dark">
                            Busqueda
                        </div>
                        <input type="text" id="buscar_inventario" class="form-control text-center input-yura_default"
                            onkeyup="buscar_inventario()">
                    </div>

                    <div id="div_inventario" style="max-height:430px; overflow:auto">
                    </div>
                </div>
            </div>
        </td>
        <td style="vertical-align: top; padding-left: 5px" id="td_seleccionados">
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
                        <ul class="dropdown-menu dropdown-menu-right sombra_pequeña">
                            <li>
                                <a href="javascript:void(0)" onclick="unificar_detalles()">
                                    <i class="fa fa-fw fa-gift"></i> Unificar detalles
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="quitar_detalles()">
                                    <i class="fa fa-fw fa-trash"></i> Quitar detalles
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel-body" style="height: max-500px; overflow:auto" id="body_seleccionados">
                    <div id="droppable"
                        style="height: 100%; display:flex; align-items: center; justify-content: center"
                        class="ui-droppable">
                        <div style="color:silver; font-size:16px" id="mensaje-drop">
                            <b>AGREGUE LOS PRODUCTOS AL PEDIDO</b>
                        </div>
                    </div>
                    <div id="div_seleccionados" class="hidden" style="height: 100%; overflow: auto">
                        <table class="table-bordered" style="width: 100%; border:1px solid #9d9d9d"
                            id="table_seleccionados">
                            <tr class="tr_fija_top_0">
                                <th class="text-center th_yura_green" style="width: 60px">
                                    <div style="width: 60px">
                                        Caja
                                    </div>
                                </th>
                                <th class="text-center th_yura_green">
                                    <input type="checkbox" id="check_all_precio" class="pull-left"
                                        style="margin-left: 5px"
                                        onchange="$('.check_all_precio').prop('checked', $(this).prop('checked'))">
                                    <label for="check_all_precio" class="mouse-hand">Variedad</label>
                                </th>
                                <th class="text-center padding_lateral_5 th_yura_green">
                                    Long.
                                </th>
                                <th class="text-center padding_lateral_5 th_yura_green" colspan="2">
                                    Tallos
                                </th>
                                <th class="text-center padding_lateral_5 th_yura_green" colspan="2">
                                    Ramos
                                </th>
                                <th class="text-center th_yura_green" style="width: 80px">
                                    Precio
                                    <input type="number" id="all_precio" style="width: 100%;"
                                        class="text-center th_yura_green" onchange="igualar_precio()"
                                        onkeyup="igualar_precio()">
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

<div class="text-center" style="margin-top: 5px">
    <div class="btn-group">
        <button type="button" class="btn btn-yura_primary" onclick="store_pedido()">
            <i class="fa fa-fw fa-save"></i> Grabar Pedido
        </button>
        <button type="button" class="btn btn-yura_default" onclick="deshacer_pedido(); add_pedido()">
            <i class="fa fa-fw fa-refresh"></i> Reiniciar Formulario
        </button>
        <button type="button" class="btn btn-yura_warning" onclick="deshacer_pedido()">
            <i class="fa fa-fw fa-ban"></i> Cancelar
        </button>
    </div>
</div>

<script>
    $('.bootstrap-dialog-footer').addClass('hidden');
    buscar_inventario();
    cant_seleccionados = 0;

    function check_all_selec() {
        $('.check_selec').prop('checked', $('#check_all_selec').prop('checked'));
    }

    function quitar_detalles() {
        for (i = 1; i <= cant_seleccionados; i++) {
            if ($('#check_selec_' + i).prop('checked') == true) {
                dependencias = $('#check_selec_' + i).val().split(',');
                for (x = 0; x < dependencias.length; x++) {
                    $('#tr_seleccionado_' + dependencias[x]).remove();
                }
            }
        }
    }

    function store_pedido() {
        ids_caja_selected = $('.ids_caja_selected');
        data_caja = [];
        for (i = 0; i < ids_caja_selected.length; i++) {
            id = ids_caja_selected[i].value;
            data_caja.push(id);
        }
        ids_detalles_selected = $('.ids_detalles_selected');
        data_precio = [];
        for (i = 0; i < ids_detalles_selected.length; i++) {
            id = ids_detalles_selected[i].value;
            precio = $('#precio_' + id).val();
            data_precio.push({
                id: id,
                precio: precio,
            });
        }
        if (data_caja.length > 0 && $('#add_cliente').val() != '') {
            datos = {
                _token: '{{ csrf_token() }}',
                data_caja: JSON.stringify(data_caja),
                data_precio: JSON.stringify(data_precio),
                fecha: $('#add_fecha').val(),
                cliente: $('#add_cliente').val(),
                finca: $('#add_finca').val(),
                agencia: $('#add_agencia').val(),
                consignatario: $('#add_consignatario').val(),
                marcacion: $('#add_marcacion').val(),
            };
            post_jquery_m('{{ url('pedidos/store_pedido') }}', datos, function() {
                $(window).off('beforeunload');
                cerrar_modals();
                listar_reporte();
            });
        }
    }

    function deshacer_pedido() {
        ids_caja_selected = $('.ids_caja_selected');
        data = [];
        for (i = 0; i < ids_caja_selected.length; i++) {
            id = ids_caja_selected[i].value;
            data.push(id);
        }
        datos = {
            _token: '{{ csrf_token() }}',
            data: JSON.stringify(data)
        }
        post_jquery_m('{{ url('pedidos/deshacer_pedido') }}', datos, function() {
            $(window).off('beforeunload');
            cerrar_modals();
        })
    }

    function igualar_precio() {
        all = $('#all_precio').val();
        check_all_precio = $('.check_all_precio');
        for (i = 0; i < check_all_precio.length; i++) {
            id_det = check_all_precio[i].value;
            if ($('#' + check_all_precio[i].id).prop('checked') == true) {
                $('#precio_' + id_det).val(all);
            }
        }
    }

    $(window).on('beforeunload', function() {
        deshacer_pedido();
        return '¿Desea salir?';
    });
</script>

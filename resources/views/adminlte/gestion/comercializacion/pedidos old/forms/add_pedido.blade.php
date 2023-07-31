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
                <select id="add_cliente" class="form-control" style="width: 100%"
                    onchange="seleccionar_cliente()">
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
                <select id="add_consignatario" class="form-control input_seleccionar_cliente"
                    style="width: 100%">
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

<table style="width:100%">
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
                <div class="panel-body" id="body_inventarios" style="height: 500px">
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
                            Medida
                        </div>
                        <select id="longitud_inventario" class="form-control input-yura_default"
                            onchange="buscar_inventario()">
                            <option value="">Todas</option>
                            @foreach ($longitudes as $l)
                                <option value="{{ $l }}">{{ $l }}cm</option>
                            @endforeach
                        </select>
                        <div class="input-group-addon bg-yura_dark">
                            Busqueda
                        </div>
                        <input type="text" id="buscar_inventario"
                            class="form-control text-center input-yura_default" onkeyup="buscar_inventario()">
                    </div>

                    <div id="div_inventario" style="height:430px; overflow:auto">
                    </div>
                </div>
            </div>
        </td>
        <td style="vertical-align: top; padding-left: 5px" id="td_seleccionados">
            <div class="panel panel-success" style="margin-bottom:0px" id="panel_seleccionados">
                <div class="panel-heading"
                    style="display: flex; justify-content: space-between; align-items: center;">
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
                <div class="panel-body" style="height: 500px; overflow:auto" id="body_seleccionados">
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
                                <th class="text-center th_yura_green">
                                    <input type="checkbox" id="check_all_selec" class="mouse-hand"
                                        onclick="check_all_selec()">
                                </th>
                                <th class="text-center th_yura_green" style="width: 60px">
                                    <div style="width: 60px">
                                        Cajas
                                    </div>
                                </th>
                                <th class="text-center th_yura_green">
                                    <div style="width: 90px">
                                        Finca
                                    </div>
                                </th>
                                <th class="text-center th_yura_green">
                                    <div style="width: 90px">
                                        Origen
                                    </div>
                                </th>
                                <th class="text-center th_yura_green">
                                    <div style="width: 70px">
                                        Planta
                                    </div>
                                </th>
                                <th class="text-center th_yura_green">
                                    <div style="width: 110px">
                                        Variedad
                                    </div>
                                </th>
                                <th class="text-center padding_lateral_5 th_yura_green">
                                    Long.
                                </th>
                                <th class="text-center padding_lateral_5 th_yura_green">
                                    Tallos
                                </th>
                                <th class="text-center padding_lateral_5 th_yura_green">
                                    Ramos
                                </th>
                                <th class="text-center padding_lateral_5 th_yura_green">
                                    Precio
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
    <button type="button" class="btn btn-yura_primary" onclick="store_pedido()">
        <i class="fa fa-fw fa-save"></i> Grabar Pedido
    </button>
</div>

<script>
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

    function unificar_detalles() {
        list_seleccionados = [];
        for (i = 1; i <= cant_seleccionados; i++) {
            if ($('#check_selec_' + i).prop('checked') == true) {
                dependencias = $('#check_selec_' + i).val();
                if (dependencias.split(',').length == 1)
                    list_seleccionados.push(i);
                else {
                    alerta(
                        '<div class="alert alert-info text-center">No se pueden unificar detalles que ya esten <b>UNIFICADOS</b></div>'
                    );
                    return false;
                }
            }
        }
        if (list_seleccionados.length > 1) {
            ids_dependientes = [];
            num_usados = cant_seleccionados;
            for (i = 0; i < list_seleccionados.length; i++) {
                num_usados++;
                ids_dependientes.push(num_usados);
            }

            for (i = 0; i < list_seleccionados.length; i++) {
                inv = $('#id_inventario_selec_' + list_seleccionados[i]).val();
                id_finca = $('#finca_selec_' + list_seleccionados[i]).val();
                nombre_finca = $('#nombre_finca_selec_' + list_seleccionados[i]).val();
                id_finca_destino = $('#finca_destino_selec_' + list_seleccionados[i]).val();
                finca_destino_nombre = $('#nombre_finca_destino_selec_' + list_seleccionados[i]).val();
                planta_nombre = $('#nombre_planta_selec_' + list_seleccionados[i]).val();
                variedad_nombre = $('#nombre_variedad_selec_' + list_seleccionados[i]).val();
                id_variedad = $('#id_variedad_selec_' + list_seleccionados[i]).val();
                longitud = $('#longitud_selec_' + list_seleccionados[i]).val();
                //id_clasificacion_ramo = $('#id_clasificacion_ramo_inv_' + list_seleccionados[i]).val();
                tallos_x_ramo = $('#tallos_x_ramo_selec_' + list_seleccionados[i]).val();
                ramos_x_caja = $('#ramos_x_caja_selec_' + list_seleccionados[i]).val();
                max_disp = $('#ramos_x_caja_selec_' + list_seleccionados[i]).prop('max');
                precio = $('#precio_selec_' + list_seleccionados[i]).val();

                $('#droppable').addClass('hidden');
                $('#div_seleccionados').removeClass('hidden');

                cant_seleccionados++;
                tr = '<tr id="tr_seleccionado_' + cant_seleccionados + '">';
                if (i == 0) {
                    tr +=
                        '<th class="text-center" style="border-color: #9d9d9d" rowspan="' + list_seleccionados.length +
                        '">' +
                        '<input type="checkbox" id="check_selec_' + cant_seleccionados +
                        '" class="check_selec" value="' + ids_dependientes + '" checked>' +
                        '<input type="hidden" id="id_inventario_selec_' + cant_seleccionados + '" value="' + inv +
                        '">' +
                        '</th>' +
                        '<th class="text-center" style="border-color: #9d9d9d" rowspan="' + list_seleccionados.length +
                        '">' +
                        '<input type="number" style="width: 100%" class="text-center form-control" id="cajas_selec_' +
                        cant_seleccionados +
                        '" value="1">' +
                        '</th>';
                }
                tr +=
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" readonly style="width: 100%" class="text-center" id="nombre_finca_selec_' +
                    cant_seleccionados +
                    '" value="' + nombre_finca + '">' +
                    '<input type="hidden" style="width: 100%" class="text-center" id="finca_selec_' +
                    cant_seleccionados +
                    '" value="' + id_finca + '">' +
                    '</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" readonly style="width: 100%" class="text-center" id="nombre_finca_destino_selec_' +
                    cant_seleccionados +
                    '" value="' + finca_destino_nombre + '">' +
                    '<input type="hidden" style="width: 100%" class="text-center" id="finca_destino_selec_' +
                    cant_seleccionados +
                    '" value="' + id_finca_destino + '">' +
                    '</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" readonly style="width: 100%" class="text-center" id="nombre_planta_selec_' +
                    cant_seleccionados +
                    '" value="' + planta_nombre + '">' +
                    '</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" readonly style="width: 100%" class="text-center" id="nombre_variedad_selec_' +
                    cant_seleccionados +
                    '" value="' + variedad_nombre + '">' +
                    '<input type="hidden" style="width: 100%" class="text-center" id="id_variedad_selec_' +
                    cant_seleccionados +
                    '" value="' + id_variedad + '">' +
                    '</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="number" readonly style="width: 100%" class="text-center" id="longitud_selec_' +
                    cant_seleccionados + '" value="' + longitud + '">' +
                    '</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="number" readonly style="width: 100%" class="text-center" id="tallos_x_ramo_selec_' +
                    cant_seleccionados + '" value="' + tallos_x_ramo + '">' +
                    '</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="number" style="width: 100%" class="text-center" id="ramos_x_caja_selec_' +
                    cant_seleccionados + '" value="' + ramos_x_caja + '" max="' + max_disp + '" min="0">' +
                    '</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="number" style="width: 100%" class="text-center" id="precio_selec_' +
                    cant_seleccionados +
                    '" min="0">' +
                    '</th>' +
                    '</tr>';

                $('#table_seleccionados').append(tr);
                $('#tr_seleccionado_' + list_seleccionados[i]).remove();
            }
        }
    }

    function store_pedido() {
        data = [];
        for (i = 1; i <= cant_seleccionados; i++) {
            if ($('#check_selec_' + i).val() != undefined) {
                dependencias = $('#check_selec_' + i).val().split(',');
                pos = dependencias[0];
                cajas = $('#cajas_selec_' + pos).val();
                detalles = [];
                for (x = 0; x < dependencias.length; x++) {
                    pos = dependencias[x];
                    finca = $('#finca_selec_' + pos).val();
                    finca_destino = $('#finca_destino_selec_' + pos).val();
                    id_variedad = $('#id_variedad_selec_' + pos).val();
                    longitud = $('#longitud_selec_' + pos).val();
                    tallos_x_ramo = $('#tallos_x_ramo_selec_' + pos).val();
                    ramos_x_caja = $('#ramos_x_caja_selec_' + pos).val();
                    precio = $('#precio_selec_' + pos).val();
                    detalles.push({
                        finca: finca,
                        finca_destino: finca_destino,
                        id_variedad: id_variedad,
                        longitud: longitud,
                        tallos_x_ramo: tallos_x_ramo,
                        ramos_x_caja: ramos_x_caja,
                        precio: precio,
                    });
                }
                data.push({
                    cajas: cajas,
                    detalles: detalles,
                })
            }
        }
        if (data.length > 0 && $('#add_cliente').val() != '') {
            datos = {
                _token: '{{ csrf_token() }}',
                data: JSON.stringify(data),
                fecha: $('#add_fecha').val(),
                cliente: $('#add_cliente').val(),
                finca: $('#add_finca').val(),
                agencia: $('#add_agencia').val(),
                consignatario: $('#add_consignatario').val(),
                marcacion: $('#add_marcacion').val(),
            };
            post_jquery_m('{{ url('pedidos/store_pedido') }}', datos, function() {
                cerrar_modals();
                listar_reporte();
            });
        }
    }
</script>

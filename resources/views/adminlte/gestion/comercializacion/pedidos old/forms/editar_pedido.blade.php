<table width="100%">
    <tr>
        <td>
            <div class="input-group">
                <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    Fecha de Entrega
                </div>
                <input type="date" id="add_fecha" required class="form-control text-center"
                    style="width: 100% !important;" value="{{ $pedido->fecha_pedido }}">
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
                <select id="add_agencia" class="form-control input-yura_default input_seleccionar_cliente"
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
                <select id="add_consignatario" class="form-control input_seleccionar_cliente" style="width: 100%">
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
                <input type="text" id="add_marcacion" required class="form-control text-center"
                    value="{{ $pedido->marcacion }}" style="width: 100% !important;" maxlength="250">
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
                        <ul class="dropdown-menu dropdown-menu-right sombra_pequeña">
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fa fa-fw fa-gift"></i> Agregar detalle
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel-body" style="height: 500px;" id="body_seleccionados">
                    <div style="overflow-y: scroll; height: 460px">
                        <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
                            <tr class="tr_fija_top_0">
                                <th class="text-center th_yura_green" colspan="2">
                                    Cajas
                                </th>
                                <th class="text-center th_yura_green">
                                    Planta
                                </th>
                                <th class="text-center th_yura_green">
                                    Variedad
                                </th>
                                <th class="text-center th_yura_green">
                                    Longitud
                                </th>
                                <th class="text-center th_yura_green">
                                    Tallos x Ramo
                                </th>
                                <th class="text-center th_yura_green">
                                    Ramos x Caja
                                </th>
                                <th class="text-center th_yura_green">
                                    Precio
                                </th>
                                <th class="text-center th_yura_green">
                                </th>
                            </tr>
                            @php
                                $monto_pedido = 0;
                            @endphp
                            @foreach ($pedido->detalles as $pos_d => $det)
                                @foreach ($det->items as $pos_i => $item)
                                    @php
                                        $variedad = $item->variedad;
                                        $monto_pedido += $item->precio * $item->ramos_x_caja * $item->tallos_x_ramo;
                                    @endphp
                                    <tr>
                                        @if ($pos_i == 0)
                                            <td class="text-center" style="border-color: #9d9d9d"
                                                rowspan="{{ count($det->items) }}">
                                                <input type="hidden" class="ids_detalles"
                                                    value="{{ $det->id_detalle_pedido }}">
                                                <input type="number" value="{{ $det->cantidad }}" class="text-center"
                                                    style="width: 100%; height: {{ 26 * count($det->items) }}px"
                                                    id="cantidad_detalle_{{ $det->id_detalle_pedido }}">
                                            </td>
                                            <td class="text-center" style="border-color: #9d9d9d"
                                                rowspan="{{ count($det->items) }}">
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-yura_dark btn-xs dropdown-toggle"
                                                        data-toggle="dropdown">
                                                        <i class="fa fa-fw fa-gears"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right sombra_pequeña"
                                                        role="menu" style="z-index: 10 !important">
                                                        <li>
                                                            <a href="javascript:void(0)">
                                                                <i class="fa fa-fw fa-copy"></i> Duplicar detalle
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0)">
                                                                <i class="fa fa-fw fa-plus"></i> Agregar al detalle
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0)">
                                                                <i class="fa fa-fw fa-trash"></i> Eliminar del Pedido
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <input type="hidden" class="ids_items"
                                                value="{{ $item->id_item_detalle_pedido }}">
                                            <select id="planta_items_{{ $item->id_item_detalle_pedido }}"
                                                style="width: 100%; height: 26px;"
                                                onchange="select_planta($(this).val(), 'variedad_items_{{ $item->id_item_detalle_pedido }}', 'variedad_items_{{ $item->id_item_detalle_pedido }}', '<option value=>Seleccione</option>')"
                                                ondblclick="select_planta($(this).val(), 'variedad_items_{{ $item->id_item_detalle_pedido }}', 'variedad_items_{{ $item->id_item_detalle_pedido }}', '<option value=>Seleccione</option>')">
                                                @foreach ($plantas as $p)
                                                    <option value="{{ $p->id_planta }}"
                                                        {{ $p->id_planta == $variedad->id_planta ? 'selected' : '' }}>
                                                        {{ $p->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <select id="variedad_items_{{ $item->id_item_detalle_pedido }}"
                                                style="width: 100%; height: 26px">
                                                <option value="{{ $item->id_variedad }}">
                                                    {{ $variedad->nombre }}
                                                </option>
                                            </select>
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <input type="number" value="{{ $item->longitud }}" style="width: 100%"
                                                class="text-center"
                                                id="longitud_detalle_{{ $item->id_item_detalle_pedido }}">
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <input type="number" value="{{ $item->tallos_x_ramo }}"
                                                style="width: 100%" class="text-center"
                                                id="tallos_x_ramo_detalle_{{ $item->id_item_detalle_pedido }}">
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <input type="number" value="{{ $item->ramos_x_caja }}"
                                                style="width: 100%" class="text-center"
                                                id="ramos_x_caja_detalle_{{ $item->id_item_detalle_pedido }}">
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <input type="number" value="{{ $item->precio }}" style="width: 100%"
                                                class="text-center"
                                                id="precio_detalle_{{ $item->id_item_detalle_pedido }}">
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
                                                        <a href="javascript:void(0)">
                                                            <i class="fa fa-fw fa-copy"></i> Duplicar esta fila
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)">
                                                            <i class="fa fa-fw fa-trash"></i> Eliminar del Pedido
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
                            <button type="button" class="btn btn-yura_primary">
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
                        <b> <i class="fa fa-leaf"></i> INVENTARIO DISPONIBLE </b>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button class="btn btn-xs btn-yura_default" onclick="modificar_div_inv('right')">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                            {{-- 
                            <button class="btn btn-xs btn-yura_primary" onclick="modificar_div_inv('center')">
                                <i class="fa fa-compress"></i>
                            </button>
                             --}}
                            <button class="btn btn-xs btn-yura_default" onclick="modificar_div_inv('left')">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="panel-body" id="body_inventarios">
                    caca
                </div>
            </div>
        </td>
    </tr>
</table>

<script>
    modificar_div_inv('left')
</script>

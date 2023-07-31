@if (count($listado) > 0)
    <div style="overflow-y: scroll; overflow-x: scroll; max-height: 550px;">
        <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
            <tr class="tr_fija_top_0">
                <th class="text-center th_yura_green">
                    Cliente
                </th>
                <th class="text-center th_yura_green">
                    Cajas
                </th>
                <th class="text-center th_yura_green">
                    Variedad
                </th>
                <th class="text-center th_yura_green">
                    Longitud
                </th>
                <th class="text-center th_yura_green" colspan="3">
                    Tallos
                </th>
                <th class="text-center th_yura_green" colspan="3">
                    Ramos
                </th>
                <th class="text-center th_yura_green">
                    Precio
                </th>
                <th class="text-center th_yura_green" colspan="3">
                    Monto
                </th>
                <th class="text-center th_yura_green">
                </th>
            </tr>
            @php
                $resumen_variedades = [];
            @endphp
            @foreach ($listado as $pos_ped => $ped)
                @php
                    $getCantidadDetallesByPedido = getCantidadDetallesByPedido($ped);
                    $ped_getTotales = $ped->getTotales();
                @endphp
                @foreach ($ped->detalles as $pos_det => $det)
                    @php
                        $det_getTotales = $det->getTotales();
                        $caja_frio = $det->caja_frio;
                    @endphp
                    @foreach ($caja_frio->detalles as $pos_item => $item)
                        @php
                            $variedad = $item->variedad;
                            $pos_en_resumen = -1;
                            foreach ($resumen_variedades as $pos => $r) {
                                if ($r['variedad']->id_variedad == $item->id_variedad && $r['longitud'] == $item->longitud) {
                                    $pos_en_resumen = $pos;
                                }
                            }
                            if ($pos_en_resumen != -1) {
                                $resumen_variedades[$pos_en_resumen]['tallos'] += $item->ramos * $item->tallos_x_ramo;
                                $resumen_variedades[$pos_en_resumen]['ramos'] += $item->ramos;
                                $resumen_variedades[$pos_en_resumen]['monto'] += $item->ramos * $item->tallos_x_ramo * $item->precio;
                            } else {
                                $resumen_variedades[] = [
                                    'variedad' => $variedad,
                                    'longitud' => $item->longitud,
                                    'tallos' => $item->ramos * $item->tallos_x_ramo,
                                    'ramos' => $item->ramos,
                                    'monto' => $item->ramos * $item->tallos_x_ramo * $item->precio,
                                ];
                            }
                        @endphp
                        <tr class="tr_pedido_{{ $ped->id_pedido }}"
                            onmouseover="$('.tr_pedido_{{ $ped->id_pedido }}').addClass('bg-yura_dark')"
                            onmouseleave="$('.tr_pedido_{{ $ped->id_pedido }}').removeClass('bg-yura_dark')">
                            @if ($pos_det == 0 && $pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ $getCantidadDetallesByPedido }}">
                                    <b>{{ $ped->cliente->detalle()->nombre }}</b>
                                    <br>
                                    <small><b>Consignatario:</b></small>{{ $ped->consignatario->nombre }}
                                    <br>
                                    <small><b>Agencia:</b></small>{{ $ped->agencia_carga->nombre }}
                                    <br>
                                    <small><b>Marcacion:</b></small>{{ $ped->marcacion }}
                                    <br>
                                    <small><b>Finca:</b></small>{{ $ped->empresa->nombre }}
                                </td>
                            @endif
                            @if ($pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ count($caja_frio->detalles) }}">
                                    {{ $caja_frio->nombre }}
                                </td>
                            @endif
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{ $variedad->nombre }}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{ $item->longitud }} <sup><b>cm</b></sup>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{ number_format($item->ramos * $item->tallos_x_ramo) }}
                            </td>
                            @if ($pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ count($caja_frio->detalles) }}">
                                    {{ number_format($det_getTotales['tallos']) }}
                                </td>
                            @endif
                            @if ($pos_det == 0 && $pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ $getCantidadDetallesByPedido }}">
                                    {{ number_format($ped_getTotales['tallos']) }}
                                </td>
                            @endif
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{ number_format($item->ramos) }} <sup>x{{ $item->tallos_x_ramo }}</sup>
                            </td>
                            @if ($pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ count($caja_frio->detalles) }}">
                                    {{ number_format($det_getTotales['ramos']) }}
                                </td>
                            @endif
                            @if ($pos_det == 0 && $pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ $getCantidadDetallesByPedido }}">
                                    {{ number_format($ped_getTotales['ramos']) }}
                                </td>
                            @endif
                            <td class="text-center" style="border-color: #9d9d9d">
                                ${{ $item->precio }}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                ${{ number_format($item->ramos * $item->tallos_x_ramo * $item->precio, 2) }}
                            </td>
                            @if ($pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ count($caja_frio->detalles) }}">
                                    ${{ number_format($det_getTotales['monto'], 2) }}
                                </td>
                            @endif
                            @if ($pos_det == 0 && $pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ $getCantidadDetallesByPedido }}">
                                    ${{ number_format($ped_getTotales['monto'], 2) }}
                                </td>
                            @endif
                            @if ($pos_det == 0 && $pos_item == 0)
                                <td class="text-center" style="border-color: #9d9d9d"
                                    rowspan="{{ $getCantidadDetallesByPedido }}">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-yura_default btn-xs dropdown-toggle"
                                            data-toggle="dropdown">
                                            <i class="fa fa-fw fa-gears"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right sombra_pequeña" role="menu"
                                            style="z-index: 10 !important">
                                            <li>
                                                <a href="javascript:void(0)" title="Editar"
                                                    onclick="editar_pedido('{{ $ped->id_pedido }}')">
                                                    <i class="fa fa-fw fa-pencil"></i> Editar Pedido
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" title="Generar Packing"
                                                    onclick="generar_packing('{{ $ped->id_pedido }}')">
                                                    <i class="fa fa-fw fa-gift"></i> Generar Packing
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" title="Generar Packing"
                                                    onclick="generar_factura('{{ $ped->id_pedido }}')">
                                                    <i class="fa fa-fw fa-money"></i> Generar Factura
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" title="Generar Packing"
                                                    onclick="generar_prefactura('{{ $ped->id_pedido }}')">
                                                    <i class="fa fa-fw fa-shopping-cart"></i> Generar Pre-Factura
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" title="Generar Packing"
                                                    onclick="exportar_etiqueta('{{ $ped->id_pedido }}')">
                                                    <i class="fa fa-fw fa-file"></i> Exportar Etiqueta
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" title="Eliminar"
                                                    onclick="eliminar_pedido('{{ $ped->id_pedido }}')">
                                                    <i class="fa fa-fw fa-trash"></i> Eliminar Pedido
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </table>
    </div>

    <div style="overflow-y: scroll; overflow-x: scroll; max-height: 350px;">
        <table class="table-bordered pull-right" style="width: 50%; margin-top: 5px; border: 1px solid #9d9d9d"
            id="table_resumen_variedades">
            <thead>
                <tr class="tr_fija_top_0">
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
                    <th class="text-center th_yura_green">
                        Monto
                        <button class="btn btn-xs btn-yura_default" onclick="exportar_resumen_pedidos()">
                            <i class="fa fa-fw fa-file-excel-o"></i>
                        </button>
                    </th>
                </tr>
            </thead>
            @php
                $total_tallos = 0;
                $total_ramos = 0;
                $total_monto = 0;
            @endphp
            <tbody>
                @foreach ($resumen_variedades as $r)
                    @php
                        $total_tallos += $r['tallos'];
                        $total_ramos += $r['ramos'];
                        $total_monto += $r['monto'];
                    @endphp
                    <tr onmouseover="$(this).addClass('bg-yura_dark')"
                        onmouseleave="$(this).removeClass('bg-yura_dark')">
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{ $r['variedad']->nombre }}
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{ $r['longitud'] }} <sup><b>cm</b></sup>
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{ number_format($r['tallos']) }}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{ number_format($r['ramos']) }}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            ${{ number_format($r['monto'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tr>
                <th class="text-center th_yura_green" colspan="2">
                    TOTALES
                </th>
                <th class="text-center th_yura_green">
                    {{ number_format($total_tallos) }}
                </th>
                <th class="text-center th_yura_green">
                    {{ number_format($total_ramos) }}
                </th>
                <th class="text-center th_yura_green">
                    ${{ number_format($total_monto, 2) }}
                </th>
            </tr>
        </table>
    </div>
@else
    <div class="alert alert-info text-center">No se han encontrado resultados</div>
@endif

<script>
    function editar_pedido(ped) {
        datos = {
            ped: ped,
        }
        get_jquery('{{ url('pedidos/editar_pedido') }}', datos, function(retorno) {
            modal_view('modal_editar_pedido', retorno, '<i class="fa fa-fw fa-plus"></i> Formulario Pedido',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        })
    }

    function generar_packing(ped) {
        $.LoadingOverlay('show');
        window.open('{{ url('pedidos/generar_packing') }}?ped=' + ped, '_blank');
        $.LoadingOverlay('hide');
    }

    function generar_factura(ped) {
        $.LoadingOverlay('show');
        window.open('{{ url('pedidos/generar_factura') }}?ped=' + ped, '_blank');
        $.LoadingOverlay('hide');
    }

    function exportar_etiqueta(ped) {
        $.LoadingOverlay('show');
        window.open('{{ url('pedidos/exportar_etiqueta') }}?ped=' + ped, '_blank');
        $.LoadingOverlay('hide');
    }

    function generar_prefactura(ped) {
        $.LoadingOverlay('show');
        window.open('{{ url('pedidos/generar_prefactura') }}?ped=' + ped, '_blank');
        $.LoadingOverlay('hide');
    }
</script>

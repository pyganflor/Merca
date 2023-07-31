@if (count($listado) > 0)
    <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr id="tr_fija_top_0">
            <th class="text-center th_yura_green">
                Cliente
            </th>
            <th class="text-center th_yura_green">
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
            <th class="text-center th_yura_green" colspan="3">
                Total Tallos
            </th>
            <th class="text-center th_yura_green">
                Ramos x Caja
            </th>
            <th class="text-center th_yura_green" colspan="3">
                Total Ramos
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
                @endphp
                @foreach ($det->items as $pos_item => $item)
                    @php
                        $variedad = $item->variedad;
                        $pos_en_resumen = -1;
                        foreach ($resumen_variedades as $pos => $r) {
                            if ($r['variedad']->id_variedad == $item->id_variedad && $r['longitud'] == $item->longitud) {
                                $pos_en_resumen = $pos;
                            }
                        }
                        if ($pos_en_resumen != -1) {
                            $resumen_variedades[$pos_en_resumen]['tallos'] += $item->ramos_x_caja * $item->tallos_x_ramo * $det->cantidad;
                            $resumen_variedades[$pos_en_resumen]['ramos'] += $item->ramos_x_caja * $det->cantidad;
                            $resumen_variedades[$pos_en_resumen]['monto'] += $item->ramos_x_caja * $item->tallos_x_ramo * $det->cantidad * $item->precio;
                        } else {
                            $resumen_variedades[] = [
                                'variedad' => $variedad,
                                'longitud' => $item->longitud,
                                'tallos' => $item->ramos_x_caja * $item->tallos_x_ramo * $det->cantidad,
                                'ramos' => $item->ramos_x_caja * $det->cantidad,
                                'monto' => $item->ramos_x_caja * $item->tallos_x_ramo * $det->cantidad * $item->precio,
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
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($det->items) }}">
                                {{ $det->cantidad }}
                            </td>
                        @endif
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{ $variedad->planta->nombre }}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{ $variedad->nombre }}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{ $item->longitud }} <sup><b>cm</b></sup>
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{ $item->tallos_x_ramo }}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{ number_format($item->ramos_x_caja * $item->tallos_x_ramo * $det->cantidad) }}
                        </td>
                        @if ($pos_item == 0)
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($det->items) }}">
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
                            {{ $item->ramos_x_caja }}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{ number_format($item->ramos_x_caja * $det->cantidad) }}
                        </td>
                        @if ($pos_item == 0)
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($det->items) }}">
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
                            ${{ number_format($item->ramos_x_caja * $item->tallos_x_ramo * $det->cantidad * $item->precio, 2) }}
                        </td>
                        @if ($pos_item == 0)
                            <td class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($det->items) }}">
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

    <table class="table-bordered pull-right" style="width: 50%; margin-top: 5px; border: 1px solid #9d9d9d"
        id="table_resumen_variedades">
        <thead>
            <tr>
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
                <tr>
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
</script>

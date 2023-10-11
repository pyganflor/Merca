<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="tabla_ranking">
    <thead>
        <tr class="tr_fija_top_0">
            <th class="th_yura_green padding_lateral_5">
                Producto
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                Cantidad
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                Subtotal
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                Iva
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                Total
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                Costos
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                Margen
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                Utilidad
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listado as $pos => $item)
            @php
                $margen_total = $item['monto_total'] - $item['costo_total'];
                $utilidad_total = porcentaje($margen_total, $item['costo_total'], 1);
                $list_fechas = '';
                foreach ($item['fechas_entregado'] as $f) {
                    $list_fechas .= explode(' del ', convertDateToText($f))[0] . '<br>';
                }
            @endphp
            <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
                <th class="padding_lateral_5" style="border-color: #9d9d9d" data-toggle="tooltip" data-placement="top">
                    {{ $item['producto']->nombre }}
                    <button class="btn btn-xs btn-yura_dark listado_fechas_{{ $pos }} pull-right" title="Ver Fechas"
                        onclick="$('.listado_fechas_{{ $pos }}').toggleClass('hidden')">
                        <i class="fa fa-fw fa-calendar"></i>
                    </button>
                    <ul class="pull-right hidden listado_fechas_{{ $pos }} mouse-hand" onclick="$('.listado_fechas_{{ $pos }}').toggleClass('hidden')">
                        @foreach ($item['fechas_entregado'] as $f)
                            <li class="padding_lateral_5">
                                {{ explode(' del ', convertDateToText($f))[0] }}
                            </li>
                        @endforeach
                    </ul>
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item['cantidad'] }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ round($item['monto_subtotal'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ round($item['monto_total_iva'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ round($item['monto_total'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ round($item['costo_total'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ round($margen_total, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ round($utilidad_total, 2) }}%
                </th>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr class="tr_fija_top_0">
        <th class="padding_lateral_5 th_yura_green" style="min-width: 150px">
            <div style="min-width: 150px">
                Indicadores x Finca
            </div>
        </th>
        @foreach ($meses as $mes)
            <th class="padding_lateral_5 th_yura_green" style="min-width: 110px">
                <div style="min-width: 110px">
                    {{ getMeses()[$mes['mes'] - 1] }} de {{ $mes['anno'] }}
                </div>
            </th>
        @endforeach
    </tr>

    {{-- VENTAS --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_venta_total').toggleClass('hidden')">
            VENTAS TOTALES <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($total_ventas as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_venta_total hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($item['valores_ventas'] as $val)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    ${{ number_format($val, 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- COSTOS --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_costo_total').toggleClass('hidden')">
            COSTOS TOTALES <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($total_costos as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_costo_total hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($item['valores_costos'] as $val)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    ${{ number_format($val, 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- OTROS COSTOS --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            OTROS COSTOS
        </th>
        @foreach ($total_otros_costos as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>

    {{-- MARGEN TOTAL --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_margen_total').toggleClass('hidden')">
            MARGEN TOTAL <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($meses as $pos_m => $mes)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($total_ventas[$pos_m] - $total_costos[$pos_m], 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_margen_total hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($meses as $pos_m => $mes)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    ${{ number_format($item['valores_ventas'][$pos_m] - $item['valores_costos'][$pos_m], 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- % MARGEN --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand"
            onclick="$('.tr_porcentaje_margen').toggleClass('hidden')">
            % MARGEN <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($meses as $pos_m => $mes)
            <th class="padding_lateral bg-yura_dark">
                {{ porcentaje($total_ventas[$pos_m] - $total_costos[$pos_m], $total_ventas[$pos_m], 1) }}%
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_porcentaje_margen hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($meses as $pos_m => $mes)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    {{ porcentaje($item['valores_ventas'][$pos_m] - $item['valores_costos'][$pos_m], $item['valores_ventas'][$pos_m], 1) }}%
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- GASTOS ADMINISTRATIVOS --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            GASTOS ADMINISTRATIVOS
        </th>
        @foreach ($gastos_administrativos as $pos_m => $ga)
            @php
                $valor = $ga != '' ? $ga->ga : '';
            @endphp
            <th class="bg-yura_dark">
                <input type="number" style="width: 100%" id="gasto_admin_{{ $pos_m }}" class="bg-yura_dark"
                    value="{{ $valor }}"
                    onchange="update_ga('{{ $meses[$pos_m]['mes'] }}', '{{ $meses[$pos_m]['anno'] }}', $(this).val())">
            </th>
        @endforeach
    </tr>

    {{-- EBITDA --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            EBITDA
        </th>
        @foreach ($meses as $pos_m => $mes)
            @php
                $margen_total = $total_ventas[$pos_m] - $total_costos[$pos_m];
                $ga = $gastos_administrativos[$pos_m] != '' ? $gastos_administrativos[$pos_m]->ga : 0;
            @endphp
            <th class="bg-yura_dark">
                ${{ number_format($margen_total - $ga, 2) }}
            </th>
        @endforeach
    </tr>

    {{-- PERSONAL --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_personal_total').toggleClass('hidden')">
            PERSONAL <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($total_personal as $val)
            <th class="padding_lateral bg-yura_dark">
                {{ number_format($val) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_personal_total hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($item['valores_personal'] as $val)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    {{ number_format(count($val)) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- COMPRA x PERSONA --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_compra_x_persona').toggleClass('hidden')">
            COMPRA x PERSONA <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($meses as $pos_m => $mes)
            <th class="padding_lateral bg-yura_dark">
                ${{ $total_personal[$pos_m] > 0 ? number_format($total_ventas[$pos_m] / $total_personal[$pos_m], 2) : 0 }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_compra_x_persona hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($meses as $pos_m => $mes)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    ${{ count($item['valores_personal'][$pos_m]) > 0 ? number_format($item['valores_ventas'][$pos_m] / count($item['valores_personal'][$pos_m]), 2) : 0 }}
                </th>
            @endforeach
        </tr>
    @endforeach
</table>

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

    {{-- DESCUENTOS DIFERIDOS --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand"
            onclick="$('.tr_descuento_diferido').toggleClass('hidden')">
            DESCUENTOS DIFERIDOS <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($total_descuentos_diferidos as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_descuento_diferido hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($item['valores_descuentos_diferidos'] as $val)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    ${{ number_format($val, 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- DESCUENTOS NORMALES --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_descuento_normal').toggleClass('hidden')">
            DESCUENTOS NORMALES <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($total_descuentos_normales as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_descuento_normal hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($item['valores_descuentos_normales'] as $val)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    ${{ number_format($val, 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- DESCUENTOS TOTALES --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_descuento_total').toggleClass('hidden')">
            DESCUENTOS TOTALES <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($meses as $pos_m => $mes)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($total_descuentos_diferidos[$pos_m] + $total_descuentos_normales[$pos_m], 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_descuento_total hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($meses as $pos_m => $mes)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    ${{ number_format($item['valores_descuentos_diferidos'][$pos_m] + $item['valores_descuentos_normales'][$pos_m], 2) }}
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

    {{-- FLUJO OPERATIVO --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_flujo_operativo').toggleClass('hidden')">
            FLUJO OPERATIVO <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($meses as $pos_m => $mes)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($total_descuentos_diferidos[$pos_m] + $total_descuentos_normales[$pos_m] - $total_costos[$pos_m], 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_flujo_operativo hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($meses as $pos_m => $mes)
                <th class="padding_lateral" style="border-color: #9d9d9d">
                    ${{ number_format($item['valores_descuentos_diferidos'][$pos_m] + $item['valores_descuentos_normales'][$pos_m] - $item['valores_costos'][$pos_m], 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- GASTOS ADMINISTRATIVOS --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            GASTOS ADMINISTRATIVOS
        </th>
        @foreach ($meses as $pos_m => $mes)
            <th class="bg-yura_dark">
                <input type="number" style="width: 100%" value="" id="gasto_admin_{{ $pos_m }}"
                    class="text-center bg-yura_dark">
            </th>
        @endforeach
    </tr>

    {{-- FLUJO NETO --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            FLUJO NETO
        </th>
        @foreach ($meses as $pos_m => $mes)
            <th class="bg-yura_dark">
                {{-- FLUJO OPERATIVO - GASTOS ADMINISTRATIVOS --}}
            </th>
        @endforeach
    </tr>
</table>

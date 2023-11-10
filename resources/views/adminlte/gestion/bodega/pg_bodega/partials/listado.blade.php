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
        <th class="padding_lateral_5 bg-yura_dark">
            DESCUENTOS DIFERIDOS
        </th>
        @foreach ($total_descuentos_diferidos as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr>
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
        <th class="padding_lateral_5 bg-yura_dark">
            DESCUENTOS NORMALES
        </th>
        @foreach ($total_descuentos_normales as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr>
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

    {{-- VENTAS --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            VENTAS TOTALES
        </th>
        @foreach ($total_ventas as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr>
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
        <th class="padding_lateral_5 bg-yura_dark">
            COSTOS TOTALES
        </th>
        @foreach ($total_costos as $val)
            <th class="padding_lateral bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr>
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

    {{-- PERSONAL --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            PERSONAL
        </th>
        @foreach ($total_personal as $val)
            <th class="padding_lateral bg-yura_dark">
                {{ number_format($val) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr>
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
</table>

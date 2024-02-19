<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr class="tr_fija_top_0">
        <th class="padding_lateral_5 th_yura_green" style="min-width: 150px">
            <div style="min-width: 180px">
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
            <th class="padding_lateral_5 bg-yura_dark">
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
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
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
            <th class="padding_lateral_5 bg-yura_dark">
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
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($val, 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- AL CONTADO --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_al_contado').toggleClass('hidden')">
            AL CONTADO <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($total_al_contado as $val)
            <th class="padding_lateral_5 bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_al_contado hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($item['valores_al_contado'] as $val)
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
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
            <th class="padding_lateral_5 bg-yura_dark">
                ${{ number_format($total_descuentos_diferidos[$pos_m] + $total_descuentos_normales[$pos_m] + $total_al_contado[$pos_m], 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_descuento_total hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($meses as $pos_m => $mes)
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['valores_descuentos_diferidos'][$pos_m] + $item['valores_descuentos_normales'][$pos_m] + $item['valores_al_contado'][$pos_m], 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach

    {{-- COSTOS --}}
    {{-- <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_costo_total').toggleClass('hidden')">
            COSTOS TOTALES <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($total_costos as $val)
            <th class="padding_lateral_5 bg-yura_dark">
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
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($val, 2) }}
                </th>
            @endforeach
        </tr>
    @endforeach --}}

    {{-- OTROS COSTOS --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            COSTOS TOTALES
        </th>
        @foreach ($total_otros_costos as $val)
            <th class="padding_lateral_5 bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>

    {{-- FLUJO OPERATIVO --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark mouse-hand" onclick="$('.tr_flujo_operativo').toggleClass('hidden')">
            FLUJO OPERATIVO <i class="fa fa-fw fa-caret-down pull-right"></i>
        </th>
        @foreach ($meses as $pos_m => $mes)
            <th class="padding_lateral_5 bg-yura_dark">
                ${{ number_format($total_descuentos_diferidos[$pos_m] + $total_descuentos_normales[$pos_m] + $total_al_contado[$pos_m] - $total_costos[$pos_m], 2) }}
            </th>
        @endforeach
    </tr>
    @foreach ($listado as $pos => $item)
        <tr class="tr_flujo_operativo hidden">
            <th class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['finca']->nombre }}
            </th>
            @foreach ($meses as $pos_m => $mes)
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['valores_descuentos_diferidos'][$pos_m] + $item['valores_descuentos_normales'][$pos_m] + $item['valores_al_contado'][$pos_m] - $item['valores_costos'][$pos_m], 2) }}
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

    {{-- FLUJO NETO --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            FLUJO NETO
        </th>
        @foreach ($meses as $pos_m => $mes)
            @php
                $flujo_operativo = $total_descuentos_diferidos[$pos_m] + $total_descuentos_normales[$pos_m] + $total_al_contado[$pos_m] - $total_costos[$pos_m];
                $ga = $gastos_administrativos[$pos_m] != '' ? $gastos_administrativos[$pos_m]->ga : 0;
            @endphp
            <th class="padding_lateral_5 bg-yura_dark">
                ${{ number_format($flujo_operativo - $ga, 2) }}
            </th>
        @endforeach
    </tr>

    {{-- VALOR INVENTARIO --}}
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            VALOR INVENTARIO
        </th>
        @foreach ($valores_inventario as $pos_m => $val)
            <th class="padding_lateral_5 bg-yura_dark">
                ${{ number_format($val, 2) }}
            </th>
        @endforeach
    </tr>
</table>

<script>
    function update_ga(mes, anno, valor) {
        mensaje = {
            title: '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de confirmacion',
            mensaje: '<div class="alert alert-info text-center" style="font-size: 16px">¿Está seguro de <b>GRABAR</b> el gasto administrativo?</div>',
        };
        modal_quest('modal_delete_etiqueta', mensaje['mensaje'], mensaje['title'], true, false,
            '{{ isPC() ? '50%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    mes: mes,
                    anno: anno,
                    valor: valor,
                };
                post_jquery_m('{{ url('flujo_mensual/update_ga') }}', datos, function(retorno) {
                    cerrar_modals();
                    listar_reporte();
                });
            });
    }
</script>

<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_descuentos">
    <thead>
        <tr>
            <th class="th_yura_green padding_lateral_5">
                Cliente
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                CI
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 60px">
                Subtotal
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 40px">
                Iva
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Total
            </th>
            @if ($tipo == 'D')
                <th class="th_yura_green padding_lateral_5" style="width: 60px">
                    #Pago
                </th>
            @endif
        </tr>
    </thead>
    @php
        $monto_subtotal = 0;
        $monto_total_iva = 0;
        $monto_total = 0;
    @endphp
    <tbody>
        @foreach ($listado as $item)
            @php
                $monto_subtotal += $item['subtotal'];
                $monto_total_iva += $item['total_iva'];
                $monto_total += $item['total'];
            @endphp
            <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item['usuario']->nombre_completo }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item['usuario']->username }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['subtotal'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['total_iva'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['total'], 2) }}
                </th>
                @if ($tipo == 'D')
                    <th class="text-center" style="border-color: #9d9d9d">
                        @foreach ($item['num_diferido'] as $pos_dif => $dif)
                            {{ $dif + 1 }}° {{ $pos_dif > 0 ? '- ' : '' }}
                        @endforeach
                    </th>
                @endif
            </tr>
        @endforeach
    </tbody>
    <tr>
        <th class="th_yura_green padding_lateral_5" colspan="2">
            TOTALES
        </th>
        <th class="th_yura_green padding_lateral_5">
            ${{ number_format($monto_subtotal, 2) }}
        </th>
        <th class="th_yura_green padding_lateral_5">
            ${{ number_format($monto_total_iva, 2) }}
        </th>
        <th class="th_yura_green padding_lateral_5">
            ${{ number_format($monto_total, 2) }}
        </th>
        @if ($tipo == 'D')
            <th class="th_yura_green padding_lateral_5">
            </th>
        @endif
    </tr>
</table>

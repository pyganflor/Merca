<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr>
        <th class="th_yura_green text-left padding_lateral_5">
            Cliente
        </th>
        <th class="th_yura_green text-center" style="width: 80px">
            CI
        </th>
        <th class="th_yura_green text-center" style="width: 60px">
            Subtotal
        </th>
        <th class="th_yura_green text-center" style="width: 40px">
            Iva
        </th>
        <th class="th_yura_green text-center" style="width: 50px">
            Total
        </th>
    </tr>
    @php
        $monto_subtotal = 0;
        $monto_total_iva = 0;
        $monto_total = 0;
    @endphp
    @foreach ($listado as $item)
        @php
            $monto_subtotal += $item['subtotal'];
            $monto_total_iva += $item['total_iva'];
            $monto_total += $item['total'];
        @endphp
        <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
            <th class="text-left padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['usuario']->nombre_completo }}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                {{ $item['usuario']->username }}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                ${{ number_format($item['subtotal'], 2) }}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                ${{ number_format($item['total_iva'], 2) }}
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                ${{ number_format($item['total'], 2) }}
            </th>
        </tr>
    @endforeach
    <tr>
        <th class="th_yura_green text-right padding_lateral_5" colspan="2">
            TOTALES
        </th>
        <th class="th_yura_green text-center">
            ${{ number_format($monto_subtotal, 2) }}
        </th>
        <th class="th_yura_green text-center">
            ${{ number_format($monto_total_iva, 2) }}
        </th>
        <th class="th_yura_green text-center">
            ${{ number_format($monto_total, 2) }}
        </th>
    </tr>
</table>

<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="tabla_ranking">
    <thead>
        <tr class="tr_fija_top_0">
            <th class="th_yura_green padding_lateral_5">
                Producto
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                Cantidad
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
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Costos
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Margen
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Utilidad
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listado as $pos => $item)
            @php
                $margen_total = $item['monto_total'] - $item['costo_total'];
                $utilidad_total = porcentaje($margen_total, $item['costo_total'], 1);
            @endphp
            <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item['producto']->nombre }}
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

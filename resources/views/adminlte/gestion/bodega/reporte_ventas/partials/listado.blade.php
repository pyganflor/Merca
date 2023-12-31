<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_reporte">
    <thead>
        <tr>
            <th class="th_yura_green padding_lateral_5">
                <div style="width: 120px">
                    Finca
                </div>
            </th>
            <th class="bg-yura_dark padding_lateral_5" style="width: 60px">
                Venta Armados
            </th>
            <th class="bg-yura_dark padding_lateral_5" style="width: 80px">
                Costos Armados
            </th>
            <th class="bg-yura_dark padding_lateral_5" style="width: 50px">
                Margen Armados
            </th>
            <th class="bg-yura_dark padding_lateral_5" style="width: 50px">
                Utilidad Armados
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Venta Pendientes
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 40px">
                Costos Pendientes
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Margen Pendientes
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Utilidad Pendientes
            </th>
            <th class="bg-yura_dark padding_lateral_5" style="width: 50px">
                Total Venta
            </th>
            <th class="bg-yura_dark padding_lateral_5" style="width: 40px">
                Total Costos
            </th>
            <th class="bg-yura_dark padding_lateral_5" style="width: 50px">
                Total Margen
            </th>
            <th class="bg-yura_dark padding_lateral_5" style="width: 50px">
                Total Utilidad
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                # Personas
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Venta x Persona
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                Total Personas
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 50px">
                % Personas
            </th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_costos_armados = 0;
            $total_venta_armados = 0;
            $total_costos_pendientes = 0;
            $total_venta_pendientes = 0;
            $total_personas = 0;
            $total_all_personas = 0;
        @endphp
        @foreach ($listado as $item)
            @php
                $margen_armados = $item['venta_armados'] - $item['costos_armados'];
                $utilidad_armados = porcentaje($margen_armados, $item['costos_armados'], 1);
                $margen_pendientes = $item['venta_pendientes'] - $item['costos_pendientes'];
                $utilidad_pendientes = porcentaje($margen_pendientes, $item['costos_pendientes'], 1);
                $total_costos = $item['costos_armados'] + $item['costos_pendientes'];
                $total_venta = $item['venta_armados'] + $item['venta_pendientes'];
                $margen_total = $total_venta - $total_costos;
                $utilidad_total = porcentaje($margen_total, $total_costos, 1);
                $personas = count($item['personas']);
                
                $total_costos_armados += $item['costos_armados'];
                $total_venta_armados += $item['venta_armados'];
                $total_costos_pendientes += $item['costos_pendientes'];
                $total_venta_pendientes += $item['venta_pendientes'];
                $total_personas += $personas;
                $total_all_personas += $item['total_personas'];
            @endphp
            <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $item['finca']->nombre }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['venta_armados'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['costos_armados'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($margen_armados, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ number_format($utilidad_armados, 2) }}%
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['venta_pendientes'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($item['costos_pendientes'], 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($margen_pendientes, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ number_format($utilidad_pendientes, 2) }}%
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($total_venta, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($total_costos, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($margen_total, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ number_format($utilidad_total, 2) }}%
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $personas }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($total_venta / $personas, 2) }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item['total_personas'] }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ porcentaje($personas, $item['total_personas'], 1) }}%
                </th>
            </tr>
        @endforeach
    </tbody>
    <tr>
        @php
            $margen_armados = $total_venta_armados - $total_costos_armados;
            $utilidad_armados = porcentaje($margen_armados, $total_costos_armados, 1);
            $margen_pendientes = $total_venta_pendientes - $total_costos_pendientes;
            $utilidad_pendientes = porcentaje($margen_pendientes, $total_costos_pendientes, 1);
            $total_costos = $total_costos_armados + $total_costos_pendientes;
            $total_venta = $total_venta_armados + $total_venta_pendientes;
            $margen_total = $total_venta - $total_costos;
            $utilidad_total = porcentaje($margen_total, $total_costos, 1);
        @endphp
        <th class="padding_lateral_5 th_yura_green">
            TOTALES
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($total_venta_armados, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($total_costos_armados, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($margen_armados, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            {{ number_format($utilidad_armados, 2) }}%
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($total_venta_pendientes, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($total_costos_pendientes, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($margen_pendientes, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            {{ number_format($utilidad_pendientes, 2) }}%
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($total_venta, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($total_costos, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($margen_total, 2) }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            {{ number_format($utilidad_total, 2) }}%
        </th>
        <th class="text-center th_yura_green">
            {{ $total_personas }}
        </th>
        <th class="padding_lateral_5 th_yura_green">
            ${{ number_format($total_venta / $total_personas, 2) }}
        </th>
        <th class="text-center th_yura_green">
            {{ $total_all_personas }}
        </th>
        <th class="text-center th_yura_green">
            {{ porcentaje($total_personas, $total_all_personas, 1) }}%
        </th>
    </tr>
</table>

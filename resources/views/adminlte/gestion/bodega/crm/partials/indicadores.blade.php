<div class="row">
    <div class="col-md-3">
        <div class="div_indicadores border-radius_16" style="background-color: #30BBBB; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: white">
                <strong>Ventas <sup>-4 meses</sup></strong>
            </legend>
            <table style="width: 100%;">
                @php
                    $total_venta = 0;
                @endphp
                @for ($i = count($indicadores) - 1; $i > 0; $i--)
                    @php
                        $item = $indicadores[$i];
                        $total_venta += $item['venta'];
                    @endphp
                    <tr>
                        <th style="color: white">
                            {{ getMeses(TP_ABREVIADO)[$item['mes']['mes'] - 1] }}/{{ $item['mes']['anno'] }}
                        </th>
                        <th class="text-right">
                            ${{ number_format($item['venta'], 2) }}
                        </th>
                    </tr>
                @endfor
            </table>
            <legend style="margin-bottom: 5px; color: white"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: white">
                    <strong>${{ number_format($total_venta, 2) }}</strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="div_indicadores border-radius_16" style="background-color: #30BBBB; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: white">
                <strong>Costos <sup>-4 meses</sup></strong>
            </legend>
            <table style="width: 100%;">
                @php
                    $total_costo = 0;
                @endphp
                @for ($i = count($indicadores) - 1; $i > 0; $i--)
                    @php
                        $item = $indicadores[$i];
                        $total_costo += $item['costo'];
                    @endphp
                    <tr>
                        <th style="color: white">
                            {{ getMeses(TP_ABREVIADO)[$item['mes']['mes'] - 1] }}/{{ $item['mes']['anno'] }}
                        </th>
                        <th class="text-right">
                            ${{ number_format($item['costo'], 2) }}
                        </th>
                    </tr>
                @endfor
            </table>
            <legend style="margin-bottom: 5px; color: white"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: white">
                    <strong>${{ number_format($total_costo, 2) }}</strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="div_indicadores border-radius_16" style="background-color: #30BBBB; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: white">
                <strong>Margen <sup>-4 meses</sup></strong>
            </legend>
            <table style="width: 100%;">
                @for ($i = count($indicadores) - 1; $i > 0; $i--)
                    <tr>
                        @php
                            $item = $indicadores[$i];
                        @endphp
                        <th style="color: white">
                            {{ getMeses(TP_ABREVIADO)[$item['mes']['mes'] - 1] }}/{{ $item['mes']['anno'] }}
                        </th>
                        <th class="text-right">
                            ${{ number_format($item['venta'] - $item['costo'], 2) }}
                        </th>
                    </tr>
                @endfor
            </table>
            <legend style="margin-bottom: 5px; color: white"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: white">
                    <strong>${{ number_format($total_venta - $total_costo, 2) }}</strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="div_indicadores border-radius_16" style="background-color: #30BBBB; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: white">
                <strong>% Margen <sup>-4 meses</sup></strong>
            </legend>
            <table style="width: 100%;">
                @for ($i = count($indicadores) - 1; $i > 0; $i--)
                    <tr>
                        @php
                            $item = $indicadores[$i];
                        @endphp
                        <th style="color: white">
                            {{ getMeses(TP_ABREVIADO)[$item['mes']['mes'] - 1] }}/{{ $item['mes']['anno'] }}
                        </th>
                        <th class="text-right">
                            {{ porcentaje($item['venta'] - $item['costo'], $item['venta'], 1) }}%
                        </th>
                    </tr>
                @endfor
            </table>
            <legend style="margin-bottom: 5px; color: white"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: white">
                    <strong>
                        {{ porcentaje($total_venta - $total_costo, $total_venta, 1) }}%
                    </strong>
                </a>
            </p>
        </div>
    </div>
</div>

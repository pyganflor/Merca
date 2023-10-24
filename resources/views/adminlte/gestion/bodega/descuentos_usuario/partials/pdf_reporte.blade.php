<div style="position: relative; left: -20px; width: 655px">
    <table class="text-center" style="width: 100%">
        <tr>
            <th style="text-align: center" colspan="2">
                RESUMEN de DESCUENTOS {{ $datos['tipo_reporte'] }} "{{ $datos['finca']->nombre }}"
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.7em; text-align: center" colspan="2">
                {{ convertDateToText($datos['desde']) }} - {{ convertDateToText($datos['hasta']) }}
            </th>
        </tr>
        <tr>
            <th style="font-size: 0.8em; text-align: left">
                BENCHMARKET S.A.S.
            </th>
            <th style="font-size: 0.8em; text-align: right">
                RUC <b>1793209142001</b>
            </th>
        </tr>
    </table>

    @php
        $monto_subtotal = 0;
        $monto_total_iva = 0;
        $monto_total = 0;
    @endphp
    <table>
        <tr style="font-size: 0.8em">
            <th class="border-1px text-center">
                Cliente
            </th>
            <th class="border-1px text-center" style="width: 80px">
                CI
            </th>
            <th class="border-1px text-center" style="width: 60px">
                Subtotal
            </th>
            <th class="border-1px text-center" style="width: 40px">
                Iva
            </th>
            <th class="border-1px text-center" style="width: 50px">
                Total
            </th>
            @if ($datos['tipo'] == 'D')
                <th class="border-1px text-center" style="width: 50px">
                    #Pago
                </th>
            @endif
        </tr>
        @foreach ($datos['listado'] as $pos_ped => $item)
            @php
                $monto_subtotal += $item['subtotal'];
                $monto_total_iva += $item['total_iva'];
                $monto_total += $item['total'];
            @endphp
            <tr style="font-size: 0.7em">
                <th class="border-1px" style="text-align: left">
                    {{ $item['usuario']->nombre_completo }}
                </th>
                <th class="border-1px">
                    {{ $item['usuario']->username }}
                </th>
                <th class="border-1px">
                    ${{ number_format($item['subtotal'], 2) }}
                </th>
                <th class="border-1px">
                    ${{ number_format($item['total_iva'], 2) }}
                </th>
                <th class="border-1px">
                    ${{ number_format($item['total'], 2) }}
                </th>
                @if ($datos['tipo'] == 'D')
                    <th class="border-1px">
                        @foreach ($item['num_diferido'] as $pos_dif => $dif)
                            {{ $pos_dif > 0 ? '-' : '' }} {{ $dif + 1 }}°
                        @endforeach
                    </th>
                @endif
            </tr>
        @endforeach
        <tr style="font-size: 0.7em">
            <th class="border-1px text-right" colspan="2">
                TOTALES
            </th>
            <th class="border-1px text-center">
                ${{ number_format($monto_subtotal, 2) }}
            </th>
            <th class="border-1px text-center">
                ${{ number_format($monto_total_iva, 2) }}
            </th>
            <th class="border-1px text-center">
                ${{ number_format($monto_total, 2) }}
            </th>
            @if ($datos['tipo'] == 'D')
                <th class="border-1px text-center">
                </th>
            @endif
        </tr>
    </table>
</div>

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        margin: 0;
    }

    .border-1px {
        border: 1px solid black;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
    }

    td,
    th {
        padding: 0;
        margin: 0;
    }

    .hidden {
        display: none;
    }
</style>

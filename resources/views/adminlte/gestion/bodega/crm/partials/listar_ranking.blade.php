@php
    $total = 0;
    $colores_array = ['#00b388', '#30bbbb', '#ef6e11', '#d01c62'];
    foreach ($listado as $q) {
        if ($criterio == 'V') {
            $total += $q['venta'];
        }
        if ($criterio == 'C') {
            $total += $q['costo'];
        }
        if ($criterio == 'M') {
            $total += $q['margen'];
        }
        if ($criterio == 'P') {
            $total += $q['porcentaje_margen'];
        }
    }
@endphp

@foreach ($listado as $pos => $item)
    @php
        if ($criterio == 'V') {
            $valor = $item['venta'];
        }
        if ($criterio == 'C') {
            $valor = $item['costo'];
        }
        if ($criterio == 'M') {
            $valor = $item['margen'];
        }
        if ($criterio == 'P') {
            $valor = $item['porcentaje_margen'];
        }
    @endphp
    <div class="progress-group">
        <table style="width: 100%">
            <tr>
                <th>
                    {{ $item['finca']->nombre }} <sup>{{ porcentaje($valor, $total, 1) }}%</sup>
                </th>
                <td class="text-right">
                    {{ number_format($valor, 2) }}
                </td>
            </tr>
        </table>

        <div class="progress progress-sm">
            <div class="progress-bar"
                style="width: {{ porcentaje($valor, $total, 1) }}%; background-color: {{ $colores_array[0] }}"></div>
        </div>
    </div>
@endforeach

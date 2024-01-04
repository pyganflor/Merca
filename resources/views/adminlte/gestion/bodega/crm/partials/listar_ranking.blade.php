@php
    $total = 0;
    $colores_array = ['#00b388', '#30bbbb', '#ef6e11', '#d01c62'];
    foreach ($query as $q) {
        if ($criterio == 'T') {
            $total += $q->tallos;
        }
        if ($criterio == 'R') {
            $total += $q->ramos;
        }
        if ($criterio == 'M') {
            $total += $q->monto;
        }
    }
@endphp

@foreach ($query as $pos => $item)
    @php
        if ($criterio == 'T') {
            $valor = $item->tallos;
        }
        if ($criterio == 'R') {
            $valor = $item->ramos;
        }
        if ($criterio == 'M') {
            $valor = $item->monto;
        }
    @endphp
    <div class="progress-group">
        <table style="width: 100%">
            <tr>
                <th>
                    {{ $item->nombre }} <sup>{{ porcentaje($valor, $total, 1) }}%</sup>
                </th>
                <td class="text-right">
                    {{ number_format($valor) }}
                </td>
            </tr>
        </table>

        <div class="progress progress-sm">
            <div class="progress-bar"
                style="width: {{ porcentaje($valor, $total, 1) }}%; background-color: {{ $colores_array[$pos] }}"></div>
        </div>
    </div>
@endforeach

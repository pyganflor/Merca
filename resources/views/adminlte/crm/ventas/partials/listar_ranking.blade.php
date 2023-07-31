@php
    $total = 0;
    $colores_array = ['#00b388', '#30bbbb', '#ef6e11', '#d01c62'];
    foreach ($query as $q) {
        if ($criterio == 'D') {
            $total += $q->dinero;
        }
        if ($criterio == 'R') {
            $total += $q->ramos;
        }
        if ($criterio == 'T') {
            $total += $q->tallos;
        }
    }
@endphp

@foreach ($query as $pos => $item)
    @php
        if ($criterio == 'D') {
            $valor = $item->dinero;
        }
        if ($criterio == 'R') {
            $valor = $item->ramos;
        }
        if ($criterio == 'T') {
            $valor = $item->tallos;
        }
    @endphp
    <div class="progress-group">
        <table style="width: 100%">
            <tr>
                <th>
                    {{ $item->nombre }} <sup>{{ porcentaje($valor, $total, 1) }}%</sup>
                </th>
                <td class="text-right">
                    @if ($criterio == 'D')
                        ${{ number_format($valor, 2) }}
                    @elseif($criterio == 'R')
                        {{ number_format($valor) }}
                    @elseif($criterio == 'T')
                        {{ number_format($valor) }}
                    @endif
                </td>
            </tr>
        </table>

        <div class="progress progress-sm">
            <div class="progress-bar"
                style="width: {{ porcentaje($valor, $total, 1) }}%; background-color: {{ $colores_array[$pos] }}"></div>
        </div>
    </div>
@endforeach

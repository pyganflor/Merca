@if ($pedido != '')
    <tr class="{{ $pedido->armado == 1 ? 'bg-yura_dark' : '' }}">
        <th class="text-center" style="border-color: #9d9d9d">
            #{{ str_pad($pedido->id_pedido_bodega, 8, '0', STR_PAD_LEFT) }}
            <input type="hidden" class="id_pedido_escaneado" value="{{ $pedido->id_pedido_bodega }}">
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            {{ $pedido->usuario->nombre_completo }}
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            {{ convertDateToText($pedido->getFechaEntrega()) }}
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            ${{ number_format($pedido->getTotalMonto(), 2) }}
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            {{ $pedido->armado == 1 ? 'ARMADO' : '' }}
        </th>
    </tr>
@endif

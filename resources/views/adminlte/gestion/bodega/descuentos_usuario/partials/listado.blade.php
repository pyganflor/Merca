<legend style="font-size: 1.2em; margin-bottom: 5px" class="text-center">
    Descuentos de <b>{{ $usuario->nombre_completo }}</b> fecha
    <b>{{ explode(' del ', convertDateToText(hoy()))[0] }}</b>
</legend>
<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_descuentos">
    <thead>
        <tr class="tr_fija_top_0">
            <th class="th_yura_green padding_lateral_5" style="width: 140px">
                Fecha
            </th>
            <th class="th_yura_green padding_lateral_5">
                Producto
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 70px">
                Subtotal
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 70px">
                Iva
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 70px">
                Monto Unitario
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 70px">
                Monto Total
            </th>
            <th class="th_yura_green padding_lateral_5" style="width: 80px">
                #Pago
            </th>
        </tr>
    </thead>
    @php
        $monto_subtotal = 0;
        $monto_total_iva = 0;
        $monto_unitario = 0;
        $monto_total = 0;
    @endphp
    <tbody>
        @foreach ($listado as $item)
            @php
                $producto = $item->producto;
                $precio_prod = 0;
                if ($producto->peso == 1) {
                    foreach ($item->etiquetas_peso as $e) {
                        $precio_prod += $e->peso * $e->precio_venta;
                    }
                } else {
                    $precio_prod = $item->cantidad * $item->precio;
                }
                if ($item->diferido == 0 || $item->diferido == null) {
                    $monto_pedido = $precio_prod;
                } else {
                    $monto_pedido = $precio_prod / $item->diferido;
                }
                if ($item->iva == true) {
                    $subtotal = $precio_prod / 1.12;
                    $iva = ($precio_prod / 1.12) * 0.12;
                } else {
                    $subtotal = $precio_prod;
                    $iva = 0;
                }
                if ($item->diferido > 0) {
                    $subtotal = $subtotal / $item->diferido;
                    $iva = $iva / $item->diferido;
                }

                $monto_subtotal += $subtotal;
                $monto_total_iva += $iva;
                $monto_unitario += $monto_pedido;
                if ($item->diferido > 0) {
                    $monto_total += $monto_pedido * count($item->pagos_pendientes);
                }
            @endphp
            <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ convertDateToText($item->fecha_entrega) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $producto->nombre }}

                    <button type="button" class="btn btn-xs btn-yura_default pull-right" title="Etiqueta"
                        onclick="imprimir_pedido('{{ $item->id_pedido_bodega }}')">
                        <i class="fa fa-fw fa-barcode"></i>
                    </button>
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($subtotal, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($iva, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($monto_pedido, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    @if ($item->diferido > 0)
                        ${{ number_format($monto_pedido * count($item->pagos_pendientes), 2) }}
                    @else
                        ${{ number_format($monto_pedido, 2) }}
                    @endif
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    @if ($item->diferido > 0)
                        @foreach ($item->pagos_pendientes as $pos_p => $p)
                            @if ($pos_p == 0)
                                {{ $p }}°
                            @else
                                | {{ $p }}°
                            @endif
                        @endforeach
                    @else
                        <small><em>No Dif.</em></small>
                    @endif
                </th>
            </tr>
        @endforeach
    </tbody>
    <tr class="tr_fija_bottom_0">
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
            ${{ number_format($monto_unitario, 2) }}
        </th>
        <th class="th_yura_green padding_lateral_5">
            ${{ number_format($monto_total, 2) }}
        </th>
        <th class="th_yura_green padding_lateral_5">
        </th>
    </tr>
</table>

<script>
    function imprimir_pedido(ped) {
        $.LoadingOverlay('show');
        window.open('{{ url('pedido_bodega/imprimir_pedido') }}?pedido=' + ped, '_blank');
        $.LoadingOverlay('hide');
    }
</script>

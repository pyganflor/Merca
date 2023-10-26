<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_descuentos">
    <thead>
        <tr class="tr_fija_top_0">
            <th class="th_yura_green padding_lateral_5" style="width: 140px">
                Fecha
            </th>
            <th class="th_yura_green padding_lateral_5">
                Producto
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
            <th class="th_yura_green padding_lateral_5" style="width: 60px">
                #Pago
            </th>
        </tr>
    </thead>
    @php
        $monto_subtotal = 0;
        $monto_total_iva = 0;
        $monto_total = 0;
    @endphp
    <tbody>
        @foreach ($listado as $item)
            @php
                $producto = $item->producto;
                $precio_prod = 0;
                if ($producto->peso == 1) {
                    foreach($item->etiquetas_peso as $e){
                        $precio_prod += $e->peso * $e->precio_venta;
                    }
                } else {
                    $precio_prod = $item->cantidad * $item->precio;
                }
                $diferido = $precio_prod / $item->diferido;
                if ($item->iva == true) {
                    $subtotal = $precio_prod / 1.12;
                    $iva = ($precio_prod / 1.12) * 0.12;
                } else {
                    $subtotal = $precio_prod;
                    $iva = 0;
                }
                $subtotal = $subtotal / $item->diferido;
                $iva = $iva / $item->diferido;

                $monto_subtotal += $subtotal;
                $monto_total_iva += $iva;
                $monto_total += $diferido;
            @endphp
            <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ convertDateToText($item->fecha_entrega) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $producto->nombre }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($subtotal, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($iva, 2) }}
                </th>
                <th class="padding_lateral_5" style="border-color: #9d9d9d">
                    ${{ number_format($diferido, 2) }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item->num_pago }}°
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
            ${{ number_format($monto_total, 2) }}
        </th>
        <th class="th_yura_green padding_lateral_5">
        </th>
    </tr>
</table>

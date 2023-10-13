@if (sizeof($listado) > 0)
    <table width="100%" class="table-bordered" style="border-color: #9d9d9d">
        <thead>
            <tr class="tr_fija_top_0">
                <th class="padding_lateral_5 th_yura_green">
                    FECHA de ENTREGA
                </th>
                <th class="padding_lateral_5 th_yura_green" style="width: 130px">
                    FINCA de Nomina
                </th>
                <th class="padding_lateral_5 th_yura_green" style="width: 130px">
                    FINCA de ENTREGA
                </th>
                <th class="text-center th_yura_green" style="width: 60px">
                    SUBTOTAL
                </th>
                <th class="text-center th_yura_green" style="width: 60px">
                    IVA
                </th>
                <th class="text-center th_yura_green" style="width: 60px">
                    TOTAL
                </th>
                <th class="text-center th_yura_green" style="width: 60px">
                    DESCUENTO
                </th>
                <th class="text-center th_yura_green" style="width: 60px">
                </th>
            </tr>
        </thead>
        @foreach ($listado as $pedido)
            @php
                $monto_total = 0;
                $monto_subtotal = 0;
                $monto_total_iva = 0;

                foreach ($pedido->detalles as $det) {
                    $producto = $det->producto;
                    if ($producto->peso == 0) {
                        $precio_prod = $det->cantidad * $det->precio;
                    } else {
                        $precio_prod = 0;
                        foreach ($det->etiquetas_peso as $e) {
                            $precio_prod += $e->peso * $e->precio_venta;
                        }
                    }
                    if ($det->iva == true) {
                        $monto_subtotal += $precio_prod / 1.12;
                        $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                    } else {
                        $monto_subtotal += $precio_prod;
                    }
                    $monto_total += $precio_prod;
                }
            @endphp
            <tr onmouseover="$(this).css('background-color','#add8e6')"
                onmouseleave="$(this).css('background-color','')">
                <th style="border-color: #9d9d9d" class="padding_lateral_5">
                    {{ convertDateToText($pedido->getFechaEntrega()) }}
                </th>
                <th style="border-color: #9d9d9d" class="padding_lateral_5">
                    {{ $pedido->getFincaNomina->nombre }}
                </th>
                <th style="border-color: #9d9d9d" class="padding_lateral_5">
                    {{ $pedido->empresa->nombre }}
                </th>
                <th style="border-color: #9d9d9d" class="text-center">
                    ${{ round($monto_subtotal, 2) }}
                </th>
                <th style="border-color: #9d9d9d" class="text-center">
                    ${{ round($monto_total_iva, 2) }}
                </th>
                <th style="border-color: #9d9d9d" class="text-center">
                    ${{ round($monto_total, 2) }}
                </th>
                <th style="border-color: #9d9d9d" class="text-center">
                    ${{ round($pedido->getTotalMontoDiferido(), 2) }}
                </th>
                <th style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-yura_dark" title="Etiqueta"
                            onclick="imprimir_pedido('{{ $pedido->id_pedido_bodega }}')">
                            <i class="fa fa-fw fa-barcode"></i>
                        </button>
                    </div>
                </th>
            </tr>
        @endforeach
    </table>
@else
    <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
@endif

<script>
    function imprimir_pedido(ped) {
        $.LoadingOverlay('show');
        window.open('{{ url('pedido_bodega/imprimir_pedido') }}?pedido=' + ped, '_blank');
        $.LoadingOverlay('hide');
    }
</script>

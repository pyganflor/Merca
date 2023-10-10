<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr>
        <th class="th_yura_green padding_lateral_5">
            Etiqueta
        </th>
        <th class="th_yura_green padding_lateral_5">
            Producto
        </th>
        <th class="th_yura_green padding_lateral_5" style="width: 60px">
            Peso
        </th>
        <th class="th_yura_green padding_lateral_5" style="width: 60px">
            Precio Unit.
        </th>
        <th class="th_yura_green padding_lateral_5" style="width: 60px">
            Precio Total
        </th>
        <th class="th_yura_green padding_lateral_5" style="width: 80px">
        </th>
    </tr>
    @php
        $total_precio = 0;
    @endphp
    @foreach ($listado as $pos => $item)
        @php
            $total_precio += round($item->peso * $item->precio_venta, 2);
        @endphp
        <tr style="background-color: {{ $pos % 2 == 0 ? 'azure' : '' }}" id="tr_etiqueta_{{ $item->id_etiqueta_peso }}">
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item->id_etiqueta_peso }}
            </td>
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $producto->nombre }}
            </td>
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item->peso }}{{ $producto->unidad_medida }}
            </td>
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                ${{ $item->precio_venta }}
            </td>
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                ${{ round($item->peso * $item->precio_venta, 2) }}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-yura_dark" title="Ver Etiqueta"
                        onclick="imprimir_etiqueta('{{ $item->id_etiqueta_peso }}')">
                        <i class="fa fa-fw fa-barcode"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_danger" title="Eliminar Etiqueta"
                        onclick="delete_etiqueta('{{ $item->id_etiqueta_peso }}')">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
    <tr>
        <th class="th_yura_green padding_lateral_5 text-right" colspan="4">
            TOTAL
        </th>
        <th class="th_yura_green padding_lateral_5" style="width: 60px">
            ${{ round($total_precio, 2) }}
        </th>
        <th class="th_yura_green padding_lateral_5" style="width: 80px">
        </th>
    </tr>
</table>
<input type="hidden" id="det_ped_selected" value="{{ $det_ped->id_detalle_pedido_bodega }}">

<script>
    function delete_etiqueta(id) {
        mensaje = {
            title: '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de confirmacion',
            mensaje: '<div class="alert alert-info text-center" style="font-size: 16px">¿Está seguro de <b>ELIMINAR</b> la etiqueta?</div>',
        };
        modal_quest('modal_delete_etiqueta', mensaje['mensaje'], mensaje['title'], true, false,
            '{{ isPC() ? '50%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    id: id,
                };
                post_jquery_m('{{ url('etiquetar_peso/delete_etiqueta') }}', datos, function(retorno) {
                    cerrar_modals();
                    ver_etiquetas($('#det_ped_selected').val());
                    id_inv = $('#id_inventario_selected').val();
                    inventario_disponible = $('#inventario_disponibles_' + id_inv).val();
                    inventario_disponible++;
                    $('#inventario_disponibles_' + id_inv).val(inventario_disponible);
                    seleccionar_inventario(id_inv);
                });
            });
    }
</script>

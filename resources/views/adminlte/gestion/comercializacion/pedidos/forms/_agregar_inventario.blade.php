@if ($caja != '')
    @if ($caja_reservada)
        <script>
            alerta('<div class="alert alert-info text-center">La caja ya se encuentra <b>RESERVADA</b></div>');
        </script>
    @else
        @php
            $getTotales = $caja->getTotales();
        @endphp
        @foreach ($caja->detalles as $pos_d => $det)
            <tr class="tr_caja_{{ $caja->id_caja_frio }} tr_caja_seleccionada_{{ $caja->id_caja_frio }}"
                onmouseover="$('.tr_caja_{{ $caja->id_caja_frio }}').css('background-color', 'cyan')"
                onmouseleave="$('.tr_caja_{{ $caja->id_caja_frio }}').css('background-color', '')">
                @if ($pos_d == 0)
                    <th class="text-center padding_lateral_5" style="border-color: #9d9d9d"
                        rowspan="{{ count($caja->detalles) }}">
                        <input type="checkbox" id="check_all_precio_caja_{{ $caja->id_caja_frio }}"
                            onchange="$('.check_all_precio_caja_{{ $caja->id_caja_frio }}').prop('checked', $(this).prop('checked'))">
                        <label for="check_all_precio_caja_{{ $caja->id_caja_frio }}" class="mouse-hand">
                            {{ $caja->nombre }}
                        </label>
                        <button type="button" class="btn btn-xs btn-yura_danger" title="Regresar caja al inventario"
                            onclick="regresar_inventario('{{ $caja->id_caja_frio }}')">
                            <i class="fa fa-fw fa-trash"></i>
                        </button>
                        <input type="hidden" class="ids_caja_selected" value="{{ $caja->id_caja_frio }}">
                    </th>
                @endif
                <th class="text-center padding_lateral_5" style="border-color: #9d9d9d">
                    <input type="checkbox" id="check_precio_{{ $det->id_detalle_caja_frio }}"
                        value="{{ $det->id_detalle_caja_frio }}"
                        class="pull-left check_all_precio check_all_precio_caja_{{ $caja->id_caja_frio }}"
                        style="margin-left: 5px">
                    <label for="check_precio_{{ $det->id_detalle_caja_frio }}"
                        class="mouse-hand">{{ $det->variedad->nombre }}</label>

                    <input type="hidden" class="ids_detalles_selected" value="{{ $det->id_detalle_caja_frio }}">
                </th>
                <th class="text-center padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $det->longitud }}<sup>cm</sup>
                </th>
                <th class="text-center padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $det->tallos_x_ramo * $det->ramos }}
                </th>
                @if ($pos_d == 0)
                    <th class="text-center padding_lateral_5" style="border-color: #9d9d9d"
                        rowspan="{{ count($caja->detalles) }}">
                        {{ $getTotales->tallos }}
                    </th>
                @endif
                <th class="text-center padding_lateral_5" style="border-color: #9d9d9d">
                    {{ $det->ramos }} <sup>x{{ $det->tallos_x_ramo }}</sup>
                </th>
                @if ($pos_d == 0)
                    <th class="text-center padding_lateral_5" style="border-color: #9d9d9d"
                        rowspan="{{ count($caja->detalles) }}">
                        {{ $getTotales->ramos }}
                    </th>
                @endif
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" min="0" style="width: 100%" class="text-center input_precio"
                        id="precio_{{ $det->id_detalle_caja_frio }}" value="{{ getPrecioByClienteLongitudVariedad($cliente, $det->longitud, $det->id_variedad) }}">
                </th>
            </tr>
        @endforeach
    @endif
@else
    <script>
        alerta('<div class="alert alert-info text-center">Esta caja ya se no se encuentra en el inventario</div>');
    </script>
@endif

<script>
    function regresar_inventario(id_caja) {
        texto =
            "<div class='alert alert-warning text-center'>¿Esta seguro de <b>QUITAR</b> la caja del pedido?</div>";

        modal_quest('modal_eliminar_pedido', texto, 'Eliminar pedido', true, false, '40%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                id_caja: id_caja
            }
            post_jquery_m('{{ url('pedidos/regresar_inventario') }}', datos, function() {
                $('.tr_caja_seleccionada_' + id_caja).remove();
            })
        })
    }
</script>

@if (count($listado) > 0)
    <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr class="tr_fija_top_0">
            <th class="text-center th_yura_green">
                Caja
            </th>
            <th class="text-center th_yura_green">
                Variedad
            </th>
            <th class="text-center th_yura_green">
                Long.
            </th>
            <th class="text-center th_yura_green" colspan="2">
                Tallos
            </th>
            <th class="text-center th_yura_green" colspan="2">
                Ramos
            </th>
        </tr>
        @foreach ($listado as $item)
            <tr>
                <th class="text-center bg-yura_dark" colspan="8">
                    {{ $item['finca']->nombre }}
                </th>
            </tr>
            @foreach ($item['inventarios'] as $pos_c => $caja)
                @php
                    $getTotales = $caja->getTotales();
                @endphp
                @foreach ($caja->detalles as $pos_d => $det)
                    <tr class="tr_caja_{{ $caja->id_caja_frio }}"
                        onmouseover="$('.tr_caja_{{ $caja->id_caja_frio }}').css('background-color', 'cyan')"
                        onmouseleave="$('.tr_caja_{{ $caja->id_caja_frio }}').css('background-color', '')">
                        @if ($pos_d == 0)
                            <th class="text-center padding_lateral_5" style="border-color: #9d9d9d"
                                rowspan="{{ count($caja->detalles) }}">
                                {{ $caja->nombre }}
                                <br>
                                <button type="button" class="btn btn-xs btn-yura_primary" title="Reservar Caja"
                                    onclick="agregar_caja('{{ $caja->id_caja_frio }}')">
                                    <i class="fa fa-fw fa-arrow-left"></i> Agregar al Pedido
                                </button>
                            </th>
                        @endif
                        <th class="text-center padding_lateral_5" style="border-color: #9d9d9d">
                            {{ $det->variedad->nombre }}
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
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    </table>
@else
    <div class="alert alert-info text-center">
        No hay cajas disponibles en el inventario
    </div>
@endif

<script>
    function agregar_caja(id_caja) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 1.5em'>Esta a punto de <b>AGREGAR</b> la caja al Pedido</div>";

        modal_quest('modal_eliminar_pedido', texto, 'Eliminar pedido', true, false, '40%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                id_caja: id_caja,
                pedido: $('#pedido_selected').val(),
            }
            post_jquery_m('{{ url('pedidos/agregar_caja') }}', datos, function(retorno) {
                cerrar_modals();
                editar_pedido(datos['pedido']);
                setTimeout(() => {
                    add_caja();
                }, 800);
            });
        });
    }
</script>

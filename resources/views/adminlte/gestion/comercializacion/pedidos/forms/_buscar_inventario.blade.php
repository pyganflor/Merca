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
        <th class="text-center th_yura_green">
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
                        <th class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($caja->detalles) }}">
                            <button type="button" class="btn btn-xs btn-yura_dark" title="Reservar Caja"
                                onclick="agregar_inventario('{{ $caja->id_caja_frio }}')">
                                <i class="fa fa-fw fa-arrow-right"></i>
                            </button>
                        </th>
                    @endif
                </tr>
            @endforeach
        @endforeach
    @endforeach
</table>

<script>
    function agregar_inventario(id_caja) {
        datos = {
            id_caja: id_caja,
            cliente: $('#add_cliente').val()
        }
        get_jquery('{{ url('pedidos/agregar_inventario') }}', datos, function(retorno) {
            $('#table_seleccionados').append(retorno);
            $('#droppable').addClass('hidden');
            $('#div_seleccionados').removeClass('hidden');
        })
    }
</script>

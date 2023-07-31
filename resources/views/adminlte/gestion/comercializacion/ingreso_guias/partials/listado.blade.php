@if (count($listado) > 0)
    <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <thead>
            <tr id="tr_fija_top_0">
                <th class="text-center th_yura_green">
                    Cliente
                </th>
                <th class="text-center th_yura_green">
                    Consignatario
                </th>
                <th class="text-center th_yura_green">
                    Agencia
                </th>
                <th class="text-center th_yura_green">
                    Marcacion
                </th>
                <th class="text-center th_yura_green">
                    Pais
                </th>
                <th class="text-center th_yura_green" style="width: 110px">
                    DAE
                </th>
                <th class="text-center th_yura_green" style="width: 110px">
                    Guia Madre
                </th>
                <th class="text-center th_yura_green" style="width: 110px">
                    Guia Hija
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listado as $pos => $item)
                @php
                    $dae = '';
                    if ($item['pedido']->codigo_dae != '') {
                        $dae = $item['pedido']->codigo_dae;
                    } elseif ($item['codigo_dae'] != '') {
                        $dae = $item['codigo_dae']->codigo_dae;
                    }
                @endphp
                <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')">
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{ $item['pedido']->cliente_nombre }}
                        <input type="hidden" class="ids_pedido" value="{{ $item['pedido']->id_pedido }}">
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{ $item['pedido']->consignatario_nombre }}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{ $item['pedido']->agencia_nombre }}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{ $item['pedido']->marcacion }}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{ $item['pedido']->pais_nombre }}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        <input type="text" class="text-center" style="width: 100%; color: black"
                            id="codigo_dae_{{ $item['pedido']->id_pedido }}"
                            value="{{ $dae }}">
                    </th>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" class="text-center" style="width: 100%; color: black"
                            id="guia_madre_{{ $item['pedido']->id_pedido }}" value="{{ $item['pedido']->guia_madre }}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="text" class="text-center" style="width: 100%; color: black"
                            id="guia_hija_{{ $item['pedido']->id_pedido }}" value="{{ $item['pedido']->guia_hija }}">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="text-center" style="margin-top: 5px">
        <button type="button" class="btn btn-yura_primary" onclick="store_guias()">
            <i class="fa fa-fw fa-save"></i> GRABAR GUIAS
        </button>
    </div>
@else
    <div class="alert alert-info text-center">No se han encontrado resultados</div>
@endif


<script>
    function store_guias() {
        ids_pedido = $('.ids_pedido');
        data = [];
        for (i = 0; i < ids_pedido.length; i++) {
            id = ids_pedido[i].value;
            dae = $('#codigo_dae_' + id).val();
            madre = $('#guia_madre_' + id).val();
            hija = $('#guia_hija_' + id).val();
            data.push({
                id: id,
                dae: dae,
                madre: madre,
                hija: hija,
            })
        }

        datos = {
            _token: '{{ csrf_token() }}',
            data: JSON.stringify(data),
        }
        post_jquery_m('{{ url('ingreso_guias/store_guias') }}', datos, function() {})
    }
</script>

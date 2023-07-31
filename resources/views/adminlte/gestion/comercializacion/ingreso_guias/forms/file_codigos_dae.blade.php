<div style="overflow-y: scroll; max-height: 550px">
    <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr class="tr_fija_top_0">
            <th class="text-center th_yura_green">
                CODIGO
            </th>
            <th class="text-center th_yura_green">
                NOMBRE
            </th>
            <th class="text-center th_yura_green">
                DAE
            </th>
            <th class="text-center th_yura_green">
                MES
            </th>
            <th class="text-center th_yura_green">
                AÑO
            </th>
        </tr>
        @foreach ($listado as $item)
            <tr>
                <th class="text-center {{ $item['pais'] == '' ? 'error' : '' }}" style="border-color: #9d9d9d">
                    {{ $item['pais'] != '' ? $item['pais']->codigo : $item['row']['A'] }}
                    @if ($item['pais'] != '')
                        <input type="hidden" class="codigo_pais_importar" value="{{ $item['pais']->codigo }}">
                        <input type="hidden" id="codigo_dae_importar_{{ $item['pais']->codigo }}"
                            value="{{ $item['row']['C'] }}">
                        <input type="hidden" id="mes_importar_{{ $item['pais']->codigo }}"
                            value="{{ $item['row']['D'] }}">
                        <input type="hidden" id="anno_importar_{{ $item['pais']->codigo }}"
                            value="{{ $item['row']['E'] }}">
                    @endif
                </th>
                <th class="text-center {{ $item['pais'] == '' ? 'error' : '' }}" style="border-color: #9d9d9d">
                    {{ $item['pais'] != '' ? $item['pais']->nombre : $item['row']['B'] }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item['row']['C'] }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item['row']['D'] }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item['row']['E'] }}
                </th>
            </tr>
        @endforeach
    </table>
</div>

<div class="text-center" style="margin-top: 5px">
    @if ($fallos)
        <button type="button" class="btn btn-yura_danger" disabled>
            <i class="fa fa-fw fa-ban"></i> Solucione los fallos antes de PROCESAR
        </button>
    @else
        <button type="button" class="btn btn-yura_primary" onclick="store_codigos_dae()">
            <i class="fa fa-fw fa-save"></i> PROCESAR
        </button>
    @endif
</div>

<script>
    function store_codigos_dae() {
        codigo_pais_importar = $('.codigo_pais_importar');
        data = [];
        for (i = 0; i < codigo_pais_importar.length; i++) {
            codigo_pais = codigo_pais_importar[i].value;
            data.push({
                codigo_pais: codigo_pais,
                dae: $('#codigo_dae_importar_' + codigo_pais).val(),
                mes: $('#mes_importar_' + codigo_pais).val(),
                anno: $('#anno_importar_' + codigo_pais).val(),
            })
        }

        datos = {
            _token: '{{ csrf_token() }}',
            data: JSON.stringify(data),
        }
        post_jquery_m('{{ url('codigo_dae/store_codigos_dae') }}', datos, function() {
            cerrar_modals();
            listar_reporte();
        })
    }
</script>

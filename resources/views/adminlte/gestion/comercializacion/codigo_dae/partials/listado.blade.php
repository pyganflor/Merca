@if (count($listado) > 0)
    <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <thead>
            <tr id="tr_fija_top_0">
                <th class="text-center th_yura_green">
                    CODIGO PAIS
                </th>
                <th class="text-center th_yura_green">
                    NOMBRE PAIS
                </th>
                <th class="text-center th_yura_green">
                    DAE
                </th>
                <th class="text-center th_yura_green">
                    AÑO
                </th>
                <th class="text-center th_yura_green">
                    MES
                </th>
                <th class="text-center th_yura_green">
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listado as $pos => $item)
                <tr onmouseover="$(this).addClass('bg-yura_dark')" onmouseleave="$(this).removeClass('bg-yura_dark')"
                    id="tr_codigo_dae_{{ $item->id_codigo_dae }}">
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $item->codigo_pais }}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $item->nombre }}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $item->codigo_dae }}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $item->anno }}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $item->mes }}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <div class="btn-group">
                            <button type="button" class="btn btn-yura_danger btn-xs" title="Desactivar"
                                onclick="cambiar_estado('{{ $item->id_codigo_dae }}')">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-info text-center">No se han encontrado resultados</div>
@endif


<script>
    function cambiar_estado(id) {
        datos = {
            _token: '{{ csrf_token() }}',
            id: id,
        }
        post_jquery_m('{{ url('codigo_dae/cambiar_estado') }}', datos, function() {
            $('#tr_codigo_dae_' + id).remove();
        })
    }
</script>

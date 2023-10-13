<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr>
        <th class="padding_lateral_5 th_yura_green">
            FINCA
        </th>
        <th class="text-center th_yura_green" style="width: 30px">
        </th>
    </tr>
    @foreach ($fincas as $f)
        <tr>
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $f->nombre }}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="checkbox" id="check_finca_{{ $f->id_configuracion_empresa }}"
                    data-id="{{ $f->id_configuracion_empresa }}" class="mouse-hand check_finca">
            </td>
        </tr>
    @endforeach
</table>
<div class="text-center" style="margin-top: 5px">
    <button type="button" class="btn btn-yura_primary" onclick="store_copiar_fechas()">
        GRABAR
    </button>
</div>

<script>
    function store_copiar_fechas() {
        data_fincas = [];
        check_finca = $('.check_finca');
        for (i = 0; i < check_finca.length; i++) {
            id = check_finca[i].id;
            if ($('#' + id).prop('checked') == true) {
                data_fincas.push($('#' + id).attr('data-id'));
            }
        }

        data_entregas = [];
        check_entrega = $('.check_entrega');
        for (i = 0; i < check_entrega.length; i++) {
            id = check_entrega[i].id;
            if ($('#' + id).prop('checked') == true) {
                data_entregas.push($('#' + id).attr('data-id'));
            }
        }

        if (data_fincas.length > 0 && data_entregas.length > 0) {
            datos = {
                _token: '{{ csrf_token() }}',
                data_fincas: JSON.stringify(data_fincas),
                data_entregas: JSON.stringify(data_entregas),
            };
            post_jquery_m('{{ url('fecha_entrega/store_copiar_fechas') }}', datos, function() {
                cerrar_modals();
                listar_reporte();
            });
        }
    }
</script>

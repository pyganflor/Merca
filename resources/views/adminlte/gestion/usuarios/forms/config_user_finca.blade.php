<div style="overflow-y: scroll; max-height: 650px">
    <table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d" id="table_fincas_usuario">
        <thead>
            <tr class="tr_fija_top_1">
                <th class="text-center th_yura_green" style="border-color: white; width: 80%">
                    Finca
                </th>
                <th class="text-center th_yura_green" style="border-color: white">
                    <label for="check_all_fincas" class="mouse-hand">
                        Todas
                    </label>
                    <input type="checkbox" id="check_all_fincas" onchange="$('.check_all_fincas').prop('checked', $(this).prop('checked'))">
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empresas as $emp)
                @php
                    $getUsuarioFincaByFincas = getUsuarioFincaByFincas($fincas, $emp->id_configuracion_empresa);
                @endphp
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $emp->nombre }}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="checkbox" id="check_finca_user_{{ $emp->id_configuracion_empresa }}"
                            class="mouse-hand checkbox_config_finca_user {{ $emp->id_configuracion_empresa }} check_all_fincas"
                            {{ $getUsuarioFincaByFincas != '' ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="text-center">
    <button type="button" class="btn btn-yura_primary" style="margin-top: 10px" onclick="store_finca_user()">
        <i class="fa fa-fw fa-save"></i> Guardar
    </button>
</div>
<input type="hidden" id="id_usuario_config_finca" value="{{ $usuario->id_usuario }}">

<script>
    estructura_tabla('table_fincas_usuario');

    function store_finca_user() {
        data = [];
        checkboxes = $('.checkbox_config_finca_user');
        for (i = 0; i < checkboxes.length; i++) {
            if ($('#' + checkboxes[i].id).prop('checked') == true) {
                var id_emp = document.getElementById(checkboxes[i].id).classList[2];
                data.push(id_emp);
            }
        }
        datos = {
            _token: '{{ csrf_token() }}',
            user: $('#id_usuario_config_finca').val(),
            data: data
        };
        post_jquery_m('{{ url('usuarios/store_finca_user') }}', datos, function(retorno) {
            cerrar_modals();
            location.reload();
        });
    }
</script>

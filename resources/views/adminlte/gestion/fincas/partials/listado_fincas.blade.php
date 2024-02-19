<table class="table-bordered table-striped" style="width: 100%; border: 1px solid #9d9d9d; border-radius: 18px 18px 0 0"
    id="table_fincas">
    <thead>
        <tr>
            <th class="text-center bg-yura_dark" style="border-radius: 18px 18px 0 0" colspan="4">
                Fincas
            </th>
        </tr>
        <tr>
            <th class="th_yura_green" style="padding-left: 5px">
                Nombre
            </th>
            <th class="th_yura_green" style="padding-left: 5px">
                Empresa
            </th>
            <th class="th_yura_green" style="padding-left: 5px">
                Usar PERSONAL
            </th>
            <th class="text-center th_yura_green" style="width: 80px">
                <div class="btn-group">
                    <button type="button" class="btn btn-yura_default btn-xs"
                        onclick="$('#tr_new_finca').removeClass('hidden')">
                        <i class="fa fa-fw fa-plus"></i>
                    </button>
                </div>
            </th>
        </tr>
        <tr id="tr_new_finca" class="hidden">
            <td style="border-color: #9d9d9d;">
                <input type="text" id="nombre_finca_new" style="width: 100%" class="text-center">
            </td>
            <td style="border-color: #9d9d9d;">
                <select id="id_super_finca_new" style="width: 100%; height: 26px;">
                    <option value="">Default</option>
                    @foreach ($super_fincas as $sf)
                        <option value="{{ $sf->id_super_finca }}">
                            {{ $sf->nombre }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td style="border-color: #9d9d9d;">
                <input type="checkbox" id="usar_personal_new" style="width: 100%" class="text-center" checked>
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-yura_primary btn-xs" title="Grabar" onclick="store_finca()">
                        <i class="fa fa-fw fa-save"></i> Grabar
                    </button>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
        @foreach ($listado as $item)
            <tr id="tr_finca_{{ $item->id_configuracion_empresa }}">
                <td style="border-color: #9d9d9d;">
                    <input type="text" id="nombre_finca_{{ $item->id_configuracion_empresa }}"
                        value="{{ $item->nombre }}" style="width: 100%"
                        class="{{ $item->estado == 0 ? 'error' : '' }} text-center">
                </td>
                <td style="border-color: #9d9d9d;">
                    <select id="id_super_finca_{{ $item->id_configuracion_empresa }}" style="width: 100%; height: 26px;"
                        class="{{ $item->estado == 0 ? 'error' : '' }}">
                        <option value="">Default</option>
                        @foreach ($super_fincas as $sf)
                            <option value="{{ $sf->id_super_finca }}"
                                {{ $sf->id_super_finca == $item->id_super_finca ? 'selected' : '' }}>
                                {{ $sf->nombre }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td style="border-color: #9d9d9d;">
                    <input type="checkbox" id="usar_personal_{{ $item->id_configuracion_empresa }}" style="width: 100%"
                        class="text-center" {{ $item->usar_personal == 1 ? 'checked' : '' }}>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <div class="btn-group">
                        <button type="button" class="btn btn-yura_primary btn-xs" title="Modificar"
                            onclick="update_finca('{{ $item->id_configuracion_empresa }}')">
                            <i class="fa fa-fw fa-save"></i>
                        </button>
                        <button type="button" class="btn btn-yura_danger btn-xs" title="Activar/Desactivar"
                            onclick="cambiar_estado_finca('{{ $item->id_configuracion_empresa }}', '{{ $item->estado }}')">
                            <i class="fa fa-fw fa-lock"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    function update_finca(id) {
        datos = {
            _token: '{{ csrf_token() }}',
            id: id,
            nombre: $('#nombre_finca_' + id).val(),
            super_finca: $('#id_super_finca_' + id).val(),
            usar_personal: $('#usar_personal_' + id).prop('checked'),
        };
        post_jquery_m('{{ url('fincas/update_finca') }}', datos, function() {}, 'tr_finca_' + id);
    }

    function cambiar_estado_finca(id, estado) {
        mensaje = {
            title: estado == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar finca' :
                '<i class="fa fa-fw fa-unlock"></i> Activar finca',
            mensaje: estado == 1 ?
                '<div class="alert alert-danger text-center" style="font-size: 16px"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar esta finca?</div>' :
                '<div class="alert alert-info text-center" style="font-size: 16px"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar esta finca?</div>',
        };
        modal_quest('modal_cambiar_estado_finca', mensaje['mensaje'], mensaje['title'], true, false,
            '{{ isPC() ? '45%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    id: id
                };
                post_jquery_m('{{ url('fincas/cambiar_estado_finca') }}', datos, function(retorno) {
                    listar_fincas();
                })
            });
    }

    function store_finca() {
        datos = {
            _token: '{{ csrf_token() }}',
            nombre: $('#nombre_finca_new').val(),
            super_finca: $('#id_super_finca_new').val(),
            usar_personal: $('#usar_personal_new').prop('checked'),
        };
        post_jquery_m('{{ url('fincas/store_finca') }}', datos, function() {
            listar_fincas();
        }, 'tr_new_finca');
    }
</script>

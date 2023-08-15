<div style="overflow-y: scroll; overflow-x: scroll; height: 700px;">
    <table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr id="tr_fija_top_0">
            <th class="text-center th_yura_green">
                DESDE
            </th>
            <th class="text-center th_yura_green">
                HASTA
            </th>
            <th class="text-center th_yura_green">
                ENTREGA
            </th>
            <th class="text-center th_yura_green" style="width: 20%">
                FINCA
            </th>
            <th class="text-center th_yura_green" style="width: 60px">
                <button type="button" class="btn btn-xs btn-yura_default"
                    onclick="$('#tr_new_fecha').removeClass('hidden'); $('#desde_new').focus()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </th>
        </tr>
        <tr id="tr_new_fecha" class="hidden">
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="date" style="width: 100%" class="text-center bg-yura_dark" id="desde_new"
                    name="desde_new" placeholder="Desde" required value="{{ hoy() }}">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="date" style="width: 100%" class="text-center bg-yura_dark" id="hasta_new"
                    name="hasta_new" placeholder="Hasta" required value="{{ hoy() }}">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="date" style="width: 100%" class="text-center bg-yura_dark" id="entrega_new"
                    name="entrega_new" placeholder="Entrega" required value="{{ hoy() }}">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <select id="finca_new" style="width: 100%; height: 26px;" class="bg-yura_dark">
                    @foreach ($fincas as $f)
                        <option value="{{ $f->id_configuracion_empresa }}">
                            {{ $f->nombre }}
                        </option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="border-color: #9d9d9d" colspan="3">
                <button type="button" class="btn btn-xs btn-yura_primary" onclick="store_fecha()">
                    <i class="fa fa-fw fa-save"></i>
                </button>
            </th>
        </tr>
        @foreach ($listado as $item)
            <tr>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="date" style="width: 100%" class="text-center"
                        id="desde_{{ $item->id_fecha_entrega }}" value="{{ $item->desde }}" required>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="date" style="width: 100%" class="text-center"
                        id="hasta_{{ $item->id_fecha_entrega }}" value="{{ $item->hasta }}" required>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="date" style="width: 100%" class="text-center"
                        id="entrega_{{ $item->id_fecha_entrega }}" value="{{ $item->entrega }}" required>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <select id="finca_{{ $item->id_fecha_entrega }}" style="width: 100%; height: 26px;">
                        @foreach ($fincas as $f)
                            <option value="{{ $f->id_configuracion_empresa }}"
                                {{ $f->id_configuracion_empresa == $item->id_empresa ? 'selected' : '' }}>
                                {{ $f->nombre }}
                            </option>
                        @endforeach
                    </select>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-yura_warning"
                            onclick="update_fecha('{{ $item->id_fecha_entrega }}')">
                            <i class="fa fa-fw fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-yura_danger"
                            onclick="eliminar_fecha('{{ $item->id_fecha_entrega }}')">
                            <i class="fa fa-fw fa-trash"></i>
                        </button>
                    </div>
                </th>
            </tr>
        @endforeach
    </table>
</div>

<script>
    function store_fecha() {
        datos = {
            _token: '{{ csrf_token() }}',
            desde: $('#desde_new').val(),
            hasta: $('#hasta_new').val(),
            entrega: $('#entrega_new').val(),
            finca: $('#finca_new').val(),
        };
        post_jquery_m('{{ url('fecha_entrega/store_fecha') }}', datos, function() {
            cerrar_modals();
            listar_reporte();
        });
    }

    function update_fecha(id) {
        datos = {
            _token: '{{ csrf_token() }}',
            id: id,
            desde: $('#desde_' + id).val(),
            hasta: $('#hasta_' + id).val(),
            entrega: $('#entrega_' + id).val(),
            finca: $('#finca_' + id).val(),
        };
        post_jquery_m('{{ url('fecha_entrega/update_fecha') }}', datos, function() {
            cerrar_modals();
        });
    }

    function eliminar_fecha(id) {
        mensaje = {
            title: '<i class="fa fa-fw fa-trash"></i> ELIMINAR fecha de entrega',
            mensaje: '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de ELIMINAR esta fecha de entrega?</div>',
        };
        modal_quest('modal_eliminar_fecha', mensaje['mensaje'], mensaje['title'], true, false,
            '{{ isPC() ? '45%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    id: id,
                };
                post_jquery_m('{{ url('fecha_entrega/eliminar_fecha') }}', datos, function() {
                    cerrar_modals();
                    listar_reporte();
                });
            });
    }

    function store_categoria() {
        mensaje = {
            title: '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de confirmacion',
            mensaje: '<div class="alert alert-info text-center" style="font-size: 16px">¿Está seguro de <b>CREAR</b> una nueva categoria?</div>',
        };
        modal_quest('modal_delete_producto', mensaje['mensaje'], mensaje['title'], true, false,
            '{{ isPC() ? '50%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    nombre: $('#new_nombre_categoria').val(),
                };
                post_jquery_m('{{ url('bodega_productos/store_categoria') }}', datos, function() {
                    cerrar_modals();
                    listar_reporte();
                });
            });
    }
</script>

<table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr id="tr_fija_top_0">
        <th class="text-center th_yura_green" style="width: 60px">
            ORDEN
        </th>
        <th class="text-center th_yura_green" style="width: 60px">
            IMAGEN
        </th>
        <th class="text-center th_yura_green" style="width: 60px">
            CODIGO
        </th>
        <th class="text-center th_yura_green" style="width: 180px">
            NOMBRE
        </th>
        <th class="text-center th_yura_green" style="width: 60px">
            COSTO
        </th>
        <th class="text-center th_yura_green" style="width: 60px">
            VENTA
        </th>
        <th class="text-center th_yura_green" style="width: 60px">
            MARGEN
        </th>
        <th class="text-center th_yura_green" style="width: 60px">
            % UTILDIAD
        </th>
        <th class="text-center th_yura_green" style="width: 80px">
            <button type="button" class="btn btn-xs btn-yura_default"
                onclick="$('#tr_new_producto').removeClass('hidden'); $('#codigo_new').focus()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_new_producto" class="hidden">
        <th class="text-center" style="border-color: #9d9d9d">
            <input type="number" style="width: 100%" class="text-center bg-yura_dark" required min="0"
                id="orden_new" name="orden_new" placeholder="0">
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <form id="form_add_producto" action="{{ url('bodega_productos/store_combo') }}" method="post"
                enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input type="file" style="width: 100%;" class="text-center bg-yura_dark" id="imagen_new"
                    name="imagen_new" placeholder="Codigo" accept="image/png, image/jpeg">
            </form>
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <input type="text" style="width: 100%" class="text-center bg-yura_dark" id="codigo_new" name="codigo_new"
                placeholder="Codigo" required>
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <input type="text" style="width: 100%" class="text-center bg-yura_dark" id="nombre_new" name="nombre_new"
                placeholder="NOMBRE" required>
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <input type="number" style="width: 100%" class="text-center bg-yura_dark" required min="0"
                id="precio_venta_new" name="precio_venta_new" placeholder="0">
        </th>
        <th class="text-center" style="border-color: #9d9d9d" colspan="3">
            <button type="button" class="btn btn-xs btn-yura_primary" onclick="store_combo()">
                <i class="fa fa-fw fa-save"></i> Grabar
            </button>
        </th>
    </tr>
    @foreach ($listado as $item)
        @php
            $url_imagen = 'images\productos\*' . $item->imagen;
            $url_imagen = str_replace('*', '', $url_imagen);
            $precio_costo = $item->getCostoCombo();
            if ($item->tiene_iva) {
                $temp = $item->precio_venta - porcentaje(12, $item->precio_venta, 2);
                $margen = $temp - $precio_costo;
            } else {
                $margen = $item->precio_venta - $precio_costo;
            }
        @endphp
        <tr id="tr_producto_{{ $item->id_producto }}" class="{{ $item->estado == 0 ? 'error' : '' }}">
            <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                <input type="number" style="width: 100%" class="text-center" required min="0"
                    id="orden_{{ $item->id_producto }}" value="{{ $item->orden }}">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <img src="{{ url($url_imagen) }}" alt="..."
                    class="img-fluid img-thumbnail mouse-hand imagen_{{ $item->id_producto }}"
                    style="border-radius: 16px;" onclick="$('.imagen_{{ $item->id_producto }}').toggleClass('hidden')">
                <form id="form_edit_producto_{{ $item->id_producto }}"
                    action="{{ url('bodega_productos/update_combo') }}" method="post" enctype="multipart/form-data"
                    class="imagen_{{ $item->id_producto }} hidden">
                    {!! csrf_field() !!}
                    <input type="file" style="width: 100%;" class="text-center bg-yura_dark"
                        id="imagen_{{ $item->id_producto }}" name="imagen_{{ $item->id_producto }}"
                        placeholder="Codigo" accept="image/png, image/jpeg">
                </form>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                <input type="text" style="width: 100%" class="text-center" id="codigo_{{ $item->id_producto }}"
                    value="{{ $item->codigo }}" required>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                <input type="text" style="width: 100%" class="text-center" id="nombre_{{ $item->id_producto }}"
                    value="{{ $item->nombre }}" required>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                {{ $precio_costo }}
            </th>
            <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                <input type="number" style="width: 100%" class="text-center" required min="0"
                    id="precio_venta_{{ $item->id_producto }}" value="{{ $item->precio_venta }}">
                <label for="tiene_iva_{{ $item->id_producto }}" class="mouse-hand">IVA</label>
                <input type="checkbox" id="tiene_iva_{{ $item->id_producto }}"
                    {{ $item->tiene_iva == 1 ? 'checked' : '' }}>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                {{ $margen }}
            </th>
            <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                {{ porcentaje($margen, $precio_costo, 1) }}%
            </th>
            <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-yura_warning"
                        onclick="update_combo('{{ $item->id_producto }}')">
                        <i class="fa fa-fw fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_dark"
                        onclick="admin_combo('{{ $item->id_producto }}')">
                        <i class="fa fa-fw fa-gift"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_danger"
                        onclick="cambiar_estado_producto('{{ $item->id_producto }}', '{{ $item->estado }}')">
                        <i class="fa fa-fw fa-{{ $item->estado == 1 ? 'lock' : 'unlock' }}"></i>
                    </button>
                </div>
            </th>
        </tr>
    @endforeach
</table>

<script>
    function store_combo() {
        if ($('#form_add_producto').valid()) {
            $.LoadingOverlay('show');
            formulario = $('#form_add_producto');
            var formData = new FormData(formulario[0]);
            formData.append('codigo', $('#codigo_new').val());
            formData.append('nombre', $('#nombre_new').val());
            formData.append('precio_venta', $('#precio_venta_new').val());
            formData.append('orden', $('#orden_new').val());
            formData.append('disponibles', 0);
            //hacemos la petición ajax
            $.ajax({
                url: formulario.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                //necesario para subir archivos via ajax
                cache: false,
                contentType: false,
                processData: false,

                success: function(retorno2) {
                    if (retorno2.success) {
                        mini_alerta('success', retorno2.mensaje, 5000);
                        cerrar_modals();
                        listar_reporte();
                    } else {
                        alerta(retorno2.mensaje);
                    }
                    $.LoadingOverlay('hide');
                },
                //si ha ocurrido un error
                error: function(retorno2) {
                    console.log(retorno2);
                    alerta(retorno2.responseText);
                    alert('Hubo un problema en la envío de la información');
                    $.LoadingOverlay('hide');
                }
            });
        }
    }

    function update_combo(id) {
        if ($('#form_edit_producto_' + id).valid()) {
            $.LoadingOverlay('show');
            formulario = $('#form_edit_producto_' + id);
            var formData = new FormData(formulario[0]);
            formData.append('codigo', $('#codigo_' + id).val());
            formData.append('nombre', $('#nombre_' + id).val());
            formData.append('precio_venta', $('#precio_venta_' + id).val());
            formData.append('tiene_iva', $('#tiene_iva_' + id).prop('checked'));
            formData.append('orden', $('#orden_' + id).val());
            formData.append('id', id);
            //hacemos la petición ajax
            $.ajax({
                url: formulario.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                //necesario para subir archivos via ajax
                cache: false,
                contentType: false,
                processData: false,

                success: function(retorno2) {
                    if (retorno2.success) {
                        mini_alerta('success', retorno2.mensaje, 5000);
                        cerrar_modals();
                        listar_reporte();
                        //listar_reporte();
                    } else {
                        alerta(retorno2.mensaje);
                    }
                    $.LoadingOverlay('hide');
                },
                //si ha ocurrido un error
                error: function(retorno2) {
                    console.log(retorno2);
                    alerta(retorno2.responseText);
                    alert('Hubo un problema en la envío de la información');
                    $.LoadingOverlay('hide');
                }
            });
        }
    }

    function cambiar_estado_producto(p, estado) {
        mensaje = {
            title: estado == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar combo' :
                '<i class="fa fa-fw fa-unlock"></i> Activar combo',
            mensaje: estado == 1 ?
                '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este combo?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este combo?</div>',
        };
        modal_quest('modal_delete_producto', mensaje['mensaje'], mensaje['title'], true, false,
            '{{ isPC() ? '45%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    id: p,
                };
                post_jquery_m('{{ url('bodega_productos/cambiar_estado_producto') }}', datos, function() {
                    cerrar_modals();
                    listar_reporte();
                });
            });
    }

    function admin_combo(id) {
        datos = {
            id: id
        }
        get_jquery('{{ url('bodega_productos/admin_combo') }}', datos, function(retorno) {
            modal_view('modal_admin_combo', retorno, '<i class="fa fa-fw fa-plus"></i> Formulario Cosecha',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        })
    }
</script>

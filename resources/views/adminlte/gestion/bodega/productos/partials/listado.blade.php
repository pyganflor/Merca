<div style="overflow-y: scroll; overflow-x: scroll; height: 700px;">
    <table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr id="tr_fija_top_0">
            <th class="text-center th_yura_green" style="width: 130px">
                IMAGEN
            </th>
            <th class="text-center th_yura_green" style="width: 150px">
                CATEGORIA
                <input type="text" style="width: 100%; color: black" class="text-center" placeholder="Nueva"
                    onchange="store_categoria()" id="new_nombre_categoria">
            </th>
            <th class="text-center th_yura_green" style="width: 110px">
                CODIGO
            </th>
            <th class="text-center th_yura_green">
                NOMBRE
            </th>
            <th class="text-center th_yura_green" style="width: 60px">
                UM
            </th>
            <th class="text-center th_yura_green" style="width: 60px">
                STOCK MINIMO
            </th>
            <th class="text-center th_yura_green" style="width: 60px">
                CONVERSION
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
            <th class="text-center th_yura_green" style="width: 60px">
                <button type="button" class="btn btn-xs btn-yura_default"
                    onclick="$('#tr_new_producto').removeClass('hidden'); $('#codigo_new').focus()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </th>
        </tr>
        <tr id="tr_new_producto" class="hidden">
            <th class="text-center" style="border-color: #9d9d9d">
                <form id="form_add_producto" action="{{ url('bodega_productos/store_producto') }}" method="post"
                    enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input type="file" style="width: 100%;" class="text-center bg-yura_dark" id="imagen_new"
                        name="imagen_new" placeholder="Codigo" accept="image/png, image/jpeg">
                </form>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <select id="categoria_new" style="width: 100%; height: 26px;">
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->id_categoria_producto }}">
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="text" style="width: 100%" class="text-center bg-yura_dark" id="codigo_new"
                    name="codigo_new" placeholder="Codigo" required>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="text" style="width: 100%" class="text-center bg-yura_dark" id="nombre_new"
                    name="nombre_new" placeholder="NOMBRE" required>
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="text" style="width: 100%" class="text-center bg-yura_dark" required min="0"
                    id="unidad_medida_new" name="unidad_medida_new" placeholder="0">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" style="width: 100%" class="text-center bg-yura_dark" required min="0"
                    id="stock_minimo_new" name="stock_minimo_new" placeholder="0">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" style="width: 100%" class="text-center bg-yura_dark" required min="0"
                    id="conversion_new" name="conversion_new" placeholder="0">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" style="width: 100%" class="text-center bg-yura_dark" required min="0"
                    id="precio_compra_new" name="precio_compra_new" placeholder="0">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="number" style="width: 100%" class="text-center bg-yura_dark" required min="0"
                    id="precio_venta_new" name="precio_venta_new" placeholder="0">
            </th>
            <th class="text-center" style="border-color: #9d9d9d" colspan="3">
                <button type="button" class="btn btn-xs btn-yura_primary" onclick="store_producto()">
                    <i class="fa fa-fw fa-save"></i>
                </button>
            </th>
        </tr>
        @foreach ($listado as $item)
            @php
                $url_imagen = 'images\productos\*' . $item->imagen;
                $url_imagen = str_replace('*', '', $url_imagen);
            @endphp
            <tr id="tr_producto_{{ $item->id_producto }}" class="{{ $item->estado == 0 ? 'error' : '' }}">
                <th class="text-center" style="border-color: #9d9d9d">
                    <img src="{{ url($url_imagen) }}" alt="..."
                        class="img-fluid img-thumbnail mouse-hand imagen_{{ $item->id_producto }}"
                        style="border-radius: 16px"
                        onclick="$('.imagen_{{ $item->id_producto }}').toggleClass('hidden')">
                    <form id="form_edit_producto_{{ $item->id_producto }}"
                        action="{{ url('bodega_productos/update_producto') }}" method="post"
                        enctype="multipart/form-data" class="imagen_{{ $item->id_producto }} hidden">
                        {!! csrf_field() !!}
                        <input type="file" style="width: 100%;" class="text-center bg-yura_dark"
                            id="imagen_{{ $item->id_producto }}" name="imagen_{{ $item->id_producto }}"
                            placeholder="Codigo" accept="image/png, image/jpeg">
                    </form>
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <select id="categoria_{{ $item->id_producto }}" style="width: 100%; height: 26px;">
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id_categoria_producto }}"
                                {{ $cat->id_categoria_producto == $item->id_categoria_producto ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <input type="text" style="width: 100%" class="text-center"
                        id="codigo_{{ $item->id_producto }}" value="{{ $item->codigo }}" required>
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <input type="text" style="width: 100%" class="text-center"
                        id="nombre_{{ $item->id_producto }}" value="{{ $item->nombre }}" required>
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <input type="text" style="width: 100%" class="text-center"
                        id="unidad_medida_{{ $item->id_producto }}" value="{{ $item->unidad_medida }}" required>
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="stock_minimo_{{ $item->id_producto }}" value="{{ $item->stock_minimo }}">
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="conversion_{{ $item->id_producto }}" value="{{ $item->conversion }}">
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="precio_compra_{{ $item->id_producto }}" value="{{ $item->precio }}">
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <input type="number" style="width: 100%" class="text-center" required min="0"
                        id="precio_venta_{{ $item->id_producto }}" value="{{ $item->precio_venta }}">
                    <label for="tiene_iva_{{ $item->id_producto }}" class="mouse-hand">IVA</label>
                    <input type="checkbox" id="tiene_iva_{{ $item->id_producto }}"
                        {{ $item->tiene_iva == 1 ? 'checked' : '' }}>
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    {{ $item->precio_venta - $item->precio }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    {{ porcentaje($item->precio_venta - $item->precio, $item->precio, 1) }}%
                </th>
                <th class="text-center" style="border-color: #9d9d9d; vertical-align: top">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-yura_warning"
                            onclick="update_producto('{{ $item->id_producto }}')">
                            <i class="fa fa-fw fa-edit"></i>
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
</div>

<script>
    function store_producto() {
        if ($('#form_add_producto').valid()) {
            $.LoadingOverlay('show');
            formulario = $('#form_add_producto');
            var formData = new FormData(formulario[0]);
            formData.append('categoria', $('#categoria_new').val());
            formData.append('codigo', $('#codigo_new').val());
            formData.append('nombre', $('#nombre_new').val());
            formData.append('unidad_medida', $('#unidad_medida_new').val());
            formData.append('stock_minimo', $('#stock_minimo_new').val());
            formData.append('conversion', $('#conversion_new').val());
            formData.append('precio_compra', $('#precio_compra_new').val());
            formData.append('precio_venta', $('#precio_venta_new').val());
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

    function update_producto(id) {
        if ($('#form_edit_producto_' + id).valid()) {
            $.LoadingOverlay('show');
            formulario = $('#form_edit_producto_' + id);
            var formData = new FormData(formulario[0]);
            formData.append('categoria', $('#categoria_' + id).val());
            formData.append('codigo', $('#codigo_' + id).val());
            formData.append('nombre', $('#nombre_' + id).val());
            formData.append('unidad_medida', $('#unidad_medida_' + id).val());
            formData.append('stock_minimo', $('#stock_minimo_' + id).val());
            formData.append('conversion', $('#conversion_' + id).val());
            formData.append('precio_compra', $('#precio_compra_' + id).val());
            formData.append('precio_venta', $('#precio_venta_' + id).val());
            formData.append('tiene_iva', $('#tiene_iva_' + id).prop('checked'));
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
                        location.reload(true);
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
            title: estado == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar producto' :
                '<i class="fa fa-fw fa-unlock"></i> Activar producto',
            mensaje: estado == 1 ?
                '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este producto?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este producto?</div>',
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

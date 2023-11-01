<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr>
        <td class="text-center" colspan="7">
            <div class="input-group" style="font-size: 1em;">
                <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                    <b id="html_unidades_restantes_selected">{{ $unidades }}</b>
                    Piezas de
                    <b>{{ $producto->nombre }}</b> restantes a
                </span>
                <input type="number" id="precio_venta_selected" style="width: 100%; height: 28px; color: black"
                    value="{{ $producto->precio_venta }}" class="form-control text-center">
                <span class="input-group-addon bg-yura_dark">
                    / {{ $producto->unidad_medida }}
                </span>
            </div>
        </td>
    </tr>
    <tr>
        <th class="padding_lateral_5 bg-yura_dark">
            Pedido
        </th>
        <th class="padding_lateral_5 bg-yura_dark">
            Finca
        </th>
        <th class="padding_lateral_5 bg-yura_dark">
            Usuario
        </th>
        <th class="padding_lateral_5 bg-yura_dark" style="width: 60px">
            Cantidad
        </th>
        <th class="text-center bg-yura_dark" style="width: 60px">
            Usados
        </th>
        <th class="text-center bg-yura_dark" style="width: 60px">
            Peso
        </th>
        <th class="padding_lateral_5 bg-yura_dark">
            Opciones
        </th>
    </tr>
    @foreach ($listado as $pos => $item)
        @php
            $usados = count($item['det_ped']->etiquetas_peso);
        @endphp
        <tr style="background-color: {{ $pos % 2 == 0 ? 'azure' : '' }}">
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['pedido']->id_pedido_bodega }}
            </td>
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['pedido']->empresa->nombre }}
            </td>
            <td class="padding_lateral_5" style="border-color: #9d9d9d">
                {{ $item['pedido']->usuario->nombre_completo }}
            </td>
            <td style="border-color: #9d9d9d">
                <input type="text" readonly style="width: 100%; background-color: #ffffff"
                    value="{{ $item['det_ped']->cantidad }}" class="text-center"
                    id="cantidad_{{ $item['det_ped']->id_detalle_pedido_bodega }}">
            </td>
            <td style="border-color: #9d9d9d">
                <input type="text" readonly style="width: 100%; background-color: #dddddd"
                    value="{{ $usados }}" class="text-center"
                    id="usados_{{ $item['det_ped']->id_detalle_pedido_bodega }}">
            </td>
            <td style="border-color: #9d9d9d">
                <input type="text" style="width: 100%" class="text-center"
                    id="peso_{{ $item['det_ped']->id_detalle_pedido_bodega }}" min="0">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    @if ($usados < $item['det_ped']->cantidad)
                        <button type="button" class="btn btn-xs btn-yura_dark" title="Etiquetar"
                            id="btn_etiquetar_{{ $item['det_ped']->id_detalle_pedido_bodega }}"
                            onclick="store_etiqueta('{{ $item['det_ped']->id_detalle_pedido_bodega }}')">
                            <i class="fa fa-fw fa-barcode"></i>
                        </button>
                    @endif
                    <button type="button" class="btn btn-xs btn-yura_default" title="Ver Etiquetas"
                        onclick="ver_etiquetas('{{ $item['det_ped']->id_detalle_pedido_bodega }}')">
                        <i class="fa fa-fw fa-eye"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
</table>
<input type="hidden" id="id_inventario_selected" value="{{ $inv_bod->id_inventario_bodega }}">
<input type="hidden" id="unidades_restantes_selected" value="{{ $unidades }}">

<script>
    function store_etiqueta(det_ped) {
        mensaje = {
            title: '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de confirmacion',
            mensaje: '<div class="alert alert-info text-center" style="font-size: 16px">¿Está seguro de <b>CREAR</b> la etiqueta?</div>',
        };
        modal_quest('modal_store_etiqueta', mensaje['mensaje'], mensaje['title'], true, false,
            '{{ isPC() ? '50%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    det_ped: det_ped,
                    id_inv: parseInt($('#id_inventario_selected').val()),
                    peso: parseFloat($('#peso_' + det_ped).val()),
                    precio_venta: parseFloat($('#precio_venta_selected').val()),
                };
                unidades_restantes = parseInt($('#unidades_restantes_selected').val());
                if (datos['peso'] > 0) {
                    if (unidades_restantes > 0) {
                        $.LoadingOverlay('show');
                        $.post('{{ url('etiquetar_peso/store_etiqueta') }}', datos, function(retorno) {
                            if (retorno.success) {
                                mini_alerta('success', retorno.mensaje, 5000);

                                unidades_restantes--;
                                $('#unidades_restantes_selected').val(unidades_restantes);
                                $('#inventario_disponibles_' + datos['id_inv']).val(unidades_restantes);
                                $('#html_unidades_restantes_selected').html(unidades_restantes);
                                cantidad = parseInt($('#cantidad_' + det_ped).val());
                                usados = parseInt($('#usados_' + det_ped).val());
                                usados++;
                                $('#usados_' + det_ped).val(usados);
                                $('#peso_' + det_ped).val('');

                                if (usados >= cantidad)
                                    $('#btn_etiquetar_' + det_ped).addClass('hidden');

                                imprimir_etiqueta(retorno.id);
                            } else {
                                alerta(retorno.mensaje);
                            }
                        }, 'json').fail(function(retorno) {
                            console.log(retorno);
                            alerta_errores(retorno.responseText);
                        }).always(function() {
                            $.LoadingOverlay('hide');
                        });
                    } else {
                        alerta('<div class="alert alert-danger text-center">No hay mas piezas disponibles</div>');
                    }
                } else {
                    alerta('<div class="alert alert-danger text-center">Es necesario el PESO</div>');
                }
            });
    }

    function imprimir_etiqueta(id) {
        $.LoadingOverlay('show');
        window.open('{{ url('etiquetar_peso/imprimir_etiqueta') }}?id=' + id, '_blank');
        $.LoadingOverlay('hide');
    }

    function ver_etiquetas(det_ped) {
        datos = {
            det_ped: det_ped,
        }
        get_jquery('{{ url('etiquetar_peso/ver_etiquetas') }}', datos, function(retorno) {
            modal_view('modal_ver_etiquetas', retorno,
                '<i class="fa fa-fw fa-shopping-cart"></i> Etiquetas del Pedido',
                true, false, '{{ isPC() ? '75%' : '' }}',
                function() {});
        });
    }
</script>

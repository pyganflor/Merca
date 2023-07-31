<input type="hidden" id="id_caja" value="{{ $caja->id_caja_frio }}">
<div style="overflow-x: scroll">
    <table style="width: 100%">
        <tr>
            <td style="vertical-align: top; width: 50%; min-width: 550px; padding-right: 5px;">
                <div class="panel panel-success" style="margin-bottom: 0px; min-width: 550px;" id="panel_inventarios">
                    <div class="panel-heading"
                        style="display: flex; justify-content: space-between; align-items: center;">
                        <b> <i class="fa fa-gift"></i> CONTENIDO DE LA CAJA </b>
                    </div>
                    <div class="panel-body" style="max-height: 450px; overflow-y: scroll">
                        <div class="text-center">
                            <div class="input-group">
                                <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                    Nombre
                                </div>
                                <input type="text" id="nombre_caja" required="" class="form-control text-center"
                                    style="width: 100% !important;" placeholder="Nombre de Caja"
                                    value="{{ $caja->nombre }}">
                                <div class="input-group-addon bg-yura_dark">
                                    Tipo
                                </div>
                                <select id="tipo_caja" required class="form-control"
                                    style="min-width: 80px !important;">
                                    <option value="QB" {{ $caja->tipo == 'QB' ? 'selected' : '' }}>QB</option>
                                    <option value="HB" {{ $caja->tipo == 'HB' ? 'selected' : '' }}>HB</option>
                                    <option value="EB" {{ $caja->tipo == 'EB' ? 'selected' : '' }}>EB</option>
                                </select>
                                <div class="input-group-addon bg-yura_dark">
                                    Fecha
                                </div>
                                <input type="date" id="fecha_caja" required=""
                                    class="form-control input-yura_default text-center" value="{{ $caja->fecha }}"
                                    style="width: 100% !important;" placeholder="Fecha de Armado">
                            </div>
                        </div>
                        <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d; margin-top: 5px">
                            <tbody>
                                <tr>
                                    <th class="text-center th_yura_green">
                                        Variedad
                                    </th>
                                    <th class="text-center th_yura_green">
                                        Longitud
                                    </th>
                                    <th class="text-center th_yura_green">
                                        Edad
                                    </th>
                                    <th class="text-center th_yura_green">
                                        Tallos
                                    </th>
                                    <th class="text-center th_yura_green">
                                        Ramos
                                    </th>
                                    <th class="text-center th_yura_green" style="width: 80px">
                                        <button type="button" class="btn btn-xs btn-yura_dark"
                                            onclick="add_detalle({{ $caja->id_caja_frio }})">
                                            <i class="fa fa-fw fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </tbody>
                            <tbody id="table_caja">
                                @php
                                    $getTotales = $caja->getTotales();
                                    $total_tallos = $getTotales->tallos;
                                    $total_ramos = $getTotales->ramos;
                                @endphp
                                @foreach ($caja->detalles as $det)
                                    <tr onmouseover="$(this).addClass('bg-yura_dark')"
                                        onmouseleave="$(this).removeClass('bg-yura_dark')">
                                        <th class="text-center" style="border-color: #9d9d9d">
                                            {{ $det->variedad->nombre }}
                                        </th>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <b>{{ $det->longitud }}</b> <sup>cm</sup>
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <b>{{ difFechas(hoy(), $det->fecha)->days }}</b> <sup>dias</sup>
                                        </td>
                                        <th class="text-center" style="border-color: #9d9d9d">
                                            {{ $det->tallos_x_ramo * $det->ramos }}
                                        </th>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <b>{{ $det->ramos }}</b> <sup>x{{ $det->tallos_x_ramo }}</sup>
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-yura_warning btn-xs"
                                                    onclick="cambiar_caja('{{ $det->id_detalle_caja_frio }}')">
                                                    <i class="fa fa-fw fa-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-yura_danger btn-xs"
                                                    onclick="eliminar_detalle('{{ $det->id_detalle_caja_frio }}')">
                                                    <i class="fa fa-fw fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tbody>
                                <tr>
                                    <th class="text-center th_yura_green" colspan="3">
                                        Totales
                                    </th>
                                    <th class="text-center th_yura_green">
                                        {{ $total_tallos }}
                                    </th>
                                    <th class="text-center th_yura_green">
                                        {{ $total_ramos }}
                                    </th>
                                    <th class="text-center th_yura_green">
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="button" class="btn btn-yura_primary"
                            onclick="update_caja('{{ $caja->id_caja_frio }}')">
                            <i class="fa fa-fw fa-save"></i> GRABAR CAMBIOS
                        </button>
                    </div>
                </div>
            </td>
            <td style="vertical-align: top; min-width: 450px; padding-right: 5px">
                <div class="panel panel-success" style="margin-bottom: 0px; min-width: 550px;" id="panel_inventarios">
                    <div class="panel-heading"
                        style="display: flex; justify-content: space-between; align-items: center;">
                        <b> <i class="fa fa-gears"></i> OPCIONES </b>

                    </div>
                    <div class="panel-body" id="body_contenido_caja" style="max-height: 500px">
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

<script>
    function eliminar_detalle(det) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 1.5em'>Esta a punto de <b>ELIMINAR</b> la FLOR de la CAJA</div>" +
            "<div class='alert alert-info text-center' style='font-size: 1.5em'>¿Desea devolver el contenido al inventario de cuarto frio?" +
            "<div class='row'>" +
            "<div class='col-md-6'>" +
            "<label class='mouse-hand' for='radio_devolver_detalle0'>No</label>" +
            "<input type='radio' name='radio_devolver_detalle' id='radio_devolver_detalle0' value='0' style='width: 20px; height: 20px'>" +
            "</div>" +
            "<div class='col-md-6'>" +
            "<input type='radio' name='radio_devolver_detalle' id='radio_devolver_detalle1' value='1' style='width: 20px; height: 20px' checked>" +
            "<label class='mouse-hand' for='radio_devolver_detalle1'>Si</label>" +
            "</div>" +
            "</div>" +
            "</div>";

        modal_quest('modal_eliminar_pedido', texto, 'Eliminar flor de la caja', true, false,
            '{{ isPC() ? '50%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    det: det,
                    devolver: $('#radio_devolver_detalle1').prop('checked') == true ? 1 : 0,
                };
                post_jquery_m('inventario_cajas/eliminar_detalle', datos, function() {
                    cerrar_modals();
                    editar_caja('{{ $caja->id_caja_frio }}');
                });

            })
    }

    function cambiar_caja(det) {
        datos = {
            det: det
        }
        get_jquery('{{ url('inventario_cajas/cambiar_caja') }}', datos, function(retorno) {
            $('#body_contenido_caja').html(retorno);
        });
    }

    function add_detalle(caja) {
        datos = {
            caja: caja
        }
        get_jquery('{{ url('inventario_cajas/add_detalle') }}', datos, function(retorno) {
            $('#body_contenido_caja').html(retorno);
        });
    }
</script>

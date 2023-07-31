<div style="overflow-x: scroll; overflow-y: scroll; max-height: 550px">
    <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr class="tr_fija_top_0">
            <th class="text-center th_yura_green">
                CAJA
            </th>
            <th class="text-center th_yura_green">
                Variedad
            </th>
            <th class="text-center th_yura_green">
                Longitud
            </th>
            <th class="text-center th_yura_green" colspan="2">
                Tallos
            </th>
            <th class="text-center th_yura_green" colspan="2">
                Ramos
            </th>
            <th class="text-center th_yura_green" style="width: 80px">
            </th>
        </tr>
        @foreach ($listado as $pos_c => $item)
            @php
                $tallos_caja = $item->getTotales()->tallos;
                $ramos_caja = $item->getTotales()->ramos;
            @endphp
            @foreach ($item->detalles as $pos_d => $det)
                <tr class="tr_caja_{{ $item->id_caja_frio }}"
                    onmouseover="$('.tr_caja_{{ $item->id_caja_frio }}').addClass('bg-yura_dark')"
                    onmouseleave="$('.tr_caja_{{ $item->id_caja_frio }}').removeClass('bg-yura_dark')">
                    @if ($pos_d == 0)
                        <th class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($item->detalles) }}">
                            {{ $item->nombre }}
                            <br>
                            {{ $item->tipo }}
                            <br>
                            {{ convertDateToText($item->fecha) }}
                        </th>
                    @endif
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $det->variedad->nombre }}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $det->longitud }} <sup>cm</sup>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{ $det->tallos_x_ramo * $det->ramos }}
                    </td>
                    @if ($pos_d == 0)
                        <td class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($item->detalles) }}">
                            {{ $tallos_caja }}
                        </td>
                    @endif
                    <td class="text-center" style="border-color: #9d9d9d">
                        <b>{{ $det->ramos }}</b> <sup>x{{ $det->tallos_x_ramo }}</sup>
                    </td>
                    @if ($pos_d == 0)
                        <td class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($item->detalles) }}">
                            {{ $ramos_caja }}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d" rowspan="{{ count($item->detalles) }}">
                            <div class="btn-group">
                                <button type="button" class="btn btn-yura_warning btn-xs"
                                    onclick="editar_caja('{{ $item->id_caja_frio }}')">
                                    <i class="fa fa-fw fa-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-yura_danger btn-xs"
                                    onclick="eliminar_caja('{{ $item->id_caja_frio }}')">
                                    <i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    </table>
</div>

<script>
    function eliminar_caja(caja) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 1.5em'>Esta a punto de <b>ELIMINAR</b> la Caja</div>" +
            "<div class='alert alert-info text-center' style='font-size: 1.5em'>¿Desea devolver el contenido al inventario de cuarto frio?" +
            "<div class='row'>" +
            "<div class='col-md-6'>" +
            "<label class='mouse-hand' for='radio_devolver0'>No</label>" +
            "<input type='radio' name='radio_devolver' id='radio_devolver0' value='0' style='width: 20px; height: 20px'>" +
            "</div>" +
            "<div class='col-md-6'>" +
            "<input type='radio' name='radio_devolver' id='radio_devolver1' value='1' style='width: 20px; height: 20px' checked>" +
            "<label class='mouse-hand' for='radio_devolver1'>Si</label>" +
            "</div>" +
            "</div>" +
            "</div>";

        modal_quest('modal_eliminar_pedido', texto, 'Eliminar caja', true, false, '50%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                caja: caja,
                devolver: $('#radio_devolver1').prop('checked') == true ? 1 : 0,
            };
            post_jquery_m('inventario_cajas/eliminar_caja', datos, function() {
                cerrar_modals();
                listar_reporte();
            });

        })
    }

    function editar_caja(caja) {
        datos = {
            caja: caja
        }
        get_jquery('{{ url('inventario_cajas/editar_caja') }}', datos, function(retorno) {
            modal_view('modal_editar_caja', retorno, '<i class="fa fa-fw fa-plus"></i> Formulario Caja',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        });
    }

    function verificar_disponibles() {
        ramos = parseInt($('#ramos_cambiar').val());
        disponibles = parseInt($('#ramos_cambiar').prop('max'));
        if (ramos > disponibles || ramos < 1)
            $('#ramos_cambiar').val(disponibles);
    }

    function store_cambiar_caja() {
        caja_model = $('#id_caja').val();
        det = $('#id_detalle').val();
        ramos = parseInt($('#ramos_cambiar').val());
        caja = $('#caja_cambiar').val();

        datos = {
            _token: '{{ csrf_token() }}',
            det: det,
            ramos: ramos,
            caja: caja,
        };
        post_jquery_m('inventario_cajas/store_cambiar_caja', datos, function() {
            cerrar_modals();
            editar_caja(caja_model);
            listar_reporte();
        });
    }

    function agregar_a_caja(inv) {
        nombre_variedad = $('#scan_nombre_variedad').val();
        longitud = $('#scan_longitud').val();
        tallos_x_ramo = $('#scan_tallos_x_ramo').val();
        disponibles = $('#scan_disponibles').val();
        edad = $('#scan_edad').val();
        id_inv = inv;
        existe = $('#new_id_inventario_frio_' + id_inv).val();
        if (existe != undefined) {
            ramos = parseInt($('#new_ramos_' + id_inv).val());
            ramos++;
            if (ramos <= $('#new_ramos_' + id_inv).prop('max')) {
                $('#new_ramos_' + id_inv).val(ramos)
            } else {
                alerta(
                    '<div class="alert alert-warning text-center">La cantidad <b>INGRESADA</b> supera los ramos <b>DISPONIBLES</b></div>'
                );
            }
        } else {
            $('#table_caja').append('<tr id="new_tr_' + id_inv + '">' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                nombre_variedad +
                '<input type="hidden" value="' + id_inv + '" class="new_id_inventario_frio">' +
                '<input type="hidden" value="' + id_inv + '" id="new_id_inventario_frio_' + id_inv + '">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                longitud + ' <sup>cm</sup>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                tallos_x_ramo +
                '<input type="hidden" value="' + tallos_x_ramo + '" id="new_tallos_x_ramo_' + id_inv + '">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                edad + ' <sup>dias</sup>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" value="1" min="1" max="' + disponibles +
                '" style="width: 100%" class="text-center" id="new_ramos_' + id_inv +
                '" onchange="calcular_totales_caja()">' +
                '<input type="hidden" value="' + disponibles + '" id="new_disponibles_' + id_inv + '">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<button type="button" class="btn btn-xs btn-yura_danger" onclick="eliminar_fila_caja(' + id_inv +
                ')">' +
                '<i class="fa fa-fw fa-trash"></i>' +
                '</button>' +
                '</td>' +
                '</tr>');
        }
        //calcular_totales_caja();
    }

    function eliminar_fila_caja(id) {
        $('#new_tr_' + id).remove();
        calcular_totales_caja();
    }

    function update_caja(caja) {
        new_id_inventario_frio = $('.new_id_inventario_frio');
        data = [];
        for (i = 0; i < new_id_inventario_frio.length; i++) {
            id_inv = new_id_inventario_frio[i].value;
            ramos = parseInt($('#new_ramos_' + id_inv).val());
            disponibles = parseInt($('#new_disponibles_' + id_inv).val());
            if (ramos > 0 && ramos <= disponibles) {
                data.push({
                    id_inv: id_inv,
                    ramos: ramos,
                });
            } else {
                alerta(
                    '<div class="alert alert-warning text-center">La cantidad <b>INGRESADA</b> supera los ramos <b>DISPONIBLES</b></div>'
                );
                return false;
            }
        }
        datos = {
            _token: '{{ csrf_token() }}',
            nombre: $('#nombre_caja').val(),
            fecha: $('#fecha_caja').val(),
            tipo: $('#tipo_caja').val(),
            data: JSON.stringify(data),
            caja: caja
        }
        post_jquery_m('{{ url('inventario_cajas/update_caja') }}', datos, function() {
            cerrar_modals();
            editar_caja(caja);
            listar_reporte();
        });
    }
</script>

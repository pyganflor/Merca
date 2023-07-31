<script>
    $('#vista_actual').val('armado_cajas');
    var customLoading = $("<p>", {
        "css": {
            "font-size": "2em",
            "text-align": "center",
            "margin-top": "7px",
            "color": "white",
        },
        "text": "ESPERANDO_LECTURA"
    });
    setTimeout(() => {
        $('#filtro_codigo_barra').focus();
    }, 500);

    function escanear_codigo(consulta = false, codigo = '') {
        datos = {
            codigo: codigo == '' ? $('#filtro_codigo_barra').val() : codigo,
            consulta: consulta,
        }
        get_jquery('{{ url('armado_cajas/escanear_codigo') }}', datos, function(retorno) {
            $('#body_escaneado').html(retorno);
            $('#filtro_codigo_barra').val('');
            $('#filtro_codigo_barra').focus();
        }, 'body_escaneado');
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
                '<a href="javascript:void(0)" onclick="escanear_codigo(' + true + ', ' + id_inv +
                ')" style="color: black">' +
                '<sup><i class="fa fa-fw fa-eye"></i></sup>' +
                nombre_variedad +
                '</a>' +
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
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" disabled value="' + disponibles +
                '" style="width: 100%" class="text-center" id="new_disponibles_' + id_inv + '">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<button type="button" class="btn btn-xs btn-yura_danger" onclick="eliminar_fila_caja(' + id_inv +
                ')">' +
                '<i class="fa fa-fw fa-trash"></i>' +
                '</button>' +
                '</td>' +
                '</tr>');
        }
        calcular_totales_caja();
    }

    var input_scan = document.getElementById("filtro_codigo_barra");
    input_scan.addEventListener("focus", myFocusFunction, true);
    input_scan.addEventListener("blur", myBlurFunction, true);

    function myFocusFunction() {
        $("#filtro_codigo_barra").LoadingOverlay("show", {
            image: "",
            custom: customLoading
        });
    }

    function myBlurFunction() {
        $("#filtro_codigo_barra").LoadingOverlay('hide');
    }

    function eliminar_fila_caja(id) {
        $('#new_tr_' + id).remove();
        calcular_totales_caja();
    }

    function calcular_totales_caja() {
        new_id_inventario_frio = $('.new_id_inventario_frio');
        total_tallos = 0;
        total_ramos = 0;
        for (i = 0; i < new_id_inventario_frio.length; i++) {
            id_inv = new_id_inventario_frio[i].value;
            tallos_x_ramo = parseInt($('#new_tallos_x_ramo_' + id_inv).val());
            ramos = parseInt($('#new_ramos_' + id_inv).val());
            disponibles = parseInt($('#new_disponibles_' + id_inv).val());
            if (ramos > 0 && ramos <= disponibles) {
                total_tallos += tallos_x_ramo * ramos;
                total_ramos += ramos;
            } else {
                alerta(
                    '<div class="alert alert-warning text-center">La cantidad <b>INGRESADA</b> debe ser menor que los ramos <b>DISPONIBLES</b></div>'
                );
                $('#new_ramos_' + id_inv).val(1);
                calcular_totales_caja()
            }
        }
        $('#td_total_tallos_caja').html(total_tallos);
        $('#td_total_ramos_caja').html(total_ramos);
    }

    function store_caja() {
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
        }
        if (datos['tipo'] != '' && datos['fecha'] != '' && datos['nombre'] != '' && data.length > 0)
            post_jquery_m('{{ url('armado_cajas/store_caja') }}', datos, function() {
                cargar_url('armado_cajas');
            });
    }
</script>

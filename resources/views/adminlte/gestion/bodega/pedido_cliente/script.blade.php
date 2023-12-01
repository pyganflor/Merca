<script>
    function listar_reporte() {
        datos = {
            entrega: $('#filtro_entrega').val(),
            finca: $('#filtro_finca').val(),
        };
        get_jquery('{{ url('pedido_bodega/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
        }, 'div_listado');
    }

    /* PRODUCTOS */
    function ordenar_menor_precio(cat) {
        var $fila = $("#tr_productos_" + cat);
        var $tds = $fila.children('td');

        $tds.detach().sort(function(a, b) {
            var aValue = parseFloat($(a).data('precio')) || 0;
            var bValue = parseFloat($(b).data('precio')) || 0;

            // Ordenar de menor a mayor
            return aValue - bValue;
        });

        $fila.append($tds);
    }

    function ordenar_mayor_precio(cat) {
        var $fila = $("#tr_productos_" + cat);
        var $tds = $fila.children('td');

        $tds.detach().sort(function(a, b) {
            var aValue = parseFloat($(a).data('precio')) || 0;
            var bValue = parseFloat($(b).data('precio')) || 0;

            // Ordenar de mayor a menor
            return bValue - aValue;
        });

        $fila.append($tds);
    }

    function ordenar_menor_nombre(cat) {
        var $fila = $("#tr_productos_" + cat);
        var $tds = $fila.children('td');

        $tds.detach().sort(function(a, b) {
            var aValue = $(a).data('nombre') || '';
            var bValue = $(b).data('nombre') || '';

            // Ordenar de menor a mayor
            return aValue.localeCompare(bValue);
        });

        $fila.append($tds);
    }

    function ordenar_mayor_nombre(cat) {
        var $fila = $("#tr_productos_" + cat);
        var $tds = $fila.children('td');

        $tds.detach().sort(function(a, b) {
            var aValue = $(a).data('nombre') || '';
            var bValue = $(b).data('nombre') || '';

            // Ordenar de mayor a menor
            return bValue.localeCompare(aValue);
        });

        $fila.append($tds);
    }

    function filtrar_nombre_prod(cat) {
        var filtro = $("#filtro_nombre_prod_" + cat).val().toLowerCase();

        $("#tr_productos_" + cat + " td").each(function() {
            var tdTexto = $(this).data('nombre').toLowerCase() || '';
            var mostrar = tdTexto.includes(filtro);

            // Mostrar u ocultar según la condición
            $(this).toggle(mostrar);
        });
    };

    /* COMBOS */
    function ordenar_menor_precio_combo(cat) {
        var $fila = $("#tr_combos_" + cat);
        var $tds = $fila.children('td');

        $tds.detach().sort(function(a, b) {
            var aValue = parseFloat($(a).data('precio')) || 0;
            var bValue = parseFloat($(b).data('precio')) || 0;

            // Ordenar de menor a mayor
            return aValue - bValue;
        });

        $fila.append($tds);
    }

    function ordenar_mayor_precio_combo(cat) {
        var $fila = $("#tr_combos_" + cat);
        var $tds = $fila.children('td');

        $tds.detach().sort(function(a, b) {
            var aValue = parseFloat($(a).data('precio')) || 0;
            var bValue = parseFloat($(b).data('precio')) || 0;

            // Ordenar de mayor a menor
            return bValue - aValue;
        });

        $fila.append($tds);
    }

    function ordenar_menor_nombre_combo(cat) {
        var $fila = $("#tr_combos_" + cat);
        var $tds = $fila.children('td');

        $tds.detach().sort(function(a, b) {
            var aValue = $(a).data('nombre') || '';
            var bValue = $(b).data('nombre') || '';

            // Ordenar de menor a mayor
            return aValue.localeCompare(bValue);
        });

        $fila.append($tds);
    }

    function ordenar_mayor_nombre_combo(cat) {
        var $fila = $("#tr_combos_" + cat);
        var $tds = $fila.children('td');

        $tds.detach().sort(function(a, b) {
            var aValue = $(a).data('nombre') || '';
            var bValue = $(b).data('nombre') || '';

            // Ordenar de mayor a menor
            return bValue.localeCompare(aValue);
        });

        $fila.append($tds);
    }

    function filtrar_nombre_combo(cat) {
        var filtro = $("#filtro_nombre_combo_" + cat).val().toLowerCase();

        $("#tr_combos_" + cat + " td").each(function() {
            var tdTexto = $(this).data('nombre').toLowerCase() || '';
            var mostrar = tdTexto.includes(filtro);

            // Mostrar u ocultar según la condición
            $(this).toggle(mostrar);
        });
    };

    /* CARRITO */
    function agregar_producto(id) {
        cant = $('#input_catalogo_prod_' + id).val();
        cant++;
        $('#input_catalogo_prod_' + id).val(cant);

        if ($('#td_contenido_pedido_' + id).length == 0) { // es un producto nuevo en el carrito
            td_prod = $('#td_producto_' + id);
            nombre = td_prod.data('nombre');
            precio = td_prod.data('precio');
            url = td_prod.data('url');
            tiene_iva = td_prod.data('tiene_iva');
            peso = td_prod.data('peso');
            texto_iva = tiene_iva == 1 ? '<sup><em>incluye IVA</em></sup>' : '';
            new_td =
                '<td class="padding_lateral_20 text-center td_contenido_pedido" style="width: 150px; vertical-align: top" id="td_contenido_pedido_' +
                id + '" data-precio="' + precio + '" data-nombre="' + nombre + '" data-tiene_iva="' + tiene_iva +
                '" data-id_producto="' + id + '" data-peso="' + peso + '">' +
                '<div style="width: 150px; height: 150px;" class="text-center">' +
                '<img src="' + url + '" alt="..." ' +
                'class="img-fluid img-thumbnail sombra_pequeña" ' +
                'style="border-radius: 16px; max-width: 150px; max-height: 150px">' +
                '</div>' +
                '<legend class="text-center" ' +
                'style="font-size: 1.1em; margin-bottom: 5px; height: 67px">' +
                nombre +
                '</legend>' +
                '<b>' +
                '$' + precio +
                texto_iva +
                '</b>' +
                '<div class="input-group">' +
                '<span class="input-group-btn">' +
                '<button type="button" class="btn btn-yura_dark" onclick="quitar_producto(' + id + ')">' +
                '<i class="fa fa-fw fa-minus"></i>' +
                '</button>' +
                '</span>' +
                '<input type="number" id="input_contenido_prod_' + id + '" readonly ' +
                'style="width: 100%;" class="text-center form-control input_cantidad" value="' + cant + '">' +
                '<span class="input-group-btn">' +
                '<button type="button" class="btn btn-yura_dark" onclick="agregar_producto(' + id + ')">' +
                '<i class="fa fa-fw fa-plus"></i>' +
                '</button>' +
                '</span>' +
                '</div>' +
                '</td>';
            $('#table_contenido_pedido tr').append(new_td);
        } else { // ya existe el producto en el carrito
            $('#input_contenido_prod_' + id).val(cant);
        }
        calcular_totales_pedido();
    }

    function quitar_producto(id) {
        cant = $('#input_catalogo_prod_' + id).val();
        if (cant > 0) {
            cant--;
            $('#input_catalogo_prod_' + id).val(cant);
            $('#input_contenido_prod_' + id).val(cant);
            if (cant == 0) {
                $('#td_contenido_pedido_' + id).remove();
                $('#input_catalogo_prod_' + id).val('');
            }
        }
        calcular_totales_pedido();
    }

    function calcular_totales_pedido() {
        /* AL contado */
        monto_total = 0;
        monto_subtotal = 0;
        monto_total_iva = 0;
        monto_diferido = 0;
        td_contenido_pedido = $('.td_contenido_pedido');
        for (i = 0; i < td_contenido_pedido.length; i++) {
            id_td = td_contenido_pedido[i].id;
            id_prod = $('#' + id_td).data('id_producto');
            precio_venta = $('#' + id_td).data('precio');
            tiene_iva = $('#' + id_td).data('tiene_iva');
            cantidad = $('#input_contenido_prod_' + id_prod).val();
            precio_prod = cantidad * precio_venta;
            if (tiene_iva == true) {
                monto_subtotal += precio_prod / 1.12;
                monto_total_iva += (precio_prod / 1.12) * 0.12;
            } else {
                monto_subtotal += precio_prod;
            }
            monto_total += precio_prod;
        }
        monto_saldo = monto_total;

        monto_total = Math.round(monto_total * 100) / 100;
        monto_subtotal = Math.round(monto_subtotal * 100) / 100;
        monto_total_iva = Math.round(monto_total_iva * 100) / 100;
        monto_diferido = Math.round(monto_diferido * 100) / 100;
        monto_saldo = Math.round(monto_saldo * 100) / 100;
        $('#html_subtotal_al_contado').html('$' + monto_subtotal);
        $('#html_iva_al_contado').html('$' + monto_total_iva);
        $('#html_monto_al_contado').html('$' + monto_total);
        $('#html_diferido_al_contado').html('$' + monto_diferido);
        $('#html_saldo_al_contado').html('$' + monto_saldo);

        /* Una Cuota */
        monto_total = 0;
        monto_subtotal = 0;
        monto_total_iva = 0;
        monto_diferido = 0;
        td_contenido_pedido = $('.td_contenido_pedido');
        for (i = 0; i < td_contenido_pedido.length; i++) {
            id_td = td_contenido_pedido[i].id;
            id_prod = $('#' + id_td).data('id_producto');
            precio_venta = $('#' + id_td).data('precio');
            tiene_iva = $('#' + id_td).data('tiene_iva');
            cantidad = $('#input_contenido_prod_' + id_prod).val();
            precio_prod = cantidad * precio_venta;
            if (tiene_iva == true) {
                monto_subtotal += precio_prod / 1.12;
                monto_total_iva += (precio_prod / 1.12) * 0.12;
            } else {
                monto_subtotal += precio_prod;
            }
            monto_total += precio_prod;
        }
        monto_saldo = monto_total;

        monto_total = Math.round(monto_total * 100) / 100;
        monto_subtotal = Math.round(monto_subtotal * 100) / 100;
        monto_total_iva = Math.round(monto_total_iva * 100) / 100;
        monto_diferido = Math.round(monto_diferido * 100) / 100;
        monto_saldo = Math.round(monto_saldo * 100) / 100;
        $('#html_subtotal_una_cuota').html('$' + monto_subtotal);
        $('#html_iva_una_cuota').html('$' + monto_total_iva);
        $('#html_monto_una_cuota').html('$' + monto_total);
        $('#html_diferido_una_cuota').html('$' + monto_diferido);
        $('#html_saldo_una_cuota').html('$' + monto_saldo);

        /* 2 Meses */
        monto_total = 0;
        monto_subtotal = 0;
        monto_total_iva = 0;
        monto_diferido = 0;
        td_contenido_pedido = $('.td_contenido_pedido');
        for (i = 0; i < td_contenido_pedido.length; i++) {
            id_td = td_contenido_pedido[i].id;
            id_prod = $('#' + id_td).data('id_producto');
            precio_venta = $('#' + id_td).data('precio');
            tiene_iva = $('#' + id_td).data('tiene_iva');
            cantidad = $('#input_contenido_prod_' + id_prod).val();
            precio_prod = cantidad * precio_venta;
            if (tiene_iva == true) {
                monto_subtotal += precio_prod / 1.12;
                monto_total_iva += (precio_prod / 1.12) * 0.12;
            } else {
                monto_subtotal += precio_prod;
            }
            monto_diferido += precio_prod / 2; // 2 meses diferido

            monto_total += precio_prod;
        }
        monto_saldo = monto_total - (monto_diferido * (2 - 1)); // 2 => diferido 2 meses

        monto_total = Math.round(monto_total * 100) / 100;
        monto_subtotal = Math.round(monto_subtotal * 100) / 100;
        monto_total_iva = Math.round(monto_total_iva * 100) / 100;
        monto_diferido = Math.round(monto_diferido * 100) / 100;
        monto_saldo = Math.round(monto_saldo * 100) / 100;
        $('#html_subtotal_2_meses').html('$' + monto_subtotal);
        $('#html_iva_2_meses').html('$' + monto_total_iva);
        $('#html_monto_2_meses').html('$' + monto_total);
        $('#html_diferido_2_meses').html('$' + monto_diferido);
        $('#html_saldo_2_meses').html('$' + monto_saldo);

        /* 3 Meses */
        monto_total = 0;
        monto_subtotal = 0;
        monto_total_iva = 0;
        monto_diferido = 0;
        td_contenido_pedido = $('.td_contenido_pedido');
        for (i = 0; i < td_contenido_pedido.length; i++) {
            id_td = td_contenido_pedido[i].id;
            id_prod = $('#' + id_td).data('id_producto');
            precio_venta = $('#' + id_td).data('precio');
            tiene_iva = $('#' + id_td).data('tiene_iva');
            cantidad = $('#input_contenido_prod_' + id_prod).val();
            precio_prod = cantidad * precio_venta;
            if (tiene_iva == true) {
                monto_subtotal += precio_prod / 1.12;
                monto_total_iva += (precio_prod / 1.12) * 0.12;
            } else {
                monto_subtotal += precio_prod;
            }
            monto_diferido += precio_prod / 3; // 3 meses diferido

            monto_total += precio_prod;
        }
        monto_saldo = monto_total - (monto_diferido * (3 - 1)); // 3 => diferido 3 meses

        monto_total = Math.round(monto_total * 100) / 100;
        monto_subtotal = Math.round(monto_subtotal * 100) / 100;
        monto_total_iva = Math.round(monto_total_iva * 100) / 100;
        monto_diferido = Math.round(monto_diferido * 100) / 100;
        monto_saldo = Math.round(monto_saldo * 100) / 100;
        $('#html_subtotal_3_meses').html('$' + monto_subtotal);
        $('#html_iva_3_meses').html('$' + monto_total_iva);
        $('#html_monto_3_meses').html('$' + monto_total);
        $('#html_diferido_3_meses').html('$' + monto_diferido);
        $('#html_saldo_3_meses').html('$' + monto_saldo);
    }

    function seleccionar_forma_pago(fp) {
        $('.div_forma_pago').css('border', '');
        $('#div_' + fp).css('border', '2px solid #00B388 ');
        $('#span_forma_pago').html($('#div_' + fp).data('nombre'));
        $('#input_forma_pago').val($('#div_' + fp).data('diferido'));
        $('#btn_grabar_pedido').removeClass('hidden');
    }

    function store_pedido() {
        diferido = $('#input_forma_pago').val();
        td_contenido_pedido = $('.td_contenido_pedido');
        monto_total = 0;
        monto_diferido = 0;
        data_productos_no_peso = [];
        data_productos_peso = [];
        for (i = 0; i < td_contenido_pedido.length; i++) {
            id_td = td_contenido_pedido[i].id;
            id_prod = $('#' + id_td).data('id_producto');
            precio_venta = $('#' + id_td).data('precio');
            tiene_iva = $('#' + id_td).data('tiene_iva');
            peso = $('#' + id_td).data('peso');
            cantidad = $('#input_contenido_prod_' + id_prod).val();
            precio_prod = cantidad * precio_venta;
            monto_total += precio_prod;
            if (diferido > 0)
                monto_diferido += precio_prod / diferido;

            if (peso == 0)
                data_productos_no_peso.push({
                    id_prod: id_prod,
                    precio_venta: precio_venta,
                    tiene_iva: tiene_iva,
                    cantidad: cantidad,
                });
            else
                data_productos_peso.push({
                    id_prod: id_prod,
                    tiene_iva: tiene_iva,
                    cantidad: cantidad,
                });
        }
        if (diferido > 0) {
            monto_saldo = monto_total - (monto_diferido * (diferido - 1));
        } else {
            monto_saldo = monto_total;
        }
        monto_total = Math.round(monto_total * 100) / 100;
        monto_diferido = Math.round(monto_diferido * 100) / 100;
        monto_saldo = Math.round(monto_saldo * 100) / 100;

        saldo_usuario = $('#input_saldo').val();
        if (data_productos_no_peso.length > 0 || data_productos_peso.length > 0)
            if (saldo_usuario >= monto_saldo) {
                datos = {
                    _token: '{{ csrf_token() }}',
                    data_productos_no_peso: JSON.stringify(data_productos_no_peso),
                    data_productos_peso: JSON.stringify(data_productos_peso),
                    usuario: $('#input_usuario').val(),
                    finca: $('#input_empresa').val(),
                    monto_saldo: monto_saldo,
                    diferido: diferido,
                    mes_actual: $('#check_mes_actual').prop('checked'),
                }
                $.LoadingOverlay('hide');
                $.post('{{ url('pedido_bodega_cliente/store_pedido') }}', datos, function(retorno) {
                    if (retorno.success) {
                        alerta_accion(retorno.mensaje, function() {
                            //imprimir_recibo(retorno.id_pedido);
                            cargar_url('pedido_bodega_cliente');
                        });
                    } else {
                        alerta(retorno.success);
                    }
                }, 'json').fail(function(retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                }).always(function() {
                    $.LoadingOverlay('hide');
                })
            } else {
                alerta(
                    '<div class="alert alert-warning text-center"><h3>Su <b>SALDO</b> actual no es suficiente para realizar este pedido</h3></div>'
                );
            }
    }
</script>

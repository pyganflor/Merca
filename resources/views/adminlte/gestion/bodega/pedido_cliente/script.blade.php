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
            texto_iva = tiene_iva == 1 ? '<sup><em>incluye IVA</em></sup>' : '';
            new_td =
                '<td class="padding_lateral_20 text-center td_contenido_pedido" style="width: 150px; vertical-align: top" id="td_contenido_pedido_' +
                id + '" data-precio="' + precio + '" data-nombre="' + nombre + '" data-tiene_iva="' + tiene_iva + '">' +
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
                '<input type="number" id="input_contenido_prod_' + id + '" ' +
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
    }
</script>

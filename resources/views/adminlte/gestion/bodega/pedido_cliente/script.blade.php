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
</script>

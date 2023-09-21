<script>
    $('#vista_actual').val('bodega_productos');
    listar_reporte();

    function listar_reporte() {
        datos = {
            busqueda: $('#filtro_busqueda').val(),
            categoria: $('#filtro_categoria').val(),
            tipo: $('#filtro_tipo').val(),
        };
        get_jquery('{{ url('bodega_productos/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
        }, 'div_listado');
    }

    function exportar_reporte() {
        $.LoadingOverlay('show');
        window.open('{{ url('bodega_productos/exportar_reporte') }}?busqueda=' + $('#filtro_busqueda').val() +
            '&categoria=' + $('#filtro_categoria').val() +
            '&tipo=' + $('#filtro_tipo').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

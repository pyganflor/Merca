<script>
    $('#vista_actual').val('resumen_pedidos');
    setTimeout(() => {
        listar_reporte();
    }, 1000);

    function listar_reporte() {
        datos = {
            desde: $('#filtro_desde').val(),
            hasta: $('#filtro_hasta').val(),
            finca: $('#filtro_finca').val(),
            tipo: $('#filtro_tipo').val(),
        };
        get_jquery('{{ url('resumen_pedidos/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
            estructura_tabla('table_descuentos');
        }, 'div_listado');
    }

    function exportar_reporte() {
        $.LoadingOverlay('show');
        window.open('{{ url('resumen_pedidos/exportar_reporte') }}?desde=' + $('#filtro_desde').val() +
            '&tipo=' + $('#filtro_tipo').val() +
            '&hasta=' + $('#filtro_hasta').val() +
            '&finca=' + $('#filtro_finca').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

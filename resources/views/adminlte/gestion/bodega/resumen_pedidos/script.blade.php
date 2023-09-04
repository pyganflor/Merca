<script>
    $('#vista_actual').val('resumen_pedidos');
    listar_reporte();

    function listar_reporte() {
        datos = {
            desde: $('#filtro_desde').val(),
            hasta: $('#filtro_hasta').val(),
            finca: $('#filtro_finca').val(),
        };
        get_jquery('{{ url('resumen_pedidos/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
        }, 'div_listado');
    }

    function exportar_reporte() {
        $.LoadingOverlay('show');
        window.open('{{ url('resumen_pedidos/exportar_reporte') }}?desde=' + $('#filtro_desde').val() +
            '&hasta=' + $('#filtro_hasta').val() +
            '&finca=' + $('#filtro_finca').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

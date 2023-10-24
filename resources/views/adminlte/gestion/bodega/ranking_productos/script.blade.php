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
        };
        get_jquery('{{ url('ranking_productos/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
            estructura_tabla('tabla_ranking');
        });
    }

    function exportar_reporte() {
        $.LoadingOverlay('show');
        window.open('{{ url('ranking_productos/exportar_reporte') }}?desde=' + $('#filtro_desde').val() +
            '&hasta=' + $('#filtro_hasta').val() +
            '&finca=' + $('#filtro_finca').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

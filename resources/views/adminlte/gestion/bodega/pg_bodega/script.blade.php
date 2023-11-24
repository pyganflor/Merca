<script>
    $('#vista_actual').val('pg_bodega');
    listar_reporte();
    
    function listar_reporte() {
        datos = {
            desde: $('#filtro_desde').val(),
            hasta: $('#filtro_hasta').val(),
            finca: $('#filtro_finca').val(),
        };
        get_jquery('{{ url('pg_bodega/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
        });
    }

    function exportar_reporte() {
        $.LoadingOverlay('show');
        window.open('{{ url('pg_bodega/exportar_reporte') }}?desde=' + $('#filtro_desde').val() +
            '&hasta=' + $('#filtro_hasta').val() +
            '&finca=' + $('#filtro_finca').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

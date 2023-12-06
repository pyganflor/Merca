<script>
    $('#vista_actual').val('inventario_bodega');
    listar_reporte();

    function listar_reporte() {
        datos = {
            desde: $('#filtro_desde').val(),
            hasta: $('#filtro_hasta').val(),
        };
        get_jquery('{{ url('inventario_bodega/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
        }, 'div_listado');
    }

    function exportar_reporte() {
        $.LoadingOverlay('show');
        window.open('{{ url('inventario_bodega/exportar_reporte') }}?desde=' + $('#filtro_desde').val() +
            '&hasta=' + $('#filtro_hasta').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

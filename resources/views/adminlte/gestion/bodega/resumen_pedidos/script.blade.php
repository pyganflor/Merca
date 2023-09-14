<script>
    $('#vista_actual').val('resumen_pedidos');
    seleccionar_finca_filtro();
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
        }, 'div_listado');
    }

    function seleccionar_finca_filtro() {
        datos = {
            _token: '{{ csrf_token() }}',
            finca: $('#filtro_finca').val()
        }
        $('.filtro_entrega').LoadingOverlay('show');
        $.post('{{ url('pedido_bodega/seleccionar_finca_filtro') }}', datos, function(retorno) {
            $('#filtro_desde').html(retorno.options);
            $('#filtro_hasta').html(retorno.options);
        }, 'json').fail(function(retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function() {
            $('.filtro_entrega').LoadingOverlay('hide');
        });
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
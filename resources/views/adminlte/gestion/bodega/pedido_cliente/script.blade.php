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

    function add_pedido() {
        datos = {
            finca: $('#filtro_finca').val(),
        }
        get_jquery('{{ url('pedido_bodega/add_pedido') }}', datos, function(retorno) {
            modal_view('modal_add_pedido', retorno,
                '<i class="fa fa-fw fa-shopping-cart"></i> Nuevo Pedido',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        });
    }

    function get_armar_pedido() {
        datos = {}
        get_jquery('{{ url('pedido_bodega/get_armar_pedido') }}', datos, function(retorno) {
            modal_view('modal_get_armar_pedido', retorno,
                '<i class="fa fa-fw fa-shopping-cart"></i> Nuevo Pedido',
                true, false, '{{ isPC() ? '70%' : '' }}',
                function() {});
        });
    }

    function modal_contabilidad() {
        datos = {}
        get_jquery('{{ url('pedido_bodega/modal_contabilidad') }}', datos, function(retorno) {
            modal_view('modal_modal_contabilidad', retorno,
                '<i class="fa fa-fw fa-shopping-cart"></i> Exportar archivo de contabilidad',
                true, false, '{{ isPC() ? '70%' : '' }}',
                function() {});
        });
    }

    function seleccionar_finca_filtro() {
        datos = {
            _token: '{{ csrf_token() }}',
            finca: $('#filtro_finca').val()
        }
        $('#filtro_entrega').LoadingOverlay('show');
        $.post('{{ url('pedido_bodega/seleccionar_finca_filtro') }}', datos, function(retorno) {
            $('#filtro_entrega').html(retorno.options);
        }, 'json').fail(function(retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function() {
            $('#filtro_entrega').LoadingOverlay('hide');
        });
    }

    function exportar_resumen_pedidos() {
        $.LoadingOverlay('show');
        window.open('{{ url('pedido_bodega/exportar_resumen_pedidos') }}?entrega=' + $('#filtro_entrega').val() +
            '&finca=' + $('#filtro_finca').val(), '_blank');
        $.LoadingOverlay('hide');
    }

    function imprimir_pedido(ped) {
        $.LoadingOverlay('show');
        window.open('{{ url('pedido_bodega/imprimir_pedido') }}?pedido=' + ped, '_blank');
        $.LoadingOverlay('hide');
    }

    function imprimir_pedidos_all() {
        $.LoadingOverlay('show');
        window.open('{{ url('pedido_bodega/imprimir_pedidos_all') }}?entrega=' + $('#filtro_entrega').val() +
            '&finca=' + $('#filtro_finca').val(), '_blank');
        $.LoadingOverlay('hide');
    }

    function imprimir_entregas_all() {
        if ($('#filtro_finca').val() != 'T') {
            $.LoadingOverlay('show');
            window.open('{{ url('pedido_bodega/imprimir_entregas_all') }}?entrega=' + $('#filtro_entrega').val() +
                '&finca=' + $('#filtro_finca').val(), '_blank');
            $.LoadingOverlay('hide');
        }
    }

    function imprimir_entregas_peso_all() {
        if ($('#filtro_finca').val() != 'T') {
            $.LoadingOverlay('show');
            window.open('{{ url('pedido_bodega/imprimir_entregas_peso_all') }}?entrega=' + $('#filtro_entrega').val() +
                '&finca=' + $('#filtro_finca').val(), '_blank');
            $.LoadingOverlay('hide');
        }
    }
</script>

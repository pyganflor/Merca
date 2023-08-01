<script>
    $('#vista_actual').val('pedido_bodega');
    listar_reporte();

    function listar_reporte() {
        datos = {
            fecha: $('#filtro_fecha').val(),
            finca: $('#filtro_finca').val(),
        };
        get_jquery('{{ url('pedido_bodega/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
        }, 'div_listado');
    }

    function add_pedido() {
        datos = {}
        get_jquery('{{ url('pedido_bodega/add_pedido') }}', datos, function(retorno) {
            modal_view('modal_add_pedido', retorno,
                '<i class="fa fa-fw fa-shopping-cart"></i> Nuevo Pedido',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        });
    }
</script>

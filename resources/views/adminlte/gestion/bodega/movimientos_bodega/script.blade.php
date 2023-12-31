<script>
    $('#vista_actual').val('movimientos_bodega')
    listar_reporte();

    function listar_reporte() {
        datos = {
            busqueda: $('#filtro_busqueda').val(),
            categoria: $('#filtro_categoria').val(),
        };
        get_jquery('{{ url('movimientos_bodega/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
            estructura_tabla('table_inventario');
        }, 'div_listado');
    }

    function add_ingresos() {
        datos = {}
        get_jquery('{{ url('movimientos_bodega/add_ingresos') }}', datos, function(retorno) {
            modal_view('modal_add_ingresos', retorno,
                '<i class="fa fa-fw fa-arrow-up"></i> Ingresos a Bodega',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        });
    }

    function add_salidas() {
        datos = {}
        get_jquery('{{ url('movimientos_bodega/add_salidas') }}', datos, function(retorno) {
            modal_view('modal_add_salidas', retorno,
                '<i class="fa fa-fw fa-arrow-down"></i> Salidas de Bodega',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        });
    }
</script>

<script>
    $('#vista_actual').val('inventario_cajas');
    listar_reporte();
    
    function listar_reporte() {
        datos = {
            fecha: $('#filtro_fecha').val(),
            busqueda: $('#filtro_busqueda').val(),
        }
        get_jquery('{{ url('inventario_cajas/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
        }, 'div_listado');
    }

</script>

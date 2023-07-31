<script>
    listar_reporte(); 
    
    function listar_reporte() {
        datos = {
            cliente: $('#filtro_cliente').val(),
            agencia: $('#filtro_agencia').val(),
            fecha: $('#filtro_fecha').val(),
        }
        if (datos['anno'] != '')
            get_jquery('{{ url('ingreso_guias/listar_reporte') }}', datos, function(retorno) {
                $('#div_listado').html(retorno);
                estructura_tabla('table_listado', );
                $('#table_listado_filter').addClass('hidden')
                $('#table_listado_filter label input').addClass('input-yura_default')
            });
    }
</script>

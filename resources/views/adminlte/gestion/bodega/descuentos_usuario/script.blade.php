<script>
    $('#vista_actual').val('descuentos_usuario');
    $('#filtro_usuario').select2();
    seleccionar_finca_filtro();

    function listar_reporte() {
        datos = {
            finca: $('#filtro_finca').val(),
            usuario: $('#filtro_usuario').val(),
        };
        get_jquery('{{ url('descuentos_usuario/listar_reporte') }}', datos, function(retorno) {
            $('#div_listado').html(retorno);
            //estructura_tabla('table_descuentos');
        }, 'div_listado');
    }

    function seleccionar_finca_filtro() {
        datos = {
            _token: '{{ csrf_token() }}',
            finca: $('#filtro_finca').val()
        }
        $('#filtro_usuario').LoadingOverlay('show');
        $.post('{{ url('descuentos_usuario/seleccionar_finca_filtro') }}', datos, function(retorno) {
            $('#filtro_usuario').html(retorno.options_usuarios);
        }, 'json').fail(function(retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function() {
            $('#filtro_usuario').LoadingOverlay('hide');
        });
    }

    function exportar_reporte() {
        $.LoadingOverlay('show');
        window.open('{{ url('descuentos_usuario/exportar_reporte') }}?usuario=' + $('#filtro_usuario').val() +
            '&finca=' + $('#filtro_finca').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

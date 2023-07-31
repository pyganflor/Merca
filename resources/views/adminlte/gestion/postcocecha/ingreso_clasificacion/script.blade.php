<script>
    listar_blanco();
    $('#vista_actual').val('ingreso_clasificacion');

    function listar_blanco() {
        datos = {
            fecha: $('#fecha_blanco_filtro').val(),
            planta: $('#planta_blanco_filtro').val(),
        };
        get_jquery('{{ url('ingreso_clasificacion/listar_blanco') }}', datos, function(retorno) {
            $('#div_listar_blanco').html(retorno);
        });
    }
</script>

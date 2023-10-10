<script>
    $('#vista_actual').val('etiquetar_peso');
    listar_inventario();

    function listar_inventario() {
        datos = {
        };
        get_jquery('{{ url('etiquetar_peso/listar_inventario') }}', datos, function(retorno) {
            $('#div_inventario').html(retorno);
        }, 'div_inventario');
    }
</script>

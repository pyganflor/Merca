<script>
    function seleccionar_cliente(cliente) {
        datos = {
            cliente: cliente
        }
        $('.tr_clientes').css('background-color', '')
        get_jquery('{{ url('precio/seleccionar_cliente') }}', datos , function(retorno) {
            $('#body_precios').html(retorno);
            $('#tr_cliente_' + cliente).css('background-color', '#03dcff');
        });
    }
</script>

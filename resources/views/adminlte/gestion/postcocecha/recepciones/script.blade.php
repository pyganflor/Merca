<script>
    function buscar_listado_recepcion() {
        datos = {
            fecha: $('#filtro_fecha').val(),
        }
        get_jquery('{{ url('recepcion/buscar_listado_recepcion') }}', datos, function(retorno) {
            $('#div_listado_recepciones').html(retorno);
        });
    }

    function add_recepcion() {
        datos = {}
        get_jquery('{{ url('recepcion/add_recepcion') }}', datos, function(retorno) {
            modal_view('modal_add_recepcion', retorno, '<i class="fa fa-fw fa-plus"></i> Formulario Cosecha',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        })
    }

    function delete_desglose(id) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 1.5em'>Esta a punto de <b>ELIMINAR</b> la cosecha</div>";

        modal_quest('modal_delete_desglose', texto, 'Eliminar pedido', true, false, '40%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                id: id,
            };
            post_jquery_m('recepcion/delete_desglose', datos, function() {
                cerrar_modals();
                buscar_listado_recepcion();
            });
        })
    }
</script>

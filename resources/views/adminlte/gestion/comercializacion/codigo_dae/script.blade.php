<script>
    listar_reporte(); 
    
    function listar_reporte() {
        datos = {
            anno: $('#filtro_anno').val(),
            mes: $('#filtro_mes').val(),
        }
        if (datos['anno'] != '')
            get_jquery('{{ url('codigo_dae/listar_reporte') }}', datos, function(retorno) {
                $('#div_listado').html(retorno);
                estructura_tabla('table_listado', );
                $('#table_listado_filter').addClass('hidden')
                $('#table_listado_filter label input').addClass('input-yura_default')
            });
    }

    function exportar_paises() {
        datos = {}
        get_jquery('{{ url('codigo_dae/exportar_paises') }}', datos, function(retorno) {
            modal_view('modal_exportar_paises', retorno,
                '<i class="fa fa-fw fa-plus"></i> Formulario para exportar plantilla por paises',
                true, false, '{{ isPC() ? '60%' : '' }}',
                function() {});
        });
    }

    function importar_codigos_dae() {
        datos = {}
        get_jquery('{{ url('codigo_dae/importar_codigos_dae') }}', datos, function(retorno) {
            modal_view('modal_importar_codigos_dae', retorno,
                '<i class="fa fa-fw fa-plus"></i> Formulario para importar los CODIGOS DAE',
                true, false, '{{ isPC() ? '50%' : '' }}',
                function() {});
        });
    }
</script>

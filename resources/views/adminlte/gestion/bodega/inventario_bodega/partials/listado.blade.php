<div class="nav-tabs-custom">
    <ul class="nav nav-pills nav-justified">
        <li class="active">
            <a href="#inventario-chart" data-toggle="tab" aria-expanded="false">
                Inventario Actual
            </a>
        </li>
        <li class="">
            <a href="#ingresos-chart" data-toggle="tab" aria-expanded="true">
                Ingresos
            </a>
        </li>
        <li class="">
            <a href="#salidas-chart" data-toggle="tab" aria-expanded="true">
                Salidas
            </a>
        </li>
    </ul>
    <div class="tab-content no-padding">
        <div class="tab-pane active" id="inventario-chart" style="position: relative">
            <div style="overflow-y: scroll; overflow-x: scroll; max-height: 700px;">
                @include('adminlte/gestion/bodega/inventario_bodega/partials/_inventario')
            </div>
        </div>
        <div class="tab-pane" id="ingresos-chart" style="position: relative">
            <div style="overflow-y: scroll; overflow-x: scroll; max-height: 700px;">
                @include('adminlte/gestion/bodega/inventario_bodega/partials/_ingresos')
            </div>
        </div>
        <div class="tab-pane" id="salidas-chart" style="position: relative">
            <div style="overflow-y: scroll; overflow-x: scroll; max-height: 700px;">
                @include('adminlte/gestion/bodega/inventario_bodega/partials/_salidas')
            </div>
        </div>
    </div>
</div>

<script>
    estructura_tabla('table_inventario');
    $('#table_inventario_filter').addClass('hidden');
    estructura_tabla('table_ingresos');
    $('#table_ingresos_filter').addClass('hidden');

    function update_inventario(id) {
        datos = {
            _token: '{{ csrf_token() }}',
            id: id,
            cantidad: $('#cantidad_' + id).val(),
            disponibles: $('#disponibles_' + id).val(),
            precio: $('#precio_' + id).val(),
        }
        post_jquery_m('{{ url('inventario_bodega/update_inventario') }}', datos, function() {
            listar_reporte();
        });
    }

    function delete_inventario(id) {
        mensaje = {
            title: '<i class="fa fa-fw fa-trash"></i> Eliminar inventario',
            mensaje: '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de <b>ELIMINAR</b> este inventario?</div>',
        };
        modal_quest('modal_delete_inventario', mensaje['mensaje'], mensaje['title'], true, false,
            '{{ isPC() ? '50%' : '' }}',
            function() {
                datos = {
                    _token: '{{ csrf_token() }}',
                    id: id,
                }
                post_jquery_m('{{ url('inventario_bodega/delete_inventario') }}', datos, function() {
                    listar_reporte();
                });
            });
    }
</script>

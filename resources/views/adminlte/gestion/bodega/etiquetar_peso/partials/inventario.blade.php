<div style="overflow-y: scroll; overflow-x: scroll; max-height: 700px;">
    <table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
        <tr id="tr_fija_top_0">
            <th class="text-center th_yura_green">
                Fecha
            </th>
            <th class="text-center th_yura_green">
                Producto
            </th>
            <th class="text-center th_yura_green">
                Precio
            </th>
            <th class="text-center th_yura_green">
                Ingreso
            </th>
            <th class="text-center th_yura_green" style="width: 60px">
                Disponibles
            </th>
            <th class="text-center th_yura_green" style="width: 30px">
            </th>
        </tr>
        @foreach ($listado as $item)
            <tr class="tr_inventario_bodega" id="tr_inventario_bodega_{{ $item->id_inventario_bodega }}">
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ convertDateToText($item->fecha_ingreso) }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item->producto_nombre }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    ${{ $item->precio }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{ $item->cantidad }}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="number" readonly id="inventario_disponibles_{{ $item->id_inventario_bodega }}" class="text-center"
                        value="{{ $item->disponibles }}"
                        style="width: 100%; color: black !important">
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-yura_dark" title="Seleccionar"
                            onclick="seleccionar_inventario('{{ $item->id_inventario_bodega }}')">
                            <i class="fa fa-fw fa-arrow-right"></i>
                        </button>
                    </div>
                </th>
            </tr>
        @endforeach
    </table>
</div>

<script>
    function seleccionar_inventario(id) {
        $('.tr_inventario_bodega').removeClass('bg-yura_dark');
        $('#tr_inventario_bodega_' + id).addClass('bg-yura_dark');
        datos = {
            _token: '{{ csrf_token() }}',
            id: id,
            unidades: $('#inventario_disponibles_' + id).val(),
            fecha: $('#fecha_entrega').val()
        }
        get_jquery('{{ url('etiquetar_peso/seleccionar_inventario') }}', datos, function(retorno) {
            $('#div_pedidos').html(retorno);
        });
    }
</script>

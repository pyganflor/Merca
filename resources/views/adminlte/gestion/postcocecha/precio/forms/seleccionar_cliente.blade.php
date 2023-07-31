<input type="hidden" id="cliente_seleccionado" value="{{ $cliente }}">
<table class="table-bordered" style="border: 1px solid #9d9d9d; width: 100%">
    <tr class="tr_fija_top_0">
        <th class="text-center th_yura_green">
            Longitud <sup>cm</sup>
        </th>
        <th class="text-center th_yura_green">
            Variedad
        </th>
        <th class="text-center th_yura_green">
            Tipo
        </th>
        <th class="text-center th_yura_green" style="width: 80px">
            Precio
        </th>
        <th class="text-center th_yura_green" style="width: 60px">
            <button type="button" class="btn btn-xs btn-yura_default"
                onclick="$('#tr_new_precio').removeClass('hidden')" title="Agregar nuevo Precio">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    <tr id="tr_new_precio" class="hidden">
        <td style="border-color: #9d9d9d">
            <input type="number" id="new_longitud" style="width: 100%; background-color: #dddddd"
                placeholder="Todas las Longitudes" class="text-center" min="0">
        </td>
        <td style="border-color: #9d9d9d">
            <select id="new_planta" style="width: 100%; background-color: #dddddd; height: 26px;"
                onchange="select_planta_global($(this).val(), 'new_variedad', 'new_variedad', '<option value=>Todos los tipos</option>')">
                <option value="">Seleccione</option>
                @foreach ($plantas as $p)
                    <option value="{{ $p->id_planta }}">{{ $p->nombre }}</option>
                @endforeach
            </select>
        </td>
        <td style="border-color: #9d9d9d">
            <select id="new_variedad" style="width: 100%; background-color: #dddddd; height: 26px;">
                <option value="">Todas los tipos</option>
            </select>
        </td>
        <td style="border-color: #9d9d9d">
            <input type="number" id="new_precio" style="width: 100%; background-color: #dddddd" placeholder="$"
                class="text-center" min="0">
        </td>
        <td style="border-color: #9d9d9d" class="text-center">
            <button type="button" class="btn btn-xs btn-yura_primary" onclick="store_precio()"
                title="Grabar nuevo Precio">
                <i class="fa fa-fw fa-save"></i>
            </button>
        </td>
    </tr>
    @foreach ($listado as $item)
        <tr id="tr_precio_{{ $item->id_precio }}">
            <td class="text-center" style="border-color: #9d9d9d">
                {{ $item->longitud }}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{ $item->variedad->planta->nombre }}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{ $item->variedad->nombre }}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="number" value="{{ $item->cantidad }}" style="width: 100%"
                    id="edit_precio_{{ $item->id_precio }}" class="text-center">
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-yura_primary"
                        onclick="update_precio('{{ $item->id_precio }}')">
                        <i class="fa fa-fw fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_danger"
                        onclick="delete_precio('{{ $item->id_precio }}')">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
</table>

<script>
    function store_precio() {
        datos = {
            _token: '{{ csrf_token() }}',
            longitud: $('#new_longitud').val(),
            planta: $('#new_planta').val(),
            variedad: $('#new_variedad').val(),
            precio: $('#new_precio').val(),
            cliente: $('#cliente_seleccionado').val(),
        }
        if (datos['precio'] != '' && datos['planta'] != '') {
            post_jquery_m('{{ url('precio/store_precio') }}', datos, function() {
                seleccionar_cliente(datos['cliente'])
            })
        }
    }

    function update_precio(id) {
        datos = {
            _token: '{{ csrf_token() }}',
            id: id,
            precio: $('#edit_precio_' + id).val(),
        }
        if (datos['precio'] != '') {
            post_jquery_m('{{ url('precio/update_precio') }}', datos, function() {})
        }
    }

    function delete_precio(id) {
        datos = {
            _token: '{{ csrf_token() }}',
            id: id,
        }
        post_jquery_m('{{ url('precio/delete_precio') }}', datos, function() {
            seleccionar_cliente($('#cliente_seleccionado').val())
        }, 'tr_precio_' + id)
    }
</script>

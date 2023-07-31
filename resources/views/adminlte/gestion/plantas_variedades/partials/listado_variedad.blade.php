<table width="100%" class="table-striped table-bordered" style="font-size: 0.9em; border-color: #9d9d9d"
    id="table_content_variedades">
    <thead>
        <tr id="th_fija_top_0">
            <th class="text-center th_yura_green">Variedad</th>
            <th class="text-center th_yura_green">Siglas</th>
            <th class="text-center th_yura_green">Tallos por malla</th>
            <th class="text-center th_yura_green">Tallos por Ramo</th>
            @if ($proveedor != '')
                <th class="text-center th_yura_green">
                    Asignar Proveedor
                </th>
            @endif
            <th class="text-center th_yura_green">
                <button type="button" class="btn btn-xs btn-yura_default" title="Añadir Variedad"
                    onclick="add_variedad()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </th>
        </tr>
    </thead>
    @if (sizeof($variedades) > 0)
        @foreach ($variedades as $v)
            <tr onmouseover="$(this).css('background-color','#add8e6')"
                onmouseleave="$(this).css('background-color','')" class="{{ $v->estado == 1 ? '' : 'error' }}"
                id="row_variedad_{{ $v->id_variedad }}">
                <td style="border-color: #9d9d9d" class="text-center">
                    {{ $v->nombre }}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{ $v->siglas }}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{ $v->tallos_x_malla }}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{ $v->tallos_x_ramo_estandar }}
                </td>
                @if ($proveedor != '')
                    <td style="border-color: #9d9d9d" class="text-center">
                        <input type="checkbox" id="check_proveedor_{{ $v->id_variedad }}"
                            class="check_proveedor mouse-hand" onchange="asignar_proveedor({{ $v->id_variedad }})"
                            {{ in_array($v->id_variedad, $variedades_del_proveedor) ? 'checked' : '' }}>
                    </td>
                @endif
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-yura_default btn-xs dropdown-toggle"
                            data-toggle="dropdown">
                            <i class="fa fa-fw fa-gears"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li>
                                <a href="javascript:void(0)" title="Editar"
                                    onclick="edit_variedad('{{ $v->id_variedad }}')">
                                    <i class="fa fa-fw fa-pencil"></i> Editar
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" title="Clasificaciones Unitarias"
                                    onclick="vincular_variedad_unitaria('{{ $v->id_variedad }}')">
                                    <i class="fa fa-fw fa-filter"></i> Clasificaciones Unitarias
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" title="Regalías"
                                    onclick="add_regalias('{{ $v->id_variedad }}')">
                                    <i class="fa fa-fw fa-usd"></i> Regalías
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" title="{{ $v->estado == 1 ? 'Desactivar' : 'Activar' }}"
                                    onclick="cambiar_estado_variedad('{{ $v->id_variedad }}','{{ $v->estado }}')">
                                    <i class="fa fa-fw fa-{{ $v->estado == 1 ? 'trash' : 'unlock' }}"></i>
                                    {{ $v->estado == 1 ? 'Desactivar' : 'Activar' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    {{-- <button class="btn btn-xs btn-default" type="button" title="Precio"
                            onclick="add_precio('{{$v->id_variedad}}')">
                        <i class="fa fa-usd"></i>
                    </button> --}}
                </td>
            </tr>
        @endforeach
    @else
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
            <td style="border-color: #9d9d9d" class="text-center mouse-hand" colspan="4">
                No hay variedades registradas para esta planta
            </td>
        </tr>
    @endif
</table>

<style>
    #th_fija_top_0 th {
        position: sticky;
        top: 0;
        z-index: 9;
    }
</style>

<script>
    function asignar_proveedor(variedad) {
        proveedor = $('#proveedor_seleccionada').val();
        datos = {
            _token: '{{ csrf_token() }}',
            variedad: variedad,
            id_proveedor: proveedor,
        };
        post_jquery_m('{{ url('plantas_variedades/asignar_proveedor') }}', datos, function() {}, 'row_variedad_' +
            variedad);
    }
</script>

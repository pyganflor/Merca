<div id="table_usuarios">
    @if (sizeof($listado) > 0)
        <table width="100%" class="table-bordered" style="border-color: #9d9d9d" id="table_content_usuarios">
            <thead>
                <tr class="tr_fija_top_0">
                    <th class="text-center th_yura_green">
                        NOMBRE COMPLETO
                    </th>
                    <th class="text-center th_yura_green">
                        FINCA
                    </th>
                    <th class="text-center th_yura_green">
                        IDENTIFICACION
                    </th>
                    <th class="text-center th_yura_green">
                        ROL
                    </th>
                    <th class="text-center th_yura_green">
                        CUPO DISPONIBLE
                    </th>
                    <th class="text-center th_yura_green">
                        SALDO
                    </th>
                    <th class="text-center th_yura_green">
                        APLICA
                    </th>
                    <th class="text-center th_yura_green">
                        OPCIONES
                    </th>
                </tr>
            </thead>
            @foreach ($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')"
                    onmouseleave="$(this).css('background-color','')" class="{{ $item->estado == 'A' ? '' : 'error' }}"
                    id="row_usuarios_{{ $item->id_usuario }}">
                    <th style="border-color: #9d9d9d" class="text-center">
                        {{ $item->nombre_completo }}
                    </th>
                    <td style="border-color: #9d9d9d" class="text-center">
                        @foreach (getUsuario($item->id_usuario)->empresas as $uf)
                            {{ $uf->empresa->nombre }}
                            <br>
                        @endforeach
                    </td>
                    <th style="border-color: #9d9d9d" class="text-center">
                        {{ $item->username }}
                    </th>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{ $item->rol }}
                    </td>
                    <th style="border-color: #9d9d9d" class="text-center">
                        ${{ number_format($item->cupo_disponible, 2) }}
                    </th>
                    <th style="border-color: #9d9d9d" class="text-center">
                        ${{ number_format($item->saldo, 2) }}
                    </th>
                    <td style="border-color: #9d9d9d" class="text-center">
                        @if ($item->aplica == 1)
                            SI
                        @else
                            <b>NO</b>
                        @endif
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-yura_default" title="Detalles"
                                onclick="ver_usuario('{{ $item->id_usuario }}')"
                                id="btn_view_usuario_{{ $item->id_usuario }}">
                                <i class="fa fa-fw fa-eye" style="color: black"></i>
                            </button>
                            @if (getUsuario($item->id_usuario)->rol()->tipo == 'S')
                                <button type="button" class="btn btn-xs btn-yura_danger"
                                    title="{{ $item->estado == 'A' ? 'Desactivar' : 'Activar' }}"
                                    onclick="eliminar_usuario('{{ $item->id_usuario }}', '{{ $item->estado }}')"
                                    id="btn_usuarios_{{ $item->id_usuario }}">
                                    <i class="fa fa-fw {{ $item->estado == 'A' ? 'fa-lock' : 'fa-unlock' }}"
                                        style="color: black" id="icon_usuarios_{{ $item->id_usuario }}"></i>
                                </button>
                            @endif
                            <button type="button" class="btn btn-xs btn-yura_primary text-white" title="Fincas"
                                onclick="config_user_finca('{{ $item->id_usuario }}')">
                                <i class="fa fa-fw fa-leaf"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>

<script>
    function config_user_finca(user) {
        datos = {
            user: user
        };
        $.LoadingOverlay('show');
        get_jquery('{{ url('usuarios/config_user_finca') }}', datos, function(retorno) {
            modal_view('modal-view_config_user_finca', retorno,
                '<i class="fa fa-fw fa-leaf"></i> Configurar las fincas del usuario', true, false, '45%');
        });
        $.LoadingOverlay('hide');
    }
</script>

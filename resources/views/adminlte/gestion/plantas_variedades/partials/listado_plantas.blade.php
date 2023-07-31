<table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_menus">
    <thead>
    <tr id="th_fija_top_0">
        <th class="text-center th_yura_green">PLANTA</th>
        <th class="text-center th_yura_green">
            <div class="btn-group">
                <button type="button" class="btn btn-xs btn-yura_default" title="Añadir Planta" onclick="add_planta()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
                <button type="button" class="btn btn-xs btn-yura_dark" title="Formulario completo" onclick="form_compelto()">
                    <i class="fa fa-fw fa-leaf"></i>
                </button>
            </div>
        </th>
    </tr>
    </thead>
    @if(sizeof($plantas)>0)
        @foreach($plantas as $p)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$p->estado == 1 ? '' : 'error'}}" id="row_planta_{{$p->id_planta}}">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand" onclick="select_planta('{{$p->id_planta}}')">
                    <i class="fa fa-fw fa-check hidden icon_hidden_p" id="icon_planta_{{$p->id_planta}}"></i> {{$p->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-yura_default" type="button" title="Editar"
                                onclick="edit_planta('{{$p->id_planta}}')">
                            <i class="fa fa-fw fa-pencil"></i>
                        </button>
                        <button class="btn btn-xs btn-yura_danger" type="button" title="{{$p->estado == 1 ? 'Desactivar' : 'Activar'}}"
                                onclick="cambiar_estado_planta('{{$p->id_planta}}','{{$p->estado}}')">
                            <i class="fa fa-fw fa-{{$p->estado == 1 ? 'trash' : 'unlock'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td class="text-center" colspan="2" style="border-color: #9d9d9d">
                No hay plantas registradas
            </td>
        </tr>
        </div>
    @endif
</table>

<style>
    #th_fija_top_0 th {
        position: sticky;
        top: 0;
        z-index: 9;
    }
</style>

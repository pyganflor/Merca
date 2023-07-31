<div id="table_consignatarios">
    @if (sizeof($listado) > 0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
            id="table_content_consignatarios">
            <thead>
                <tr style="background-color: #dd4b39; color: white">
                    <th class="text-center th_yura_green">
                        NOMBRE COMPLETO
                    </th>
                    <th class="text-center th_yura_green">
                        DIRECCIÓN
                    </th>
                    <th class="text-center th_yura_green">
                        PAÍS
                    </th>
                    <th class="text-center th_yura_green">
                        IDENTIFICACIÓN
                    </th>
                    <th class="text-center th_yura_green">
                        TÉLEFONO
                    </th>
                    <th class="text-center th_yura_green">
                        CORREO
                    </th>
                    <th class="text-center th_yura_green">
                        OPCIONES
                    </th>
                </tr>
            </thead>
            @foreach ($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')"
                    onmouseleave="$(this).css('background-color','')">
                    <td style="border-color: #9d9d9d" class="text-center">{{ $item->nombre }}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{ $item->direccion }}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{ $item->pais()->nombre }}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{ $item->identificacion }}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{ $item->telefono }}</td>
                    <td style="border-color: #9d9d9d" class="text-center"><a
                            href="mailto:{{ $item->correo }}">{{ $item->correo }}</a></td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a href="javascript:void(0)" class="btn btn-default btn-xs" title="Ver consignatario"
                            onclick="add_consignatario('{{ $item->id_consignatario }}')"
                            id="btn_consignatario_{{ $item->id_consignatario }}">
                            <i class="fa fa-fw fa-eye" style="color: black"></i>
                        </a>
                        <button class="btn btn-{{ $item->estado == 1 ? 'warning' : 'success' }} btn-xs"
                            title="Eliminar consignatario"
                            onclick="update_consignatario('{{ $item->id_consignatario }}','{{ $item->estado }}')">
                            <i class="fa fa-fw fa-{{ $item->estado == 1 ? 'ban' : 'check' }}"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>

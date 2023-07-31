<div style="overflow-y: scroll; max-height: 420px">
    <table class="table-bordered table-striped" style="width: 100%">
        @foreach($plantas as $p)
            <tr id="tr_planta_{{$p->id_planta}}">
                <th class="text-center" colspan="2">
                    <input type="text" value="{{$p->nombre}}" id="nombre_planta_{{$p->id_planta}}"
                           style="width: 100%; background-color: #00B388; color: white">
                </th>
                <th class="text-center">
                    <input type="text" value="{{$p->siglas}}" id="siglas_planta_{{$p->id_planta}}"
                           style="width: 100%; background-color: #00B388; color: white">
                </th>
                <th class="text-center">
                    <select id="tipo_planta_{{$p->id_planta}}" style="width: 100%; background-color: #00B388; color: white">
                        <option value="N" {{$p->tipo == 'N' ? 'selected' : ''}}>Normal</option>
                        <option value="P" {{$p->tipo == 'P' ? 'selected' : ''}}>Perenne</option>
                    </select>
                </th>
                <th class="text-center th_yura_green">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-yura_default" onclick="actualizar_planta('{{$p->id_planta}}')">
                            <i class="fa fa-fw fa-save"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-yura_dark" onclick="$('.tr_var_{{$p->id_planta}}').toggleClass('hidden')">
                            <i class="fa fa-fw fa-caret-down"></i>
                        </button>
                    </div>
                </th>
            </tr>
            @foreach($p->variedades_activos as $v)
                <tr class="tr_var_{{$p->id_planta}} hidden" id="tr_variedad_{{$v->id_variedad}}">
                    <td class="text-center">
                        <input type="text" value="{{$v->nombre}}" id="nombre_var_{{$v->id_variedad}}" style="width: 100%" placeholder="Nombre">
                    </td>
                    <td class="text-center">
                        <input type="text" value="{{$v->siglas}}" id="siglas_var_{{$v->id_variedad}}" style="width: 100%" placeholder="Siglas">
                    </td>
                    <td class="text-center">
                        <div class="input-group" style="width: 100%">
                            <div class="input-group-addon bg-yura_dark">
                                Desecho. enraizamiento
                            </div>
                            <input type="number" value="{{$v->desecho_enraizamiento}}" id="desecho_enraizamiento_var_{{$v->id_variedad}}"
                                   style="width: 100%" placeholder="Desecho de enraizamiento">
                        </div>
                    </td>
                    <td class="text-center">
                        <select id="proyectar_semanal_{{$v->id_variedad}}" style="width: 100%">
                            <option value="1" {{$v->proyectar_semanal == 1 ? 'selected' : ''}}>Proyectar x Semana</option>
                            <option value="0" {{$v->proyectar_semanal == 0 ? 'selected' : ''}}>Proyectar x Módulo</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-yura_primary" onclick="actualizar_variedad('{{$v->id_variedad}}')">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </table>
</div>

<script>
    function actualizar_planta(id_pta) {
        datos = {
            _token: '{{csrf_token()}}',
            nombre: $('#nombre_planta_' + id_pta).val(),
            id_planta: id_pta,
            siglas: $('#siglas_planta_' + id_pta).val(),
            tipo: $('#tipo_planta_' + id_pta).val(),
        };
        $('#tr_planta_' + id_pta).LoadingOverlay('show');
        $.post('{{url('plantas_variedades/actualizar_planta')}}', datos, function (retorno) {
            if (retorno.success) {
                //cerrar_modals();
            } else {
                alerta(retorno.mensaje);
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_planta_' + id_pta).LoadingOverlay('hide');
        });
    }

    function actualizar_variedad(id_var) {
        datos = {
            _token: '{{csrf_token()}}',
            nombre: $('#nombre_var_' + id_var).val(),
            id_variedad: id_var,
            siglas: $('#siglas_var_' + id_var).val(),
            des_enr: $('desecho_enraizamiento_var_' + id_var).val(),
            proy_sem: $('#proyectar_semanal_' + id_var).val(),
        };
        $('#tr_variedad_' + id_var).LoadingOverlay('show');
        $.post('{{url('plantas_variedades/actualizar_variedad')}}', datos, function (retorno) {
            if (retorno.success) {
                //cerrar_modals();
            } else {
                alerta(retorno.mensaje);
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_variedad_' + id_var).LoadingOverlay('hide');
        });
    }
</script>
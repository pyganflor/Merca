@if(count($variedades) > 0)
    <div class="input-group">
        <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
            <i class="fa fa-fw fa-leaf"></i> Variedades
        </span>
        <select id="variedad_indiccador" class="form-control input-yura_default" onchange="mostrar_desglose_ciclo()">
            <option value="T">Seleccione</option>
            @foreach($variedades as $var)
                <option value="{{$var->id_variedad}}">{{$var->planta_nombre}}: {{$var->variedad_nombre}}</option>
            @endforeach
        </select>
    </div>
    <div id="div_desglose_ciclo" style="margin-top: 10px"></div>
@else
    <div class="alert alert-info text-center">
        No se han encontrado resultados que mostrar
    </div>
@endif

<script>
    function mostrar_desglose_ciclo() {
        datos = {
            variedad: $('#variedad_indiccador').val(),
        };
        if (datos['variedad'] != 'T')
            get_jquery('{{url('crm_area/mostrar_desglose_ciclo')}}', datos, function (retorno) {
                $('#div_desglose_ciclo').html(retorno);
            });
    }
</script>
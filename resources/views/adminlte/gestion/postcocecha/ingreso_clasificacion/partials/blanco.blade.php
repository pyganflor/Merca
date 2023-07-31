<div class="input-group" style="margin-top: 5px;">
    <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
        <i class="fa fa-fw fa-calendar"></i> Fecha de trabajo
    </div>
    <input type="date" id="fecha_blanco_filtro" class="form-control input-yura_default text-center" value="{{hoy()}}" max="{{hoy()}}"
           onchange="listar_blanco()">
    <div class="input-group-addon bg-yura_dark">
        <i class="fa fa-fw fa-leaf"></i> Variedad
    </div>

    <select id="planta_blanco_filtro" class="form-control input-yura_default">
        <option value="T">Todas las variedades</option>
        @foreach($plantas as $p)
            <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
        @endforeach
    </select>
    <div class="input-group-btn">
        <button class="btn btn-yura_primary" onclick="listar_blanco()">
            <i class="fa fa-fw fa-search"></i>
        </button>
    </div>
</div>
<div style="margin-top: 5px" id="div_listar_blanco"></div>
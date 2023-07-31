<input type="hidden" id="id_detalle" value="{{ $detalle->id_detalle_caja_frio }}">
<legend style="font-size: 1.1em; margin-bottom: 2px" class="text-center">
    Seleccione la cantidad de <b>RAMOS</b> que desea cambiar de caja
</legend>
<table class="table-bordered" style="width: 100%">
    <tr>
        <th class="text-center th_yura_green">
            Variedad
        </th>
        <th class="text-center th_yura_green">
            Longitud
        </th>
        <th class="text-center th_yura_green">
            Edad
        </th>
        <th class="text-center th_yura_green">
            Tallos
        </th>
        <th class="text-center th_yura_green" style="width: 80px">
            Ramos
        </th>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d">
            {{ $detalle->variedad->nombre }}
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            {{ $detalle->longitud }} <sup>cm</sup>
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <b>{{ difFechas(hoy(), $detalle->fecha)->days }}</b> <sup>dias</sup>
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            {{ $detalle->tallos_x_ramo * $detalle->ramos }}
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <input type="number" style="width: 100%" class="text-center bg-yura_dark" value="{{ $detalle->ramos }}"
                min="1" max="{{ $detalle->ramos }}" onkeyup="verificar_disponibles()" id="ramos_cambiar"
                autofocus>
        </th>
    </tr>
</table>

<legend style="font-size: 1.1em; margin-bottom: 2px; margin-top: 10px" class="text-center">
    Seleccione la <b>CAJA</b> destinataria
</legend>

<div class="input-group">
    <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
        <i class="fa fa-fw fa-gift"></i> OTRAS CAJAS
    </div>
    <select class="form-control input-yura_default" style="width: 100% !important;" id="caja_cambiar">
        @foreach ($cajas as $c)
            <option value="{{ $c->id_caja_frio }}">{{ $c->nombre }}</option>
        @endforeach
    </select>
    <div class="input-group-btn">
        <button class="btn btn-yura_primary" onclick="store_cambiar_caja()">
            <i class="fa fa-fw fa-refresh"></i> Aceptar Cambio
        </button>
    </div>
</div>

<script>
    setTimeout(() => {
        $('#caja_cambiar').select2({
            dropdownParent: $('#div_modal-modal_editar_caja')
        })
        $('.select2-selection').css('height', '34px');
        $('.select2-selection').css('border-radius', '0');
    }, 500);
</script>

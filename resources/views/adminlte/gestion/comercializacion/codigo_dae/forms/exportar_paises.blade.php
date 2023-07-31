<table style="width: 100%">
    <tr>
        <td style="vertical-align: top; width: 50%; padding-right: 5px">
            <div class="panel panel-success" style="margin-bottom: 0px" id="panel_inventarios">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <div id="titulo_inventarios">
                        <b> <i class="fa fa-flag"></i> LISTADO DE PAISES </b>
                    </div>
                </div>
                <div class="panel-body" id="body_inventarios" style="max-height: 600px">
                    <div id="div_inventario" style="max-height:550px; overflow:auto">
                        <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d" id="table_paises">
                            <thead>
                                <tr class="tr_fija_top_0">
                                    <th class="text-center th_yura_green padding_lateral_5">
                                        Codigo
                                    </th>
                                    <th class="text-center th_yura_green">
                                        Nombre
                                    </th>
                                    <th class="text-center th_yura_green">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paises as $item)
                                    <tr onmouseover="$('.tr_pais_{{ $item->codigo }}').css('background-color', 'cyan')"
                                        onmouseleave="$('.tr_pais_{{ $item->codigo }}').css('background-color', '')"
                                        class="tr_pais_{{ $item->codigo }}">
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            {{ $item->codigo }}
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            {{ $item->nombre }}
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <button type="button" class="btn btn-xs btn-yura_dark"
                                                onclick="agregar_pais_exportar('{{ $item->codigo }}', '{{ $item->nombre }}')">
                                                <i class="fa fa-fw fa-arrow-right"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </td>
        <td style="vertical-align: top; width: 50%; padding-left: 5px">
            <div class="panel panel-success" style="margin-bottom: 0px" id="panel_inventarios">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <div id="titulo_inventarios">
                        <b> <i class="fa fa-download"></i> PAISES A EXPORTAR </b>
                    </div>
                </div>
                <div class="panel-body" id="body_inventarios" style="max-height: 600px">
                    <div id="div_inventario" style="max-height:550px; overflow:auto">
                        <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d"
                            id="table_exportar">
                            <thead>
                                <tr class="tr_fija_top_0">
                                    <th class="text-center th_yura_green" style="width: 30px">
                                    </th>
                                    <th class="text-center th_yura_green">
                                        Nombre
                                    </th>
                                    <th class="text-center th_yura_green padding_lateral_5">
                                        Codigo
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mis_paises as $item)
                                    <tr class="tr_pais_{{ $item->codigo }}">
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            <button type="button" class="btn btn-xs btn-yura_danger"
                                                onclick="$(this).closest('tr').remove()">
                                                <i class="fa fa-fw fa-arrow-left"></i>
                                            </button>
                                            <input type="hidden" id="codigo_pais_exportar_{{ $item->codigo }}"
                                                class="codigo_pais_exportar" value="{{ $item->codigo }}">
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            {{ $item->nombre }}
                                        </td>
                                        <td class="text-center" style="border-color: #9d9d9d">
                                            {{ $item->codigo }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

<div class="text-center" style="margin-top: 5px">
    <button type="button" class="btn btn-yura_primary" onclick="descargar_plantilla()">
        <i class="fa fa-fw fa-download"></i> Descargar Plantilla
    </button>
</div>

<script>
    estructura_tabla('table_paises');
    $('#table_paises_filter').parent().removeClass('col-sm-6').addClass('col-sm-12');
    $('#table_paises_filter>label>input').addClass('input-yura_default text-center').prop('placeholder', 'Busqueda');

    function agregar_pais_exportar(codigo, nombre) {
        if (!$('#codigo_pais_exportar_' + codigo).length) {
            parametro_button = "'tr'";
            $('#table_exportar').append('<tr class="tr_pais_' + codigo + '">' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<button type="button" class="btn btn-xs btn-yura_danger"' +
                'onclick="$(this).closest(' + parametro_button + ').remove()">' +
                '<i class="fa fa-fw fa-arrow-left"></i>' +
                '</button>' +
                '<input type="hidden" id="codigo_pais_exportar_' + codigo + '"' +
                'value="' + codigo + '" class="codigo_pais_exportar">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                nombre +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                codigo +
                '</td>' +
                '</tr>')
        }
    }

    function descargar_plantilla() {
        codigo_pais_exportar = $('.codigo_pais_exportar');
        data = [];
        for (i = 0; i < codigo_pais_exportar.length; i++) {
            data.push(codigo_pais_exportar[i].value);
        }
        if (data.length > 0) {
            $.LoadingOverlay('show');
            window.open('{{ url('codigo_dae/descargar_plantilla') }}?data=' + JSON.stringify(data), '_blank');
            $.LoadingOverlay('hide');
        }
    }
</script>

<div style="overflow-x: scroll">
    <table style="width: 100%">
        <tr>
            <td>
                <div class="input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        Finca
                    </span>
                    <select id="finca_contabilidad" style="width: 100%" class="form-control">
                        <option value="T">Todas las fincas</option>
                        @foreach ($fincas as $f)
                            <option value="{{ $f->id_empresa }}">
                                {{ $f->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-addon bg-yura_dark">
                        Desde
                    </span>
                    <input type="date" id="desde_contabilidad" style="width: 100%" class="form-control text-center"
                        value="{{ hoy() }}">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <div class="input-group">
                        <span class="input-group-addon bg-yura_dark">
                            Hasta
                        </span>
                        <input type="date" id="hasta_contabilidad" style="width: 100%"
                            class="form-control text-center" value="{{ hoy() }}">
                    </div>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-yura_primary" onclick="descargar_contabilidad()">
                            <i class="fa fa-fw fa-download"></i> Descargar archivo
                        </button>
                    </span>
                </div>
            </td>
        </tr>
    </table>
</div>

<script>
    function descargar_contabilidad() {
        $.LoadingOverlay('show');
        window.open('{{ url('pedido_bodega/descargar_contabilidad') }}?finca=' + $('#finca_contabilidad').val() +
            '&desde=' + $('#desde_contabilidad').val() +
            '&hasta=' + $('#hasta_contabilidad').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

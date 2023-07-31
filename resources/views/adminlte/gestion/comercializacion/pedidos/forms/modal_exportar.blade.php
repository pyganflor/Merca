<div class="input-group input-group">
    <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
        Desde
    </div>
    <input type="date" id="filtro_desde" name="filtro_desde" required class="form-control input-yura_default text-center"
        style="width: 100% !important;" value="{{ $desde }}">
    <div class="input-group-addon bg-yura_dark">
        Hasta
    </div>
    <input type="date" id="filtro_hasta" name="filtro_hasta" required
        class="form-control input-yura_default text-center" style="width: 100% !important;" value="{{ $hasta }}">
    <div class="input-group-btn">
        <button class="btn btn-primary btn-yura_primary" onclick="exportar_pedidos()">
            <i class="fa fa-fw fa-download"></i>
        </button>
    </div>
</div>

<script>
    function exportar_pedidos() {
        $.LoadingOverlay('show');
        window.open('{{ url('pedidos/exportar_pedidos') }}?desde=' + $('#filtro_desde').val() +
            '&hasta=' + $('#filtro_hasta').val(), '_blank');
        $.LoadingOverlay('hide');
    }
</script>

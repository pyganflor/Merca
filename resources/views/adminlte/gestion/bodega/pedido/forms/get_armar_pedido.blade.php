<div class="input-group">
    <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
        <i class="fa fa-fw fa-barcode"></i> Escanear Codigo
    </div>
    <input type="text" id="filtro_codigo_barra" required class="form-control input-yura_default text-center" autofocus
        style="width: 100% !important;" onchange="escanear_codigo()">
    <div class="input-group-btn">
        <button class="btn btn-yura_primary" onclick="escanear_codigo()">
            <i class="fa fa-fw fa-search"></i>
        </button>
    </div>
</div>
<div id="div_escaneo_codigo" style="margin-top: 5px"></div>

<script>
    var customLoading = $("<p>", {
        "css": {
            "font-size": "2em",
            "text-align": "center",
            "margin-top": "7px",
            "color": "white",
        },
        "text": "ESPERANDO_LECTURA"
    });
    setTimeout(() => {
        $('#filtro_codigo_barra').focus();
    }, 500);

    var input_scan = document.getElementById("filtro_codigo_barra");
    input_scan.addEventListener("focus", myFocusFunction, true);
    input_scan.addEventListener("blur", myBlurFunction, true);

    function myFocusFunction() {
        $("#filtro_codigo_barra").LoadingOverlay("show", {
            image: "",
            custom: customLoading
        });
    }

    function myBlurFunction() {
        $("#filtro_codigo_barra").LoadingOverlay('hide');
    }

    function escanear_codigo_pedido() {
        datos = {
            codigo: $('#filtro_codigo_barra').val(),
        }
        get_jquery('{{ url('inventario_cajas/escanear_codigo_pedido') }}', datos, function(retorno) {
            $('#div_escaneo_codigo').html(retorno);
            $('#filtro_codigo_barra').val('');
            $('#filtro_codigo_barra').focus();
        }, 'div_escaneo_codigo');
    }
</script>

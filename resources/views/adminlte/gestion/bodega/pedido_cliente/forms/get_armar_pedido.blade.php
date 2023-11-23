<div class="input-group">
    <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
        <i class="fa fa-fw fa-barcode"></i> Escanear Codigo
    </div>
    <input type="text" id="filtro_codigo_barra" required class="form-control input-yura_default text-center" autofocus
        style="width: 100% !important;" onchange="escanear_codigo_pedido()">
    <div class="input-group-btn">
        <button class="btn btn-yura_primary" onclick="escanear_codigo_pedido()">
            <i class="fa fa-fw fa-search"></i>
        </button>
    </div>
</div>
<table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
    <tr>
        <th class="text-center th_yura_green">
            #PEDIDO
        </th>
        <th class="text-center th_yura_green">
            USUARIO
        </th>
        <th class="text-center th_yura_green">
            FECHA ENTREGA
        </th>
        <th class="text-center th_yura_green">
            $TOTAL
        </th>
        <th class="text-center th_yura_green">
        </th>
    </tr>
    <tbody id="tbody_pedidos_escaneados"></tbody>
</table>

<div class="text-center" style="margin-top: 5px">
    <button type="button" class="btn btn-sm btn-yura_primary" onclick="store_armar_pedidos()">
        <i class="fa fa-fw fa-save"></i> ARMAR PEDIDOS
    </button>
</div>

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
        get_jquery('{{ url('pedido_bodega/escanear_codigo_pedido') }}', datos, function(retorno) {
            $('#tbody_pedidos_escaneados').append(retorno);
            $('#filtro_codigo_barra').val('');
            $('#filtro_codigo_barra').focus();
        }, 'tbody_pedidos_escaneados');
    }

    function store_armar_pedidos() {
        data = [];
        id_pedido_escaneado = $('.id_pedido_escaneado');
        for (i = 0; i < id_pedido_escaneado.length; i++) {
            data.push(id_pedido_escaneado[i].value);
        }
        datos = {
            _token: '{{ csrf_token() }}',
            codigo: $('#filtro_codigo_barra').val(),
            data: JSON.stringify(data)
        }
        post_jquery_m('{{ url('pedido_bodega/store_armar_pedidos') }}', datos, function(retorno) {
            cerrar_modals();
            listar_reporte();
        });
    }
</script>

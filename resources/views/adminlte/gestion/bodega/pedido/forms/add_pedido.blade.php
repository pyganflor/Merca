<div style="overflow-x: scroll">
    <table style="width: 100%">
        <tr>
            <td class="text-center" style="border-color: #9d9d9d; min-width: 220px">
                <div class="input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        Fecha
                    </span>
                    <input type="date" id="form_fecha" style="width: 100%" class="text-center form-control"
                        value="{{ hoy() }}" min="{{ hoy() }}">
                </div>
            </td>
            <td class="text-center" style="border-color: #9d9d9d; min-width: 260px">
                <div class="input-group">
                    <span class="input-group-addon bg-yura_dark">
                        Finca
                    </span>
                    <select id="form_finca" style="width: 100%" class="form-control">
                        @foreach ($fincas as $f)
                            <option value="{{ $f->id_empresa }}">
                                {{ $f->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td class="text-center" style="border-color: #9d9d9d; min-width: 260px">
                <div class="input-group">
                    <span class="input-group-addon bg-yura_dark">
                        Usuario
                    </span>
                    @if (in_array(session('id_usuario'), [1, 2]))
                        <select id="form_usuario" style="width: 100%" class="form-control input-yura_default">
                        </select>
                    @else
                        <input type="text" style="width: 100%" class="form-control input-yura_default" readonly
                            value="{{ getUsuario(session('id_usuario'))->nombre_completo }}">
                        <input type="hidden" id="form_usuario" value="{{ session('id_usuario') }}">
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>

<ul class="nav nav-pills nav-justified">
    <li class="active">
        <a data-toggle="tab" href="#tab-catalogo" aria-expanded="true">
            <i class="fa fa-fw fa-list"></i> Catalogo
        </a>
    </li>
    <li class="">
        <a data-toggle="tab" href="#tab-contenido_pedido" aria-expanded="false">
            <i class="fa fa-fw fa-shopping-cart"></i> Contenido del Pedido
            <sup><span class="badge" id="span_total_monto_pedido">$0.00</span></sup>
        </a>
    </li>
</ul>
<div class="tab-content" style="margin-top: 2px;">
    <div id="tab-catalogo" class="tab-pane fade active in">
        <div class="input-group">
            <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                Buscar
            </span>
            <input type="text" id="buscar_catalogo" style="width: 100%" onchange="listar_catalogo()"
                onkeyup="listar_catalogo()" class="text-center form-control" placeholder="Busqueda de productos">
            <span class="input-group-addon bg-yura_dark">
                Categoria
            </span>
            <select id="categoria_catalogo" style="width: 100%" onchange="listar_catalogo()" onkeyup="listar_catalogo()"
                class="form-control input-yura_default">
                <option value="T">Todas</option>
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->id_categoria_producto }}">
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="div_catalogo" style="overflow-x: scroll; overflow-y: scroll; margin-top: 3px">catalogo</div>
    </div>
    <div id="tab-contenido_pedido" class="tab-pane fade" style="overflow-x: scroll; overflow-y: scroll">
        <div class="container">
            <div class="row" id="row_contenido_pedido"></div>
        </div>
    </div>
</div>

<div class="text-center">
    <div class="btn-group">
        <button type="button" class="btn btn-yura_primary" onclick="store_pedido()">
            <i class="fa fa-fw fa-save"></i> Grabar Pedido
        </button>
        <button type="button" class="btn btn-yura_default" onclick="cerrar_modals(); add_pedido()">
            <i class="fa fa-fw fa-refresh"></i> Reiniciar Formulario
        </button>
    </div>
</div>

<script>
    listar_catalogo();
    @if (in_array(session('id_usuario'), [1, 2]))
        seleccionar_finca();
        setTimeout(() => {
            $('#form_usuario').select2({
                dropdownParent: $('#div_modal-modal_add_pedido')
            })
            $('.select2-selection').css('height', '34px');
            $('.select2-selection').css('border-radius', '0');
        }, 500);
    @endif

    function listar_catalogo() {
        datos = {
            busqueda: $('#buscar_catalogo').val(),
            categoria: $('#categoria_catalogo').val(),
        }
        get_jquery('{{ url('pedido_bodega/listar_catalogo') }}', datos, function(retorno) {
            $('#div_catalogo').html(retorno)
        }, 'div_catalogo');
    }

    function seleccionar_finca() {
        datos = {
            _token: '{{ csrf_token() }}',
            finca: $('#form_finca').val()
        }
        $('#form_usuario').LoadingOverlay('show');
        $.post('{{ url('pedido_bodega/seleccionar_finca') }}', datos, function(retorno) {
            $('#form_usuario').html(retorno.options_usuarios);
        }, 'json').fail(function(retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function() {
            $('#form_usuario').LoadingOverlay('hide');
        });
    }
</script>

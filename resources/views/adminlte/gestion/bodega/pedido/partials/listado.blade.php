<div style="overflow-y: scroll; max-height: 700px" class="container">
    <div class="row">
        @foreach ($listado as $item)
            <div class="col-md-2 sombra_pequeña col-md-listado" onmouseover="$(this).addClass('sombra_primary')"
                onmouseleave="$(this).removeClass('sombra_primary')"
                style="background-image: linear-gradient(to bottom, #6ce0e4, #7ef6ff8a)">
                <span class="span_pedido">
                    <button type="button" class="btn btn-xs btn-yura_dark btn_ver_pedido_listado" title="Ver Pedido"
                        onclick="ver_pedido('{{ $item->id_pedido_bodega }}')">
                        <i class="fa fa-fw fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-yura_danger btn_elimiar_pedido_listado"
                        title="Eliminar Pedido" onclick="delete_pedido('{{ $item->id_pedido_bodega }}')">
                        <i class="fa fa-fw fa-times"></i>
                    </button>
                    {{ $item->usuario->nombre_completo }}
                    <br>
                    <em>{{ $item->empresa->nombre }}</em>
                    <br>
                    <small>{{ convertDateToText($item->fecha) }}</small>
                    <br>
                    <small class="span_contador_productos" title="Productos">
                        {{ $item->getTotalProductos() }} <i class="fa fa-fw fa-gift"></i>
                    </small>
                    <small class="span_contador_monto" title="Monto Total">
                        <i class="fa fa-fw fa-dollar"></i> {{ number_format($item->getTotalMonto(), 2) }}
                    </small>
                </span>
            </div>
        @endforeach
    </div>
</div>

<style>
    .col-md-listado {
        border-radius: 16px;
        border: 1px solid #9d9d9d;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 120px;
        margin-left: 5px;
        margin-right: 5px;
        margin-bottom: 13px;
    }

    .span_pedido {
        padding: 5px;
        text-align: center;
        color: #242424;
        font-weight: bold;
    }

    .btn_ver_pedido_listado {
        position: absolute;
        padding: 5px;
        left: 0;
        top: 0;
        border-radius: 14px 0 16px 0 !important;
    }

    .btn_elimiar_pedido_listado {
        position: absolute;
        padding: 5px;
        right: 0;
        top: 0;
        border-radius: 0 14px 0 16px;
    }

    .span_contador_productos {
        position: absolute;
        padding: 5px;
        left: 0;
        bottom: 0;
        background-image: linear-gradient(to right, #72aac4, #c5d9ff62);
        border-radius: 0 16px 0 14px;
    }

    .span_contador_monto {
        position: absolute;
        padding: 5px;
        right: 0;
        bottom: 0;
        background-image: linear-gradient(to left, #72aac4, #c5d9ff62);
        border-radius: 16px 0 14px 0;
    }
</style>

<script>
    function delete_pedido(ped) {
        texto =
            "<div class='alert alert-warning text-center' style='font-size: 16px'>¿Desea <b>ELIMINAR</b> el pedido?</div>" +
            "</div>";

        modal_quest('modal_delete_pedido', texto, 'Eliminar pedido', true, false, '40%', function() {
            datos = {
                _token: '{{ csrf_token() }}',
                ped: ped,
            };
            post_jquery_m('pedido_bodega/delete_pedido', datos, function() {
                cerrar_modals();
                listar_reporte();
            });
        })
    }
</script>

<div style="overflow-y: scroll; max-height: 700px" class="container">
    <div class="row">
        @foreach ($listado as $item)
            @php
                $fecha_entrega = $item->getFechaEntrega();
            @endphp
            <div class="col-md-2 sombra_pequeña col-md-listado {{ $item->armado == 1 ? 'pedido_armado' : 'pedido_sin_armar' }}"
                onmouseover="$(this).addClass('sombra_primary')" onmouseleave="$(this).removeClass('sombra_primary')"
                title="{{ $item->armado == 1 ? 'Armado' : 'Sin Armar' }}">
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
                    <small title="Fecha de Toma">
                        <i class="fa fa-fw fa-arrow-right color_text-yura_primary"></i>
                        {{ convertDateToText($item->fecha) }}
                    </small>
                    <br>
                    <small title="Fecha de Entrega">
                        <i class="fa fa-fw fa-arrow-right color_text-yura_danger"></i>
                        {{ $fecha_entrega != '' ? convertDateToText($item->getFechaEntrega()) : '' }}
                    </small>
                    <br>
                    <small class="span_contador_productos" title="Productos">
                        {{ $item->getTotalProductos() }}<i class="fa fa-fw fa-gift"></i>
                    </small>
                    <small class="span_contador_monto" title="Monto Total">
                        <i class="fa fa-fw fa-dollar"></i>{{ number_format($item->getTotalMonto(), 2) }}
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
        height: 150px;
        margin-left: 5px;
        margin-right: 5px;
        margin-bottom: 13px;
    }

    .pedido_armado {
        background-image: linear-gradient(to bottom, #3f3f3f, #41414154);
        color: #e7e7e7;
    }

    .pedido_sin_armar {
        background-image: linear-gradient(to bottom, #6ce0e4, #7ef6ff8a);
        color: #242424;
    }

    .span_pedido {
        padding: 5px;
        text-align: center;
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
        color: white;
        background-image: linear-gradient(to right, #3f3f3f, #c5d9ff34);
        border-radius: 0 16px 0 14px;
    }

    .span_contador_monto {
        position: absolute;
        padding: 5px;
        right: 0;
        bottom: 0;
        color: white;
        background-image: linear-gradient(to left, #3f3f3f, #c5d9ff34);
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

    function ver_pedido(ped) {
        datos = {
            ped: ped
        }
        get_jquery('{{ url('pedido_bodega/ver_pedido') }}', datos, function(retorno) {
            modal_view('modal_ver_pedido', retorno,
                '<i class="fa fa-fw fa-shopping-cart"></i> Ver Pedido',
                true, false, '{{ isPC() ? '98%' : '' }}',
                function() {});
        });
    }
</script>

<div style="overflow-y: scroll; max-height: 700px" class="container">
    <div class="row">
        @php
            $total_costos_armados = 0;
            $total_costos_pendientes = 0;
            $total_venta_armados = 0;
            $total_venta_pendientes = 0;
        @endphp
        @foreach ($listado as $item)
            @php
                $costo = $item->getCostos();
                $venta = $item->getTotalMonto();
                if ($item->armado == 1) {
                    $total_costos_armados += $costo;
                    $total_venta_armados += $venta;
                } else {
                    $total_costos_pendientes += $costo;
                    $total_venta_pendientes += $venta;
                }
            @endphp
            <div class="col-md-2 sombra_pequeña col-md-listado {{ $item->armado == 1 ? 'pedido_armado' : 'pedido_sin_armar' }}"
                onmouseover="$(this).addClass('sombra_primary')" onmouseleave="$(this).removeClass('sombra_primary')"
                title="{{ $item->armado == 1 ? 'Armado' : 'Sin Armar' }}">
                <span class="span_pedido">
                    <div class="btn-group btn_ver_pedido_listado">
                        <button type="button" class="btn btn-xs btn-yura_dark" title="Ver Pedido"
                            onclick="ver_pedido('{{ $item->id_pedido_bodega }}')"
                            style="height: 30px; border-radius: 14px 0 0 0">
                            <i class="fa fa-fw fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-yura_default" title="Imprimir Pedido"
                            onclick="imprimir_pedido('{{ $item->id_pedido_bodega }}')"
                            style="height: 30px; border-radius: 0 0 14px 0;">
                            <i class="fa fa-fw fa-print"></i>
                        </button>
                    </div>
                    @if ($item->armado == 0 && substr($item->fecha, 0, 7) == substr(hoy(), 0, 7))
                        <button type="button" class="btn btn-xs btn-yura_danger btn_elimiar_pedido_listado"
                            title="Eliminar Pedido" onclick="delete_pedido('{{ $item->id_pedido_bodega }}')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    @endif
                    {{ $item->usuario->nombre_completo }}
                    <br>
                    <em>{{ $item->empresa->nombre }}</em>
                    <br>
                    <small title="Fecha de Toma">
                        <i class="fa fa-fw fa-arrow-right color_text-yura_primary"></i>
                        {{ convertDateToText($item->fecha) }}
                    </small>
                    <br>
                    <small class="span_contador_productos color_text-yura_danger" title="Costo">
                        ${{ number_format($costo, 2) }}
                    </small>
                    <small class="span_contador_monto color_text-yura_primary" title="Venta">
                        ${{ number_format($venta, 2) }}
                    </small>
                </span>
            </div>
        @endforeach
    </div>
</div>
<legend style="font-size: 1em; margin-bottom: 5px" class="text-right">
    <b>Resumen</b>
</legend>
<table class="table-bordered" style="font-size: 1em; width: 100%" class="text-right">
    <tr>
        <td></td>
        <th class="text-right" style="width: 80px; border-bottom-color: #9d9d9d">
            Valor
        </th>
        <th class="text-right" style="width: 80px; border-bottom-color: #9d9d9d">
            Margen
        </th>
        <th class="text-right" style="width: 80px; border-bottom-color: #9d9d9d">
            Utilidad
        </th>
    </tr>
    @php
        $margen_armados = $total_venta_armados - $total_costos_armados;
        $utilidad_armados = porcentaje($margen_armados, $total_costos_armados, 1);
    @endphp
    <tr>
        <th class="text-right">
            <span class="badge bg-yura_dark">
                Costos Armados
            </span>
        </th>
        <th class="text-right">
            ${{ number_format($total_costos_armados, 2) }}
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d" rowspan="2">
            ${{ number_format($margen_armados, 2) }}
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d" rowspan="2">
            {{ number_format($utilidad_armados, 2) }}%
        </th>
    </tr>
    <tr>
        <th class="text-right">
            <span class="badge bg-yura_dark">
                Venta Armados
            </span>
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d">
            ${{ number_format($total_venta_armados, 2) }}
        </th>
    </tr>
    @php
        $margen_pendientes = $total_venta_pendientes - $total_costos_pendientes;
        $utilidad_pendientes = porcentaje($margen_pendientes, $total_costos_pendientes, 1);
    @endphp
    <tr>
        <th class="text-right">
            <span class="badge" style="background-color: #6ce0e4; color: black">
                Costos Pendientes
            </span>
        </th>
        <th class="text-right" style="width: 80px">
            ${{ number_format($total_costos_pendientes, 2) }}
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d" rowspan="2">
            ${{ number_format($margen_pendientes, 2) }}
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d" rowspan="2">
            {{ number_format($utilidad_pendientes, 2) }}%
        </th>
    </tr>
    <tr>
        <th class="text-right">
            <span class="badge" style="background-color: #6ce0e4; color: black">
                Venta Pendientes
            </span>
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d">
            ${{ number_format($total_venta_pendientes, 2) }}
        </th>
    </tr>
    @php
        $total_costos = $total_costos_armados + $total_costos_pendientes;
        $total_venta = $total_venta_armados + $total_venta_pendientes;
        $margen_total = $total_venta - $total_costos;
        $utilidad_total = porcentaje($margen_total, $total_costos, 1);
    @endphp
    <tr>
        <th class="text-right">
            <span class="badge bg-yura_danger">
                TOTAL COSTOS
            </span>
        </th>
        <th class="text-right">
            ${{ number_format($total_costos, 2) }}
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d" rowspan="2">
            ${{ number_format($margen_total, 2) }}
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d" rowspan="2">
            {{ number_format($utilidad_total, 2) }}%
        </th>
    </tr>
    <tr>
        <th class="text-right">
            <span class="badge bg-yura_primary">
                TOTAL VENTA
            </span>
        </th>
        <th class="text-right" style="border-bottom-color: #9d9d9d">
            ${{ number_format($total_venta, 2) }}
        </th>
    </tr>
</table>
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
        left: 0;
        top: 0;
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
        background-image: linear-gradient(to bottom, #ffffffea, #dbdbdb);
        border-radius: 0 16px 0 14px;
    }

    .span_contador_monto {
        position: absolute;
        padding: 5px;
        right: 0;
        bottom: 0;
        background-image: linear-gradient(to bottom, #ffffffea, #dbdbdb);
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

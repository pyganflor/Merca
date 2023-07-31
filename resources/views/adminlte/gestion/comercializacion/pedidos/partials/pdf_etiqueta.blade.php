@foreach ($datos['pedido']->detalles as $pos_det => $det)
    @php
        $caja_frio = $det->caja_frio;
    @endphp
    <div style="position: relative; top: -40px; left: -30px; width: 260px">
        <table class="text-center" style="font-size: 0.9em; width: 100%">
            <tr>
                <td style="text-align: center;">
                    {!! $barCode->getBarcode($datos['pedido']->codigo_dae, $barCode::TYPE_CODE_128, 1) !!}
                </td>
            </tr>
        </table>

        <table style="width: 100%">
            <tr>
                <td rowspan="2">
                    <img src="{{ public_path('/images/logo_senae.png') }}" width="50px" alt="Logo">
                </td>
                <td class="text-center" style="font-size: 0.6em" colspan="2">
                    <b>{{ $datos['pedido']->codigo_dae }}</b>
                </td>
            </tr>
            <tr>
                <td class="text-center" style="font-size: 0.8em" colspan="3">
                    <b>Miranda Flowers S.A.S.</b>
                </td>
            </tr>
            <tr>
                <td style="font-size: 0.6em" colspan="2">
                    <em>RUC 1792721032001 .17030802</em>
                </td>
                <td style="font-size: 0.6em; text-align: right">
                    <em>{{ str_pad($datos['pedido']->packing, 8, '0', STR_PAD_LEFT) }}</em>
                </td>
            </tr>
            <tr>
                <td style="font-size: 0.6em" colspan="2">
                    <em>CUST {{ $datos['pedido']->consignatario->nombre }}</em>
                </td>
                <td style="font-size: 0.6em; text-align: right">
                    <small><em>invoice:</em></small> <em>{{ str_pad($datos['pedido']->factura, 8, '0', STR_PAD_LEFT) }}</em>
                </td>
            </tr>
            <tr>
                <td style="font-size: 0.6em" colspan="3">
                    <em>AWB:</em> {{ $datos['pedido']->guia_madre }}___<em>HAWB:</em> {{ $datos['pedido']->guia_hija }}
                </td>
            </tr>
        </table>

        <table class="border-1px" style="font-size: 10px">
            <tr>
                @php
                    $total_30 = 0;
                    $total_40 = 0;
                    $total_50 = 0;
                    $total_60 = 0;
                    $total_70 = 0;
                    $total_80 = 0;
                    $total_90 = 0;
                    $total_100 = 0;
                    $total_110 = 0;
                    $total_120 = 0;
                    $total_tallos = 0;
                    $total_ramos = 0;
                @endphp
                <td class="border-1px text-center" style="width: 110px">
                    VARIEDAD
                </td>
                <td class="border-1px text-center">
                    30
                </td>
                <td class="border-1px text-center">
                    40
                </td>
                <td class="border-1px text-center">
                    50
                </td>
                <td class="border-1px text-center">
                    60
                </td>
                <td class="border-1px text-center">
                    70
                </td>
                <td class="border-1px text-center">
                    80
                </td>
                <td class="border-1px text-center">
                    90
                </td>
                <td class="border-1px text-center">
                    100
                </td>
                <td class="border-1px text-center">
                    110
                </td>
                <td class="border-1px text-center">
                    120
                </td>
            </tr>
            @foreach ($caja_frio->getDetallesAgrupados() as $pos_item => $item)
                @php
                    $variedad = getVariedad($item->id_variedad);
                    $total_tallos += $item->ramos * $item->tallos_x_ramo;
                    $total_ramos += $item->ramos;
                @endphp
                <tr>
                    <td class="text-center border-1px">
                        {{ $variedad->nombre }}
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 30 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 30) {
                                $total_30 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 40 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 40) {
                                $total_40 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 50 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 50) {
                                $total_50 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 60 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 60) {
                                $total_60 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 70 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 70) {
                                $total_70 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 80 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 80) {
                                $total_80 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 90 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 90) {
                                $total_90 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 100 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 100) {
                                $total_100 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 110 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 110) {
                                $total_110 += $item->ramos;
                            }
                        @endphp
                    </td>
                    <td class="border-1px text-center">
                        {{ $item->longitud == 120 ? $item->ramos : '' }}
                        @php
                            if ($item->longitud == 120) {
                                $total_120 += $item->ramos;
                            }
                        @endphp
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="text-center">
                    Stems: <b>{{ number_format($total_tallos) }}</b>
                </td>
                <th class="border-1px text-center">
                    {{ $total_30 > 0 ? $total_30 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_40 > 0 ? $total_40 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_50 > 0 ? $total_50 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_60 > 0 ? $total_60 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_70 > 0 ? $total_70 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_80 > 0 ? $total_80 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_90 > 0 ? $total_90 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_100 > 0 ? $total_100 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_110 > 0 ? $total_110 : '' }}
                </th>
                <th class="border-1px text-center">
                    {{ $total_120 > 0 ? $total_120 : '' }}
                </th>
            </tr>
            <tr>
                <td class="border-1px" style="text-align: right; padding-right: 5px" colspan="8">
                    <em style="font-size: 0.8em">AGENCY:</em><b>{{ $datos['pedido']->agencia_carga->nombre }}</b>
                </td>
                <td class="border-1px" style="text-align: right; padding-right: 5px" colspan="3">
                    <b>{{ $pos_det + 1 }}/{{ count($datos['pedido']->detalles) }}</b> {{ $caja_frio->tipo }}
                </td>
            </tr>
        </table>
        <div class="text-center" style="font-size: 0.8em; width: 100%">
            <b>PRODUCT GROWN IN ECUADOR</b>
        </div>
    </div>

    @if ($pos_det < count($datos['pedido']->detalles) - 1)
        <div style="page-break-after:always;"></div>
    @endif
@endforeach

<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        margin: 0;
    }

    .sin_margen {
        margin: 0;
        padding: 0;
    }

    .border-1px {
        border: 1px solid black;
    }

    .text-center {
        text-align: center;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
    }

    td,
    th {
        padding: 0;
        margin: 0;
    }
</style>

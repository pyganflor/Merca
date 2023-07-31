<div style="position: relative; top: -35px; left: -30px; width: 450px">
    <table class="border-1px" style="font-size: 0.9em">
        <tr>
            <td style="width: 20%">
                <img src="{{ public_path('/images/Miranda-logo.png') }}" width="80px" alt="Logo">
            </td>
            <td style="text-align: center; vertical-align: top">
                <table>
                    <tr>
                        <td style="text-align: center; vertical-align: top">
                            Miranda Flowers S.A.S.
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; vertical-align: top; font-size: 0.7em">
                            Tabacundo-Ecuador
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td style="width: 50%; font-size: 0.8em">
                            PACKING LIST: <b>{{ str_pad($datos['pedido']->packing, 8, '0', STR_PAD_LEFT) }}</b>
                        </td>
                        <td style="width: 50%; font-size: 0.8em; text-align: right">
                            DATE: <b>{{ $datos['pedido']->fecha_pedido }}</b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="vertical-align: top; width: 50%;">
                <table>
                    <tr>
                        <td style="font-size: 0.6em; width: 70px">
                            CUSTOMER:
                        </td>
                        <td style="font-size: 0.6em">
                            <b>{{ $datos['pedido']->cliente->detalle()->nombre }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 0.6em">
                            CONSIGNEE:
                        </td>
                        <td style="font-size: 0.6em">
                            <b>{{ $datos['pedido']->consignatario->nombre }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 0.6em">
                            ADDRESS:
                        </td>
                        <td style="font-size: 0.6em">
                            <b>{{ $datos['pedido']->consignatario->pais()->nombre }}</b>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td style="font-size: 0.6em; text-align: right; margin-right: 5px !important">
                            M.A.W.B:
                        </td>
                        <td style="font-size: 0.6em; text-align: left">
                            <b>{{ $datos['pedido']->guia_madre }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 0.6em; text-align: right; margin-right: 5px !important">
                            H.A.W.B:
                        </td>
                        <td style="font-size: 0.6em; text-align: left">
                            <b>{{ $datos['pedido']->guia_hija }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 0.6em; text-align: right; margin-right: 5px !important">
                            CARRIER:
                        </td>
                        <td style="font-size: 0.6em; text-align: left">
                            <b>{{ $datos['pedido']->agencia_carga->nombre }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 0.6em; text-align: right; margin-right: 5px !important">
                            COUNTRY:
                        </td>
                        <td style="font-size: 0.6em; text-align: left">
                            <b>{{ $datos['pedido']->consignatario->pais()->nombre }}</b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="border-1px" style="font-size: 10px">
        <tr>
            <td class="text-center" colspan="2">
            </td>
            <td class="border-1px text-center">
                VARIETY
            </td>
            <td class="border-1px text-center">
            </td>
            <td class="border-1px text-center" colspan="10">
                SIZE STEMS / GRADO
            </td>
            <td class="border-1px text-center">
                T. STEMS
            </td>
        </tr>
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
                $total_full_box = 0;
            @endphp
            <td class="border-1px text-center">
                #BOX
            </td>
            <td class="border-1px text-center">
                BOX T
            </td>
            <td class="border-1px text-center">
                VARIEDAD
            </td>
            <td class="border-1px text-center">
                TxB
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
            <td class="border-1px text-center">
                T. TALLOS
            </td>
        </tr>
        @foreach ($datos['pedido']->detalles as $pos_det => $det)
            @php
                $caja_frio = $det->caja_frio;
                switch ($caja_frio->tipo) {
                    case 'HB':
                        $total_full_box += 0.5;
                        break;
                    case 'QB':
                        $total_full_box += 0.25;
                        break;
                    case 'EB':
                        $total_full_box += 0.125;
                        break;
                
                    default:
                        break;
                }
            @endphp
            @foreach ($caja_frio->getDetallesAgrupados() as $pos_item => $item)
                @php
                    $variedad = getVariedad($item->id_variedad);
                @endphp
                <tr>
                    <td class="text-center border-1px">
                        {{ $pos_det + 1 }}
                    </td>
                    <td class="text-center border-1px">
                        {{ $caja_frio->tipo }}
                    </td>
                    <td class="text-center border-1px">
                        {{ $variedad->nombre }}
                    </td>
                    <td class="text-center border-1px">
                        {{ $item->tallos_x_ramo }}
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
                    <td class="border-1px text-center">
                        {{ $item->ramos * $item->tallos_x_ramo }}
                        @php
                            $total_tallos += $item->ramos * $item->tallos_x_ramo;
                            $total_ramos += $item->ramos;
                        @endphp
                    </td>
                </tr>
            @endforeach
        @endforeach
        <tr>
            <td class="text-center" colspan="4">
                TOTALS
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
            <th class="border-1px text-center">
                {{ number_format($total_tallos) }}
            </th>
        </tr>
        <tr>
            <td class="border-1px text-center">
                #BOX
            </td>
            <td class="border-1px text-center">
                BOX T
            </td>
            <td class="border-1px text-center">
                VARIEDAD
            </td>
            <td class="border-1px text-center">
                TxB
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
            <td class="border-1px text-center">
                T. TALLOS
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width: 50%;">
            </td>
            <td style="width: 50%; font-size: 0.7em; text-align: right">
                TOT.BOX: <b>{{ $total_full_box }}</b>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
            </td>
            <td style="width: 50%; font-size: 0.7em; text-align: right">
                TOT.BOUNCHES: <b>{{ $total_ramos }}</b>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;">
            </td>
            <td style="width: 50%; font-size: 0.7em; text-align: right">
                TOT.STEMS: <b>{{ number_format($total_tallos) }}</b>
            </td>
        </tr>
    </table>
</div>
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

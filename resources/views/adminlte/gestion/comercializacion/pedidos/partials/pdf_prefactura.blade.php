<div style="position: relative; top: -35px; width: 100%">
    <table style="font-size: 0.9em">
        <tr>
            <td class="text-center">
                <img src="{{ public_path('/images/Miranda-logo.png') }}" width="160px" alt="Logo">
            </td>
        </tr>
        <tr>
            <th class="text-center">
                COMMERCIAL INVOICE
            </th>
        </tr>
    </table>

    <table style="width: 100%">
        <tr>
            <td style="vertical-align: top; width: 55%; padding-right: 5px">
                <table style="width: 100%">
                    <tr>
                        <td class="text-center" style="font-size: 0.6em">
                            Shiper Name and Address
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center border-1px" style="font-size: 0.8em; vertical-align: top; height: 85px;">
                            <b style="font-size: 1.1em">Miranda Flowers S.A.S.</b>
                            <br>
                            Tabacundo-Ecuador
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" style="font-size: 0.6em">
                            Marketing name
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center border-1px" style="font-size: 0.8em">
                            Miranda Flowers S.A.S.
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" style="font-size: 0.6em">
                            Customer
                        </td>
                    </tr>
                    <tr>
                        <th class="border-1px" style="font-size: 0.8em; text-align: left">
                            {{ $datos['pedido']->cliente->detalle()->nombre }}
                        </th>
                    </tr>
                    <tr>
                        <td class="text-center" style="font-size: 0.6em">
                            Consignee Name and Address
                        </td>
                    </tr>
                    <tr>
                        <td class="border-1px"
                            style="font-size: 0.8em; text-align: left; vertical-align: top; height: 85px;">
                            <b>{{ $datos['pedido']->consignatario->nombre }}</b>
                            <br>
                            {{ $datos['pedido']->consignatario->pais()->nombre }}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; width: 45%">
                <table style="width: 100%">
                    <tr>
                        <td class="text-center" style="font-size: 0.6em">
                            Packing List
                        </td>
                        <td class="text-center" style="font-size: 0.6em">
                            Country Code
                        </td>
                        <td class="text-center" style="font-size: 0.6em">
                            Date
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px">
                            {{ str_pad($datos['pedido']->packing, 8, '0', STR_PAD_LEFT) }}
                        </th>
                        <th class="text-center border-1px">
                            EC
                        </th>
                        <th class="text-center border-1px">
                            {{ $datos['pedido']->fecha_pedido }}
                        </th>
                    </tr>
                </table>

                <table style="width: 100%">
                    <tr>
                        <td class="text-center" style="font-size: 0.6em">
                            AWB:
                        </td>
                        <td class="text-center" style="font-size: 0.6em">
                            HAWB:
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px">
                            {{ $datos['pedido']->guia_madre }}
                        </th>
                        <th class="text-center border-1px">
                            {{ $datos['pedido']->guia_hija }}
                        </th>
                    </tr>
                    <tr class="text-center" style="font-size: 0.6em">
                        <td class="text-center" colspan="2">
                            SESA
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px" colspan="2">
                            1792721032001 .17030802
                        </th>
                    </tr>
                    <tr class="text-center" style="font-size: 0.6em">
                        <td class="text-center" colspan="2">
                            AIRLINE
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px" colspan="2">
                            {{ $datos['aerolinea'] }}
                        </th>
                    </tr>
                    <tr class="text-center" style="font-size: 0.6em">
                        <td class="text-center" colspan="2">
                            Pais
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px" colspan="2">
                            {{ $datos['pedido']->consignatario->pais()->nombre }}
                        </th>
                    </tr>
                    <tr class="text-center" style="font-size: 0.6em">
                        <td class="text-center" colspan="2">
                            FREIGHT FORWARDER
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px" colspan="2">
                            {{ $datos['pedido']->agencia_carga->nombre }}
                        </th>
                    </tr>
                    <tr class="text-center" style="font-size: 0.6em">
                        <td class="text-center" colspan="2">
                            T. OF SALE
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px" colspan="2">
                            FIXE PRICE
                        </th>
                    </tr>
                    <tr class="text-center" style="font-size: 0.6em">
                        <td class="text-center" colspan="2">
                            C Frio
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px" colspan="2">
                            TABACARCEN
                        </th>
                    </tr>
                    <tr>
                        <td class="text-center" style="font-size: 0.6em">
                            R.U.C. No.
                        </td>
                        <td class="text-center" style="font-size: 0.6em">
                            DAE No.
                        </td>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <th class="text-center border-1px">
                            1792721032001
                        </th>
                        <th class="text-center border-1px">
                            {{ $datos['pedido']->codigo_dae }}
                        </th>
                    </tr>
                    <tr style="font-size: 0.8em">
                        <td class="text-center border-1px" colspan="2">
                            1792721032001 .17030802
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 5px" class="border-1px">
        <tr>
            <td class="text-center border-1px" style="font-size: 0.7em">
                BOX CAJAS
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em">
                PIECE PIEZAS
            </td>
            <td class="text-center border-1px" style="font-size: 0.6em; width: 130px">
                DESCRIPTION (Scientific Genus,
                Scientific species)
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em">
                STEMS
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em">
                SGP
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em">
                NANDINA
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em">
                HTS#
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em">
                BUNCH BONCHES
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em">
                PRICE PRECIO
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em">
                TOTAL
            </td>
        </tr>
        @php
            $total_piezas = 0;
            $total_full = 0;
            $total_tallos = 0;
            $total_monto = 0;
        @endphp
        @foreach ($datos['getResumenTipoCaja'] as $pos_i => $item)
            @php
                $total_piezas += $item['t']->cantidad;
                switch ($item['t']->tipo) {
                    case 'HB':
                        $total_full += 0.5 * $item['t']->cantidad;
                        break;
                    case 'QB':
                        $total_full += 0.25 * $item['t']->cantidad;
                        break;
                    case 'EB':
                        $total_full += 0.125 * $item['t']->cantidad;
                        break;
                
                    default:
                        break;
                }
            @endphp
            @foreach ($item['plantas'] as $pos_p => $p)
                @php
                    $total_tallos += $p->tallos;
                    $total_monto += $p->monto;
                @endphp
                <tr>
                    @if ($pos_p == 0)
                        <th class="text-center border-1px" style="font-size: 0.7em"
                            rowspan="{{ count($item['plantas']) }}">
                            {{ $item['t']->tipo }}
                        </th>
                        <th class="text-center border-1px" style="font-size: 0.7em"
                            rowspan="{{ count($item['plantas']) }}">
                            {{ $item['t']->cantidad }}
                        </th>
                    @endif
                    <td class="text-center border-1px" style="font-size: 0.7em">
                        {{ $p->nombre }}
                    </td>
                    <td class="text-center border-1px" style="font-size: 0.7em">
                        {{ number_format($p->tallos) }}
                    </td>
                    <td class="text-center border-1px" style="font-size: 0.7em">
                        A
                    </td>
                    <td class="text-center border-1px" style="font-size: 0.7em">
                        {{ $p->nandina }}
                    </td>
                    <td class="text-center border-1px" style="font-size: 0.7em">
                        {{ $p->hts }}
                    </td>
                    <td class="text-center border-1px" style="font-size: 0.7em">
                        {{ number_format($p->ramos) }}
                    </td>
                    <td class="text-center border-1px" style="font-size: 0.7em">
                        {{ number_format($p->monto / $p->tallos, 2) }}
                    </td>
                    <td class="text-center border-1px" style="font-size: 0.7em">
                        {{ number_format($p->monto, 2) }}
                    </td>
                </tr>
            @endforeach
        @endforeach
        <tr>
            <th class="text-center border-1px" style="font-size: 0.7em">
                PIEZA
            </th>
            <th class="text-center border-1px" style="font-size: 0.7em">
                {{ $total_piezas }}
            </th>
            <th class="text-center border-1px" style="font-size: 0.7em">
                FULL BOXES: {{ $total_full }}
            </th>
            <th class="text-center border-1px" style="font-size: 0.7em">
                {{ number_format($total_tallos) }}
            </th>
            <td class="text-center border-1px" style="font-size: 0.7em" colspan="2">
                Neto: {{ number_format($total_monto, 2) }}
            </td>
            <td class="text-center border-1px" style="font-size: 0.7em" colspan="2">
                IVA: {{ 0.0 }}
            </td>
            <th class="text-center border-1px" style="font-size: 0.7em" colspan="2">
                TOTAL: ${{ number_format($total_monto, 2) }}
            </th>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 10px">
        <tr>
            <th style="font-size: 0.7em; text-align: left" colspan="2">
                Name and Title of Person Preparing Invoice
            </th>
        </tr>
        <tr>
            <td class="border-1px" style="font-size: 0.7em; vertical-align: top; height: 40px;" colspan="2">
                CARLOS EMANUELE
            </td>
        </tr>
        <tr>
            <td class="border-1px" style="font-size: 0.7em; height: 25px">
            </td>
            <td class="border-1px" style="font-size: 0.7em; height: 25px">
            </td>
        </tr>
        <tr>
            <td class="border-1px text-center" style="font-size: 0.7em; width: 50%; background-color: #dddddd">
                CUSTOM USE ONLY
            </td>
            <td class="border-1px text-center" style="font-size: 0.7em; background-color: #dddddd">
                USDA, APHIS, P.P.Q. Use Only
            </td>
        </tr>
    </table>

    <table style="width: 100%">
        <tr>
            <th style="font-size: 0.7em; width: 75%">
                The flowers and plants on this invoice were fully grown in ECUADOR
            </th>
            <th style="font-size: 0.7em;">
                Fully Grown in Ecuador
            </th>
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

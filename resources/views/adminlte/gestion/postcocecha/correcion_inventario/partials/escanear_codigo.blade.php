@if ($inventario_frio != '')
    <table style="width: 100%;">
        <tr>
            <td style="padding-right: 5px; width: 55%">
                <table class="table-bordered" style="width: 100%; border: 1px solid #9d9d9d">
                    <tr>
                        <th class="text-right th_yura_green" style="padding-right: 5px">
                            Variedad
                        </th>
                        <th class="text-left bg-yura_dark" style="padding-left: 5px">
                            {{ $inventario_frio->variedad->nombre }}
                            <input type="hidden" id="scan_nombre_variedad"
                                value="{{ $inventario_frio->variedad->nombre }}">
                        </th>
                    </tr>
                    <tr>
                        <th class="text-right th_yura_green" style="padding-right: 5px">
                            Longitud
                        </th>
                        <th class="text-left bg-yura_dark" style="padding-left: 5px">
                            {{ $inventario_frio->clasificacion_ramo->nombre }} <sup>cm</sup>
                        </th>
                        <input type="hidden" id="scan_longitud"
                            value="{{ $inventario_frio->clasificacion_ramo->nombre }}">
                    </tr>
                    <tr>
                        <th class="text-right th_yura_green" style="padding-right: 5px">
                            Tallos x Ramo
                        </th>
                        <th class="text-left bg-yura_dark" style="padding-left: 5px">
                            {{ $inventario_frio->tallos_x_ramo }}
                        </th>
                        <input type="hidden" id="scan_tallos_x_ramo" value="{{ $inventario_frio->tallos_x_ramo }}">
                    </tr>
                    <tr>
                        <th class="text-right th_yura_green" style="padding-right: 5px">
                            Fecha
                        </th>
                        <th class="text-left bg-yura_dark" style="padding-left: 5px">
                            {{ convertDateToText($inventario_frio->fecha) }}
                        </th>
                    </tr>
                    <tr>
                        <th class="text-right th_yura_green" style="padding-right: 5px">
                            Edad
                        </th>
                        <th class="text-left bg-yura_dark" style="padding-left: 5px">
                            {{ difFechas(hoy(), $inventario_frio->fecha)->days }} <sup>dias</sup>
                            <input type="hidden" id="scan_edad"
                                value="{{ difFechas(hoy(), $inventario_frio->fecha)->days }}">
                        </th>
                    </tr>
                    <tr>
                        <th class="text-right th_yura_green" style="padding-right: 5px">
                            Finca Origen
                        </th>
                        <th class="text-left bg-yura_dark" style="padding-left: 5px">
                            {{ $inventario_frio->get_finca_destino->nombre }}
                        </th>
                    </tr>
                    <tr>
                        <th class="text-right th_yura_green" style="padding-right: 5px">
                            Disponibles
                        </th>
                        <th class="text-left bg-yura_dark" style="padding-left: 5px">
                            {{ $inventario_frio->disponibles }}
                            <input type="hidden" id="scan_disponibles" value="{{ $inventario_frio->disponibles }}">
                        </th>
                    </tr>
                </table>
            </td>

            <td style="vertical-align: center;">
                <table style="font-family: Arial, Helvetica, sans-serif; max-width: 100%; border: 1px dotted #9d9d9d;"
                    class="sombra_pequeña">
                    <tr style="padding: 0">
                        <th style="text-align: center; padding: 0; " colspan="3">
                            {{ $inventario_frio->variedad->nombre }}
                        </th>
                    </tr>
                    <tr style="padding: 0">
                        <th style="text-align: center; padding-left: 2px;" colspan="2">
                            {!! $barCode->getBarcode($inventario_frio->id_inventario_frio, $barCode::TYPE_CODE_128, 2) !!}
                        </th>
                        <td style="text-align: left; padding-left: 5px">
                            <b>{{ $inventario_frio->clasificacion_ramo->nombre }}</b> <small><sup>cm</sup></small>
                            <br>
                            <b>{{ $inventario_frio->tallos_x_ramo }}</b> <small><sup>tallos</sup></small>
                        </td>
                    </tr>
                    <tr style="padding: 0">
                        <td style="text-align: left; font-size: 10px; padding: 0" colspan="3">
                            {{ getDias(TP_LETRA)[transformDiaPhp(date('w', strtotime($inventario_frio->fecha)))] }}{{ intVal(substr($inventario_frio->fecha, 5, 2)) }}.{{ substr($inventario_frio->fecha, 8, 2) }}F<small>in</small>{{ mb_strtoupper(substr($inventario_frio->get_finca_destino->nombre, 0, 2)) }}
                            <span style="vertical-align: right">
                                <b>PRODUCT of ECUADOR</b>
                            </span>
                        </td>
                </table>
            </td>
        </tr>
    </table>

    <input type="hidden" id="id_inventario_frio_scan" value="{{ $inventario_frio->id_inventario_frio }}">

    @if ($inventario_frio->disponibles > 0)
        <div class="alert alert-success text-center" style="margin-top: 5px">
            <i class="fa fa-fw fa-check"></i> LECTURA EXITOSA
        </div>
    @else
        <div class="alert alert-warning text-center" style="margin-top: 5px">
            <i class="fa fa-fw fa-ban"></i> NO HAY FLOR DISPONIBLE
        </div>
    @endif

    <script>
        agregar_a_listado('{{ $inventario_frio->id_inventario_frio }}');
    </script>
@else
    <div class="alert alert-danger text-center">
        <i class="fa fa-fw fa-ban"></i> LECTURA FALLIDA
    </div>
@endif

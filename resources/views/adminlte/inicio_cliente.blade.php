@extends('layouts.adminlte_cliente.master')

@section('titulo')
    Bienvenido
@endsection

@section('css_inicio')
    <style>
        .nodo_org {
            background-color: #e9ecef !important;
            width: 200px;
            cursor: pointer;
            -webkit-box-shadow: 9px 8px 11px -2px rgba(0, 0, 0, 0.34);
            -moz-box-shadow: 9px 8px 11px -2px rgba(0, 0, 0, 0.34);
            box-shadow: 5px 3px 11px -2px rgba(0, 0, 0, 0.34);
            font-size: 1em;
        }

        .nodo_org_selected {
            background-color: #ccc9c9 !important;
        }
    </style>
@endsection

@section('script_inicio')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="https://bernii.github.io/gauge.js/dist/gauge.min.js"></script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Bienvenido(a) <b>{{ $usuario->nombre_completo }}</b> a <b>1 TOQUE</b>
        </h1>
    </section>

    <section class="content">
        <div style="margin-bottom: 10px; padding-left: 15px">
            <ul class="timeline">
                <li class="time-label">
                    <span class="bg-yura_dark">
                        Hoy es {{ convertDateToText(hoy()) }}
                    </span>
                </li>
                <li>
                    <i class="fa fa-industry bg-yura_primary"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header">
                            <b class="text-color_yura">
                                FINCA
                            </b>
                        </h3>
                        <div class="timeline-body">
                            <h1 style="margin-top: 0">{{ $finca->nombre }}</h1>
                        </div>
                    </div>
                </li>
                <li>
                    <i class="fa fa-money bg-yura_primary"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header">
                            <b class="text-color_yura">
                                SALDO RESTANTE
                            </b>
                        </h3>
                        <div class="timeline-body">
                            <h2 style="margin-top: 0">
                                ${{ number_format($usuario->saldo, 2) }}
                                <sup><em style="font-size: 0.7em">de ${{ number_format($usuario->cupo_disponible, 2) }}
                                        totales</em></sup>
                            </h2>
                        </div>
                    </div>
                </li>
                <li>
                    <i class="fa fa-truck bg-yura_primary"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header">
                            <b class="text-color_yura">
                                PROXIMA FECHA de ENTREGA
                            </b>
                        </h3>
                        <div class="timeline-body">
                            <h3 style="margin-top: 0">
                                {{ convertDateToText($fecha_entrega->entrega) }}
                                <sup><em style="font-size: 0.8em">{{ difFechas($fecha_entrega->entrega, hoy())->days }} dias
                                        restantes</em></sup>
                            </h3>
                        </div>
                    </div>
                </li>
                <li>
                    <i class="fa fa-calendar bg-yura_primary"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header">
                            <b class="text-color_yura">
                                ULTIMO DIA de TOMA de PEDIDOS
                            </b>
                        </h3>
                        <div class="timeline-body">
                            <h3 style="margin-top: 0">
                                {{ convertDateToText($fecha_entrega->hasta) }}
                                <sup><em style="font-size: 0.8em">{{ difFechas($fecha_entrega->hasta, hoy())->days }} dias
                                        restantes</em></sup>
                            </h3>
                        </div>
                    </div>
                </li>
                <li>
                    <i class="fa fa-shopping-cart bg-yura_primary"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header">
                            <b class="text-color_yura">
                                HAGA SU PEDIDO
                            </b>
                        </h3>
                        <div class="timeline-body text-center">
                            <h3 style="margin-top: 0">
                                @if ($usuario->aplica == 1)
                                    <button type="button" class="btn btn-yura_primary btn-block"
                                        onclick="cargar_url('pedido_bodega_cliente')">
                                        <i class="fa fa-fw fa-check"></i> COMENZAR
                                    </button>
                                @else
                                    <button type="button" class="btn btn-yura_danger btn-block">
                                        <i class="fa fa-fw fa-ban"></i> LO SENTIMOS, SU CUENTA NO APLICA EN ESTE MOMENTO
                                    </button>
                                @endif
                            </h3>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    <script>
        $('#vista_actual').val('inicio_resumen');

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        notificar('Bienvenid@ {{ explode(' ', getUsuario(Session::get('id_usuario'))->nombre_completo)[0] }}',
            '{{ url('') }}',
            function() {}, null, false);

        function select_finca_dashboard(id) {
            $.LoadingOverlay('show');
            location.href = '{{ url('dashboard') }}' + '?f=' + id;
            $.LoadingOverlay('hide');
        }
    </script>
@endsection

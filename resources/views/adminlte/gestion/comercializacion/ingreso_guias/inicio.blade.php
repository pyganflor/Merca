@extends('layouts.adminlte.master')

@section('titulo')
    Ingreso de Guias
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Ingreso de Guias
            <small class="text-color_yura">módulo de comercializacion</small>
        </h1>

        <ol class="breadcrumb">
            <li>
                <a href="javascript:void(0)" onclick="cargar_url('')" class="text-color_yura">
                    <i class="fa fa-home text-color_yura"></i>
                    Inicio
                </a>
            </li>
            <li class="text-color_yura">
                {{ $submenu->menu->grupo_menu->nombre }}
            </li>
            <li class="text-color_yura">
                {{ $submenu->menu->nombre }}
            </li>

            <li class="active">
                <a href="javascript:void(0)" onclick="cargar_url('{{ $submenu->url }}')" class="text-color_yura">
                    <i class="fa fa-fw fa-refresh text-color_yura"></i> {{ $submenu->nombre }}
                </a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div id="div_content_recepciones">
            <table width="100%" style="margin-bottom: 0">
                <tr>
                    <td>
                        <div class="input-group input-group">
                            <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                Cliente
                            </div>
                            <select id="filtro_cliente" name="filtro_cliente" required
                                class="form-control input-yura_default" style="width: 100% !important;">
                                <option value="">Todas</option>
                                @foreach ($clientes as $item)
                                    <option value="{{ $item->id_cliente }}">
                                        {{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="input-group-addon bg-yura_dark">
                                Agencia
                            </div>
                            <select id="filtro_agencia" name="filtro_agencia" required
                                class="form-control input-yura_default" style="width: 100% !important;">
                                <option value="">Todas</option>
                                @foreach ($agencias as $item)
                                    <option value="{{ $item->id_agencia_carga }}">
                                        {{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="input-group-addon bg-yura_dark">
                                Fecha
                            </div>
                            <input type="date" id="filtro_fecha" name="filtro_fecha" required
                                value="{{ hoy() }}" class="form-control text-center input-yura_default"
                                style="width: 100% !important;">
                            <div class="input-group-btn">
                                <button class="btn btn-primary btn-yura_primary" onclick="listar_reporte()">
                                    <i class="fa fa-fw fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div id="div_listado" style="overflow-y: scroll; overflow-x: scroll; max-height: 500px; margin-top: 5px"></div>
        </div>
    </section>

    <style>
        .tr_fija_top_0 {
            position: sticky;
            top: 0;
            z-index: 9;
        }
    </style>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.gestion.comercializacion.ingreso_guias.script')
@endsection

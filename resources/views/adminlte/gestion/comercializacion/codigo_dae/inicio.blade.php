@extends('layouts.adminlte.master')

@section('titulo')
    Codigos DAE
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Codigos DAE
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
                                Año
                            </div>
                            <input type="number" id="filtro_anno" name="filtro_anno" required value="{{ date('Y') }}"
                                class="form-control input-yura_default text-center" style="width: 100% !important;">
                            <div class="input-group-addon bg-yura_dark">
                                Mes
                            </div>
                            <input type="number" id="filtro_mes" name="filtro_mes" required value="{{ date('m') }}"
                                class="form-control input-yura_default text-center" style="width: 100% !important;">
                            <div class="input-group-btn">
                                <button class="btn btn-primary btn-yura_primary" onclick="listar_reporte()">
                                    <i class="fa fa-fw fa-search"></i> Buscar
                                </button>
                                <button class="btn btn-primary btn-yura_dark" onclick="exportar_paises()"
                                    title="Exportar Paises">
                                    <i class="fa fa-fw fa-download"></i> Descargar Plantilla
                                </button>
                                <button class="btn btn-primary btn-yura_primary" onclick="importar_codigos_dae()"
                                    title="Importar CODIGOS DAE">
                                    <i class="fa fa-fw fa-upload"></i> Importar CODIGOS DAE
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

    @include('adminlte.gestion.comercializacion.codigo_dae.script')
@endsection

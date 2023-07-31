@extends('layouts.adminlte.master')

@section('titulo')
    {{ explode('|', getConfiguracionEmpresa()->postcocecha)[0] }} {{-- Recepción --}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{ explode('|', getConfiguracionEmpresa()->postcocecha)[0] }}
            <small class="text-color_yura">módulo de postcosecha</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')" class="text-color_yura">
                    <i class="fa fa-home text-color_yura"></i>
                    Inicio</a></li>
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
                        <div class="form-group input-group">
                            <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                <i class="fa fa-fw fa-calendar"></i> Fecha de Cosecha
                            </div>
                            <input type="date" id="filtro_fecha" name="filtro_fecha" required
                                class="form-control input-yura_default text-center" onchange="buscar_listado_recepcion()"
                                style="width: 100% !important;" value="{{ hoy() }}" max="{{ hoy() }}">
                            <div class="input-group-btn">
                                <button class="btn btn-primary btn-yura_primary" onclick="buscar_listado_recepcion()">
                                    <i class="fa fa-fw fa-search"></i>
                                </button>
                                <button class="btn btn-primary btn-yura_dark" onclick="add_recepcion()">
                                    <i class="fa fa-fw fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div id="div_listado_recepciones" style="overflow-y: scroll; overflow-x: scroll; height: 450px;"></div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.gestion.postcocecha.recepciones.script')
@endsection

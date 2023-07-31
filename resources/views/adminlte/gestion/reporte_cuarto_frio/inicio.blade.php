@extends('layouts.adminlte.master')

@section('titulo')
    Cuarto Frío
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cuarto Frío
            <small class="text-color_yura">módulo de postcosecha</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('')"><i class="fa fa-home"></i>
                    Inicio</a></li>
            <li class="text-color_yura">
                {{ $submenu->menu->grupo_menu->nombre }}
            </li>
            <li class="text-color_yura">
                {{ $submenu->menu->nombre }}
            </li>

            <li class="active">
                <a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('{{ $submenu->url }}')">
                    <i class="fa fa-fw fa-refresh"></i> {{ $submenu->nombre }}
                </a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="input-group">
                        <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                            <i class="fa fa-fw fa-tree"></i> Finca
                        </div>
                        <select name="filtro_finca" id="filtro_finca" class="form-control input-yura_default"
                            onchange="listar_reporte()">
                            <option value="">Todas</option>
                            <option value="F">Todas la Fincas Propias</option>
                            <option value="P">Todos los Proveedores</option>
                            @foreach ($proveedores as $p)
                                <option value="{{ $p->id_configuracion_empresa }}">{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                            Tallos
                        </div>
                        <input type="number" name="filtro_tallos_x_ramo" id="filtro_tallos_x_ramo"
                            class="form-control input-yura_default text-center">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                            <i class="fa fa-fw fa-leaf"></i> Variedad
                        </div>
                        <select name="filtro_planta" id="filtro_planta" class="form-control input-yura_default"
                            onchange="select_planta($(this).val(), 'filtro_variedad', 'filtro_variedad',
                            '<option value=>Todos los tipos</option>')">
                            <option value="">Seleccione</option>
                            @foreach ($plantas as $p)
                                <option value="{{ $p->id_planta }}">{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <div class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                            <i class="fa fa-fw fa-leaf"></i> Tipo
                        </div>
                        <select name="filtro_variedad" id="filtro_variedad" class="form-control input-yura_default"
                            onchange="listar_reporte()">
                            <option value="" selected>Seleccione</option>
                        </select>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-yura_dark" onclick="listar_reporte()">
                                <i class="fa fa-fw fa-search"></i>
                            </button>
                            <button class="btn btn-primary btn-yura_primary" onclick="importar_bajas()">
                                <i class="fa fa-fw fa-upload"></i>
                            </button>
                            <button type="button" class="btn btn-yura_default" title="Exportar"
                                onclick="exportar_reporte()">
                                <i class="fa fa-fw fa-file-excel-o"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div id="div_listado" style="margin-top: 5px"></div>
    </section>

    <style>
        .tr_fija_top_0 {
            position: sticky;
            top: 0;
            z-index: 9;
        }

        .tr_fija_bottom_0 {
            position: sticky;
            bottom: 0;
            z-index: 9;
        }
    </style>
@endsection

@section('script_final')
    @include('adminlte.gestion.reporte_cuarto_frio.script')
@endsection

@extends('layouts.adminlte.master')

@section('titulo')
    Resumen de Descuentos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Resumen de Descuentos
            <small class="text-color_yura">módulo de bodega</small>
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
                <a href="javascript:void(0)" onclick="cargar_url('{{ $submenu->url }}')" class="text-color_yura">
                    <i class="fa fa-fw fa-refresh"></i> {{ $submenu->nombre }}
                </a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div style="overflow-x: scroll">
            <table style="width: 100%">
                <tr>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                Finca
                            </span>
                            <select id="filtro_finca" style="width: 100%" class="form-control">
                                @foreach ($fincas as $f)
                                    <option value="{{ $f->id_empresa }}">
                                        {{ $f->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon bg-yura_dark">
                                Desde
                            </span>
                            <input type="date" id="filtro_desde" style="width: 100%" class="form-control"
                                value="{{ date('Y-m-01', strtotime(hoy())) }}">
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon bg-yura_dark">
                                Hasta
                            </span>
                            <input type="date" id="filtro_hasta" style="width: 100%" class="form-control"
                                value="{{ hoy() }}">
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon bg-yura_dark">
                                Tipo
                            </span>
                            <select class="form-control" id="filtro_tipo" style="width: 100%">
                                {{--<option value="T">Total Ventas</option>--}}
                                <option value="D">Diferidos</option>
                                <option value="N">NO Diferidos</option>
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-yura_dark" onclick="listar_reporte()">
                                    <i class="fa fa-fw fa-search"></i> Buscar
                                </button>
                                <button type="button" class="btn btn-yura_default" onclick="exportar_reporte()">
                                    <i class="fa fa-fw fa-file-excel-o"></i> Exportar
                                </button>
                            </span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div id="div_listado" style="margin-top: 5px; overflow-y: scroll; max-height: 700px">
        </div>
    </section>

    <style>
        #tr_fija_top_0 {
            position: sticky;
            top: 0;
            z-index: 9;
        }

        .columna_fija_left_0 {
            position: sticky;
            left: 0;
            z-index: 9;
        }
    </style>
@endsection

@section('script_final')
    @include('adminlte.gestion.bodega.resumen_pedidos.script')
@endsection

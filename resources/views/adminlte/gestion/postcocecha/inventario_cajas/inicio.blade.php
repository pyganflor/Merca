@extends('layouts.adminlte.master')

@section('titulo')
    Inventario de Cajas
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Inventario de Cajas
            <small class="text-color_yura">módulo de postcosecha</small>
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
            <table width="100%" style="margin-bottom: 0;">
                <tr>
                    <td>
                        <div class="input-group">
                            <div class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                <i class="fa fa-fw fa-list"></i>
                            </div>
                            <input type="text" id="filtro_busqueda" class="form-control input-yura_default text-center"
                                style="width: 100% !important;" placeholder="Busqueda por Nombre"
                                onkeyup="listar_reporte()">
                            <div class="input-group-addon bg-yura_dark">
                                <i class="fa fa-fw fa-calendar"></i> Fecha
                            </div>
                            <input type="date" id="filtro_fecha" class="form-control input-yura_default text-center"
                                style="width: 100% !important;">
                            <div class="input-group-btn">
                                <button class="btn btn-yura_dark" onclick="listar_reporte()">
                                    <i class="fa fa-fw fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div style="margin-top: 5px" id="div_listado">
            </div>
        </div>
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
    @include('adminlte.gestion.postcocecha.inventario_cajas.script')
@endsection

@extends('layouts.adminlte.master')

@section('titulo')
    Etiquetar productos de PESO
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Etiquetar productos de PESO
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
        <div class="row">
            <div class="col-md-6">
                <legend class="text-center" style="margin-bottom: 2px; font-size: 1.2em">
                    Productos en el <b>Inventario de Bodega</b>
                </legend>
                <div id="div_inventario"></div>
            </div>
            <div class="col-md-6">
                <div class="input-group" style="font-size: 1em; margin-bottom: 2px">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        Pedidos del dia
                    </span>
                    <select id="fecha_entrega" style="width: 100%; height: 28px;" class="text-center">
                        @foreach ($fechas_entregas as $fecha)
                            <option value="{{ $fecha }}">
                                {{ convertDateToText($fecha) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="div_pedidos"></div>
            </div>
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
    @include('adminlte.gestion.bodega.etiquetar_peso.script')
@endsection

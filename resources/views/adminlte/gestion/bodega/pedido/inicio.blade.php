@extends('layouts.adminlte.master')

@section('titulo')
    Pedidos de Bodega
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Pedidos de Bodega
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
                            <select id="filtro_finca" style="width: 100%" class="form-control"
                                onchange="seleccionar_finca_filtro()">
                                @if (count($fincas) > 1)
                                    <option value="T">Todas mis fincas</option>
                                @endif
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
                            <div class="input-group">
                                <span class="input-group-addon bg-yura_dark">
                                    Fecha de Entrega
                                </span>
                                <select id="filtro_entrega" style="width: 100%" class="form-control"></select>
                            </div>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-yura_dark" onclick="listar_reporte()">
                                    <i class="fa fa-fw fa-search"></i>
                                </button>
                                <button type="button" class="btn btn-yura_primary" onclick="add_pedido()">
                                    <i class="fa fa-fw fa-shopping-cart"></i> Pedido
                                </button>
                                <button type="button" class="btn btn-yura_dark" onclick="get_armar_pedido()">
                                    <i class="fa fa-fw fa-gift"></i> Armado
                                </button>
                                <button type="button" class="btn btn-yura_default" onclick="exportar_resumen_pedidos()">
                                    <i class="fa fa-fw fa-file-excel-o"></i> Resumen
                                </button>
                                <button type="button" class="btn btn-yura_default" onclick="imprimir_pedidos_all()">
                                    <i class="fa fa-fw fa-print"></i> Pedidos
                                </button>
                                <button type="button" class="btn btn-yura_default" onclick="imprimir_entregas_all()">
                                    <i class="fa fa-fw fa-print"></i> Entregas
                                </button>
                            </span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div id="div_listado" style="margin-top: 5px">
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
    @include('adminlte.gestion.bodega.pedido.script')
@endsection

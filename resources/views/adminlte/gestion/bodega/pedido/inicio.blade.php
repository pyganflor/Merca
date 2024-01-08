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
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                            Finca
                        </span>
                        <select id="filtro_finca" style="width: 100%" class="form-control">
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
                        <span class="input-group-addon bg-yura_dark">
                            Desde
                        </span>
                        <input type="date" id="filtro_desde" style="width: 100%" class="form-control text-center"
                            value="{{ hoy() }}">
                        <span class="input-group-addon bg-yura_dark">
                            Hasta
                        </span>
                        <input type="date" id="filtro_hasta" style="width: 100%" class="form-control text-center"
                            value="{{ hoy() }}">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-yura_dark" onclick="listar_reporte()" title="Buscar">
                                <i class="fa fa-fw fa-search"></i>
                            </button>
                            <button type="button" class="btn btn-yura_primary" onclick="add_pedido()"
                                title="Agregar Pedido">
                                <i class="fa fa-fw fa-shopping-cart"></i>
                            </button>
                            <button type="button" class="btn btn-yura_dark" onclick="get_armar_pedido()"
                                title="Armar Pedidos">
                                <i class="fa fa-fw fa-gift"></i>
                            </button>
                            <button type="button" class="btn btn-yura_default" onclick="exportar_resumen_pedidos()"
                                title="Exportar archivo de Compras">
                                <i class="fa fa-fw fa-file-excel-o"></i>
                            </button>
                            <button type="button" class="btn btn-yura_default" onclick="imprimir_pedidos_all()"
                                title="Etiquetas">
                                <i class="fa fa-fw fa-print"></i>
                            </button>
                            <button type="button" class="btn btn-yura_dark" onclick="modal_contabilidad()"
                                title="Exportar archivo de contabilidad">
                                <i class="fa fa-fw fa-credit-card"></i>
                            </button>
                            <button type="button" class="btn btn-yura_default dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="true">
                                Entregas
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="imprimir_entregas_all()">
                                        Productos normales
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                        onclick="imprimir_entregas_peso_all()">
                                        Pollo
                                    </a>
                                </li>
                            </div>
                        </span>
                    </div>
                </td>
            </tr>
        </table>

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

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
                    <td class="text-center" style="border-color: #9d9d9d">
                        <div class="input-group">
                            <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                Fecha de Entrega
                            </span>
                            <input type="date" id="filtro_entrega" style="width: 100%" class="text-center form-control"
                                value="{{ hoy() }}">
                        </div>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <div class="input-group">
                            <span class="input-group-addon bg-yura_dark">
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
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-yura_dark" onclick="listar_reporte()">
                                    <i class="fa fa-fw fa-search"></i>
                                </button>
                                <button type="button" class="btn btn-yura_primary" onclick="add_pedido()">
                                    <i class="fa fa-fw fa-shopping-cart"></i> Pedido
                                </button>
                                <button type="button" class="btn btn-yura_dark" onclick="resumen_pedido()">
                                    <i class="fa fa-fw fa-file-excel-o"></i> Resumen
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

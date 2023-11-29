@extends('layouts.adminlte_cliente.master')

@section('titulo')
    Nuevo Pedido
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ENTREGA <i class="fa fa-fw fa-truck fa-flip-horizontal text-color_yura"></i>
            <b>{{ convertDateToText($fecha_entrega->entrega) }}</b> <i class="fa fa-fw fa-truck text-color_yura"></i>
            en <b>{{ $finca->nombre }}</b>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-pills">
                @foreach ($listado as $pos => $item)
                    <li class="{{ $pos == 0 ? 'active' : '' }}">
                        <a href="#tab_cat_{{ $item['categoria']->id_categoria_producto }}" data-toggle="tab">
                            {{ $item['categoria']->nombre }}
                        </a>
                    </li>
                @endforeach
                <li class="pull-right">
                    <a href="#tab_carrito" data-toggle="tab" class="text-muted">
                        <i class="fa fa-shopping-cart"></i>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                @foreach ($listado as $pos => $item)
                    <div class="tab-pane {{ $pos == 0 ? 'active' : '' }}"
                        id="tab_cat_{{ $item['categoria']->id_categoria_producto }}">
                        @include('adminlte.gestion.bodega.pedido_cliente.partials.listado')
                    </div>
                @endforeach
                <div class="tab-pane" id="tab_carrito">
                    @include('adminlte.gestion.bodega.pedido_cliente.forms.carrito')
                </div>
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
    @include('adminlte.gestion.bodega.pedido_cliente.script')
@endsection

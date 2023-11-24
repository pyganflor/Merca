@extends('layouts.adminlte_cliente.master')

@section('titulo')
    Nuevo Pedido
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Haga su Pedido
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        ...
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

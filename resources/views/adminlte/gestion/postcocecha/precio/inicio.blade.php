@extends('layouts.adminlte.master')

@section('titulo')
    Precio
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div style="overflow-x: scroll; width: 100%">
            <table style="width:100%; margin-top: 0">
                <tr>
                    <td style="vertical-align: top; padding-right: 5px; width: 30%">
                        <div class="panel panel-success" style="margin-bottom: 0px; min-width: 270px">
                            <div class="panel-heading"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <b><i class="fa fa-users"></i> LISTADO DE CLIENTES </b>
                            </div>
                            <div class="panel-body" style="max-height: 500px">
                                <table class="table table-bordered"
                                    style="width: 100%; border: 1px solid #9d9d9d; margin-top: 0">
                                    @foreach ($clientes as $c)
                                        <tr onmouseover="$(this).addClass('bg-yura_dark')"
                                            onmouseleave="$(this).removeClass('bg-yura_dark')"
                                            class="mouse-hand tr_clientes" id="tr_cliente_{{ $c->id_cliente }}"
                                            onclick="seleccionar_cliente('{{ $c->id_cliente }}')">
                                            <th class="padding_lateral_5 text-right" style="border-color: #9d9d9d">
                                                {{ $c->nombre }}
                                            </th>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </td>
                    <td style="vertical-align: top; padding-left: 5px;">
                        <div class="panel panel-success" style="margin-bottom: 0px;">
                            <div class="panel-heading"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <b><i class="fa fa-dollar"></i> PRECIOS DEL CLIENTE</b>
                            </div>
                            <div class="panel-body">
                                <div style="max-height: 500px; overflow:auto" id="body_precios"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.postcocecha.precio.script')
@endsection

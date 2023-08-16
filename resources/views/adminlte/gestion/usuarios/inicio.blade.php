@extends('layouts.adminlte.master')

@section('titulo')
    Usuarios
@endsection

@section('script_inicio')
    {{-- <script src="{{url('js/portada/login.js')}}"></script> --}}

    <script language="JavaScript" type="text/javascript" src="{{ url('js/rsa/jsbn.js') }}"></script>
    <script language="JavaScript" type="text/javascript" src="{{ url('js/rsa/jsbn2.js') }}"></script>
    <script language="JavaScript" type="text/javascript" src="{{ url('js/rsa/prng4.js') }}"></script>
    <script language="JavaScript" type="text/javascript" src="{{ url('js/rsa/rng.js') }}"></script>
    <script language="JavaScript" type="text/javascript" src="{{ url('js/rsa/rsa.js') }}"></script>
    <script language="JavaScript" type="text/javascript" src="{{ url('js/rsa/rsa2.js') }}"></script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Usuarios
            <small class="text-color_yura">módulo de administrador</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')" class="text-color_yura"><i class="fa fa-home"></i>
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
        <div class="box-body" id="div_content_usuarios">
            <table width="100%">
                <tr>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                Búsqueda
                            </span>
                            <input type="text" class="form-control text-center" placeholder="Búsqueda"
                                id="busqueda_usuarios" name="busqueda_usuarios">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-yura_dark" onclick="buscar_listado()">
                                    <i class="fa fa-fw fa-search"></i>
                                </button>
                                <button type="button" class="btn btn-yura_primary" onclick="add_usuario()">
                                    <i class="fa fa-fw fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-yura_default" onclick="exportar_usuarios()">
                                    <i class="fa fa-fw fa-file-excel-o"></i>
                                </button>
                            </span>
                        </div>
                    </td>
                </tr>
            </table>
            <div id="div_listado_usuarios" style="overflow-y: scroll; max-height: 650px; margin-top: 5px"></div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.usuarios.script')
@endsection

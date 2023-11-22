<header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <a href="{{ url('') }}">
                    <img src="{{ url('images/Logo_Bench_Flow_verde_negro.png') }}" alt="" width="70px">
                </a>
            </div>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu" id="li-master_username">
                        <a href="#" class="dropdown-toggle text-color_yura" data-toggle="dropdown">
                            @php
                                $usuario = getUsuario(Session::get('id_usuario'));
                                $file_img = url('storage/imagenes') . '/' . $usuario->imagen_perfil;
                            @endphp
                            @if (file_exists($file_img))
                                <img src="{{ $file_img }}" class="user-image" alt="User Image"
                                    id="img_perfil_menu_superior" title="{{ $usuario->nombre_completo }}">
                            @else
                                <i class="fa fa-fw fa-user" title="{{ $usuario->nombre_completo }}"></i>
                            @endif
                            <span class="hidden-xs" id="span-master_username">
                                {{ $usuario->nombre_completo }}
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-body text-center text-color_yura">
                                <span style="font-size: 0.9em">
                                    Miembro desde el
                                    {{ convertDateToText(substr($usuario->fecha_registro, 0, 10)) }}
                                </span>
                            </li>
                            <li class="user-footer">
                                <div class="btn-group pull-left">
                                    {{-- 
                                    <button type="button" class="btn btn-yura_default" onclick="cargar_url('perfil')">
                                        Mi Perfil
                                    </button>
                                    --}}
                                    @if (Session::get('tipo_rol') == 'P')
                                        <button type="button" class="btn btn-yura_dark" title="Reportes utiles"
                                            onclick="cargar_utiles()">
                                            <i class="fa fa-fw fa-code"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="pull-right">
                                    <a href="javascript:void(0)" onclick="cargar_url('logout')"
                                        class="btn btn-yura_default">Salir</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span>
                        @if(Auth::user()->imagen == '')
                            <img id="img_imagen" alt="image" class="img-circle" width="50" src="{!! asset('image/logo/logo_fge_256.png') !!}" />
                        @else
                            <img id="img_imagen" alt="image" class="img-circle" width="50" src="{!! asset('storage/seguridad/user/image/' . Auth::user()->imagen) !!}" />
                        @endif
                    </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ Auth::user()->name }}</strong>
                            </span>
                            {{-- <span class="text-muted text-xs block">Más ... <b class="caret"></b></span> --}}
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        {{-- <li><a href="profile.html">Mi perfil</a></li> --}}
                        {{-- <li><a href="contacts.html">Mi declaración</a></li>
                        <li><a href="mailbox.html">Mi hoja de vida</a></li> --}}
                        {{-- <li class="divider"></li> --}}
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Cerrar sesión</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    @if(Auth::user()->imagen == '')
                        <img alt="image" class="img-circle" width="50" src="{!! asset('image/logo/logo_fge_256.png') !!}" />
                    @else
                        <img alt="image" class="img-circle" width="50" src="{!! asset('storage/seguridad/user/image/' . Auth::user()->imagen) !!}" />
                    @endif
                </div>
            </li>

            <li class="{{ isActiveRoute('home') }}">
                <a href="{{ url('/home') }}"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
            </li>

            @if(in_array(['codigo' => '0501'], $permisos) || in_array(['codigo' => '0801'], $permisos) || in_array(['codigo' => '0901'], $permisos) || in_array(['codigo' => '1001'], $permisos) || in_array(['codigo' => '1101'], $permisos) || in_array(['codigo' => '1201'], $permisos) || in_array(['codigo' => '1301'], $permisos) || in_array(['codigo' => '1401'], $permisos) || in_array(['codigo' => '1501'], $permisos))
                <li class="{{ isActiveRoute('persona') }}{{ isActiveRoute('funcionario') }}{{ isActiveRoute('tipo_salida') }}{{ isActiveRoute('solicitud_salida') }}{{ isActiveRoute('confirmar_salida') }}{{ isActiveRoute('confirmar_salida_rrhh') }}{{ isActiveRoute('salida') }}{{ isActiveRoute('horario') }}{{ isActiveRoute('fthc') }}">
                    <a href="#"><i class="fa fa-group"></i> <span class="nav-label">Recursos humanos</span> </a>
                    <ul class="nav nav-second-level collapse">
                        @if(in_array(['codigo' => '1001'], $permisos))
                            <li class="{{ isActiveRoute('solicitud_salida') }}"><a href="{{ url('/solicitud_salida') }}">Solicitud de salida</a></li>
                        @endif
                        @if(in_array(['codigo' => '1101'], $permisos))
                            <li class="{{ isActiveRoute('confirmar_salida') }}"><a href="{{ url('/confirmar_salida') }}">Confirmar salida</a></li>
                        @endif
                        @if(in_array(['codigo' => '1201'], $permisos))
                            <li class="{{ isActiveRoute('confirmar_salida_rrhh') }}"><a href="{{ url('/confirmar_salida_rrhh') }}">Confirmar salida RRHH</a></li>
                        @endif
                        @if(in_array(['codigo' => '0501'], $permisos))
                            <li class="{{ isActiveRoute('persona') }}"><a href="{{ url('/persona') }}">Personas</a></li>
                        @endif
                        @if(in_array(['codigo' => '0801'], $permisos))
                            <li class="{{ isActiveRoute('funcionario') }}"><a href="{{ url('/funcionario') }}">Funcionarios</a></li>
                        @endif
                        @if(in_array(['codigo' => '1301'], $permisos))
                            <li class="{{ isActiveRoute('salida') }}"><a href="{{ url('/salida') }}">Gestor de salidas</a></li>
                        @endif
                        @if(in_array(['codigo' => '1401'], $permisos))
                            <li class="{{ isActiveRoute('horario') }}"><a href="{{ url('/horario') }}">Horarios</a></li>
                        @endif

                        @if(in_array(['codigo' => '1501'], $permisos))
                            <li class="{{ isActiveRoute('fthc') }}"><a href="{{ url('/fthc') }}">Feriado, tolerancia y horario continuo</a></li>
                        @endif
                        @if(in_array(['codigo' => '0901'], $permisos))
                            <li class="{{ isActiveRoute('tipo_salida') }}"><a href="{{ url('/tipo_salida') }}">Tipos de salida</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(in_array(['codigo' => '0601'], $permisos) || in_array(['codigo' => '0701'], $permisos))
                <li class="{{ isActiveRoute('biometrico') }}{{ isActiveRoute('persona_biometrico') }}">
                    <a href="#"><i class="fa fa-sitemap"></i> <span class="nav-label">Biometricos</span> </a>
                    <ul class="nav nav-second-level collapse">
                        @if(in_array(['codigo' => '0601'], $permisos))
                            <li class="{{ isActiveRoute('biometrico') }}"><a href="{{ url('/biometrico') }}">Gestor de biometricos</a></li>
                        @endif
                        @if(in_array(['codigo' => '0701'], $permisos))
                            <li class="{{ isActiveRoute('persona_biometrico') }}"><a href="{{ url('/persona_biometrico') }}">Personas</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(in_array(['codigo' => '0201'], $permisos) || in_array(['codigo' => '0301'], $permisos) || in_array(['codigo' => '0401'], $permisos))
                <li class="{{ isActiveRoute('unidad_desconcentrada') }}{{ isActiveRoute('auo') }}{{ isActiveRoute('cargo') }}">
                    <a href="#"><i class="fa fa-institution"></i> <span class="nav-label">Datos de la institución</span> </a>
                    <ul class="nav nav-second-level collapse">
                        @if(in_array(['codigo' => '0201'], $permisos))
                            <li class="{{ isActiveRoute('unidad_desconcentrada') }}"><a href="{{ url('/unidad_desconcentrada') }}">Unidad desconcentrada</a></li>
                        @endif
                        @if(in_array(['codigo' => '0301'], $permisos))
                            <li class="{{ isActiveRoute('auo') }}"><a href="{{ url('/auo') }}">Unidad organizacional</a></li>
                        @endif
                        @if(in_array(['codigo' => '0401'], $permisos))
                            <li class="{{ isActiveRoute('cargo') }}"><a href="{{ url('/cargo') }}">Cargo</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(in_array(['codigo' => '0101'], $permisos) || ($rol_id === 1))
                <li class="{{ isActiveRoute('usuario') }}{{ isActiveRoute('permiso_rol') }}{{ isActiveRoute('rol') }}{{ isActiveRoute('permiso') }}{{ isActiveRoute('modulo') }}">
                    <a href="#"><i class="fa fa-lock"></i> <span class="nav-label">Seguridad</span> </a>
                    <ul class="nav nav-second-level collapse">
                        @if(in_array(['codigo' => '0101'], $permisos))
                            <li class="{{ isActiveRoute('usuario') }}"><a href="{{ url('/usuario') }}">Gestor de usuarios</a></li>
                        @endif
                        @if($rol_id === 1)
                            <li class="{{ isActiveRoute('permiso_rol') }}"><a href="{{ url('/permiso_rol') }}">Asignación de permisos</a></li>
                        @endif
                        @if($rol_id === 1)
                            <li class="{{ isActiveRoute('rol') }}"><a href="{{ url('/rol') }}">Gestor de roles</a></li>
                        @endif
                        @if($rol_id === 1)
                            <li class="{{ isActiveRoute('permiso') }}"><a href="{{ url('/permiso') }}">Gestor de permisos</a></li>
                        @endif
                        @if($rol_id === 1)
                            <li class="{{ isActiveRoute('modulo') }}"><a href="{{ url('/modulo') }}">Gestor de módulos</a></li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</nav>

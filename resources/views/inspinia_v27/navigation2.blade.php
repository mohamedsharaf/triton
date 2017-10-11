<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span>
                        <img alt="image" class="img-circle" width="50" src="{!! asset('image/logo/logo_fge_256.png') !!}" />
                    </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ Auth::user()->name }}</strong>
                            </span> <span class="text-muted text-xs block">Más ... <b class="caret"></b></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="profile.html">Mi perfil</a></li>
                        <li><a href="contacts.html">Mi declaración</a></li>
                        <li><a href="mailbox.html">Mi hoja de vida</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Cerrar sesión</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    <img alt="image" class="img-circle" width="50" src="{!! asset('image/logo/logo_fge_256.png') !!}" />
                </div>
            </li>

            <li class="{{ isActiveRoute('home') }}">
                <a href="{{ url('/home') }}"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
            </li>

            <li class="{{ isActiveRoute('biometrico') }}{{ isActiveRoute('persona') }}">
                <a href="#"><i class="fa fa-check-square-o"></i> <span class="nav-label">Asistencias</span> </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ isActiveRoute('biometrico') }}"><a href="{{ url('/biometrico') }}">Gestor de biometricos</a></li>
                    <li class="{{ isActiveRoute('persona') }}"><a href="{{ url('/persona') }}">Gestor de personas</a></li>
                </ul>
            </li>

            <li class="{{ isActiveRoute('unidad_desconcentrada') }}">
                <a href="#"><i class="fa fa-institution"></i> <span class="nav-label">Datos de la institución</span> </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ isActiveRoute('unidad_desconcentrada') }}"><a href="{{ url('/unidad_desconcentrada') }}">Unidad desconcentrada</a></li>
                </ul>
            </li>

            <li class="{{ isActiveRoute('usuario') }}{{ isActiveRoute('permiso_rol') }}{{ isActiveRoute('rol') }}{{ isActiveRoute('permiso') }}{{ isActiveRoute('modulo') }}">
                <a href="#"><i class="fa fa-lock"></i> <span class="nav-label">Seguridad</span> </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ isActiveRoute('usuario') }}"><a href="{{ url('/usuario') }}">Gestor de usuarios</a></li>
                    <li class="{{ isActiveRoute('permiso_rol') }}"><a href="{{ url('/permiso_rol') }}">Asignación de permisos</a></li>
                    <li class="{{ isActiveRoute('rol') }}"><a href="{{ url('/rol') }}">Gestor de roles</a></li>
                    <li class="{{ isActiveRoute('permiso') }}"><a href="{{ url('/permiso') }}">Gestor de permisos</a></li>
                    <li class="{{ isActiveRoute('modulo') }}"><a href="{{ url('/modulo') }}">Gestor de módulos</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

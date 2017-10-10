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
                                {{-- <strong class="font-bold">{{ Auth::user()->name }}</strong> --}}
                            </span> <span class="text-muted text-xs block">Más ... <b class="caret"></b></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="profile.html">Mi perfil</a></li>
                        <li><a href="contacts.html">Mi declaración</a></li>
                        <li><a href="mailbox.html">Mi hoja de vida</a></li>
                        <li class="divider"></li>
                        <li><a href="login.html">Cerrar sesión</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    <img alt="image" class="img-circle" width="50" src="{!! asset('image/logo/logo_fge_256.png') !!}" />
                </div>
            </li>
            <li class="{{ isActiveRoute('main') }}">
                <a href="{{ url('/home') }}"><i class="fa fa-home"></i> <span class="nav-label">Inicio</span></a>
            </li>
            <li class="{{ isActiveRoute('ufv') }}">
                <a href="#"><i class="fa fa-gear"></i> <span class="nav-label">Herramientas</span> </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ isActiveRoute('ufv') }}"><a href="{{ url('/ufv') }}">UFV</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
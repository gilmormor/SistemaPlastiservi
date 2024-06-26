<header class="main-header">
  <!-- Logo -->
  <a href="/" class="logo" style="background-color: #fbfbfb;">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    
    <span class="logo-mini"><img src="{{asset("assets/$theme/dist/img/logoShor.png?timestamp=" . time())}}" style="max-width:90%;width:auto;height:auto;"></span>
    <!-- logo for regular state and mobile devices -->
    
    <span class="logo-lg"><img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI1.png?timestamp=" . time())}}" style="max-width:90%;width:auto;height:auto;"></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- Messages: style can be found in dropdown.less-->
        <!--
        <li class="dropdown messages-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-envelope-o"></i>
            <span class="label label-success">4</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have 4 messages</li>
            <li>-->
              <!-- inner menu: contains the actual data -->
              <!--<ul class="menu">
                <li>--><!-- start message -->
                  <!--<a href="#">
                    <div class="pull-left">
                      <img src="/storage/imagenes/usuario/{{session()->get('foto_usuario')}}" class="img-circle" alt="User Image">
                    </div>
                    <h4>
                      Support Team
                      <small><i class="fa fa-clock-o"></i> 5 mins</small>
                    </h4>
                    <p>Why not buy a new awesome theme?</p>
                  </a>
                </li>-->
                <!-- end message -->
              <!--</ul>
            </li>
            <li class="footer"><a href="#">See All Messages</a></li>
          </ul>
        </li>-->
        <!-- Notifications: style can be found in dropdown.less -->
        @csrf @method("put")
        @csrf
        <li class="dropdown notifications-menu" id="idnotificaciones" name="idnotificaciones">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bell-o"></i>
            <span class="label label-warning" id="idnotifnum" name="idnotifnum"></span>
          </a>
          <ul class="dropdown-menu" id="notificaciones" name="notificaciones">
            <!--
            <li class="header">You have 10 notifications</li>
            <li>-->
              <!-- inner menu: contains the actual data -->
              <!--              
              <ul class="menu">
                <li>
                  <a href="#">
                    <i class="fa fa-users text-aqua"></i> 5 new members joined today
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                    page and may cause design problems
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-users text-red"></i> 5 new members joined
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-user text-red"></i> You changed your username
                  </a>
                </li>
              </ul>
            </li>
            <li class="footer"><a href="#">View all</a></li>
            -->
          
          </ul>
        </li>
        <!-- Tasks: style can be found in dropdown.less -->
        <!--<li class="dropdown tasks-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-flag-o"></i>
            <span class="label label-danger">9</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have 9 tasks</li>
            <li>-->
              <!-- inner menu: contains the actual data -->
              <!--<ul class="menu">
                <li>--><!-- Task item -->
                  <!--<a href="#">
                    <h3>
                      Design some buttons
                      <small class="pull-right">20%</small>
                    </h3>
                    <div class="progress xs">
                      <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only">20% Complete</span>
                      </div>
                    </div>
                  </a>
                </li>-->
                <!-- end task item -->
              <!--</ul>
            </li>
            <li class="footer">
              <a href="#">View all tasks</a>
            </li>
          </ul>
        </li>-->
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="/storage/imagenes/usuario/{{session()->get('foto_usuario')}}" class="user-image" alt="User Image">
            <span class="hidden-xs">Hola {{session()->get('nombre_corto') ?? 'Invitado'}}</span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <!--<img src="{{asset("assets/$theme/dist/img/user2-160x160.jpg")}}" class="img-circle" alt="User Image">-->
              <img src="/storage/imagenes/usuario/{{session()->get('foto_usuario')}}" class="img-circle" alt="User Image">
              <p>
                {{session()->get('nombre_usuario') }}
                <small>{{session()->get('rol_nombre') }}</small>
              </p>
            </li>
            <!-- Menu Body -->
            @if(session()->get("roles") && count(session()->get("roles")) > 1)
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-12 text-center">
                    <a href="#" id="cambiarRol" name="cambiarRol">Cambiar Rol</a>
                  </div>
                </div>
              </li>
            @endif
            <!--
            <li class="user-body">
              <div class="row">
                <div class="col-xs-4 text-center">
                  <a href="#">Followers</a>
                </div>
                <div class="col-xs-4 text-center">
                  <a href="#">Sales</a>
                </div>
                <div class="col-xs-4 text-center">
                  <a href="#">Friends</a>
                </div>
              </div>
            </li>
            -->
            <!-- Menu Footer-->
            <li class="user-footer">
              <!--
              <div class="pull-left">
                <a href="{{route('login')}}" class="btn btn-default btn-flat">Login</a>
              </div>-->
              <div class="pull-left">
                <a href="{{route('cambclave_usuario')}}" class="btn btn-default btn-flat" data-toggle='tooltip' title="Cambiar Contraseña">Contraseña</a>
              </div>
              <div class="pull-left">
                <a href="{{route('datosbasicos_usuario')}}" class="btn btn-default btn-flat" data-toggle='tooltip' title="Editar datos Usuario (Foto)">Editar</a>
              </div>
              <div class="pull-right">
                <a href="{{route('logout')}}" class="btn btn-default btn-flat" data-toggle='tooltip' title="Salir del Sistema">Salir</a>
              </div>
            </li>
          </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
        <!--
        <li>
          <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
        </li>
        -->
      </ul>
    </div>
  </nav>
</header>
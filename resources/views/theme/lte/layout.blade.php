<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('titulo','Plastiservi') | 2.0</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/font-awesome/css/font-awesome.min.css")}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/Ionicons/css/ionicons.min.css")}}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/bootstrap-daterangepicker/daterangepicker.css")}}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
    <!-- bootstrap select Gilmer-->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/bootstrap-select/css/bootstrap-select.min.css")}}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{asset("assets/$theme/plugins/iCheck/all.css")}}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css")}}">
    <!-- Bootstrap time Picker -->
    <link rel="stylesheet" href="{{asset("assets/$theme/plugins/timepicker/bootstrap-timepicker.min.css")}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/select2/dist/css/select2.min.css")}}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset("assets/$theme/dist/css/AdminLTE.min.css")}}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{asset("assets/$theme/dist/css/skins/_all-skins.min.css")}}">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">-->
    <!-- tooltipster-master -->
    <link rel="stylesheet" href="{{asset("assets/$theme/bower_components/tooltipster-master/dist/css/tooltipster.bundle.min.css")}}">

    <!--<link rel="stylesheet" href="{{asset("assets/css/toastr.min.css")}}">-->
    <link rel="stylesheet" href="{{asset("assets/css/alertify.min.css")}}">
    
    @yield("styles")

    <link rel="stylesheet" href="{{asset("assets/css/custom.css")}}">



    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="shortcut icon" href="{{asset("assets/$theme/dist/img/logoShor.png")}}">
    </head>
    <body class="sidebar-mini wysihtml5-supported skin-black fixed"> <!--con esta clase el menu permanece completo abierto-->
                 
    <!--<body class="sidebar-mini wysihtml5-supported skin-black sidebar-collapse">--> <!-- Con esta clase el menu permanece cerrado -->
        <!-- Site wrapper -->
        <div class="wrapper">
            <!--Inicio de Header-->
            @include("theme/$theme/header")
            <!--Fin Header -->
            <!--Inicio Aside-->
            @include("theme/$theme/aside")
            <!--Fin Aside -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content">
                    @yield('contenido')
                </section>
            </div>
            <!--Inicio Footer-->
            @include("theme/$theme/footer")
            <!--Fin footer-->
            <!--Inicio de ventana modal para login con más de un rol -->
            @if(session()->get("roles") && count(session()->get("roles")) > 1)
                @csrf
                <div class="modal fade" id="modal-seleccionar-rol" data-rol-set="{{empty(session()->get("rol_id")) ? 'NO' : 'SI'}}" tabindex="-1" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Roles de Usuario</h4>
                            </div>
                            <div class="modal-body">
                                <p>Cuentas con mas de un Rol en la plataforma, a continuación seleccione con cual de ellos desea trabajar</p>
                                @foreach(session()->get("roles") as $key => $rol)
                                    <li>
                                        <a href="#" class="asignar-rol" data-rolid="{{$rol['id']}}" data-rolnombre="{{$rol["nombre"]}}">
                                            {{$rol["nombre"]}}
                                        </a>
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div id="loading-screen" style="display:none">
            <img src="{{asset("assets/$theme/dist/img/spinning-circles.svg")}}">
        </div>
        <!-- jQuery 3 -->
        <script src="{{asset("assets/$theme/bower_components/jquery/dist/jquery.min.js")}}"></script>
        <!-- jQuery UI - v1.12.1 - 2016-09-14 -->
        <script src="{{asset("assets/$theme/bower_components/jquery-ui/jquery-ui.min.js")}}"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="{{asset("assets/$theme/bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
        <!-- numeric Gilmer -->
        <script src="{{asset("assets/js/jquery-validation/jquery.numeric.min.js")}}"></script>
        <!-- DataTables -->
        <script src="{{asset("assets/$theme/bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>
        <script src="{{asset("assets/$theme/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
        <!-- Select2 -->
        <script src="{{asset("assets/$theme/bower_components/select2/dist/js/select2.full.min.js")}}"></script>
        <!-- bootstrap-select Gilmer -->
        <script src="{{asset("assets/$theme/bower_components/bootstrap-select/js/bootstrap-select.min.js")}}"></script>

        <!-- InputMask -->
        <script src="{{asset("assets/$theme/plugins/input-mask/jquery.inputmask.js")}}"></script>
        <script src="{{asset("assets/$theme/plugins/input-mask/jquery.inputmask.date.extensions.js")}}"></script>
        <script src="{{asset("assets/$theme/plugins/input-mask/jquery.inputmask.extensions.js")}}"></script>
        <!-- date-range-picker -->
        <script src="{{asset("assets/$theme/bower_components/moment/min/moment.min.js")}}"></script>
        <script src="{{asset("assets/$theme/bower_components/bootstrap-daterangepicker/daterangepicker.js")}}"></script>
        <!-- bootstrap datepicker -->
        <script src="{{asset("assets/$theme/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
        <script src="{{asset("assets/$theme/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.es.min.js")}}"></script>
        <!-- bootstrap color picker -->
        <script src="{{asset("assets/$theme/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js")}}"></script>
        <!-- bootstrap time picker -->
        <script src="{{asset("assets/$theme/plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
        <!-- SlimScroll -->
        <script src="{{asset("assets/$theme/bower_components/jquery-slimscroll/jquery.slimscroll.min.js")}}"></script>
        <!-- ChartJS -->
        <!--<script src="{{asset("assets/$theme/bower_components/chart.js/Chart.js")}}"></script>-->
        <!-- ChartJS -->
        <script src="{{asset("assets/$theme/bower_components/chart/Chart.js")}}"></script>
        <script src="{{asset("assets/$theme/bower_components/chart/utils.js")}}"></script>
        <!-- iCheck 1.0.1 -->
        <script src="{{asset("assets/$theme/plugins/iCheck/icheck.min.js")}}"></script>
        <!-- FastClick -->
        <script src="{{asset("assets/$theme/bower_components/fastclick/lib/fastclick.js")}}"></script>
        <!-- AdminLTE App -->
        <script src="{{asset("assets/$theme/dist/js/adminlte.min.js")}}"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{asset("assets/$theme/dist/js/demo.js")}}"></script>
        
        @yield("scriptsPlugins")
        <script src="{{asset("assets/js/jquery-validation/jquery.validate.min.js")}}"></script>
        <script src="{{asset("assets/js/jquery-validation/localization/messages_es.min.js")}}"></script>
        <!--
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        -->
        <!-- date-range-picker -->
        <script src="{{asset("assets/$theme/bower_components/tooltipster-master/dist/js/tooltipster.bundle.min.js")}}"></script>

        <script src="{{asset("assets/js/sweetalert.min.js")}}"></script>
        <script src="{{asset("assets/js/alertify.min.js")}}"></script>
        
        <script src="{{asset("assets/js/scripts.js")}}"></script>
        <script src="{{asset("assets/js/funciones.js")}}"></script>

        @yield('scripts')

    </body>
</html>
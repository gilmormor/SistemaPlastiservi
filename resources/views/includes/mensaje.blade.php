@if(session("mensaje"))
    <?
        $tipo_alert = "alert-success"; //Si a la pantalla no le he agregado la validacion de registro modificado por otro usuario entonces por defecto ponga este valor "alert-success"
    ?>

    @if(session("tipo_alert"))
        <?
            $tipo_alert = session("tipo_alert");
        ?>
    @endif

    <div class="alert {{$tipo_alert}} alert-dismissible" data-auto-dismiss="7000" id="divmensaje" name="divmensaje">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> Mensaje Sistema!</h4>
        <ul>
            <li id="mensaje" name="mensaje">{{ session("mensaje") }}</li>
        </ul>
    </div>
@endif
@section("scripts")
    <script src="{{asset("assets/pages/scripts/include/mensaje.js")}}" type="text/javascript"></script>
@endsection
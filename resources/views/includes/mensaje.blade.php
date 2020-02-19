@if(session("mensaje"))
    <div class="alert alert-success alert-dismissible" data-auto-dismiss="3000" id="divmensaje" name="divmensaje" style="display: none">
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
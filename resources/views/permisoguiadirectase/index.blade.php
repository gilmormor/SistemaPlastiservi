@extends("theme.$theme.layout")
@section('titulo')
Permiso Guia Directa
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/permisoguiadirectase/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Permiso Guia Directa Despacho</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <form action="{{route('exportPdf_notaventaconsulta')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        @csrf @method("put")
                        <input type='hidden' id='permiso' name='permiso' value="{{old('permiso', $aux_permiso ?? '')}}">
                        <input style="display:none" id="358id24" name="358id24" type="checkbox" class="permiso_rol" data-permisoid="358" value="24" {{$aux_permiso == 1 ? "checked" : ""}}>
                        <input style="display:none" id="359id24" name="359id24" type="checkbox" class="permiso_rol" data-permisoid="359" value="24" {{$aux_permiso == 1 ? "checked" : ""}}>
                        <input style="display:none" id="361id24" name="361id24" type="checkbox" class="permiso_rol" data-permisoid="361" value="24" {{$aux_permiso == 1 ? "checked" : ""}}>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-4">
                                <br>
                                <div class='checkbox'>
                                    <label style='font-size: 1.2em' data-toggle='tooltip' title='Permiso'>
                                        <input type='checkbox' id='permisoT' name='permisoT'>
                                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                        Permiso
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
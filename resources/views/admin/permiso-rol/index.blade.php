@extends("theme.$theme.layout")
@section("titulo")
Permiso - Rol
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/permiso-rol/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        @csrf
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Menús - Rol</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">
                @csrf
                <div class="col-xs-12 col-md-9 col-sm-12">
                    <div class="col-xs-12 col-sm-6">
                        <div class="col-xs-12 col-md-2 col-sm-2 text-left">
                            <label data-toggle='tooltip' title="Roles">Roles:</label>
                        </div>
                        <div class="col-xs-12 col-md-10 col-sm-10">
                            <select name='rol_id' id='rol_id' class='selectpicker form-control rol_id'  data-live-search='true' multiple data-actions-box='true'>
                                @foreach ($roles as $rol)
                                    <option value="{{$rol->id}}">{{$rol->nombre}}</option>
                                @endforeach                        
                            </select>                
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                    <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                </div>

            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data"  name="tabla-data">
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
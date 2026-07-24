@extends("theme.$theme.layout")
@section('titulo')
Etapas de produccion x Persona
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/etapasprodxpersona/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido') 
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Etapas de produccion x Persona</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-9 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" title="Sucursal">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Sucursal:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" data-live-search='true' data-actions-box='true'>
                                        @foreach($tablas["sucursales"] as $sucursal)
                                            <option
                                                value="{{$sucursal->id}}"
                                            >{{$sucursal->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>      
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>
            <div class="table-responsive">
                <table id='tabla-data-etapasprodxpersona' name='tabla-data-etapasprodxpersona' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='10'>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
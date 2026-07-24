@extends("theme.$theme.layout")
@section('titulo')
Etapas de produccion x Persona
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/personaetapaprod/index.js")}}" type="text/javascript"></script>
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
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th class="width70">RUT</th>
                            <th>Nombre Apellido</th>
                            <th>Correo</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
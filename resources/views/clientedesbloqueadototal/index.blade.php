@extends("theme.$theme.layout")
@section('titulo')
Cliente Desbloqueado Total
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/clientedesbloqueadototal/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Cliente Desbloqueado Total</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_clientedesbloqueadototal')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i>Nuevo
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" data-page-length="25">
                    <thead>
                        <tr>
                            <th class="width30">ID</th>
                            <th class="width70">CodCli</th>
                            <th class="width70">RUT</th>
                            <th>Nombre</th>
                            <th>Obs</th>
                            <th class="width70">Acción</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
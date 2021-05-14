@extends("theme.$theme.layout")
@section('titulo')
Clientes
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
@csrf @method("delete")
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Clientes</h3>
                @if(can('guardar-cliente',false) == true)
                    <div class="box-tools pull-right">
                        <a href="{{route('crear_cliente')}}" class="btn btn-block btn-success btn-sm">
                            <i class="fa fa-fw fa-plus-circle"></i> Crear Cliente
                        </a>
                    </div>
                @endif
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th class="width70">RUT</th>
                            <th>Nombre</th>
                            <th class="width70">Acción</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
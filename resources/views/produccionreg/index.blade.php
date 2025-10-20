@extends("theme.$theme.layout")
@section('titulo')
Registrar Produccion
@endsection

@section("scripts")
    <script>
        const RUTA_AJAX = "{{ url('produccionregpage') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/produccionreg/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Registrar Produccion</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('produccionreg_listaropdet')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo registro
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th class="width70">Sucursal</th>
                            <th class="width70">OP ID</th>
                            <th>opdet_id</th>
                            <th class="width70">Accion</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
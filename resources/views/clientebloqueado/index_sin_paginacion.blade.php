@extends("theme.$theme.layout")
@section('titulo')
Cliente Bloquedo
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Cliente Bloquedo</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_clientebloqueado')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo Bloqueo
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th class="width70">Cod Cliente</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->cliente_id}}</td>
                            <td>{{$data->cliente->razonsocial}}</td>
                            <td>{{$data->descripcion}}</td>
                            <td>
                                <a href="{{route('editar_clientebloqueado', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                    <i class="fa fa-fw fa-pencil"></i>
                                </a>
                                <form action="{{route('eliminar_clientebloqueado', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
                                    @csrf @method("delete")
                                    <button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro">
                                        <i class="fa fa-fw fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
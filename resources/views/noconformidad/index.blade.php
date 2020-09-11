@extends("theme.$theme.layout")
@section('titulo')
No Conformidad
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">No Conformidad</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_noconformidad')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo registro
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table display AllDataTables table-hover table-condensed tablascons" id="tabla-data" data-page-length='30'>
                <!--<table class="table table-striped table-bordered table-hover" id="tabla-data" data-page-length='30'>-->
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>Fecha</th>
                            <th>Punto Normativo Hallazgo</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                        <tr>
                            <td>{{$data->id}}
                            </td>
                            <td>
                                {{date('d-m-Y', strtotime($data->fechahora))}}
                            </td>
                            <td>
                                {{$data->hallazgo}}
                            </td>
                            <td>
                                @if (empty($data->accioninmediata))
                                    <a href="{{route('editar_noconformidad', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                        <i class="fa fa-fw fa-pencil"></i>
                                    </a>
                                @else
                                    <a href="#" class="btn-accion-tabla tooltipsC" title="No se puede editar (Iniciado proceso NC)">
                                        <i class="fa fa-fw fa-pencil"></i>
                                    </a>
                                    <a href="{{route('ver_noconformidad', ['id' => $data->id, 'sta_val' => '2'])}}" class="btn-accion-tabla tooltipsC" title="Ver Proceso NC">
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>

                                @endif
                                <!--
                                <form action="{{route('eliminar_noconformidad', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
                                    @csrf @method("delete")
                                    <button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro">
                                        <i class="fa fa-fw fa-trash text-danger"></i>
                                    </button>
                                </form>
                                -->
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
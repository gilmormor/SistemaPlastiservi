@extends("theme.$theme.layout")
@section('titulo')
Solicitud de despacho
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Solicitud de Despacho</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('listarnv_despachosol')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo registro
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>Descripción</th>
                            <th>OC</th>
                            <th>NV</th>
                            <th>NVK</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->notaventa->cliente->razonsocial}}</td>
                            <td>
                                <a class='btn-accion-tabla btn-sm' onclick='verpdf2("{{$data->notaventa->oc_file}}",2)'>
                                    {{$data->notaventa->oc_id}}
                                </a>
                            </td>
                            <td>
                                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->notaventa_id}},1)' title='Nota de venta' data-toggle='tooltip'>
                                    <i class='fa fa-fw fa-file-pdf-o'></i>
                                </a>
                            </td>
                            <td>
                                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->id}},2)' title='Nota de venta Kilos' data-toggle='tooltip'>
                                    <i class='fa fa-fw fa-file-pdf-o'></i>
                                </a>
                            </td>
                            <td>
                                <a href="{{route('editar_despachosol', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                    <i class="fa fa-fw fa-pencil"></i>
                                </a>
                                <form action="{{route('eliminar_despachosol', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
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
@include('generales.modalpdf')
@endsection
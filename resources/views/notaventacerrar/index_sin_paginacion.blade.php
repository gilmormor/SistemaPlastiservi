@extends("theme.$theme.layout")
@section('titulo')
Notas de Venta Cerradas
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Notas de Venta Cerradas</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_notaventacerrada')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo Cierre
                    </a>
                </div>
            </div>
            <div class="box-body">
                <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                <table class="table display table-striped AllDataTables table-hover table-condensed" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>Observación</th>
                            <th>Motivo</th>
                            <th class="width70"><label for="" title='PDF' data-toggle='tooltip'>PDF</label></th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->observacion}}</td>
                            <td>
                                <?php 
                                    switch ($data->motcierre_id) {
                                    case 1:
                                        $aux_motcierre_id = "Por fecha";
                                        break;
                                    case 2:
                                        $aux_motcierre_id = "Por precio";
                                        break;
                                    case 3:
                                        $aux_motcierre_id = "Por solicitud cliente";
                                        break;
                                    }
                                ?>
                                {{$aux_motcierre_id}}
                            </td>
                            <td>
                                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->notaventa_id}},{{"1"}})' title='Nota de venta' data-toggle='tooltip'>
                                    <i class="fa fa-fw fa-file-pdf-o"></i>
                                </a>
                                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->notaventa_id}},{{"2"}})' title='Precio x Kg' data-toggle='tooltip'>
                                    <i class="fa fa-fw fa-file-pdf-o"></i> {{$data->notaventa_id}}
                                </a>
                            </td>
                            
                            <td>
                                <a href="{{route('editar_notaventacerrada', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                    <i class="fa fa-fw fa-pencil"></i>
                                </a>
                                <form action="{{route('eliminar_notaventacerrada', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
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
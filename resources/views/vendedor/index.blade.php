@extends("theme.$theme.layout")
@section('titulo')
Vendedor
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Vendedor</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_vendedor')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo registro
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Roles</th>
                            <th>Activo</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                            <?php 
                                $aux_usuario_id = $data->persona->usuario_id;
                                $aux_rol_nombre = "";
                                //dd($aux_usuario_id);
                                if($aux_usuario_id){
                                    $sql = "SELECT GROUP_CONCAT(DISTINCT rol.nombre) AS rol_nombre
                                        FROM usuario_rol LEFT JOIN rol
                                        ON rol.id = usuario_rol.rol_id
                                        WHERE usuario_rol.usuario_id = $aux_usuario_id
                                        GROUP BY usuario_rol.usuario_id;";
                                    $usuario_rol = DB::select($sql);
                                    $aux_rol_nombre = $usuario_rol[0]->rol_nombre;
                                }

                                //dd($usuario_rol);
                            ?>
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->persona->nombre}} {{$data->persona->apellido}}</td>
                            <td>{{$data->persona->usuario["email"]}}</td>
                            <td>{{$aux_rol_nombre}}</td>
                            <td>{{$data->sta_activo ? 'Si' : 'No' }}</td>
                            <td>
                                <a href="{{route('editar_vendedor', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                    <i class="fa fa-fw fa-pencil"></i>
                                </a>
                                <form action="{{route('eliminar_vendedor', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
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
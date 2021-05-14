@extends("theme.$theme.layout")
@section('titulo')
    Sucursal Jefatura
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/jefaturaareasuc/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar sucursal jefatura</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('jefaturaAreaSuc')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_jefaturaAreaSuc', ['id' => $sucursales[0]->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="box-body">
                    @include('jefaturaAreaSuc.form')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    @include('includes.boton-form-editar')
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>

<form action="{{route('asignarjefe_jefaturaAreaSuc')}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
    @csrf @method("post")
    <div class="modal fade" id="myModalJefe" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Jefe de Departamento</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                                    <thead>
                                        <tr>
                                            <th style="display:none;">ID</th>
                                            <th>Jefatura</th>
                                            <th>Jefe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0;?>
                                        @foreach($jefaturasucursalareas as $jefaturasucursalarea)
                                            <tr name="fila{{$i}}" id="fila{{$i}}">
                                                <td style="display:none;">
                                                    <input type="text" name="id[]" id="id{{$i}}" class="form-control" value="{{$jefaturasucursalarea->id}}" style="display:none;"/>
                                                </td>
                                                <td name="jefatura{{$i}}" id="jefatura{{$i}}" jefatura_id={{$jefaturasucursalarea->id}}>
                                                    {{$jefaturasucursalarea->jefatura->nombre}}
                                                </td>
                                                <td name="persona_id{{$i}}" id="persona_id{{$i}}">
                                                    <select name="personal_idD[]" id="personal_idD{{$i}}" class="selectpicker form-control" data-live-search='true'>
                                                        <option value="">Seleccione...</option>
                                                        @foreach($personas as $persona)
                                                            <option
                                                                value="{{$persona->id}}"
                                                                @if ($jefaturasucursalarea->persona_id==$persona->id)
                                                                    {{'selected'}}
                                                                @endif
                                                                >
                                                                {{$persona->nombre}} {{$persona->apellido}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <?php $i++;?>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                    <!--<button type="button" id="guardarJefe" name="guardarJefe" class="btn btn-primary" items={{$i}}>Guardar</button>-->
                </div>
            </div>
            
        </div>
    </div>
</form>
@endsection
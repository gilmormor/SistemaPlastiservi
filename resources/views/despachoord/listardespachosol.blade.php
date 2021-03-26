@extends("theme.$theme.layout")
@section('titulo')
Solicitud Despacho
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/despachoord/listardespachosol.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">Pendientes Solicitud Despacho</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
                <div class="box-tools pull-right">
                    <a href="{{route('despachoord')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            @csrf
            @include('generales.filtrosconsultasoldespacho')  <!--Filtros consulta solicitid de despacho-->
        </div>
    </div>
</div>

<div class="modal fade" id="myModaldevsoldeps" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="mdialTamanio1">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Devolver Solicitud Despacho</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="nfilaanul" id="nfilaanul">
                    <div class="form-group col-xs-12 col-sm-5">
                        <label for="despachosol_id" class="control-label">Id Solicitud Despacho</label>
                        <input type="text" name="despachosol_id" id="despachosol_id" class="form-control" required placeholder="ID" disabled readonly/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                        <label for="observacion" class="control-label">Observación</label>
                        <textarea name="observacion" id="observacion" class="form-control requeridos" tipoval="texto"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnGuardarGanul" name="btnGuardarGanul" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        
    </div>
</div>

@include('generales.buscarcliente')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection

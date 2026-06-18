@extends("theme.$theme.layout")
@section('titulo')
CC — Desbloqueo de Muestras
@endsection
@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/ccdesbloqueo/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-unlock-alt"></i> Desbloqueo de Muestras CC Rechazadas
                </h3>
                <small class="text-muted" style="margin-left:10px;">
                    Muestras con status <strong>Rechazado</strong> aprobadas por supervisor — pendientes o ya desbloqueadas
                </small>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="tabla-data-ccdesbloqueo">
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th>Fecha Registro</th>
                                <th class="tooltipsC" title="Orden de Producción / Orden de Trabajo">OP / OT</th>
                                <th class="tooltipsC" title="Nota de Venta">NV</th>
                                <th class="tooltipsC" title="Registro de Producción">Reg. Prod.</th>
                                <th class="tooltipsC" title="Código de producto">CodProd</th>
                                <th>Producto</th>
                                <th>Etapa</th>
                                <th class="tooltipsC" title="Kg producidos">Kg</th>
                                <th>Observación muestra</th>
                                <th>Desbloqueo</th>
                                <th class="width70">Acción</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('generales.modalpdf')

{{-- Modal desbloqueo --}}
<div class="modal fade" id="modal-desbloquear-muestra" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#f39c12; color:#fff; border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-unlock-alt"></i> Desbloquear Muestra CC #<span id="desbloquear-id-label"></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    Al desbloquear esta muestra <strong>rechazada</strong>, se habilitará el despacho del lote
                    correspondiente en Solicitud y Orden de Despacho.
                </div>
                <div class="form-group">
                    <label><strong>Observación del desbloqueo <span class="text-danger">*</span>:</strong></label>
                    <textarea id="observacion-desbloqueo" class="form-control" rows="3"
                        maxlength="500" placeholder="Describa el motivo por el cual se autoriza el despacho del lote rechazado..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btn-confirmar-desbloqueo">
                    <i class="fa fa-unlock-alt"></i> Confirmar Desbloqueo
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal etiqueta de registro de producción --}}
<div class="modal fade" id="modalEtiquetaEtapa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-tag"></i> Etiqueta Registro de Producción</h4>
            </div>
            <div class="modal-body" style="padding:0;">
                <iframe id="ifrEtiquetaEtapa" src="" style="width:100%;height:600px;border:none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

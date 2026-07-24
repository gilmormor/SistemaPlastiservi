@extends("theme.$theme.layout")
@section('titulo')
Control de Calidad — Muestras
@endsection
@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/ccregistmuestra/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Muestras de Control de Calidad</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('listaropdetregprod_ccregistmuestra')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nueva Muestra CC
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="tabla-data-ccregistmuestra">
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
                                <th>Status CC</th>
                                <th class="tooltipsC" title="Liberado al siguiente módulo">Lib.</th>
                                <th>Desbloqueo</th>
                                <th class="ocultar">sta_env</th>
                                <th class="ocultar">status_num</th>
                                <th class="ocultar">updated_at</th>
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

{{-- Modal desbloqueo de muestra rechazada --}}
<div class="modal fade" id="modal-desbloquear-muestra" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#f39c12; color:#fff; border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-unlock-alt"></i> Desbloquear Muestra CC #<span id="desbloquear-id-label"></span></h4>
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

{{-- Modal global de anulación --}}
<div class="modal fade" id="modal-anular-muestra" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form-anular-muestra" method="POST" action="">
                @csrf
                <div class="modal-header" style="background:#dd4b39; color:#fff; border-radius:4px 4px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-ban"></i> Anular Muestra CC #<span id="anular-id-label"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> Esta acción no se puede revertir.
                    </div>
                    <div class="form-group">
                        <label><strong>Motivo de anulación <span class="text-danger">*</span>:</strong></label>
                        <textarea name="motivo" id="motivo-anulacion" class="form-control" rows="3"
                            maxlength="500" required placeholder="Describa el motivo de la anulación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-ban"></i> Confirmar Anulación
                    </button>
                </div>
            </form>
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

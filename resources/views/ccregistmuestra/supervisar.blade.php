@extends("theme.$theme.layout")
@section('titulo')
CC — Supervisión de Muestras
@endsection
@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/ccregistmuestra/supervisar.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-check-square-o"></i> Supervisión de Muestras CC — Pendientes de revisión</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('ccregistmuestra')}}" class="btn btn-default btn-sm">
                        <i class="fa fa-reply"></i> Listado operario
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed table-hover" id="tabla-data-ccsupervisar">
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
                                <th>Registrado por</th>
                                <th class="ocultar">status_num</th>
                                <th class="ocultar">updated_at</th>
                                <th class="width100">Acción</th>
                            </tr>
                        </thead>
                        <tfoot><tr></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de aprobación / rechazo supervisor --}}
<div class="modal fade" id="modal-cc-supervisar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-check-square-o"></i> Revisión Muestra CC #<span id="cc-sup-id-label"></span></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><strong>Observación</strong> <small class="text-muted">(requerida al rechazar)</small>:</label>
                    <textarea id="cc-sup-obs" class="form-control" rows="3" maxlength="500"
                        placeholder="Ingrese observación del supervisor..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btn-cc-sup-rechazar">
                    <i class="fa fa-times-circle"></i> Rechazar
                </button>
                <button type="button" class="btn btn-success" id="btn-cc-sup-aprobar">
                    <i class="fa fa-check"></i> Aprobar
                </button>
            </div>
        </div>
    </div>
</div>

@include('generales.modalpdf')

{{-- Modal etiqueta de registro de producción --}}
<div class="modal fade" id="modalEtiquetaEtapa" tabindex="-1" role="dialog" aria-labelledby="modalEtiquetaEtapaLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modalEtiquetaEtapaLabel"><i class="fa fa-tag"></i> Etiqueta Registro de Producción</h4>
            </div>
            <div class="modal-body" style="padding:0;">
                <iframe id="ifrEtiquetaEtapa" src="" style="width:100%;height:600px;border:none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@extends("theme.$theme.layout")
@section('titulo')
CC — Desbloqueo de Muestras
@endsection
@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
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
                    Ingrese al menos un filtro y presione <strong>Consultar</strong>
                </small>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">

                {{-- Panel de filtros (R5) --}}
                <div class="row">
                    <div class="col-xs-12 col-md-9 col-sm-12">

                        {{-- Fila 1: Fechas + Status desbloqueo + Etapa --}}
                        <div class="col-xs-12 col-md-12">
                            <div class="col-xs-12 col-md-3">
                                <label>Fecha registro desde:</label>
                                <input type="text" id="fecha_desde" class="form-control date-picker" placeholder="dd/mm/aaaa" readonly>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>Fecha registro hasta:</label>
                                <input type="text" id="fecha_hasta" class="form-control date-picker" placeholder="dd/mm/aaaa" readonly>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>Status desbloqueo:</label>
                                <select id="sta_desbloqueo" class="selectpicker form-control">
                                    <option value="">Todos</option>
                                    <option value="0">Bloqueado (pendiente)</option>
                                    <option value="1">Desbloqueado</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>Etapa producción:</label>
                                <select id="etapaprod_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todas</option>
                                    @foreach($etapaprods as $ep)
                                        <option value="{{$ep->id}}">{{$ep->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fila 2: RUT cliente + Producto + Máquina --}}
                        <div class="col-xs-12 col-md-12" style="margin-top:8px;">
                            <div class="col-xs-12 col-md-4">
                                <label>RUT cliente:</label>
                                <div class="input-group">
                                    <input type="text" name="rut1" id="rut1" class="form-control" value=""
                                           placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12">
                                    <p id="error-message" style="color:red; display:none;">RUT inválido</p>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente">Buscar</button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Producto (código):</label>
                                <div class="input-group">
                                    <input type="text" name="producto_idPxP" id="producto_idPxP" class="form-control"
                                           tipoval="numericootro" placeholder="ID(s) separados por coma">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="btnbuscarproducto" name="btnbuscarproducto">Buscar</button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Máquina:</label>
                                <select id="maquina_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todas</option>
                                    @foreach($maquinas as $mq)
                                        <option value="{{$mq->id}}">{{$mq->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fila 3: ID Lote + OT + OP + NV --}}
                        <div class="col-xs-12 col-md-12" style="margin-top:8px;">
                            <div class="col-xs-12 col-md-3">
                                <label>ID Lote / Reg. Prod.:</label>
                                <input type="text" id="opdetregprod_id" class="form-control" placeholder="Ej: 123" maxlength="10">
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>N° OT:</label>
                                <input type="text" id="ot_id" class="form-control" placeholder="N° Orden de Trabajo" maxlength="10">
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>N° OP:</label>
                                <input type="text" id="op_id" class="form-control" placeholder="N° Orden de Producción" maxlength="10">
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>N° Nota de Venta:</label>
                                <input type="text" id="notaventa_id" class="form-control" placeholder="N° NV" maxlength="10">
                            </div>
                        </div>
                    </div>

                    {{-- Botón Consultar --}}
                    <div class="col-xs-12 col-md-3 col-sm-12 text-center" style="padding-top:22px;">
                        <button type="button" id="btnconsultar" class="btn btn-danger btn-block tooltipsC" title="Consultar muestras rechazadas">
                            <i class="fa fa-search"></i> Consultar
                        </button>
                    </div>
                </div>

                {{-- Tabla resultado (vacía al cargar; se llena al hacer click en Consultar) --}}
                <div class="table-responsive" style="margin-top:16px;">
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

@include('generales.buscarclientebd')
@include('generales.buscarproductobd')
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

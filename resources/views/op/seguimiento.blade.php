@extends("theme.$theme.layout")
@section('titulo') Seguimiento de Producción @endsection

@section('scripts')
    <script src="{{autoVer('assets/pages/scripts/general.js')}}" type="text/javascript"></script>
    <script src="{{autoVer('assets/pages/scripts/op/seguimiento.js')}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> Seguimiento de Órdenes de Producción</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('otitemprogramacion')}}" class="btn btn-sm btn-info">
                        <i class="fa fa-reply"></i> Volver a Programación
                    </a>
                </div>
            </div>
            @csrf
            <div class="box-body">
                <div class="row">
                    {{-- Filtros --}}
                    <div class="col-xs-12 col-md-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3" title="Fecha Inicial">
                                <label>Fecha Ini:</label>
                                <input type="text" class="form-control datepicker" id="fechad" name="fechad"
                                       placeholder="DD/MM/AAAA" readonly/>
                            </div>
                            <div class="col-xs-12 col-sm-3" title="Fecha Final">
                                <label>Fecha Fin:</label>
                                <input type="text" class="form-control datepicker" id="fechah" name="fechah"
                                       placeholder="DD/MM/AAAA" readonly/>
                            </div>
                            <div class="col-xs-12 col-sm-2" title="Número OP">
                                <label>OP:</label>
                                <input type="text" class="form-control numerico-entero" id="op_id" placeholder="Nº OP"/>
                            </div>
                            <div class="col-xs-12 col-sm-2" title="Número OT">
                                <label>OT:</label>
                                <input type="text" class="form-control numerico-entero" id="ot_id" placeholder="Nº OT"/>
                            </div>
                            <div class="col-xs-12 col-sm-2" title="Nota de Venta">
                                <label>NV:</label>
                                <input type="text" class="form-control numerico-entero" id="nv_id" placeholder="Nº NV"/>
                            </div>
                        </div>
                        <div class="row" style="margin-top:6px;">
                            <div class="col-xs-12 col-sm-3" title="Código producto">
                                <label>Producto:</label>
                                <input type="text" class="form-control numerico-entero" id="producto_id" placeholder="Cód. producto"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-2 text-center" style="padding-top:20px;">
                        <button type="button" id="btnconsultar" class="btn btn-success">
                            <i class="fa fa-search"></i> Consultar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Leyenda de estados --}}
            <div class="box-body" style="padding-top:0; padding-bottom:4px;">
                <small>
                    <span style="color:#00a65a;"><i class="fa fa-circle"></i></span> Etapa completa &nbsp;
                    <span style="color:#f39c12;"><i class="fa fa-circle"></i></span> En proceso (no enviado) &nbsp;
                    <span style="color:#3c8dbc;"><i class="fa fa-circle"></i></span> Esperando supervisor &nbsp;
                    <span style="color:#dd4b39;"><i class="fa fa-circle"></i></span> Rechazado &nbsp;
                    <span style="color:#aaa;"><i class="fa fa-circle"></i></span> Sin iniciar
                </small>
            </div>

            <div class="table-responsive">
                <table id="tabla-seguimiento" class="table display AllDataTables table-hover table-condensed"
                       data-page-length="25">
                    <tfoot></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

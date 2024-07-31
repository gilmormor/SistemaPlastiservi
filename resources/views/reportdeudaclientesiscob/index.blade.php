@extends("theme.$theme.layout")
@section('titulo')
Deuda Clientes Sis Cob
@endsection

<?php
    $selecmultprod = true;
?>

@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportdeudaclientesiscob/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script> 
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Deuda Clientes Sistema Cobranza</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            @csrf
            <div class="box-body">
                <div class="row">
                    <form action="{{route('exportPdf_notaventaconsulta')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        @csrf @method("put")
                        <input type="hidden" name="selecmultprod" id="selecmultprod" value="{{old('selecmultprod', $selecmultprod ?? '')}}">
                        <input type="hidden" name="fechahoy" id="fechahoy" value="{{date('Y-m-d')}}">
                        <div class="col-xs-12 col-md-9 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="rut" data-toggle='tooltip' title="RUT">RUT:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <div class="input-group">
                                            <input type="text" name="rut" id="rut" cliente_id="" class="form-control" value="{{old('rut')}}" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12" data-toggle='tooltip'/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Vendedor">Vendedor:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <?php
                                            echo $tablas['vendedores'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="sucursal_id" data-toggle='tooltip' title="Sucursal">Sucursal</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" required>
                                            <option value="">Todos...</option>
                                            @foreach($tablas['sucursales'] as $sucursal)
                                                <option
                                                    value="{{$sucursal->id}}"
                                                >{{$sucursal->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="statusDeuda" data-toggle='tooltip' title="Estatus">Estatus Cliente</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="statusDeuda" id="statusDeuda" class="selectpicker form-control" required>
                                            <option value="0">Todos...</option>
                                            <option value="1">Bloqueados (Deuda Vencida)</option>
                                            <option value="2">DesBloqueados (Deuda Vigente)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar" onclick="ejecutarConsulta(1)">Consultar</button>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type='button' id='btnpdf2' name='btnpdf2' class='btn btn-success tooltipsC' title="Reporte PDF" onclick="ejecutarConsulta(2)">
                                    <i class='glyphicon glyphicon-print'></i> Reporte
                                </button>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="btnexportarExcel" name="btnexportarExcel" class="btn btn-success tooltipsC" title="Exportar Excel"  onclick="ejecutarConsulta(3)">
                                    <i class='fa fa-fw fa-file-excel-o'> </i> Excel
                                </button>
                            </div>
                            @if (can('boton-actualizar-data-cobranza',false))
                                <div class="col-xs-12 col-md-4 col-sm-4">
                                    <button type="button" id="llenartabladatacobranza" name="llenartabladatacobranza" class="btn btn-success tooltipsC" title="Llenar tabla">
                                        <i class='fa fa-fw fa-file-excel-o'> </i> LlenarTabla
                                    </button>
                                </div>
                            @endif
    
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="llenartabla" name="llenartabla" class="btn btn-success tooltipsC" title="Llenar tabla" onclick="ejecutarConsulta(4)">
                                    <i class='fa fa-fw fa-file-excel-o'> </i> Enviar correo
                                </button>
                            </div>
                            <!--
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="btnexportarExcelDet" name="btnexportarExcelDet" class="btn btn-success tooltipsC" title="Exportar Excel Detalle" onclick="exportarExcelDTEDet()">
                                    <i class='fa fa-fw fa-file-excel-o'> </i>  Detalle
                                </button>
                            </div>-->
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>
            
            <div class="table-responsive" id="tablaconsulta1">
                <div class="form-group col-xs-12 col-sm-6">
                    <label for="razonsocial" class="control-label" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                    <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="" maxlength="70" readonly/>
                </div>
                <div class="form-group col-xs-12 col-sm-2">
                    <label for="limitecredito" class="control-label" data-toggle='tooltip' title="Limite de Crédito">Limite de Crédito</label>
                    <input type="text" name="limitecredito" id="limitecredito" class="form-control" value="" maxlength="70" style="text-align:right" readonly/>
                </div>
                <div class="form-group col-xs-12 col-sm-2">
                    <label for="TDeuda" class="control-label" data-toggle='tooltip' title="Total Deuda">Total Deuda</label>
                    <input type="text" name="TDeuda" id="TDeuda" class="form-control" value="" maxlength="70" style="text-align:right" readonly/>
                </div>
                <div class="form-group col-xs-12 col-sm-2">
                    <label for="TDeudaFec" class="control-label" data-toggle='tooltip' title="Total Deuda Fecha">Total Deuda Vencida</label>
                    <input type="text" name="TDeudaFec" id="TDeudaFec" class="form-control" value="" maxlength="70" style="text-align:right" readonly/>
                </div>           
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data-consulta" data-page-length="25">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Nro Fact</th>
                            <th style="text-align:center;">Fecha Fact</th>
                            <th style="text-align:center;">Fecha Venc</th>
                            <th style="text-align:right;">Monto Fact</th>
                            <th style="text-align:right;">Deuda</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                        </tr>
                        <tr>
                            <th colspan='3' style='text-align:right'>Total Pagina</th>
                            <th id='subtotalfac' name='subtotalfac' style='text-align:right'>0</th>
                            <th id='subtotaldeuda' name='subtotaldeuda' style='text-align:right'>0</th>
                        </tr>
                        <tr>
                            <th colspan='3' style='text-align:right'>Total</th>
                            <th id='totalfac' name='totalfac' style='text-align:right'>0</th>
                            <th id='totaldeuda' name='totaldeuda' style='text-align:right'>0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

@include('generales.buscarclientebd')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection

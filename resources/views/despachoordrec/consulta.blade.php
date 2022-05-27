@extends("theme.$theme.layout")
@section('titulo')
{{$tablashtml['titulo']}}
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachoordrec/consulta.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachoordrec/indexguiafact.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<input type="hidden" name="aux_verestado" id="aux_verestado" value="{{old('aux_verestado', $tablashtml['aux_verestado'] ?? '')}}">
<input type="hidden" name="rutacrearrec" id="rutacrearrec" value="{{old('rutacrearrec', $tablashtml['rutacrearrec'] ?? '')}}">

<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">{{$tablashtml['titulo']}}</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
                <div class="box-tools pull-right">
                    <a href="{{route('despachoordrec')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    @csrf
                    <div class="col-xs-12 col-md-10 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial Orden Despacho">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fecha">Fecha Ini OD:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Final Orden Despacho">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="dep_fecha">Fecha Fin OD:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" class="form-control datepicker" name="fechah" id="fechah"placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial Factura">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fechaIniFac">F Ini Fact:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" bsDaterangepicker class="form-control datepicker" name="fechadfac" id="fechadfac" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Final Factura">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fechaFinFac">F Fin Fact:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" class="form-control datepicker" name="fechahfac" id="fechahfac" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Estimada de Despacho">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fecha">Fecha ED:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" bsDaterangepicker class="form-control datepicker" name="fechaestdesp" id="fechaestdesp" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                            <!--
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Estatus Orden de Compraa">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Estatus:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="statusOD" id="statusOD" class="selectpicker form-control statusOD">
                                        @if ($tablashtml['aux_verestado'] =='2' or $tablashtml['aux_verestado'] =='3')
                                            <option value="5" selected>Cerrada</option>
                                        @else
                                            <option value="" selected>Todos</option>
                                            <option value="1">Emitidas</option>
                                            <option value="2">Anuladas</option>
                                            <option value="3">Por Asignar Guia</option>
                                            <option value="4">Por Asignar Factura</option>
                                            <option value="5">Cerrada</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            -->
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="RUT">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="rut">RUT:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <div class="input-group">
                                        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut')}}" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12" data-toggle='tooltip'/>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Vendedor">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Vendedor:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <?php
                                        echo $tablashtml['vendedores'];
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Número Nota de Venta">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="notaventa_id">NotaVenta:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{old('notaventa_id')}}" maxlength="12"/>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Orden de Compra">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="oc_id">OC:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id')}}" maxlength="12"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Número Guia Despacho">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="guiadespacho">NumGuia:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="guiadespacho" id="guiadespacho" class="form-control" value="{{old('guiadespacho')}}" maxlength="12"/>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Número Factura">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="numfactura">NumFac:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="numfactura" id="numfactura" class="form-control" value="{{old('numfactura')}}" maxlength="12"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Nro. Solicitud Despacho">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="despachosol_id">SolDespacho:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="despachosol_id" id="despachosol_id" class="form-control" value="{{old('despachosol_id')}}" maxlength="12"/>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Nro Orden Despacho">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="despachoord_id">OrdDespacho:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="despachoord_id" id="despachoord_id" class="form-control" maxlength="10"/>
                                </div>
                            </div>

                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Area de Producción">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label >Area Prod:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="areaproduccion_id" id="areaproduccion_id" class="selectpicker form-control areaproduccion_id">
                                        <option value="">Todos</option>
                                        @foreach($tablashtml['areaproduccions'] as $areaproduccion)
                                            <option
                                                value="{{$areaproduccion->id}}"
                                                >
                                                {{$areaproduccion->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Tipo de Entrega">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label >T Entrega:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="tipoentrega_id" id="tipoentrega_id" class="selectpicker form-control tipoentrega_id">
                                        <option value="">Todos</option>
                                        @foreach($tablashtml['tipoentregas'] as $tipoentrega)
                                            <option
                                                value="{{$tipoentrega->id}}"
                                                >
                                                {{$tipoentrega->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6 col-md-6" data-toggle='tooltip' title="Comuna">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Comuna:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <?php
                                        echo $tablashtml['comunas'];
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Giro">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Giro:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="giro_id" id="giro_id" class="selectpicker form-control giro_id">
                                        <option value="">Todos</option>
                                        @foreach($tablashtml['giros'] as $giro)
                                            <option
                                                value="{{$giro->id}}"
                                                >
                                                {{$giro->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-2 col-sm-12 text-center" style="padding-left: 0px;padding-right: 0px;">
                        <!--
                        <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                        <button type='button' id='btnpdf' name='btnpdf' class='btn btn-success tooltipsC' title="Reporte PDF"><i class='glyphicon glyphicon-print'></i> Reporte</button>
                        -->
                        <button type="button" id="btnconsultarpage" name="btnconsultarpage" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>
            
            <div class="table-responsive" id="tablaconsulta">
            </div>

            <div class="table-responsive">
                <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-consulta">
                    <thead>
                        <tr>
                            <th class='tooltipsC' title='Orden de Despacho'>OD</th>
                            <th>Fecha</th>
                            <th>Razón Social</th>
                            <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                            <th class='tooltipsC' title='Orden de Compra'>OC</th>
                            <th class='tooltipsC' title='Nota de Venta'>NV</th>
                            <th>Comuna</th>
                            <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                            <th class='tooltipsC' title='Monto Documento'>Monto<br>Documento</th>
                            <th class='tooltipsC' title='Num Guia'>NumGuia</th>
                            <th class='tooltipsC' title='Fecha Guia'>F Guia</th>
                            <th class='tooltipsC' title='Num Factura'>NumFact</th>
                            <th class='tooltipsC' title='Fecha Factura'>F Fact</th>
                            <th class="ocultar">oc_file</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
        
                </table>
            </div>

        </div>
    </div>
</div>
@include('generales.buscarcliente')
@include('generales.modalpdf')
@include('generales.verpdf')
@include('generales.despachoguia')
@include('generales.despachofactura')

@endsection

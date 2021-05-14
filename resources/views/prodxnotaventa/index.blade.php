@extends("theme.$theme.layout")
@section('titulo')
Productos Notas de Venta
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.jss")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/prodxnotaventa/index.js")}}" type="text/javascript"></script>

@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Productos Notas de Venta</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <form action="{{route('prodxnotaventa_exportPdf')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        <div class="col-xs-12 col-md-8 col-sm-8">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" placeholder="DD/MM/AAAA" value="{{old('fechad', $fechaServ['fecha1erDiaMes'] ?? '')}}" required readonly="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Fin">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="dep_fecha">Fecha Fin:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', $fechaServ['fechaAct'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="RUT">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="rut">RUT:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <div class="input-group">
                                            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut')}}" title="F2 Buscar" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12"/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Vendedor">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Vendedor:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="vendedor_id" id="vendedor_id" class="selectpicker form-control vendedor_id">
                                            <option value="">Todos</option>
                                            @foreach($vendedores1 as $vendedor)
                                                <option
                                                    value="{{$vendedor->id}}"
                                                    >
                                                    {{$vendedor->nombre}} {{$vendedor->apellido}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Categoria">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Categoría:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="categoriaprod_id" id="categoriaprod_id" class="form-control selectpicker categoriaprod_id">
                                            <option value="">Todos</option>
                                            @foreach($categoriaprods as $categoriaprod)
                                                <option
                                                    value="{{$categoriaprod->id}}">
                                                    {{$categoriaprod->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6" data-toggle='tooltip' title="Giro">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="giro_id">Giro:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="giro_id" id="giro_id" class="form-control selectpicker giro_id">
                                            <option value="">Todos</option>
                                            @foreach($giros as $giro)
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

                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Area de Producción">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label >Area Prod:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="areaproduccion_id" id="areaproduccion_id" class="selectpicker form-control areaproduccion_id">
                                            @foreach($areaproduccions as $areaproduccion)
                                                <option
                                                    value="{{$areaproduccion->id}}"
                                                    @if ($areaproduccion->id==1)
                                                        {{'selected'}}
                                                    @endif
                                                    >
                                                    {{$areaproduccion->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xs-12 col-md-4 col-sm-4">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="col-xs-12 col-md-9 col-sm-9 text-center">
                                        <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success" data-toggle='tooltip' title="Consultar">Consultar</button>
                                        <button type="submit" class="btn-accion-tabla tooltipsC" title="PDF">
                                            <i class="fa fa-fw fa-file-pdf-o"></i>
                                        </button>
                                        <!--
                                        <button type="button" id="btnpdf1" name="btnpdf1" class="btn-accion-tabla tooltipsC" title="PDF1">
                                            <i class="fa fa-fw fa-file-pdf-o"></i>
                                        </button>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
				<div>
					<legend></legend>
				</div>
			</div>
            <div class="table-responsive" id="tablaconsulta">
            </div>			
            <!--
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="table-responsive" id="tablaconsulta">
                        </div>			
                    </div>
                </div>
            </div>
            -->
        </div>
    </div>
</div>
@include('generales.buscarcliente')
@endsection

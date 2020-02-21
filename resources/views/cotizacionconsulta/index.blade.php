@extends("theme.$theme.layout")
@section('titulo')
Cotización
@endsection

@section("scripts")
    <script type="text/javascript" src="{{asset("assets/js/jquery-barcode.js")}}"></script>
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/cotizacionconsulta/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Colsultar Cotización</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <form action="{{route('exportPdf_cotizacionconsulta')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        <div class="col-xs-12 col-md-8 col-sm-8">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-3 col-sm-3 text-right" style="padding-left: 0px;padding-right: 0px;">
                                        <label for="fecha">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-9 col-sm-9">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-3 col-sm-3 text-right" style="padding-left: 0px;padding-right: 0px;">
                                        <label for="dep_fecha">Fecha Fin:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-9 col-sm-9">
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-3 col-sm-3 text-left">
                                        <label for="rut" data-toggle='tooltip' title="RUT">RUT:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-9 col-sm-9">
                                        <div class="input-group">
                                            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut')}}" title="F2 Buscar" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12"/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-3 col-sm-3 text-left">
                                        <label>Vendedor</label>
                                    </div>
                                    <div class="col-xs-12 col-md-9 col-sm-9">
                                        <select name="vendedor_id" id="vendedor_id" class="selectpicker form-control vendedor_id">
                                            <option value="">Seleccione...</option>
                                            @foreach($vendedores as $vendedor)
                                                <option
                                                    value="{{$vendedor->id}}"
                                                    >
                                                    {{$vendedor->persona->nombre}} {{$vendedor->persona->apellido}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 col-sm-4">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-12 col-sm-12">
                                    <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success">Consultar</button>
                                    <button type="submit" class="btn-accion-tabla tooltipsC" title="Consultar">
                                        <i class="fa fa-fw fa-file-pdf-o"></i>
                                    </button>
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

            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="row" id="tablaconsulta">
                        </div>			
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div>
@include('generales.buscarcliente')
@endsection
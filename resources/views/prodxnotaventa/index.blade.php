@extends("theme.$theme.layout")
@section('titulo')
Productos Notas de Venta
@endsection

@section("scripts")
    <script type="text/javascript" src="{{asset("assets/js/jquery-barcode.js")}}"></script>
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/prodxnotaventa/index.js")}}" type="text/javascript"></script>
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
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <div class="col-xs-12 col-md-3 col-sm-3 text-left">
                                    <label for="fecha">Fecha Ini:</label>
                                </div>
                                <div class="col-xs-12 col-md-9 col-sm-9">
                                    <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <div class="col-xs-12 col-md-3 col-sm-3 text-left">
                                    <label for="dep_fecha">Fecha Fin:</label>
                                </div>
                                <div class="col-xs-12 col-md-9 col-sm-9">
                                    <input type="text" class="form-control datepicker" name="fechah" id="fechah" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <div class="col-xs-12 col-md-3 col-sm-3 text-left">
                                    <label data-toggle='tooltip' title="Categoría">Categoría</label>
                                </div>
                                <div class="col-xs-12 col-md-9 col-sm-9">
                                    <select name="categoriaprod_id" id="categoriaprod_id" class="form-control select2 categoriaprod_id">
                                        <option value="" precio="0">Seleccione...</option>
                                        @foreach($categoriaprods as $categoriaprod)
                                            <option
                                                value="{{$categoriaprod->id}}" precio="{{$categoriaprod->precio}}">
                                                {{$categoriaprod->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <div class="col-xs-12 col-md-3 col-sm-3 text-left">
                                    <label for="giro_id" data-toggle='tooltip' title="Giro">Giro</label>
                                </div>
                                <div class="col-xs-12 col-md-9 col-sm-9">
                                    <select name="giro_id" id="giro_id" class="form-control select2 giro_id">
                                        <option value="">Seleccione...</option>
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
                            <div class="col-xs-12 col-sm-4">
                                <div class="col-xs-12 col-md-3 col-sm-3 text-left">
                                    <label></label>
                                </div>
                                <div class="col-xs-12 col-md-9 col-sm-9 text-center">
                                    <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success">Consultar</button>
                                    <button type="submit" class="btn-accion-tabla tooltipsC" title="PDF">
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
                        <div class="table-responsive" id="tablaconsulta">
                        </div>			
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div>
@include('generales.buscarcliente')
@endsection

@extends("theme.$theme.layout")
@section('titulo')
Informe Materias Primas Precio X Kilo
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/estadisticaventa/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Informe Materias Primas Precio X Kilo</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <form action="{{route('estadisticaventa_exportPdf')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        <div class="col-xs-12 col-md-9 col-sm-12" style="padding-left: 0px;padding-right: 0px;">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" value="{{old('fechad', $fechaServ['fecha1erDiaMes'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Final">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="dep_fecha">Fecha Fin:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', $fechaServ['fechaAct'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="MateriaPrima">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>MateriaPrima:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="matprimdesc" id="matprimdesc" class="selectpicker form-control matprimdesc">
                                            <option value="">Todos</option>
                                            @foreach($materiaprimas as $materiaprima)
                                                <option
                                                    value="{{$materiaprima->matprimdesc}}"
                                                    >
                                                    {{$materiaprima->matprimdesc}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Producto">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Producto:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="producto" id="producto" class="selectpicker form-control producto">
                                            <option value="">Todos</option>
                                            @foreach($productos as $producto)
                                                <option
                                                    value="{{$producto->producto}}"
                                                    >
                                                    {{$producto->descripcion}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12 text-center">
                                <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consulta</button>
                                <button type="submit" class="btn btn-success tooltipsC" title="Reporte PDF">Reporte PDF</button>
                                <button type="button" id="btnconsultarT" name="btnconsultarT" class="btn btn-success tooltipsC" title="Totales Materia Prima">Totales</button>
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

            <div class="row" id="graficos" name="graficos" style="display:none;">

                <div class="col-lg-6">
                <!-- DONUT CHART -->
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h3 class="box-title" id="titulo_grafico" name="titulo_grafico"></h3>
                    
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="col-xs-12 col-sm-12">
                                <div class="table-responsive" id="tablaconsultaG">
                                </div>
                            </div>
                            
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            
                <div class="col-lg-6">
                    <!-- DONUT CHART -->
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h3 class="box-title">Gráfico Pie</h3>
                    
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="col-xs-12 col-sm-12">
                                <div class="col-xs-12 col-sm-12 text-center">
                                    <label id="tituloPie1" name="tituloPie1">Gráfico Números</label>
                                </div>
                                <div class="resultadosPie1 text-center" style="width: 100%;">
                                    <canvas id="graficoPie1"></canvas>
                                </div>
                            </div>
                            
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>

            

            </div>




        </div>
    </div>
</div>
@endsection

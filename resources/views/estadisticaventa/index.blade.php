@extends("theme.$theme.layout")
@section('titulo')
Informe Materias Primas Precio X Kilo
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/estadisticaventa/index.js")}}" type="text/javascript"></script>
@endsection
<style>
    #mdialTamanio{
      width: 85% !important;
    }
</style>

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
                                <!--<button type="button" id="btnconsultarT" name="btnconsultarT" class="btn btn-success tooltipsC" title="Totales Materia Prima">Totales</button>-->
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

            <div class="row">
                <div class="col-md-12">
                  <!-- Custom Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab"  id="tab1" name="tab1">Totales</a></li>
                            <li><a href="#tab_2" data-toggle="tab" id="tab2" name="tab2">Por Materia Prima</a></li>
                            <li><a href="#tab_3" data-toggle="tab" id="tab3" name="tab3">Gráfico</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div class="table-responsive" id="tablaconsultaT">
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_2">
                                <div class="table-responsive" id="tablaconsulta">
                                </div>                                
                            </div>
                            <div class="tab-pane" id="tab_3">
                                <div class="box box-danger">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Gráfico Pie</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="col-xs-8 col-sm-8">
                                            <div class="col-xs-12 col-sm-12 text-center">
                                                <label id="tituloPie1" name="tituloPie1">Gráfico Números</label>
                                            </div>
                                            <div class="resultadosPie1 text-center" style="width: 100%;">
                                                <canvas id="graficoPie1"></canvas>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myDetalleVenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" id="mdialTamanio">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="titulodetalle" name="titulodetalle">Detalle</h3>
            </div>
            <div class="modal-body">
                <div class="table-responsive" id="tabladetalleventa" name="tabladetalleventa">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        
    </div>
</div>

@endsection

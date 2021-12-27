@extends("theme.$theme.layout")
@section('titulo')
Nota de Venta
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/notaventa/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Nota de Venta</h3>
                @if ($aux_statusPant == '0')
                    <div class="box-tools pull-right">
                        <!--<a href="{{route('crear_notaventa')}}" class="btn btn-block btn-success btn-sm" id="btnnuevaNV">-->
                        <a href="#" class="btn btn-block btn-success btn-sm" id="btnnuevaNV">
                            <i class="fa fa-fw fa-plus-circle"></i> Crear Nota Venta
                        </a>
                    </div>                        
                @endif
            </div>
            <div class="box-body">
                @csrf @method("delete")
                <div class="table-responsive">
                    <table class="table display table-striped AllDataTables table-hover table-condensed " id="tabla-data-notaventas">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th class='width30 tooltipsC' title='Numero de Cotizacion'>Cot</th>
                                <th class="width30">Fecha</th>
                                <th>Razon Social</th>
                                <th class="width70">NV</th>
                                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                                <th class='width30 tooltipsC ocultar' title='Aprobar Nota de Venta'>CNV</th>
                                <th class="width30 ocultar">Anular</th>
                                <th class="ocultar">aprobstatus</th>
                                <th class="ocultar">aprobobs</th>
                                <th class="ocultar">contador</th>
                                <th class="ocultar">oc_file</th>
                                <th class="width150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="todo-list1">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
    <div class="modal fade" id="myModalnumcot" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" id="mdialTamanio1">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Número de Cotización</h3>
                </div>
                <div class="modal-body">
                     <div class="row">
                        <div class="form-group col-xs-12 col-sm-4" classorig="form-group col-xs-12 col-sm-4">
                            <label for="cotizacion_idM" class="control-label">Nro. Cotización</label>
                            <div class="input-group">
                                @csrf @method("put")
                                <input type="text" name="cotizacion_idM" id="cotizacion_idM" tipoval='numerico' class="form-control requeridos" required placeholder="Num Cotización"/>
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="btnbuscarcotizacion" name="btnbuscarcotizacion">Buscar</button>
                                    <!--
                                    <a id="btnbuscarcotizacion" name="btnbuscarcotizacion" href="#" class="btn btn-flat" data-toggle='tooltip' title="Buscar">
                                        <i class="fa fa-search"></i>
                                    </a>-->
                                    
                                </span>
                            </div>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group col-xs-12 col-sm-7">
                            <label for="razonsocialM" class="control-label">Razon Social</label>
                            <input type="text" name="razonsocialM" id="razonsocialM" class="form-control" required placeholder="Razon Social" readonly disabled/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btnaceptar" name="btnaceptar" class="btn btn-primary">Aceptar</button>
                </div>
            </div>
            
        </div>
    </div>
    <div class="modal fade" id="myModalBusquedaCot" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
        
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Cotizaciones</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
                    <table class="table display table-striped AllDataTables table-hover table-condensed" id="tabla-data-productos1">
                        <!--table display table-striped AllDataTables table-hover table-condensed-->
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th>Razon Social</th>
                                <th style="display:none;">B</th>
                                <th class="width70">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $aux_nfila = 0; $i = 0;?>
                            @foreach($cotizaciones as $cotizacion)
                                <?php $aux_nfila++; ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td name="cotizacion_idBtd{{$aux_nfila}}" id="cotizacion_idBtd{{$aux_nfila}}">
                                        <a href="#" class="copiar_id" onclick="copiar_numcot({{$cotizacion->id}})"> {{$cotizacion->id}} </a>
                                    </td>
                                    <td name="razonzocialBtd{{$aux_nfila}}" id="razonzocialBtd{{$aux_nfila}}">
                                        <a href="#" class="copiar_id" onclick="copiar_numcot({{$cotizacion->id}})"> {{$cotizacion->razonsocial}} </a>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="descripbloqueo[]" id="descripbloqueo{{$aux_nfila}}" class="form-control" value="{{$cotizacion->descripbloqueo}}" style="display:none;"/>
                                    </td>
                                    <td name="totalBtd{{$aux_nfila}}" id="totalBtd{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($cotizacion->total, 2, '.', ',')}}
                                    </td>
                                </tr>
                                <?php $i++;?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            
        </div>
    </div>
</div>

@include('generales.modalpdf')
@endsection
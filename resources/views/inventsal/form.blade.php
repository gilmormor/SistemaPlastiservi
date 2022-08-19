
<input type="hidden" name="aux_sta" id="aux_sta" value="1">
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">


<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required maxlength="300"/>
    </div>
</div>
<div class="form-group">
    <label for="obs" class="col-lg-3 control-label requerido">Observación</label>
    <div class="col-lg-8">
        <input type="text" name="obs" id="obs" class="form-control" value="{{old('obs', $data->obs ?? '')}}" required maxlength="100"/>
    </div>
</div>
<div class="form-group">
    <label id="lblsucursal_id" name="lblsucursal_id" for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-8">
        <?php
            $sucursal_id = 0;
            if(count($tablas['sucursales']) == 1){
                $sucursal_id = $tablas['sucursales'][0]->id;
            }
        ?>
        <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" data-live-search='true' required>
            <option value=''>Seleccione...</option>
                @foreach($tablas['sucursales'] as $sucursal)
                    <option
                        value="{{$sucursal->id}}"
                        @if (isset($data) and ($data->sucursal_id==$sucursal->id))
                            {{'selected'}}
                        @else 
                            @if(!isset($data) and $sucursal->id==$sucursal_id)
                                {{'selected'}}
                            @endif
                        @endif
                        >
                        {{$sucursal->nombre}}
                    </option>
                @endforeach                    
        </select>
    </div>
</div>

<div class="form-group">
    <label for="invmovtipo_id" class="col-lg-3 control-label requerido">Tipo Mov</label>
    <div class="col-lg-8">
        <select name="invmovtipo_id" id="invmovtipo_id" class="form-control select2" required>
            <option value="">Seleccione...</option>
            @foreach($invmovtipos as $invmovtipo)
                <option
                    value="{{$invmovtipo->id}}"
                    @if (isset($data->invmovtipo_id) and ($data->invmovtipo_id==$invmovtipo->id))
                        {{'selected'}}
                    @endif
                >
                    {{$invmovtipo->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="fechahora" class="col-lg-3 control-label requerido">Fecha</label>
    <div class="col-lg-8">
        <input type="text" name="fechahora" id="fechahora" class="form-control datepicker" value="{{old('fechahora', $data->fechahora ?? '')}}" required readonly/>
    </div>
</div>


<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
    <input type="hidden" name="total" id="total" value="{{old('total', $data->total ?? '')}}"class="form-control" style="text-align:right;" readonly required>
</div>

<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-tools pull-right">
            <a id="botonNewProd" name="botonNewProd" href="#" class="btn btn-block btn-success btn-sm">
                <i class="fa fa-fw fa-plus-circle"></i> Nuevo Producto
            </a>
        </div>                    
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="display:none;" class="width30">ID</th>
                            <th style="display:none;">NotaVentaDetalle_ID</th>
                            <th>Cod</th>
                            <th style="display:none;">Cód Int</th>
                            <th>Nombre</th>
                            <th style="display:none;">UnidadMedida</th>
                            <th>Clase</th>
                            <th>Diam</th>
                            <th style="display:none;">Diametro</th>
                            <th>Esp</th>
                            <th style="display:none;">Espesor</th>
                            <th>Largo</th>
                            <th style="display:none;">Largo</th>
                            <th>Peso</th>
                            <th style="display:none;">Peso</th>
                            <th>TU</th>
                            <th style="display:none;">TUnion</th>
                            <th class='tooltipsC' title='Bodega'>Bodega</th>
                            <th class='tooltipsC' title='Tipo Movimiento'>Tipo Mov</th>
                            <th style="text-align:right">Total Kilos</th>
                            <th style="display:none;">Total Kilos</th>
                            <th style="display:none;">Cant</th>
                            <th style="text-align:right">Cant</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data))
                            <?php $aux_nfila = 0; $i = 0;?>
                            @foreach($data->inventsaldets as $detalle)
                                <?php 
                                    $aux_nfila++;
                                    $aux_cant = $detalle->cant * $detalle->invmovtipo->tipomov;
                                    $aux_cantkg = $detalle->cantkg * $detalle->invmovtipo->tipomov;
                                ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td style="display:none;" name="NVdet_idTD{{$aux_nfila}}" id="NVdet_idTD{{$aux_nfila}}">
                                        {{$detalle->id}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="NVdet_id[]" id="NVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;" name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
                                        <input style="display:none;" type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->producto_id}}"/>
                                    </td>
                                    <td name="producto_idValor{{$aux_nfila}}" id="producto_idValor{{$aux_nfila}}">
                                        {{$detalle->producto_id}}
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                        {{$detalle->producto->nombre}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control"  value="{{$detalle->unidadmedida_id}}" style="display:none;"/>
                                    </td>
                                    <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}">
                                        {{$detalle->producto->claseprod->cla_nombre}}
                                    </td>
                                    <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->producto->diametro}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->diametro}}" style="display:none;"/>
                                    </td>
                                    <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->producto->espesor}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$detalle->espesor}}" style="display:none;"/>
                                        <input type="text" name="ancho[]" id="ancho{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->ancho}}" style="display:none;"/>
                                    </td>
                                    <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->producto->long}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->long}}" style="display:none;"/>
                                    </td>
                                    <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                        {{$detalle->producto->peso}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->peso}}" style="display:none;"/>
                                    </td>
                                    <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}"> 
                                        {{$detalle->producto->tipounion}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->tipounion}}" style="display:none;"/>
                                    </td>
                                    <td name="invbodega_idTXT{{$aux_nfila}}" id="invbodega_idTXT{{$aux_nfila}}">
                                        {{$detalle->invbodega->nombre}}
                                    </td>
                                    <td name="invmovtipo_idTXT{{$aux_nfila}}" id="invmovtipo_idTXT{{$aux_nfila}}">
                                        {{$detalle->invmovtipo->nombre}}
                                    </td>
                                    <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($aux_cantkg, 2, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control subtotalkg" value="{{$aux_cantkg}}" valor="{{$aux_cantkg}}" style="display:none;"/>
                                        <input type="text" name="invbodega_idTD[]" id="invbodega_idTD{{$aux_nfila}}" class="form-control" value="{{$detalle->invbodega_id}}" style="display:none;"/>
                                        <input type="text" name="invmovtipo_idTD[]" id="invmovtipo_idTD{{$aux_nfila}}" class="form-control" value="{{$detalle->invmovtipo_id}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control subtotalcant" value="{{$aux_cant}}" valor="{{$aux_cant}}" style="display:none;"/>
                                    </td>
                                    <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($aux_cant, 2, ',', '.')}}
                                    </td>
                                    <td>
                                        <a href="#" class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro({{$aux_nfila}})">
                                        <i class="fa fa-fw fa-pencil"></i>
                                        </a>
                                        <a href="#" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro({{$aux_nfila}})">
                                        <i class="fa fa-fw fa-trash text-danger"></i></a>
                                    </td>
                                </tr>
                                <?php $i++;?>
                            @endforeach
                            <tr id="trtotal" name="trtotal">
                                <td colspan="10" style="text-align:right"><b>Total</b></td>
                                <td id="tdtotalkg" name="tdtotalkg" style="text-align:right">0,00</td>
                                <td id="tdtotalcant" name="tdtotalcant" style="text-align:right">0,00</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<!--
<div class="file-loading">
    <input id="oc_file" name="oc_file" type="file" multiple>
</div>
-->

@include('generales.inventsalprod')
@include('generales.buscarproducto')

<input type="hidden" name="aux_sta" id="aux_sta" value="1">
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">
<input type="hidden" name="itemAct" id="itemAct" value="">
<select name="turnoselect" id="turnoselect" class="form-control" style="display: none">
    <option value="">Seleccione...</option>
    @foreach($tablas['turnos'] as $turno)
        <option
            value="{{$turno->id}}"
        >{{$turno->nombre}}</option>
    @endforeach
</select>
<select name="pesajecarroselect" id="pesajecarroselect" class="form-control" style="display: none">
    <option value="">Seleccione...</option>
    @if (isset($data))
        @foreach($tablas['pesajecarros'] as $pesajecarro)
            @if ($data->sucursal_id == $pesajecarro->sucursal_id)
                <option
                    value="{{$pesajecarro->id}}"
                    tara = "{{$pesajecarro->tara}}"
                >{{$pesajecarro->nombre}}</option>
            @endif
        @endforeach
    @endif
</select>
<?php
    $selecmultprod = false;
?>
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
                        >{{$sucursal->nombre}}</option>
                @endforeach                    
        </select>
    </div>
</div>

<input type="hidden" name="invmovtipo_id" id="invmovtipo_id" value="{{old('invmovtipo_id', $data->invmovtipo_id ?? '1')}}">

<div class="form-group">
    <label for="createdtemp_at" class="col-lg-3 control-label requerido tooltipsC" title="Fecha creación del registro">Fecha</label>
    <div class="col-lg-8">
        <input type="text" name="createdtemp_at" id="createdtemp_at" class="form-control" value="{{old('createdtemp_at', isset($data->created_at) ? date('d/m/Y', strtotime($data->created_at)) : date('d/m/Y'))}}" required readonly/>
    </div>
</div>

<div class="form-group">
    <label for="fechahora" class="col-lg-3 control-label requerido tooltipsC" title="Fecha de Producción">Fecha Producción</label>
    <div class="col-lg-8">
        <input type="text" name="fechahora" id="fechahora" class="form-control datepicker" value="{{old('fechahora', $data->fechahora ?? '')}}" required readonly/>
    </div>
</div>

<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border" style="padding-left: 3px;padding-right: 3px;">
        <h3 class="box-title">Detalle</h3>
        <div class="box-tools pull-right">
            <a onclick="agregarFila()" id="additem" name="additem" class="btn btn-block btn-success btn-sm">
                <i class="fa fa-fw fa-plus-circle"></i> Nuevo Item
            </a>
        </div>
        <div class="box-body" style="padding-left: 2px;padding-right: 2px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="text-align:center;padding-left: 0px;padding-right: 0px;" class="width30">item</th>
                            <th class="width50 tooltipsC" title="Código Producto">Cod</th>
                            <th class="width200 tooltipsC" title="Nombre Producto">Nombre</th>
                            <!--
                            <th class="width30 tooltipsC" title="Unidad de Medida">UniMed</th>
                            -->
                            <th class="width20 tooltipsC" title="Peso Norma" style="text-align:right;">PN</th>
                            <th class="width50 tooltipsC" title="Linea">Linea</th>
                            <th class="width50 tooltipsC" title="Turno">Turno</th>
                            <th class="width60 tooltipsC" title="Carro">Carro</th>
                            <th class="width30 tooltipsC" title="Peso Carro">Tara</th>
                            <th class="width60" style="text-align:right;">Cant</th>
                            <th class="width70 tooltipsC" title="Peso Balanza" style="text-align:right;">PBalanza</th>
                            <th class="width20 tooltipsC" title="Peso promedio Unitario Balanza" style="text-align:right;">PUB</th>
                            <th class="width70 tooltipsC" title="Peso Total Producto en Balanza" style="text-align:right;">PProducto</th>
                            <th class="width70 tooltipsC" title="Peso Total Norma" style="text-align:right;">PTNor</th>
                            <th class="width70 tooltipsC" title="Diferencia Kg" style="text-align:right;">DifKg</th>
                            <th class="width70 tooltipsC" title="Var %" style="text-align:right;">Var%</th>
                            <th class="width30"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i = 0;
                            $total_tara = 0;
                            $total_cant = 0;
                            $total_pesobaltotal = 0;
                            $total_pesobalprodtotal = 0;
                            $total_pesobalprodunit = 0;
                            $total_PesoTotNorma = 0;
                            $total_DiferenciaKg = 0;
                            $total_DiferenciaPorc = 0;
                        ?>
                        @if (isset($data))
                            @foreach($data->pesajedets as $pesajedet)
                                <?php 
                                    $i++;
                                    $aux_cant = $pesajedet->cant * $pesajedet->invmovtipo->tipomov;
                                    $aux_cantkg = $pesajedet->cantkg * $pesajedet->invmovtipo->tipomov;
                                    $total_tara += $pesajedet->tara;
                                    $total_cant += $pesajedet->cant;
                                    $total_pesobaltotal += $pesajedet->pesobaltotal;
                                    $pesobalprodunit = round(($pesajedet->pesobaltotal - $pesajedet->tara) / $pesajedet->cant,2);
                                    $total_pesobalprodunit += $pesobalprodunit;
                                    $pesobalprodtotal = ($pesajedet->pesobaltotal - $pesajedet->tara);
                                    $total_pesobalprodtotal += $pesobalprodtotal;
                                    $PesoTotNorma = round($pesajedet->cant * $pesajedet->peso,2);
                                    $total_PesoTotNorma += $PesoTotNorma;
                                    $DiferenciaKg = round($pesobalprodtotal - $PesoTotNorma,2);
                                    $total_DiferenciaKg += $DiferenciaKg;
                                    $DiferenciaPorc = round(($DiferenciaKg / $PesoTotNorma) * 100,2);
                                    $total_DiferenciaPorc += $DiferenciaPorc;
                                    $aux_producto_nombre = $pesajedet->producto->nombre . " D:" . $pesajedet->producto->diametro . " C:" . $pesajedet->producto->claseprod->cla_nombre . " L:" . $pesajedet->producto->long . " TU:" . $pesajedet->producto->tipounion;
                                ?>
                                <tr name="fila{{$i}}" id="fila{{$i}}" class="proditems" item="{{$i}}">
                                    <td id="nroitem{{$i}}" name="nroitem{{$i}}" class="nroitem" style="text-align:center;padding-left: 3px;padding-right: 3px;">
                                        {{$i}}
                                    </td>
                                    <td style="text-align:center;padding-left: 3px;padding-right: 3px;" name="producto_idTD{{$i}}" id="producto_idTD{{$i}}">
                                        <input type="text" name="producto_id[]" id="producto_id{{$i}}" onblur="onBlurProducto_id(this)" class="form-control numericop itemrequerido tooltipsC" onkeyup="buscarProdKeyUp(this,event)" value="{{$pesajedet->producto_id}}" valor="{{$pesajedet->producto_id}}" maxlength="4" style="text-align:right;" valor="" title="Codigo Producto (F2 para Buscar)" item="{{$i}}" placeholder="F2 Buscar"/>
                                        <input type="text" name="pesajedet_id[]" id="pesajedet_id{{$i}}" class="form-control" value="{{$pesajedet->id}}" item="{{$i}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:center;padding-left: 3px;padding-right: 3px;" name="producto_nombreTD{{$i}}" id="producto_nombreTD{{$i}}">
                                        <input type="text" name="producto_nombre[]" id="producto_nombre{{$i}}" class="form-control numericop calsubtotalitem tooltipsC" value="{{$aux_producto_nombre}}" item="{{$i}}" title="{{$aux_producto_nombre}}" readonly disabled/>
                                    </td>
                                    <!--
                                    <td name="unidadmedida_nombreTD{{$i}}" id="unidadmedida_nombreTD{{$i}}" valor="">
                                        <input type="text" name="unidadmedida_nombre[]" id="unidadmedida_nombre{{$i}}" class="form-control" value="{{$pesajedet->producto->categoriaprod->unidadmedidafact->nombre}}" readonly disabled/>
                                    </td>
                                    -->
                                    <td name="pesounitnomTD{{$i}}" id="pesounitnomTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="pesounitnom[]" id="pesounitnom{{$i}}" class="form-control" value="{{$pesajedet->pesounitnom}}" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
                                    </td>
                                    <td name="areaproduccionsuclinea_idTD{{$i}}" id="areaproduccionsuclinea_idTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <select name="areaproduccionsuclinea_id[]" id="areaproduccionsuclinea_id{{$i}}" class="form-control areaproduccionsuclinea_id itemrequerido" title="Linea de Producción" item="{{$i}}">
                                            <option value="">Seleccione...</option>
                                            @foreach($pesajedet->producto->categoriaprod->areaproduccion->areaproduccionsucs as $areaproduccionsuc)
                                                @if ($areaproduccionsuc->sucursal_id == $pesajedet->sucursal_id)
                                                    @foreach ($areaproduccionsuc->areaproduccionsuclineas as $areaproduccionsuclinea)
                                                        <option
                                                            value="{{$areaproduccionsuclinea->id}}"
                                                            @if ($pesajedet->areaproduccionsuclinea_id == $areaproduccionsuclinea->id)
                                                                {{'selected'}}
                                                            @endif
                                                        >{{$areaproduccionsuclinea->nombre}}</option>
                                                        
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td name="turno_idTD{{$i}}" id="turno_idTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <select name="turno_id[]" id="turno_id{{$i}}" class="form-control itemrequerido" title="Turno">
                                            <option value="">Seleccione...</option>
                                            @foreach($tablas['turnos'] as $turno)
                                                <option
                                                    value="{{$turno->id}}"
                                                    @if ($pesajedet->turno_id == $turno->id)
                                                        {{'selected'}}
                                                    @endif
                                                >{{$turno->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td name="pesajecarro_idTD{{$i}}" id="pesajecarro_idTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <select name="pesajecarro_id[]" id="pesajecarro_id{{$i}}" class="form-control pesajecarro_id itemrequerido" title="Carro" item="{{$i}}">
                                            <option value="">Seleccione...</option>
                                            @foreach($tablas['pesajecarros'] as $pesajecarro)
                                                @if ($pesajedet->sucursal_id == $pesajecarro->sucursal_id)
                                                    <option
                                                        value="{{$pesajecarro->id}}"
                                                        @if ($pesajedet->pesajecarro_id == $pesajecarro->id)
                                                            {{'selected'}}
                                                        @endif
                                                    >{{$pesajecarro->nombre}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td name="taraTD{{$i}}" id="taraTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="tara[]" id="tara{{$i}}" class="form-control itemrequerido subtotaltara" value="{{$pesajedet->tara}}" style="text-align:right;padding-left: 0px;padding-right: 2px;" title="Tara" readonly disabled/>
                                    </td>
                                    <td name="cantTD{{$i}}" id="cantTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="cant[]" id="cant{{$i}}" class="form-control numericop calsubtotalitem itemrequerido subtotalcant" value="{{$pesajedet->cant}}" item="{{$i}}" title="Cantidad Producto" style="text-align:right;padding-left: 0px;padding-right: 2px;"/>
                                    </td>
                                    <td name="pesobaltotalTD{{$i}}" id="pesobaltotalTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="pesobaltotal[]" id="pesobaltotal{{$i}}" class="form-control numericop calsubtotalitem itemrequerido subtotalpesobaltotal" value="{{$pesajedet->pesobaltotal}}" title="Peso balanza Total" item="{{$i}}"  style="text-align:right;padding-left: 0px;padding-right: 2px;"/>
                                    </td>
                                    <td name="pesobalprodunitTD{{$i}}" id="pesobalprodunitTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="pesobalprodunit[]" id="pesobalprodunit{{$i}}" class="form-control subtotalpesobalprodunit" value="{{number_format($pesobalprodunit, 2, '.', ',')}}" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
                                    </td>
                                    <td name="pesobalprodtotalTD{{$i}}" id="pesobalprodtotalTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="pesobalprodtotal[]" id="pesobalprodtotal{{$i}}" class="form-control subtotalpesobalprodtotal" value="{{$pesobalprodtotal}}" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
                                    </td>
                                    <td name="PesoTotNormaTD{{$i}}" id="PesoTotNormaTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="PesoTotNorma[]" id="PesoTotNorma{{$i}}" class="form-control subtotalPesoTotNorma" value="{{$PesoTotNorma}}" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
                                    </td>
                                    <td name="DiferenciaKgTD{{$i}}" id="DiferenciaKgTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="DiferenciaKg[]" id="DiferenciaKg{{$i}}" class="form-control subtotalDiferenciaKg" value="{{$DiferenciaKg}}" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
                                    </td>
                                    <td name="DiferenciaPorcTD{{$i}}" id="DiferenciaPorcTD{{$i}}" valor="" style="padding-left: 3px;padding-right: 3px;">
                                        <input type="text" name="DiferenciaPorc[]" id="DiferenciaPorc{{$i}}" class="form-control subtotalDiferenciaPorc" value="{{$DiferenciaPorc}}" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <a onclick="delitem({{$i}})" class="btn-accion-tabla tooltipsC" title="Precio Unitario" id="delitem{{$i}}" name="delitem{{$i}}" style="padding-left: 0px;">
                                            <i class="fa fa-fw fa-trash text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot style="{{$total_pesobalprodtotal > 0 ? "" : "display:none;"}}" id="foottotal" name="foottotal">
                        <div id="foot">
                            <tr id="trtotal_tara" name="trtotal_tara">
                                <th colspan="7" style="text-align:right"><b>Total</b></th>
                                <th id="tdtotal_tara" name="tdtotal_tara" style="text-align:right">{{number_format($total_tara, 2, ',', '.')}}</th>
                                <th id="tdtotal_cant" name="tdtotal_cant" style="text-align:right">{{number_format($total_cant, 2, ',', '.')}}</th>
                                <th id="tdtotal_pesobaltotal" name="tdtotal_pesobaltotal" style="text-align:right">{{number_format($total_pesobaltotal, 2, ',', '.')}}</th>
                                <th id="tdtotal_pesobalprodunit" name="tdtotal_pesobalprodunit" style="text-align:right">{{number_format($total_pesobalprodunit, 2, ',', '.')}}</th>
                                <th id="tdtotal_pesobalprodtotal" name="tdtotal_pesobalprodtotal" style="text-align:right">{{number_format($total_pesobalprodtotal, 2, ',', '.')}}</th>
                                <th id="tdtotal_PesoTotNorma" name="tdtotal_PesoTotNorma" style="text-align:right">{{number_format($total_PesoTotNorma, 2, ',', '.')}}</th>
                                <th id="tdtotal_DiferenciaKg" name="tdtotal_DiferenciaKg" style="text-align:right">{{number_format($total_DiferenciaKg, 2, ',', '.')}}</th>
                                <th id="tdtotal_DiferenciaPorc" name="tdtotal_DiferenciaPorc" style="text-align:right">{{number_format($total_DiferenciaPorc, 2, ',', '.')}}</th>
                            </tr>                        
                        </div>
                    </tfoot>
                </table>
                <div class="form-group col-xs-4 col-sm-4" style="display:none;">
                    <label name="lblitemcompletos" id="lblitemcompletos" for="itemcompletos" class="control-label requerido" data-toggle='tooltip' title="Complete valores item">Complete valores item 1</label>
                    <input type="hidden" name="itemcompletos" id="itemcompletos" value="" class="form-control" style="text-align:right;" readonly required>
                </div>
                <div class="form-group col-xs-4 col-sm-4" style="display:none;">
                    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
                    <input type="hidden" name="total" id="total" value="{{old('total', $total_pesobalprodtotal ?? '')}}"class="form-control" style="text-align:right;" readonly required>
                </div>
                <input type="text" name="ids" id="ids" value="{{$i}}" style="display: none">
            </div>
        </div>
    </div>
</div>
<!--
<div class="file-loading">
    <input id="oc_file" name="oc_file" type="file" multiple>
</div>
-->

@include('generales.buscarproductobdtemp')

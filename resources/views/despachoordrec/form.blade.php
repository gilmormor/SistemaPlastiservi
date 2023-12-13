<?php
    use Illuminate\Http\Request;
    use App\Models\DespachoOrdRecDet;
?>
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">
<input type="hidden" name="despachoord_id" id="despachoord_id" value="{{$data->id}}">
<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{$empresa->iva}}">

<input type="hidden" name="neto" id="neto" value="{{old('neto', $data->neto ?? '')}}">
<input type="hidden" name="iva" id="iva" value="{{old('iva', $data->iva ?? '')}}">
<input type="hidden" name="total" id="total" value="{{old('total', $data->total ?? '')}}">
<input type="hidden" name="imagen" id="imagen" value="{{isset($despachoordrec->documento_file) ? $despachoordrec->documento_file : "" }}">

<div class="row">
    <div class="col-xs-12 col-sm-12">
        @include('despachoordrec.datosform')
    </div>    
</div>

<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="display:none;" class="width30">ID</th>
                            <th style="display:none;">NotaVentaDetalle_ID</th>
                            <th style="display:none;">cotizacion_ID</th>
                            <th style="display:none;">Codigo Producto</th>
                            <th style="display:none;">CódInterno</th>
                            <th style="display:none;">Cant</th>
                            <th>Cant</th>
                            <!--<th>Desp</th>-->
                            <th>CantRec</th>
                            <th>Saldo</th>
                            <th class='tooltipsC' title='Marcar todo' style="text-align:center;display:none;">
                                <div class='checkbox'>
                                    <label style='font-size: 1.2em'>
                                        <input type='checkbox' id='marcarTodo' name='marcarTodo'>
                                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                    </label>
                                </div>
                            </th>
                            <th class="width90">OrdDesp</th>
                            <th>Bodegas</th>
                            <th style="display:none;">UnidadMedida</th>
                            <th>Nombre</th>
                            <th>Diam</th>
                            <th>Clase</th>
                            <th style="display:none;">Diametro</th>
                            <th>Esp</th>
                            <th style="display:none;">Espesor</th>
                            <th>Largo</th>
                            <th style="display:none;">Largo</th>
                            <th>Peso</th>
                            <th style="display:none;">Peso</th>
                            <th>Kilos</th>
                            <th style="display:none;">Total Kilos</th>
                            <th>TU</th>
                            <th style="display:none;">TUnion</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th>PUnit</th>
                            <th style="display:none;">Precio Neto Unit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th>Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                            <th style="display:none;">Sub Total Neto Sin Formato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($aux_sta==2 or $aux_sta==3)
                            <?php 
                                $aux_nfila = 0; $i = 0; //dd($detalles);
                                $aux_cantrecTotal = 0;
                            ?>
                            @foreach($detalles as $detalle)
                                <?php
                                    //dd($detalles);
                                    $sql = "SELECT despachoorddet_id,cantrec
                                            FROM vista_sumrecorddespdet
                                            WHERE despachoorddet_id=$detalle->id";
                                    $datasuma = DB::select($sql);
                                    $peso = $detalle->notaventadetalle->totalkilos/$detalle->notaventadetalle->cant;
                                    //dd($despachoordrecdets);
                                    if(empty($datasuma)){
                                        $sumacantorddesprec= 0;
                                    }else{
                                        $sumacantorddesprec= $datasuma[0]->cantrec;
                                    }
                                    $aux_cantrec = 0;
                                    $aux_subtotalkg = 0.00;
                                    $aux_totalkilosTD = 0.00;
                                    $aux_subtotalCFTD = 0.00;
                                    $aux_checked = "";
                                    $aux_despachoordrecdet_id = "";
                                    if($aux_sta==3){
                                    //if(isset($despachoordrecdets)){
                                        foreach($despachoordrecdets as $despachoordrecdet){
                                            if($detalle->id == $despachoordrecdet->despachoorddet_id){
                                                //dd($sumacantorddesprec);
                                                //$sumacantorddesprec = $sumacantorddesprec - $despachoordrecdet->cantrec;
                                                $sumacantorddesprec = $sumacantorddesprec - $despachoordrecdet->cantrec;
                                                $aux_cantrec = $despachoordrecdet->cantrec;
                                                $aux_subtotalkg = $despachoordrecdet->cantrec * $peso;
                                                $aux_subtotalCFTD = $despachoordrecdet->cantrec * $detalle->notaventadetalle->preciounit;
                                                $aux_cantrecTotal += $despachoordrecdet->cantrec;
                                                $aux_checked = "checked";
                                                $aux_despachoordrecdet_id = $despachoordrecdet->id;
                                            }
                                        }
                                    }
                                    $sumacantorddesprec = DespachoOrdRecDet::join('despachoordrec','despachoordrecdet.despachoordrec_id','despachoordrec.id')
                                                            ->where('despachoorddet_id','=',$detalle->id)
                                                            ->whereNull('despachoordrec.anulada')
                                                            ->sum('cantrec') - $aux_cantrec;
                                    if($detalle->cantdesp >= $sumacantorddesprec){
                                        $aux_nfila++;
                                        $aux_saldo = $detalle->cantdesp - $sumacantorddesprec - $aux_cantrec;
                                        $invbodegaproductos = $detalle->notaventadetalle->producto->invbodegaproductos;
                                        //dd($invbodegaproductos);
                                ?>
                                        <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                            <td style="display:none;" name="despachosoldet_id{{$aux_nfila}}" id="despachosoldet_id{{$aux_nfila}}">
                                                {{$detalle->id}}
                                            </td>
                                            <td style="display:none;">
                                                <input type="text" name="NVdet_id[]" id="NVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                                <input type="text" name="despachoorddet_id[]" id="despachoorddet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                                <input type="text" name="notaventadetalle_id[]" id="notaventadetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle_id}}" style="display:none;"/>
                                                <input type="text" name="despachoordrecdet_id[]" id="despachoordrecdet_id{{$aux_nfila}}" class="form-control" value="{{$aux_despachoordrecdet_id}}" style="display:none;"/>
                                            </td>
                                            <td style="display:none;">
                                                <input type="text" name="cotizaciondetalle_id[]" id="cotizaciondetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->cotizaciondetalle_id}}" style="display:none;"/>
                                            </td>
                                            <td name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}" style="display:none;">
                                                <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto_id}}" style="display:none;"/>
                                            </td>
                                            <td style="display:none;">
                                                <input type="text" name="codintprod[]" id="codintprod{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->codintprod}}" style="display:none;"/>
                                            </td>
                                            <td style="text-align:right;display:none;">
                                                <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->cantdesp}}" style="display:none;"/>
                                            </td>
                                            <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                                {{$detalle->cantdesp}}
                                            </td>
                                            <td name="cantorddespF{{$aux_nfila}}" id="cantorddespF{{$aux_nfila}}" style="text-align:right">
                                                {{$sumacantorddesprec}}
                                            </td>
                                            <td name="saldocantOrigF{{$aux_nfila}}" id="saldocantOrigF{{$aux_nfila}}" style="text-align:right;display:none;">
                                                {{$aux_saldo}}
                                            </td>
                                            <td name="saldocantF{{$aux_nfila}}" id="saldocantF{{$aux_nfila}}" style="text-align:right">
                                                {{$aux_saldo}}
                                            </td>
                                            <td class='tooltipsC' style='text-align:center;display:none;' class='tooltipsC' title='Marcar'>
                                                <div class='checkbox'>
                                                    <label style='font-size: 1.2em'>
                                                        <input type="checkbox" class="checkllenarCantOrd" id="llenarCantOrd{{$aux_nfila}}" name="llenarCantOrd{{$aux_nfila}}" onclick="llenarCantOrd({{$aux_nfila}})" {{$aux_checked}}>
                                                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td name="cantordF{{$aux_nfila}}" id="cantordF{{$aux_nfila}}" style="text-align:right">
                                                <input type="text" name="cantord[]" id="cantord{{$aux_nfila}}" class="form-control numerico cantordsum" onkeyup="actSaldo({{$aux_nfila}})" value="{{$aux_cantrec}}" style="text-align:right;" readonly/>
                                            </td>
                                            <td name="bodegasTB{{$aux_nfila}}" id="bodegasTB{{$aux_nfila}}" style="text-align:right;">
                                                <table class="table" id="tabla-bodrec" style="font-size:14px">
                                                    <tbody>
                                                        @foreach($invbodegaproductos as $invbodegaproducto)
                                                            <?php
                                                                $request = new Request();
                                                                $request["producto_id"] = $invbodegaproducto->producto_id;
                                                                $request["invbodega_id"] = $invbodegaproducto->invbodega_id;
                                                                $request["tipo"] = 2;
                                                                $existencia = $invbodegaproducto::existencia($request);
                                                                //$existencia = $invbodegaproductoobj->consexistencia($request);
                                                                if ($invbodegaproducto->invbodega->sucursal_id == 1) {
                                                                    $colorSuc = "#26ff00";
                                                                }
                                                                if ($invbodegaproducto->invbodega->sucursal_id == 2) {
                                                                    $colorSuc = "#1500ff";
                                                                }
                                                                if ($invbodegaproducto->invbodega->sucursal_id == 3) {
                                                                    $colorSuc = "#00c3ff";
                                                                }

                                                            ?>
                                                            @if (($invbodegaproducto->invbodega->tipo == 2)) <!--SOLO MUESTRA LAS BODEGAS TIPO 1, LAS TIPO 2 NO LAS MUESTRA YA QUE ES BODEGA DE DESPACHO -->
                                                                <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}">
                                                                    <td name="invbodegaproducto_idTD{{$invbodegaproducto->id}}" id="invbodegaproducto_idTD{{$invbodegaproducto->id}}" style="text-align:left;display:none;">
                                                                        <input type="text" name="invbodegaproducto_producto_id[]" id="invbodegaproducto_producto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$detalle->notaventadetalle->producto_id}}" style="display:none;"/>
                                                                        <input type="text" name="invbodegaproducto_id[]" id="invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$invbodegaproducto->id}}" style="display:none;"/>
                                                                        <input type="text" name="invbodegaproductoNVdet_id[]" id="invbodegaproductoNVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                                                        {{$invbodegaproducto->id}}
                                                                    </td>
                                                                    <td style="text-align:left;padding-right: 0px;padding-left: 2px;padding-top: 4px;padding-bottom: 4px;" class='tooltipsC' title='Bodega: {{$invbodegaproducto->invbodega->nombre}} {{$invbodegaproducto->invbodega->sucursal->nombre}}'>
                                                                        <div class="centrarhorizontal">
                                                                            <p name="nomabreTD{{$invbodegaproducto->id}}" id="nomabreTD{{$invbodegaproducto->id}}" style="color:{{$colorSuc}};font-size: 11px;margin-bottom: 0px">{{$invbodegaproducto->invbodega->nomabre}} {{$invbodegaproducto->invbodega->sucursal->abrev}}</p>
                                                                        </div>
                                                                    </td>
                                                                    <td style="text-align:right;padding-left: 0px;padding-right: 0px;padding-top: 4px;padding-bottom: 4px;"  class='tooltipsC' title='Stock disponible'>
                                                                        <div name="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" id="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="centrarhorizontal">
                                                                            {{$existencia["stock"]["cant"]}}
                                                                        </div>        
                                                                    </td>
                                                                    <td  class="width90 tooltipsC" name="cantorddespFInput{{$invbodegaproducto->id}}" id="cantorddespFInput{{$invbodegaproducto->id}}" style="text-align:right;padding-top: 4px;padding-bottom: 4px;" title='Cant a despachar'>
                                                                        <?php $bandera = false; ?>
                                                                        @if(isset($despachoordrecdets))
                                                                            @foreach($despachoordrecdets as $despachoordrecdet)
                                                                                @if($detalle->id == $despachoordrecdet->despachoorddet_id)
                                                                                    @foreach($despachoordrecdet->despachoordrecdet_invbodegaproductos as $despachoordrecdet_invbodegaproducto)
                                                                                        @if ($despachoordrecdet_invbodegaproducto->invbodegaproducto_id == $invbodegaproducto->id)
                                                                                            <?php $bandera = true; ?>
                                                                                            <input type="text" name="despachoordrecdet_invbodegaproducto_id[]" id="despachoordrecdet_invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control numerico" onkeyup="sumbodrec({{$aux_nfila}},{{$invbodegaproducto->id}})" style="text-align:right;display:none;" value="{{($despachoordrecdet_invbodegaproducto->id)}}"/>
                                                                                            <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control numerico bodrec{{$aux_nfila}}" onkeyup="sumbodrec({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}')" style="text-align:right;" value="{{($despachoordrecdet_invbodegaproducto->cant)}}"/>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                        @if($bandera == false)
                                                                            <input type="text" name="despachoordrecdet_invbodegaproducto_id[]" id="despachoordrecdet_invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control numerico" onkeyup="sumbodrec({{$aux_nfila}},{{$invbodegaproducto->id}})" style="text-align:right;display:none;" value=""/>
                                                                            <input type="text" name="invcant[]" id="invcant{{$invbodegaproducto->id}}" class="form-control numerico bodrec{{$aux_nfila}}" onkeyup="sumbodrec({{$aux_nfila}},{{$invbodegaproducto->id}})" style="text-align:right;"/>
                                                                        @endif
                                                                    </td>
                                                                </tr>                                                        
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td name="cantorddespinputF{{$aux_nfila}}" id="cantorddespinputF{{$aux_nfila}}" style="text-align:right;display:none;">
                                                <input type="text" name="cantorddesp[]" id="cantorddesp{{$aux_nfila}}" class="form-control" value="{{$aux_cantrec}}" style="text-align:right;"/>
                                            </td>
                                            <td style="display:none;">
                                                <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="4" style="display:none;"/>
                                            </td>
                                            <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                                {{$detalle->notaventadetalle->producto->nombre}}
                                            </td>
                                            <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right">
                                                {{$detalle->notaventadetalle->producto->diametro}}
                                            </td>
                                            <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}">
                                                {{$detalle->notaventadetalle->producto->claseprod->cla_nombre}}
                                            </td>
                                            <td style="display:none;">
                                                <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->diametro}}" style="display:none;"/>
                                            </td>
                                            <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right">
                                                {{$detalle->notaventadetalle->producto->espesor}}
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->espesor}}" style="display:none;"/>
                                            </td>
                                            <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right">
                                                {{$detalle->notaventadetalle->producto->long}}
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->long}}" style="display:none;"/>
                                            </td>
                                            <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                                {{$peso}}
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$peso}}" style="display:none;"/>
                                            </td>
                                            <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right" class="subtotalkg" valor="{{$aux_subtotalkg}}">
                                                {{number_format($aux_subtotalkg, 2, ',', '.')}}
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$aux_subtotalkg}}" style="display:none;"/>
                                            </td>
                                            <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}"> 
                                                {{$detalle->notaventadetalle->producto->tipounion}}
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->tipounion}}" style="display:none;"/>
                                            </td>
                                            <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                                <?php $aux_descPorc = $detalle->notaventadetalle->descuento * 100; ?>
                                                {{$aux_descPorc}}%
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->descuento}}" style="display:none;"/>
                                            </td>
                                            <td style="text-align:right;display:none;">
                                                <?php $aux_descVal = 1 - $detalle->notaventadetalle->descuento; ?>
                                                <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                            </td>
                                            <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right;"> 
                                                {{number_format($detalle->notaventadetalle->preciounit, 3, ',', '.')}}
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->preciounit}}" style="display:none;"/>
                                            </td>
                                            <td style="display:none;" name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                                {{number_format($detalle->notaventadetalle->precioxkilo, 3, ',', '.')}}
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->precioxkilo}}" style="display:none;"/>
                                            </td>
                                            <td style="text-align:right;display:none;"> 
                                                <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->precioxkiloreal}}" style="display:none;"/>
                                            </td>
                                            <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCFTD" style="text-align:right"> 
                                                {{number_format($aux_subtotalCFTD, 0, ',', '.')}}
                                            </td>
                                            <td class="subtotalCF" style="text-align:right;display:none;"> 
                                                <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$aux_subtotalCFTD}}" style="display:none;"/>
                                            </td>
                                            <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                                {{$aux_subtotalCFTD}}
                                            </td>
                                        </tr>
                                <?php $i++;
                                    }
                                ?>
                            @endforeach
                            <tr id="trneto" name="trneto">
                                <td colspan="3" style="text-align:right">
                                    <b>Total Unidades:</b>
                                </td>
                                <td style="text-align:right">
                                    <div class="form-group col-xs-12 col-sm-12" style="margin-bottom: 0px;padding-left: 0px;padding-right: 0px;">
                                        <input type="text" name="cantordTotal" id="cantordTotal" class="form-control" style="text-align:right;" value="{{$aux_cantrecTotal}}" readonly required/>
                                    </div>
                                </td>
                                <td colspan="7" style="text-align:right"><b>Total Kg</b></td>
                                <td id="totalkg" name="totalkg" style="text-align:right">0,00</td>
                                <td colspan="2" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="14" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                <td id="tdiva" name="tdiva" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <td colspan="14" style="text-align:right"><b>Total</b></td>
                                <td id="tdtotal" name="tdtotal" style="text-align:right">0,00</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

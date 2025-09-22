<?php
    use Illuminate\Http\Request;
    use App\Models\InvBodegaProducto;
    use App\Models\Producto;
?>
<input type="hidden" name="updatednum_at" id="updatednum_at" value="{{old('updatednum_at', isset($ot) ? strtotime($ot->updated_at) : strtotime($notaventa->updated_at))}}">
<input type="hidden" name="notaventa_id" id="notaventa_id" value="{{$notaventa->id}}">
<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{$empresa->iva}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $ot->cliente_id ?? $notaventa->cliente_id ?? '')}}">
<input type="hidden" name="comuna_id" id="comuna_id" value="{{old('comuna_id', $notaventa->comuna_id ?? '')}}">

<input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $ot->vendedor_id ?? $notaventa->vendedor_id ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">

<input type="hidden" name="neto" id="neto" value="{{old('neto', $ot->neto ?? $notaventa->neto ?? '')}}">
<input type="hidden" name="iva" id="iva" value="{{old('iva', $ot->iva ?? $notaventa->iva ?? '')}}">
<input type="hidden" name="total" id="total" value="{{old('total', $ot->total ?? $notaventa->total ?? '')}}">
<input type="hidden" name="imagen" id="imagen" value="{{old('imagen', $ot->oc_file ?? $notaventa->oc_file ?? '')}}">
<input type="hidden" name="oc_file" id="oc_file" value="{{old('oc_file', $ot->oc_file ?? $notaventa->oc_file ?? '')}}">
<input type="hidden" name="aux_obs" id="aux_obs" value="{{old('aux_obs', $ot->obs ?? '')}}">

<?php
    $disabledReadOnly = "";
    $disabledcliente = "";
    $enableCamposCot = ""; //Este campo lo cambio a disbles si llegara a necesitar desactivar los campos marcados con esta variable
    //Si la pantalla es de aprobacion de Cotizacion desactiva todos input
    //$aux_statusPant=='0', Pantalla normal CRUD de Cotizacion
    //$aux_statusPant=='1', Aprobar o rechazar cotización. Y colocar una observacion
    if($aux_sta==3){
        $disabledReadOnly = ' disabled ';
    }
    $disabledReadOnly = " readonly";
    $aux_concot = false;
    $disabledcliente = " disabled";

?>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="notaventa_id" class="control-label requerido" data-toggle='tooltip' title="Id Nota Venta">NotVenta</label>
                            <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{$notaventa->id ?? ''}}" required disabled/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $ot->cliente->rut ?? $notaventa->cliente->rut ??'')}}" maxlength="12" required {{$disabledReadOnly}} {{$disabledcliente}}/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $ot->cliente->razonsocial ?? $notaventa->cliente->razonsocial ?? '')}}" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="comuna_idD" class="control-label requerido">Comuna</label>
                            <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="{{old('comuna_nombre', $notaventa->comuna->nombre ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="fechahora" class="control-label">Fecha</label>
                            <input type="text" name="fechahora" id="fechahora" class="form-control" value="{{old('fechahora', $fecha ?? '')}}" style="padding-left: 0px;padding-right: 0px;" required readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-5">
                            <label for="direccion" class="control-label">Dirección Princ</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $ot->cliente->direccion ?? $notaventa->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="telefono" class="control-label requerido">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $ot->cliente->telefono ?? $notaventa->cliente->telefono ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="email" class="control-label requerido">Email</label>
                            <input type="text" name="email" id="email" class="form-control" value="{{old('email', $ot->cliente->email ?? $notaventa->cliente->email ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label id="lblsucursal_id" name="lblsucursal_id" for="sucursal_id" class="control-label requerido" data-toggle='tooltip' title="Sucursal de despacho">Sucursal Despacho</label>
                            @if (isset($ot->sucursal_id) or isset($notaventa->sucursal_id))
                                <input type="text" name="sucursal_nombre" id="sucursal_nombre" class="form-control" value="{{old('sucursal_nombre', $ot->sucursal->nombre ?? $notaventa->sucursal->nombre ?? '')}}" required readonly/>
                                <input type="text" name="sucursal_id" id="sucursal_id" class="form-control" value="{{old('sucursal_id', $ot->sucursal_id ?? $notaventa->sucursal_id ?? '')}}" style="display:none;" required readonly/>
                            @else
                                <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" data-live-search='true' required>
                                    <option value=''>Seleccione...</option>
                                        @foreach($tablas['sucursales'] as $sucursal)
                                            <option
                                                value="{{$sucursal->id}}"
                                                >{{$sucursal->nombre}}</option>
                                        @endforeach                    
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="vendedor_idD" class="control-label requerido">Vendedor</label>
                            @if (isset($ot->vendedor_id) or isset($notaventa->vendedor_id))
                                <input type="text" name="vendedor_nombre" id="vendedor_nombre" class="form-control" value="{{old('vendedor_nombre', $notaventa->vendedor->persona->nombre . " " . $notaventa->vendedor->persona->apellido ?? $ot->vendedor->persona->nombre . " " . $ot->vendedor->persona->apellido ?? '')}}" required readonly/>
                            @else
                                <select name="vendedor_idD" id="vendedor_idD" class="form-control select2 vendedor_idD" required readonly disabled>
                                    <option value="">Seleccione...</option>
                                    @foreach($vendedores1 as $vendedor)
                                        <option
                                            value="{{$vendedor->id}}"
                                            @if (($aux_sta==1) and ($vendedor_id==$vendedor->id))
                                                {{'selected'}}
                                            @endif
                                            @if (($aux_sta==2 or $aux_sta==3) and (($ot->vendedor_id ? $ot->vendedor_id : $notaventa->vendedor_id) == $vendedor->id))
                                                {{'selected'}}
                                            @endif
                                            >
                                            {{$vendedor->nombre}} {{$vendedor->apellido}}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="oc_id" class="control-label">Orden Compra</label>
                            <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id', $ot->otnotaventa->notaventa->oc_id ?? $notaventa->oc_id ?? '')}}" readonly>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazoentrega" class="control-label requerido">Plazo Ent.</label>
                            <input type="text" name="plazoentrega" id="plazoentrega" class="form-control pull-right"  value="{{old('plazoentrega', date("d/m/Y", strtotime($notaventa->plazoentrega)))}}" readonly required {{$enableCamposCot}}>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fechaestdesp" class="control-label requerido" data-toggle='tooltip' title="Fecha estimada de Despacho">Fec Est Despacho</label>
                            <input type="text" name="fechaestdesp" id="fechaestdesp" class="form-control pull-right" value="{{old('fechaestdesp', isset($ot) ? $ot->fechaestdesp : $notaventa->fechaestdesp)}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="obs" class="control-label">Observaciones</label>
                            {{-- <input type="text" name="obs" id="obs" class="form-control" value="{{old('obs', $data->observacion ?? '')}}" placeholder="Observaciones" {{$enableCamposCot}}/> --}}
                            <textarea class="form-control" name="obs" id="obs" value="{{old('obs', $ot->observacion ?? $notaventa->observacion ?? '')}}" maxlength="100" placeholder="Observaciones" {{$enableCamposCot}}></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                            <th style="text-align:center">Fab</th>
                            <th style="text-align:center;" class="tooltipsC" title="Código Producto">Cod</th>
                            <th style="width: 50px;text-align:center;">Cant</th>
                            <th>Desp</th>
                            <th>Solid</th>
                            <th>Saldo</th>
                            <th title='Unidad de Medida'>UM</th>
                            <th class="width150">Bodegas/Stock</th>
                            <th>Nombre</th>
                            <th style="text-align:right">Peso</th>
                            <th style="display:none;">Peso</th>
                            <th style="text-align:right">Kilos</th>
                            <th style="display:none;">Total Kilos</th>
                            <th style="text-align:right" class='tooltipsC' title='Precio por Kilo'>PxK</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th style="text-align:right">PUnit</th>
                            <th style="display:none;">Precio Neto Unit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th style="text-align:right">Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                            <th style="display:none;">Sub Total Neto Sin Formato</th>
                            <th style="text-align:center">Obs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($aux_sta==2 or $aux_sta==3)
                            <?php 
                                $aux_nfila = 0;
                                $i = 0;
                                $aux_canttotal = 0;
                                $aux_pesototal = 0;
                                $aux_total = 0;
                            ?>
                            @foreach($detalles as $detalle)
                                <?php 
                                    $aux_cant = $detalle->cant;
                                    $aux_canttotal += $detalle->cant;
                                    $notaventadetalleext = null;
                                    if($detalle->notaventadetalleext){
                                        $notaventadetalleext = $detalle->notaventadetalleext;
                                        $aux_cant = $detalle->cant + $notaventadetalleext->cantext;
                                    }

                                    /*************************/
                                    //SUMA TOTAL SOLICITADO
                                    /*************************/
                                    $sql = "SELECT cantsoldesp
                                            FROM vista_sumsoldespdet
                                            WHERE notaventadetalle_id=$detalle->id";
                                    $datasuma = DB::select($sql);
                                    if(empty($datasuma)){
                                        $sumacantsoldesp= 0;
                                    }else{
                                        $sumacantsoldesp= $datasuma[0]->cantsoldesp;
                                    }
                                    /*************************/
                                    //SUMA TOTAL DESPACHADO
                                    /*************************/
                                    $sql = "SELECT cantdesp
                                        FROM vista_sumorddespxnvdetid
                                        WHERE notaventadetalle_id=$detalle->id";
                                    $datasumadesp = DB::select($sql);
                                    if(empty($datasumadesp)){
                                        $sumacantdesp= 0;
                                    }else{
                                        $sumacantdesp= $datasumadesp[0]->cantdesp;
                                    }
                                    /*************************/

                                    $peso = 0;
                                    if(isset($detalle->totalkilos)){
                                        $peso = round($detalle->totalkilos/$aux_cant,3);
                                        $aux_totalkilos = $detalle->totalkilos;
                                    }else{
                                        $aux_totalkilos = $detalle->kg;
                                    }
                                        
                                    if(isset($detalle->peso) and ($detalle->peso > 0)){
                                        $peso = $detalle->peso;
                                    }else{
                                        if(isset($detalle->otdetnvdet->notaventadetalle) and ($detalle->otdetnvdet->notaventadetalle->peso > 0)){
                                            $peso = $detalle->otdetnvdet->notaventadetalle->peso;
                                        }
                                    }
                                    $pesounit = 0;
                                    if(isset($detalle->producto->acuerdotecnico)){
                                        $pesounit = pesounitat($detalle->producto->acuerdotecnico);
                                    }else{
                                        if(isset($detalle->otdetnvdet) and ($detalle->otdetnvdet->notaventadetalle->totalkilos > 0)){
                                            $notaventadetalle = $detalle->otdetnvdet->notaventadetalle;
                                            $pesounit = ($notaventadetalle->totalkilos / $notaventadetalle->cant);
                                        }                                        
                                    }
                                    
                                    
                                    $aux_pesototal += $aux_totalkilos;

                                    if($aux_cant > $sumacantsoldesp){
                                        $aux_nfila++;
                                        $aux_saldo = $aux_cant - $sumacantsoldesp;
                                        $aux_subtotal = $detalle->preciounit * $aux_saldo;
                                        $aux_total += $aux_subtotal;
                                        foreach ($detalle->producto->categoriaprod->invbodegas as $invbodega) {
                                            InvBodegaProducto::firstOrCreate(
                                                ['producto_id' => $detalle->producto_id, 'invbodega_id' => $invbodega->id],
                                                [   'producto_id' => $detalle->producto_id, 
                                                    'invbodega_id' => $invbodega->id
                                                ]
                                            );
                                        }
                                        $invbodegaproductos = $detalle->producto->invbodegaproductos;
                                        //dd($aux_saldo);
                                        //Este If cierra abajo

                                        $atributoProd = $detalle->producto->atributosProducto($detalle->producto_id);
                                        $aux_producto_nombre = $atributoProd["nombre"];

                                ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td class='tooltipsC' style='text-align:center;padding-left: 0px;padding-right: 0px;width: 5% !important;' class='tooltipsC' title='Marcar para enviar a produccion (fabricacion)'>
                                        <?php
                                            $aux_stafabchecked = "";
                                            $stafab = 0;
                                            if($detalle->requiere_fabricacion == 1){
                                                $aux_stafabchecked = "checked";
                                                $stafab = 1;
                                            }
                                        ?>
                                        <div class='checkbox'>
                                            <label style='font-size: 1.2em;padding-left: 0px;'>
                                                <input type="hidden" id="stafab{{$aux_nfila}}" name="stafab[]" value="{{old('stafab', $stafab ?? '0')}}">
                                                <input type="checkbox" class="checkstafab" id="aux_stafab{{$aux_nfila}}" name="aux_stafab[]" onchange="clickstafab({{$aux_nfila}})" {{$aux_stafabchecked}}>
                                                <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                            </label>
                                        </div>
                                        <input type="text" name="otdet_id[]" id="otdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id ?? 0}}" style="display:none;"/>
                                        <input type="text" name="NVdet_id[]" id="NVdet_id{{$aux_nfila}}" class="form-control" value="{{isset($ot) ? $detalle->otdetnvdet->notaventadetalle_id : $detalle->id}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:center" name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
                                        @if ($detalle->producto->acuerdotecnico)
                                            <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec({{$detalle->producto->acuerdotecnico->id}},{{$ot->cliente_id ?? $notaventa->cliente_id}},1)" data-original-title="Acuerdo Técnico PDF">
                                                {{$detalle->producto_id}}
                                            </a>
                                        @else
                                            {{$detalle->producto_id}}
                                        @endif
                                        <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->producto_id}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        <?php
                                            if($aux_sta==2){
                                                //$aux_cant = $detalle->cant;
                                            }else{
                                                $aux_cant -= $detalle->cantusada;
                                                $detalle->cant -= $detalle->cantusada;
                                            }
                                        ?>
                                        @if ($aux_sta==2)
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->cant}}" style="display:none;"/>
                                        @else 
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->cant - $detalle->cantusada}}" style="display:none;"/>
                                        @endif
                                        <input type="text" name="cantext[]" id="cantext{{$aux_nfila}}" class="form-control" value="{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" style="display:none;"/>
                                        @if ($detalle->producto->acuerdotecnico)
                                            <a id="canttitle{{$aux_nfila}}" name="canttitle{{$aux_nfila}}" class="btn-accion-tabla btn-sm" title="Valor:{{$detalle->cant}} Ext:{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" data-toggle="tooltip" style="padding-left: 0px; display: inline;">
                                                <div name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" cantorig="{{$detalle->cant}}" style="display: inline;">
                                                    {{$aux_cant}}
                                                </div>
                                            </a>
                                            {{-- <a id="cantextA{{$aux_nfila}}" name="cantextA{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoNum" title="Editar valor sobre despacho" data-toggle="tooltip" valor="{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" fila="{{$aux_nfila}}" nomcampo="cantext" style="padding-left: 0px; display: inline;">
                                                <i class="fa fa-fw fa-pencil-square-o"></i>
                                            </a> --}}
                                        @else
                                            <div name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" cantorig="{{$detalle->cant}}">
                                                {{$aux_cant}}
                                            </div>
                                        @endif
                                    </td>
                                    <td name="cantdespF{{$aux_nfila}}" id="cantdespF{{$aux_nfila}}" style="text-align:center">
                                        {{$sumacantdesp}}
                                    </td>
                                    <td name="cantsoldespF{{$aux_nfila}}" id="cantsoldespF{{$aux_nfila}}" style="text-align:center">
                                        {{$sumacantsoldesp}}
                                    </td>
                                    <td name="saldocantOrigF{{$aux_nfila}}" id="saldocantOrigF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        {{$aux_saldo}}
                                    </td>
                                    <input type="text" name="cant_saldo[]" id="cant_saldo{{$aux_nfila}}" style="display:none;" value="{{$aux_saldo}}" />
                                    <td name="saldocantF{{$aux_nfila}}" id="saldocantF{{$aux_nfila}}" style="text-align:center">
                                        {{$aux_saldo}}
                                    </td>
                                    <td>
                                        {{$atributoProd["at_unidmed"]}}
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" style="display:none;" value="{{$atributoProd["unidadmedida_id"]}}" />
                                    </td>
                                    <td name="bodegasTB{{$aux_nfila}}" id="bodegasTB{{$aux_nfila}}" style="text-align:center;width: 14% !important;padding-top: 0px;padding-bottom: 1px;">
                                        <table class="table" id="tabla-bod" style="font-size:14px;table-layout: fixed;width: 150px;">
                                            <tbody>
                                                <?php $i=0 ?>
                                                @foreach($invbodegaproductos as $invbodegaproducto)
                                                    @if (true or $invbodegaproducto->invbodega->sucursal_id == ($ot->sucursal_id ?? $notaventa->sucursal_id))
                                                        <?php
                                                            $request = new Request();
                                                            $request["producto_id"] = $invbodegaproducto->producto_id;
                                                            $request["invbodega_id"] = $invbodegaproducto->invbodega_id;
                                                            $request["tipo"] = 2;
                                                            $existencia = $invbodegaproducto::existencia($request);
                                                            //dd($existencia);
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
                                                        @if (in_array($invbodegaproducto->invbodega_id,$array_bodegasmodulo) AND ($invbodegaproducto->invbodega->activo == 1)) <!--SOLO MUESTRA LAS BODEGAS TIPO 1, LAS TIPO 2 NO LAS MUESTRA YA QUE ES BODEGA DE DESPACHO -->
                                                            <?php $i++; ?>
                                                            <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}">
                                                                <td name="invbodegaproducto_idTD{{$invbodegaproducto->id}}" id="invbodegaproducto_idTD{{$invbodegaproducto->id}}" style="text-align:left;display:none;">
                                                                    <input type="text" name="invbodegaproducto_producto_id[]" id="invbodegaproducto_producto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$detalle->producto_id}}" style="display:none;"/>
                                                                    <input type="text" name="invbodegaproducto_id[]" id="invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$invbodegaproducto->id}}" style="display:none;"/>
                                                                    <input type="text" name="invbodegaproductoNVdet_id[]" id="invbodegaproductoNVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                                                    {{$invbodegaproducto->id}}
                                                                </td>
                                                                <td style="text-align:left;width: 15% !important;padding-right: 0px;padding-left: 2px;padding-top: 0px;padding-bottom: 0px;" class='tooltipsC' title='Bodega: {{$invbodegaproducto->invbodega->nombre}} {{$invbodegaproducto->invbodega->sucursal->nombre}}'>
                                                                    <div class="centrarhorizontal">
                                                                        <p name="nomabreTD{{$invbodegaproducto->id}}" id="nomabreTD{{$invbodegaproducto->id}}" style="color:{{$colorSuc}};font-size: 11px;margin-bottom: 0px;">{{$invbodegaproducto->invbodega->nomabre}} {{$invbodegaproducto->invbodega->sucursal->abrev}}</p>
                                                                    </div>
                                                                </td>
                                                                <td style="text-align:right;width: 20% !important;padding-left: 0px;padding-right: 0px;padding-top: 0px;padding-bottom: 0px;"  class='tooltipsC' title='Stock disponible'>
                                                                    <div name="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" id="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="centrarhorizontal">
                                                                        <p style="font-size: 11px;margin-bottom: 0px;">{{$existencia["stock"]["cant"]}}</p>
                                                                    </div>
                                                                </td>
                                                                {{-- <td  class="width90 tooltipsC" name="cantorddespF{{$invbodegaproducto->id}}" id="cantorddespF{{$invbodegaproducto->id}}" style="text-align:right;width: 40% !important" title='Cant a despachar'>
                                                                    <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control numerico bod{{$aux_nfila}} dismpadding invcant" onkeyup="sumbod({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}','SD')" style="text-align:right;" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}"/>
                                                                </td> --}}
                                                                <?php
                                                                    /* $aux_staexchecked = "";
                                                                    $staex = 0;
                                                                    if($existencia["stock"]["cant"] <=0){
                                                                        $aux_staexchecked = "checked";
                                                                        $staex = 1;
                                                                    } */
                                                                ?>
                                                                {{-- <td class='tooltipsC' style='text-align:center;padding-left: 0px;padding-right: 0px;width: 10% !important;' class='tooltipsC' title='Marcar para no usar Stock'>
                                                                    <div class='checkbox'>
                                                                        <label style='font-size: 1.2em;padding-left: 0px;'>
                                                                            <input type="hidden" id="staex{{$invbodegaproducto->id}}" name="staex[]" value="{{old('staex', $staex ?? '0')}}">
                                                                            <input type="checkbox" class="checkstaex" id="aux_staex{{$invbodegaproducto->id}}" name="aux_staex[]" {{$aux_staexchecked}} onchange="clickstaex({{$invbodegaproducto->id}})">
                                                                            <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                                                        </label>
                                                                    </div>
                                                                </td> --}}
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                                @if ($i == 0)
                                                    <a style="text-align:center" class='btn-sm tooltipsC' title='Producto sin Bodega Asignada'>
                                                        <i class='fa fa-fw fa-question-circle text-aqua'></i>
                                                    </a>
                                                @endif
                                            </tbody>
                                        </table>
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}" title="{{isset($ot) ? $detalle->obs : ''}}">
                                        {{$aux_producto_nombre}}
                                    </td>
                                    <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                        {{number_format($pesounit, 7, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$pesounit}}" style="display:none;"/>
                                    </td>
                                    <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right" class="subtotalkg" valor="0.00">
                                        <input type="text" name="itemkg[]" id="itemkg{{$aux_nfila}}" class="form-control numerico itemkgotnv" value="{{number_format($aux_totalkilos, 2, ',', '.')}}" valor="{{$aux_totalkilos}}" item="{{$aux_nfila}}" style="text-align:right" maxlength="15"/>
                                        {{-- {{number_format($aux_totalkilos, 2, ',', '.')}} --}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control totalkilos" value="{{$aux_totalkilos}}" valor="{{$aux_totalkilos}}" style="display:none;"/>
                                    </td>
                                    <td name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->precioxkilo, 0, ',', '.')}}
                                    </td>
                                    <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                        <?php $aux_descPorc = $detalle->descuento * 100; ?>
                                        {{$aux_descPorc}}%
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$detalle->descuento}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <?php $aux_descVal = 1 - $detalle->descuento; ?>
                                        <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                    </td>
                                    <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->preciounit, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$detalle->preciounit}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;" name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->precioxkilo, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$detalle->precioxkilo}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$detalle->precioxkiloreal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCF" style="text-align:right"> 
                                        {{-- {{number_format($detalle->subtotal, 0, ',', '.')}} --}}
                                        {{number_format($aux_subtotal, 0, ',', '.')}}
                                    </td>
                                    <td class="subtotalCF" style="text-align:right;display:none;"> 
                                        <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$aux_subtotal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                        {{-- {{$detalle->subtotal}} --}}
                                        {{$aux_subtotal}}
                                    </td>
                                    <td>
                                        <a id="campo_obs{{$aux_nfila}}" name="campo_obs{{$aux_nfila}}" class="btn-accion-tabla tooltipsC editarcampoTex" title="Observación" valor="{{isset($ot) ? $detalle->obs : ''}}" fila="{{$aux_nfila}}" tipocampo="texto" nomcampo="campo_obs">
                                            <i class="fa fa-fw fa-comment-o"></i>
                                        </a>
                                        <input type="text" name="otdet_obs[]" id="otdet_obs{{$aux_nfila}}" class="form-control" value="{{isset($ot) ? $detalle->obs : ''}}" style="display:none;"/>
                                    </td>
                                </tr>
                                <?php 
                                        $i++;
                                    }
                                ?>
                            @endforeach
                            <tr id="trneto" name="trneto">
                                <td colspan="2" style="text-align:right;padding-bottom: 0px;padding-top: 0px;">
                                    <b>Total</b>
                                </td>
                                <td style="text-align:right;padding-bottom: 0px;padding-top: 0px;padding-left: 2px;padding-right: 2px; width: 50px;">
                                    <div class="form-group col-xs-12 col-sm-12" style="margin-bottom: 0px;width: 60px !important">
                                        <input type="text" name="cantsolTotal" id="cantsolTotal" class="form-control" style="text-align:right; width: 50px;" oninput="this.style.width = (this.value.length + 1) + 'ch';" value="{{$aux_canttotal}}" readonly required/>
                                    </div>
                                </td>
                                <td colspan="7" style="text-align:right"><b>Total Kg</b></td>
                                <td id="totalkgs" name="totalkgs" style="text-align:right">{{number_format($aux_pesototal, 2, ',', '.')}}</td>
                                <td colspan="2" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="13" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                <td id="tdiva" name="tdiva" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <td colspan="13" style="text-align:right"><b>Total</b></td>
                                <td id="tdtotal" name="tdtotal" style="text-align:right">0,00</td>
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

@include('generales.modalpdf')
@include('generales.editarcampotex')
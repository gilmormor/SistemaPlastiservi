<?php
    use Illuminate\Http\Request;
    use App\Models\InvBodegaProducto;
?>
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type='hidden' id="aux_obs" name="aux_obs" value="{{old('aux_obs', $data->obs ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{old('aux_iva', $tablas['empresa']->iva ?? '')}}">
<input type="hidden" name="dtefoliocontrol_id" id="dtefoliocontrol_id" value="1">
<input type="hidden" name="foliocontrol_id" id="foliocontrol_id" value="2">
<input type="text" name="ids" id="ids" value="0" style="display: none">
<input type="hidden" name="imagen" id="imagen" value="{{old('imagen', $data->oc_file ?? '')}}">
<input type="hidden" name="tipoprod" id="tipoprod" value="2">
<input type="hidden" name="tasaiva" id="tasaiva" value="{{$empresa->iva}}">

<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="oc_fileaux" class="control-label requerido" data-toggle='tooltip' title="Adjuntar Orden de compra">Adjuntar Orden de compra</label>
    <input type="hidden" name="oc_fileaux" id="oc_fileaux" value="" class="form-control" style="text-align:right;">
</div>
<input type="text" name="notaventa_id" id="notaventa_id" value="{{old('notaventa_id', $data->id ?? '')}}" style="display: none">

<div style="display: none">
    <select name="auxunidadmedida_id" id="unidadmedida_id" class="form-control select2">
        <option value=""></option>
        @foreach($tablas['unidadmedidas'] as $id => $descripcion)
            <option value="{{$descripcion->id}}">{{$descripcion->nombre}}</option>
        @endforeach
    </select>
</div>
<?php 
    $aux_rut = "";
    if(isset($data)){
        $aux_rut = number_format( substr ( $data->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->cliente->rut, strlen($data->cliente->rut) -1 , 1 );
    }
    $aux_labelRequerido = "";
    $aux_inputRequerido = "";
    $enableCamposCot = ""; //Este campo lo cambio a disbles si llegara a necesitar desactivar los campos marcados con esta variable
?>

<div class="row">
    <div class="col-xs-12 col-sm-9">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-3">
                <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                <input type="text" name="rut_cliente" id="rut_cliente" class="form-control" value="{{old('rut_cliente', $aux_rut ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-5">
                <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->cliente->razonsocial ?? '')}}" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-4">
                <label for="giro" class="control-label requerido">Giro</label>
                <input type="text" name="giro" id="giro" class="form-control" value="{{old('razonsocial', $data->cliente->giro ?? '')}}" required readonly/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="direccion" class="control-label">Dirección Princ</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="comuna_nombre" class="control-label requerido">Comuna</label>
                <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="{{old('comuna_nombre', $data->cliente->comuna->nombre ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="provincia_nombre" class="control-label requerido">Provincia</label>
                <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" value="{{old('provincia_nombre', $data->cliente->comuna->provincia->nombre ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="telefono" class="control-label requerido">Telefono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->cliente->telefono ?? '')}}" required readonly/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-4">
                <label for="email" class="control-label requerido">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->cliente->email ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="formapago_desc" class="control-label requerido">Forma de Pago</label>
                <input type="text" name="formapago_desc" id="formapago_desc" class="form-control" value="{{old('formapago_desc', $data->cliente->formapago->descripcion ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="plazopago" class="control-label requerido tooltipsC" title="Plazo Pago">Plazo pago</label>
                <input type="text" name="plazopago" id="plazopago" class="form-control" value="{{old('plazopago', $data->cliente->plazopago->descripcion ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="fchemis" class="control-label requerido">Fecha Emision</label>
                <input type="text" name="fchemis" id="fchemis" class="form-control pull-right datepicker"  value="{{old('fchemis', date("d/m/Y") )}}" readonly required>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="fchvenc" class="control-label requerido">F. Venc</label>
                <input type="text" name="fchvenc" id="fchvenc" class="form-control" value="{{old('fchvenc', isset($data) ? date('d/m/Y', strtotime(date('Y-m-d') ."+ " . $data->cliente->plazopago->dias . " days"))  : "")}}" required readonly/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-2">
                <label for="vendedor_id" class="control-label requerido">Vendedor</label>
                <input type="text" name="vendedor_nombre" id="vendedor_nombre" class="form-control" value="{{old('vendedor_nombre', $data->vendedor->persona->nombre . ' ' .$data->vendedor->persona->apellido ?? '')}}" required readonly/>
                <div style="display: none;">
                    <select name="vendedor_id" id="vendedor_id" class="form-control select2 vendedor_id" data-live-search='true' required>
                        <option value="">Seleccione...</option>
                        @foreach($tablas['vendedores'] as $vendedor)
                            <option
                                value="{{$vendedor->id}}"
                                @if (isset($data) and ($data->vendedor_id == $vendedor->id))
                                    {{'selected'}}
                                @endif
                                >{{$vendedor->nombre}} {{$vendedor->apellido}}
                            </option>
                        @endforeach
                    </select>    
                </div>
            </div>
    

            <div class="form-group col-xs-12 col-sm-2">
                <label for="ot" class="control-label" data-toggle='tooltip' title="Orden de trabajo">OT</label>
                <input type="text" name="ot" id="ot" class="form-control pull-right numerico" value="{{old('ot', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->ot : '')}}" maxlength="5"/>
            </div>
            <div class="form-group col-xs-12 col-sm-5">
                <label for="tipodespacho" class="control-label requerido" data-toggle='tooltip' title="Tipo Despacho">Tipo Desp</label>
                <select name="tipodespacho" id="tipodespacho" class="form-control select2  tipodespacho" data-live-search='true' value="{{old('tipodespacho', isset($dteguiadesp) ? $dteguiadesp->tipodespacho : ($data->tipodespacho ?? ''))}}" required>
                    <option 
                        value="1"
                        @if (isset($dteguiadesp) and ($dteguiadesp->tipodespacho=="1"))
                            {{'selected'}}
                        @endif
                        >Despacho por cuenta del receptor del documento (cliente o vendedor en caso de Facturas de compra.)</option>
                    <option 
                        value="2"
                        @if (isset($dteguiadesp))
                            @if (($dteguiadesp->tipodespacho=="2"))
                                {{'selected'}}
                            @endif
                        @else 
                            {{'selected'}}
                        @endif
                        >Despacho por cuenta del emisor a instalaciones del cliente</option>
                    <option 
                        value="3"
                        @if (isset($dteguiadesp) and ($dteguiadesp->tipodespacho=="3"))
                            {{'selected'}}
                        @endif
                        >Despacho por cuenta del emisor a otras instalaciones (Ejemplo: entrega en Obra)</option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="indtraslado" class="control-label requerido">Tipo Traslado</label>
                <select name="indtraslado" id="indtraslado" class="form-control select2  indtraslado" data-live-search='true' value="{{old('indtraslado', isset($dteguiadesp) ? $dteguiadesp->indtraslado : ($data->indtraslado ?? ''))}}" required>
                    <option 
                        value="1" 
                        @if(isset($dteguiadesp)) 
                            @if($dteguiadesp->indtraslado =="1")
                                {{'selected'}}
                            @endif
                        @else
                            {{'selected'}}
                        @endif
                        >Operación constituye venta</option>
                    <option 
                        value="6"
                        @if(isset($dteguiadesp) and $dteguiadesp->indtraslado =="6")
                            {{'selected'}}
                        @endif
                        >Otros traslados no venta</option>
                </select>
            </div>
            <!--
            <div class="form-group col-xs-12 col-sm-3">
                <label for="btnfotooc" class="control-label">Cargar OrdenCompra</label>
                <button type="button" class="form-control btn btn-primary" id="btnfotooc" name="btnfotooc" title="Guardar">Cargar OrdenCompra</button>
            </div>-->
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-2">
                <label for="centroeconomico_id" class="control-label requerido">Centro Economico</label>
                <select name="centroeconomico_id" id="centroeconomico_id" class="form-control select2 centroeconomico_id" data-live-search='true' required>
                    <option value="">Seleccione...</option>
                    @foreach($tablas['centroeconomicos'] as $centroeconomico)
                        <option
                            value="{{$centroeconomico->id}}"
                            @if (isset($data) and $data->sucursal_id==$centroeconomico->id) 
                                {{'selected'}}
                            @endif
                            >{{$centroeconomico->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="tipoentrega_id" class="control-label requerido">Tipo Entrega</label>
                <select name="tipoentrega_id" id="tipoentrega_id" class="form-control select2 tipoentrega_id" required {{$enableCamposCot}}>
                    <option value=''>Seleccione...</option>
                    @foreach($tablas['tipoentregas'] as $tipoentrega)
                        <option
                            value="{{$tipoentrega->id}}"
                            >
                            {{$tipoentrega->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="lugarentrega" class="control-label requerido">Lugar de Entrega</label>
                <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->lugarentrega : ($data->lugarentrega ?? ''))}}" required placeholder="Lugar de Entrega"/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="comunaentrega_id" class="control-label requerido">Comuna Entrega</label>
                <select name="comunaentrega_id" id="comunaentrega_id" class="form-control select2  comunaentrega_id" data-live-search='true' value="{{old('comunaentrega_id', $data->comunaentrega_id ?? '')}}" required>
                    <option value="">Seleccione...</option>
                    @foreach($tablas['comunas'] as $comuna)
                        <option
                            value="{{$comuna->id}}"
                            >{{$comuna->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="obs" class="control-label">Observaciones</label>
                <textarea class="form-control" name="obs" id="obs" value="{{old('obs', $data->obs ?? '')}}" placeholder="Observación" maxlength="90"></textarea>
            </div>

        </div>
    </div>
    <div class="col-xs-12 col-sm-3">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div id="group_oc_id" class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_id" name="lboc_id" for="oc_id" class="control-label {{$aux_labelRequerido}}">Nro OrdenCompra</label>
                            <div class="input-group">
                                <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id', $data->oc_id ?? '')}}" placeholder="Nro Orden de Compra" maxlength="15" {{!is_null($data->oc_id) ? "disabled readonly" : ""}}/>
                            </div>
                        </div>
                        <div id="group_oc_file" class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_oc_file" name="lboc_oc_file" for="oc_file" class="control-label">Adjuntar OC</label>
                            <div class="input-group">
                                <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->oc_file) ? Storage::url("imagenes/notaventa/$data->oc_file") : ""}}' accept="*"/>
                            </div>
                            <span id="oc_file-error" style="color:#dd4b39;display: none;">Este campo es obligatorio.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
    <input type="hidden" name="total" id="total" value="{{old('total', $data->neto ?? '')}}" class="form-control" style="text-align:right;" readonly required>
</div>
<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="text-align:center;" class="width30">item</th>
                            <th class="width30 tooltipsC" title="Código Producto">CodProd</th>
                            <th class="width100" style="text-align:right;">Cant</th>
                            <th class="width50 tooltipsC" title="Unidad de Medida">UniMed</th>
                            <th>Nombre</th>
                            <th style="text-align:right;">Kilos</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th style="text-align:right;">PUnit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th style="text-align:right;">Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $aux_nfila = 0; $i = 0;
                            $aux_Tsubtotal = 0;
                            $aux_Tcantdesp = 0;
                            $aux_Tkilos = 0;
                        ?>
                        <!--DEBO REVISAR SI LA GUIA YA FUE PROCESADA ANTERIORMENTE O SI ESTA ANULADA-->
                        @foreach($detalles as $detalle)
                            <?php 
                                $aux_cant = $detalle->cant;
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
                                $peso = $detalle->totalkilos/$aux_cant;

                                $aux_nombreprod = $detalle->producto->nombre;
                                if(isset($detalle->producto->acuerdotecnico)){
                                    $at_ancho = $detalle->producto->acuerdotecnico->at_ancho;
                                    $at_largo = $detalle->producto->acuerdotecnico->at_largo;
                                    $at_espesor = $detalle->producto->acuerdotecnico->at_espesor;
                                    $at_ancho = empty($at_ancho) ? "0.00" : $at_ancho;
                                    $at_largo = empty($at_largo) ? "0.00" : $at_largo;
                                    $at_espesor = empty($at_espesor) ? "0.00" : $at_espesor;
                                    //$aux_nombreprod = $aux_nombreprod . " " . $at_ancho . "x" . $at_largo . "x" . $at_espesor;

                                    $AcuTec = $detalle->producto->acuerdotecnico;
                                    $aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
                                    $aux_impresa = $AcuTec->at_impreso==1 ? "Impresa" : "";
                                    $aux_formatofilm = $AcuTec->at_formatofilm > 0 ? number_format($AcuTec->at_formatofilm, 2, ',', '.') . "Kg." : "";
                                    $aux_atribAcuTec = $AcuTec->materiaprima->nombre . " " . $AcuTec->color->descripcion . " " . $aux_impresa . " " . $AcuTec->at_impresoobs . " " . $aux_formatofilm;
                                    //CONCATENAR TODO LOS CAMPOS NECESARIOS PARA QUE SE FORME EL NOMBRE DEL RODUCTO EN LA GUIA
                                    $aux_nombreprod = nl2br($detalle->producto->categoriaprod->nombre . " " . $aux_atribAcuTec . " " . $at_ancho . "x" . $at_largo . "x" . number_format($AcuTec->at_espesor, 3, ',', '.'));
                                }else{
                                    //CUANDO LA CLASE TRAE N/A=NO APLICA CAMBIO ESTO POR EMPTY ""
                                    $aux_cla_nombre =str_replace("N/A","",$detalle->producto->claseprod->cla_descripcion);
                                    $aux_diametro = $detalle->producto->diametro > 0 ? " D:" . $detalle->producto->diametro : "";
                                    $aux_long = $detalle->producto->long ? " L:" . $detalle->producto->long : "";
                                    $aux_tipounion = "";
                                    if(!($detalle->producto->tipounion === "S/C" or $detalle->producto->tipounion === "S/U")){
                                        $aux_tipounion = $detalle->producto->tipounion;
                                    }                                        
                                    $aux_nombreprod = $aux_nombreprod . $aux_diametro . $aux_long . " " . $aux_cla_nombre. " " . $aux_tipounion;
                                    }
                                //esto es para reemplazar el caracter comilla doble " de la cadena, para evitar que me trunque los valores en javascript al asignar a attr val 
                                $aux_nombreprod = str_replace('"',"'",$aux_nombreprod);


                                if($aux_cant > $sumacantsoldesp){
                                    $aux_nfila++;
                                    $aux_saldo = $aux_cant - $sumacantsoldesp;
                                    $aux_subtotal = $aux_saldo * $detalle->preciounit;
                                    $aux_Tcantdesp += $aux_saldo;
                                    $aux_subtotalkilos = $detalle->totalkilos == 0 ? 0 : ($detalle->totalkilos / $detalle->cant) * $aux_saldo;
                                    $aux_Tkilos += $aux_subtotalkilos;
                                    $aux_Tsubtotal += $aux_subtotal;
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
                            ?>

                            <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}" class="proditems">
                                <td style='text-align:center'>
                                    {{($i + 1)}}
                                    <input type="text" name="nrolindet[]" id="nrolindet{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->nrolindet : ($i + 1)}}" style="display:none;"/>
                                    <input type="text" name="despachoorddet_id[]" id="despachoorddet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                    <input type="text" name="notaventadetalle_id[]" id="notaventadetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                    <input type="text" name="iddet[]" id="iddet{{$aux_nfila}}" class="form-control" value="" style="display:none;"/>
                                    <input type="text" name="obsdet[]" id="obsdet{{$aux_nfila}}" class="form-control" value="" style="display:none;"/>
                                    
                                </td>
                                <td style='text-align:center' name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
                                    {{$detalle->producto_id}}
                                    <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->producto_id}}" style="display:none;"/>
                                    <input type="text" name="vlrcodigo[]" id="vlrcodigo{{$aux_nfila}}" class="form-control" value="{{$detalle->producto_id}}" style="display:none;"/>
                                </td>
                                <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                    <a id="qtyitemlbl{{$aux_nfila}}" name="qtyitemlbl{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoNum" title="Editar valor" data-toggle="tooltip" valor="{{$aux_saldo}}" fila="{{$aux_nfila}}" nomcampo="qtyitemlbl" style="display:none;">
                                        {{$aux_saldo}}
                                    </a>
                                    <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$aux_saldo}}" style="display:none;"/>
                                    <input type="text" name="qtyitem[]" id="qtyitem{{$aux_nfila}}" class="form-control cantsum" value="{{$aux_saldo}}" onkeyup="sumcant()" valor={{$aux_saldo}} style="text-align:right;" fila="{{$aux_nfila}}"/>
                                </td>
                                <td name="unidadmedida_nombre{{$aux_nfila}}" id="unidadmedida_nombre{{$aux_nfila}}">
                                    <a id="unmditemlbl{{$aux_nfila}}" name="unmditemlbl{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoTex" title="Editar Unidad Medida" data-toggle="tooltip" valor="{{$detalle->unidadmedida->nombre}}"  fila="{{$aux_nfila}}" tipocampo="texto" nomcampo="unmditemlbl">
                                        {{$detalle->unidadmedida->nombre}}
                                    </a>
                                    <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="{{$detalle->unidadmedida_id}}" style="display:none;"/>
                                    <input type="text" name="unmditem[]" id="unmditem{{$aux_nfila}}" class="form-control" value="{{$detalle->unidadmedida->nombre}}" style="display:none;"/>
                                </td>
                                <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                    <a id="producto_nombre{{$aux_nfila}}" name="producto_nombre{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoTex" title="Editar Nombre Producto" data-toggle="tooltip" valor="{{$detalle->producto->nombre}}"  fila="{{$aux_nfila}}" tipocampo="texto" nomcampo="producto_nombre">
                                        {{$aux_nombreprod}}
                                    </a>
                                    <input type="text" name="nmbitem[]" id="nmbitem{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->nombre}}" style="display:none;"/>
                                    <input type="text" name="dscitem[]" id="dscitem{{$aux_nfila}}" class="form-control" value="" style="display:none;"/>
                                </td>
                                <td style="text-align:right;"> 
                                    <a id="aux_kilos{{$aux_nfila}}" name="aux_kilos{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoNum" title="Editar Kilos" data-toggle="tooltip" valor={{$aux_subtotalkilos}} fila="{{$aux_nfila}}" tipocampo="numerico" nomcampo="aux_kilos">
                                        {{number_format($aux_subtotalkilos, 2, ',', '.')}}
                                    </a>
                                    <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$aux_subtotalkilos}}" style="display:none;" valor={{$aux_subtotalkilos}} fila="{{$aux_nfila}}"/>
                                    <input type="text" name="itemkg[]" id="itemkg{{$aux_nfila}}" class="form-control" value="{{$aux_subtotalkilos}}" style="display:none;"/>
                                </td>
                                <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                    <?php $aux_descPorc = $detalle->descuento * 100; ?>
                                    {{$aux_descPorc}}%
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$detalle->descuento}}" style="display:none;"/>
                                </td>
                                <td style="text-align:right;display:none;">
                                    <?php $aux_descVal = 1 - ($detalle->descuento); ?>
                                    <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                </td>
                                <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right;"> 
                                    {{number_format($detalle->preciounit, 0, ',', '.')}}
                                    <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$detalle->preciounit}}" style="display:none;"/>
                                    <input type="text" name="prcitem[]" id="prcitem{{$aux_nfila}}" class="form-control" value="{{$detalle->preciounit}}" style="display:none;"/>
                                </td>
                                <td style="display:none;" name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                    {{number_format($detalle->precioxkilo, 0, ',', '.')}}                                    
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$detalle->precioxkilo}}" style="display:none;"/>
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$detalle->precioxkiloreal}}" style="display:none;"/>
                                </td>
                                <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCFTD" style="text-align:right"> 
                                    <div id="lblsubtotal{{$aux_nfila}}" name="lblsubtotal{{$aux_nfila}}">
                                        {{number_format($aux_subtotal, 0, ',', '.')}}
                                    </div>
                                    <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control sumsubtotal" value="{{$aux_subtotal}}" style="display:none;"/>
                                    <input type="text" name="montoitem[]" id="montoitem{{$aux_nfila}}" class="form-control summontoitem" value="{{$aux_subtotal}}" style="display:none;"/>
                                </td>
                            </tr>
                            <?php 
                                $i++;
                                }
                            ?>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr id="trneto" name="trneto">
                            <th colspan="2" style="text-align:right">
                                <b>Totales:</b>
                            </th>
                            <td style="text-align:right;padding-bottom: 0px;padding-top: 0px;padding-left: 2px;padding-right: 2px;">
                                <div class="form-group col-xs-12 col-sm-12" style="margin-bottom: 0px;width: 100px !important">
                                    <input type="text" name="cantTotal" id="cantTotal" class="form-control" style="text-align:right;" value={{$aux_Tcantdesp}} readonly required/>
                                </div>
                            </td>

                            <th colspan="2" style="text-align:right"><b>Total Kg</b></th>
                            <th id="totalkg" name="totalkg" style="text-align:right" valor="{{$aux_Tkilos}}">{{number_format($aux_Tkilos, 2, ',', '.')}}</th>
                            <th style="text-align:right"><b>Neto</b></th>
                            <th id="tdneto" name="tdneto" style="text-align:right">{{number_format($aux_Tsubtotal, 0, ',', '.')}}</th>
                        </tr>
                        <?php 
                            $aux_Tiva = round(($empresa->iva * $aux_Tsubtotal/100));
                            $aux_total = round($aux_Tsubtotal + $aux_Tiva);
                        ?>
                        <tr id="triva" name="triva">
                            <th colspan="7" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></th>
                            <th id="tdiva" name="tdiva" style="text-align:right">{{number_format($aux_Tiva, 0, ',', '.')}}</th>
                        </tr>
                        <tr id="trtotal" name="trtotal">
                            <th colspan="7" style="text-align:right"><b>Total</b></th>
                            <th id="tdtotal" name="tdtotal" style="text-align:right">{{number_format($aux_total, 0, ',', '.')}}</th>
                        </tr>

                        
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aprobstatus" id="aprobstatus" value="{{old('aprobstatus', $data->aprobstatus ?? '')}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{$tablas['empresa']->iva}}">
<input type="hidden" name="direccioncot" id="direccioncot" value="{{old('direccioncot', $data->direccioncot ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type="hidden" name="contacto" id="contacto" value="{{old('contacto', $data->contacto ?? '')}}">
<input type="hidden" name="comuna_id" id="comuna_id" value="{{old('comuna_id', $data->comuna_id ?? '')}}">
<input type="hidden" name="formapago_id" id="formapago_id" value="{{old('formapago_id', $data->formapago_id ?? '')}}">
<input type="hidden" name="plazopago_id" id="plazopago_id" value="{{old('plazopago_id', $data->plazopago_id ?? '')}}">
<input type="hidden" name="giro_id" id="giro_id" value="{{old('giro_id', $data->giro_id ?? '')}}">
<input type="hidden" name="giro" id="giro" value="{{old('giro', $data->giro ?? '')}}">
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">

@if($aux_sta==1)
    <input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $tablas['vendedor_id'] ?? '')}}">
@else
    <input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $data->vendedor_id ?? '')}}">
@endif
<input type="hidden" name="region_id" id="region_id" value="{{old('region_id', $data->region_id ?? '')}}">
<input type="hidden" name="provincia_id" id="provincia_id" value="{{old('provincia_id', $data->provincia_id ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">

<input type="hidden" name="neto" id="neto" value="{{old('neto', $data->neto ?? '')}}">
<input type="hidden" name="piva" id="piva" value="{{old('piva', $tablas['empresa']->iva ?? '')}}">
<input type="hidden" name="iva" id="iva" value="{{old('iva', $data->iva ?? '')}}">

<input type="hidden" name="aux_aprocot" id="aux_aprocot" value="{{old('aux_aprocot', session('aux_aprocot') ?? '')}}">
<?php
    $selecmultprod = false;
?>
<input type="hidden" name="selecmultprod" id="selecmultprod" value="{{$selecmultprod}}">

<?php
    $disabledReadOnly = "";
    //Si la pantalla es de aprobacion de Cotizacion desactiva todos input
    //if(strpos("15", session('aux_aprocot'))){ //(session('aux_aprocot')=='1'){
    if (session('aux_aprocot')=='1' or session('aux_aprocot')=='5'){
        $disabledReadOnly = ' readonly disabled ';
    }
?>

<div class="container">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-2">
            <!--
            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
            <div class="input-group">
            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" title="F2 Buscar" placeholder="F2 Buscar" required {{$disabledReadOnly}}/>
                <span class="input-group-btn">
                    @if (session('aux_aprocot')=='0')
                        <a id="btnbuscarcliente" name="btnbuscarcliente" href="#" class="btn btn-flat" data-toggle='tooltip' title="Buscar">
                            <i class="fa fa-search"></i>
                        </a>
                    @endif
                </span>
            </div>
            -->
            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT sin puntos ni guión">RUT</label>
            <div class="input-group">
                <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" title="F2 Buscar" placeholder="F2 Buscar" maxlength="12" oninput="validarInputRut(event)" onkeyup="llevarMayus(this);" required {{$disabledReadOnly}}/>
                <span class="input-group-btn">
                    @if (session('aux_aprocot')=='0')
                        <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                    @endif
                </span>
            </div>

        </div>
        <div class="form-group col-xs-12 col-sm-3">
            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
            <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $clienteselec[0]->razonsocial ?? '')}}" readonly/>
        </div>

        <div class="form-group col-xs-12 col-sm-4">
            <label for="direccion" class="control-label">Dirección Principal</label>
            <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $clienteselec[0]->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="telefono" class="control-label requerido">Telefono</label>
            <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" required readonly/>
        </div>

        <div class="form-group col-xs-12 col-sm-1">
            <label for="fechahora" class="control-label">Fecha</label>
            <input type="text" name="fechahora" id="fechahora" class="form-control" value="{{old('fechahora', $fecha ?? '')}}" style="padding-left: 2px;padding-right: 2px;" required readonly/>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-2">
            <label for="email" class="control-label requerido">Email</label>
            <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required readonly/>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="comuna_idD" class="control-label requerido">Comuna</label>
            <select name="comuna_idD" id="comuna_idD" class="selectpicker form-control comuna_idD" data-live-search='true' required readonly disabled>
                <option value="">Seleccione...</option>
                @foreach($tablas['comunas'] as $comuna)
                    <option
                        value="{{$comuna->id}}"
                        @if ($aux_sta==2 and $comuna->id==$data->comuna_id)
                            {{'selected'}}
                        @endif
                        >
                        {{$comuna->nombre}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="vendedor_idD" class="control-label requerido">Vendedor</label>
            <select name="vendedor_idD" id="vendedor_idD" class="form-control select2 vendedor_idD" data-live-search='true'  required readonly disabled>
            <!--<select name="vendedor_idD" id="vendedor_idD" class="selectpicker form-control vendedor_idD" required readonly disabled>-->
                    <option value="">Seleccione...</option>
                @foreach($tablas['vendedores'] as $vendedor)
                    <option
                        value="{{$vendedor->id}}"
                        @if (($aux_sta==1) and ($tablas['vendedor_id'] == $vendedor->id))
                            {{'selected'}}
                        @endif
                        @if (($aux_sta==2) and ($data->vendedor_id==$vendedor->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$vendedor->nombre}} {{$vendedor->apellido}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="plazopago_idD" class="control-label requerido">Plazo</label>
            <select name="plazopago_idD" id="plazopago_idD" class="form-control selectpicker plazopago_idD" required readonly disabled>
                <option value=''>Seleccione...</option>
                @foreach($tablas['plazopagos'] as $plazopago)
                    <option
                        value="{{$plazopago->id}}"
                        @if (($aux_sta==2) and ($data->plazopago_id==$plazopago->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$plazopago->descripcion}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-2">
            <label for="formapago_idD" class="control-label requerido">Forma de Pago</label>
            <select name="formapago_idD" id="formapago_idD" class="form-control selectpicker formapago_idD" required readonly disabled>
                <option value=''>Seleccione...</option>
                @foreach($tablas['formapagos'] as $formapago)
                    <option
                        value="{{$formapago->id}}"
                        @if (($aux_sta==2) and ($data->formapago_id==$formapago->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$formapago->descripcion}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="giro_idD" class="control-label requerido">Giro</label>
            <select name="giro_idD" id="giro_idD" class="form-control selectpicker giro_idD" required readonly disabled>
                <option value=''>Seleccione...</option>
                @foreach($tablas['giros'] as $giro)
                    <option
                        value="{{$giro->id}}"
                        @if (($aux_sta==2) and ($data->giro_id==$giro->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$giro->nombre}}
                    </option>
                @endforeach
            </select>
        </div>
       
    </div>

    <div class="row">
        <div class="form-group col-xs-12 col-sm-3">
            <label id="lblsucursal_id" name="lblsucursal_id" for="sucursal_id" class="control-label requerido">Sucursal</label>
            <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" data-live-search='true' {{$disabledReadOnly}} required>
                <option value=''>Seleccione...</option>
                    @foreach($tablas['sucursales'] as $sucursal)
                        <option
                            value="{{$sucursal->id}}"
                            @if (isset($data) and ($data->sucursal_id==$sucursal->id))
                                {{'selected'}}
                            @endif
                            >
                            {{$sucursal->nombre}}
                        </option>
                    @endforeach                    
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-3">
            <label for="lugarentrega" class="control-label requerido">Lugar de Entrega</label>
            <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', $data->lugarentrega ?? '')}}" required placeholder="Lugar de Entrega" {{$disabledReadOnly}}/>
        </div>
        <!--
        <div class="form-group col-xs-12 col-sm-1">
            <label for="plazoentrega" class="control-label requerido">Plazo Ent.</label>
            <input type="text" name="plazoentrega" id="plazoentrega" class="form-control pull-right datepicker"  value="{{old('plazoentrega', $data->plazoentrega ?? '')}}" readonly required {{$disabledReadOnly}}>
        </div>
        -->
        <div class="form-group col-xs-12 col-sm-1">
            <label for="plaentdias" class="control-label requerido" data-toggle='tooltip' title="Plazo entrega Días">PlaEnt Dias</label>
            <input type="number" name="plaentdias" id="plaentdias" class="form-control" min="1" max="45" value="{{old('plaentdias', $data->plaentdias ?? '')}}" required {{$disabledReadOnly}}>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="tipoentrega_id" class="control-label requerido">Tipo Entrega</label>
            <select name="tipoentrega_id" id="tipoentrega_id" class="form-control select2 tipoentrega_id" required {{$disabledReadOnly}}>
                <option value=''>Seleccione...</option>
                @foreach($tablas['tipoentregas'] as $tipoentrega)
                    <option
                        value="{{$tipoentrega->id}}"
                        @if (($aux_sta==2) and ($data->tipoentrega_id==$tipoentrega->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$tipoentrega->nombre}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-3">
            <label for="observacion" class="control-label">Observaciones</label>
            <input type="text" name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}" placeholder="Observaciones" {{$disabledReadOnly}}/>
        </div>
    </div>
    <div class="row">
    </div>
</div>
<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        @if (session('aux_aprocot')=='0') <!--Estatus en 0 si puede incluir -->
            <div class="box-tools pull-right">
                <a id="botonNewProd" name="botonNewProd" href="#" class="btn btn-block btn-success btn-sm">
                    <i class="fa fa-fw fa-plus-circle"></i> Nuevo Producto
                </a>
            </div>                    
        @endif
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Cod</th>
                            <th style="display:none;" class="width30">ID</th>
                            <th style="display:none;">cotizacionDetalle_ID</th>
                            <th style="display:none;">Codigo Producto</th>
                            <th style="display:none;">Cód Int</th>
                            <th style="display:none;">CódInterno</th>
                            <th>Cant</th>
                            <th style="display:none;">Cant</th>
                            <th>Unid</th>
                            <th>Nombre Producto</th>
                            <th style="display:none;">UnidadMedida</th>
                            <th>Clase<br>Sello</th>
                            <th>Diam<br>Ancho</th>
                            <th style="display:none;">Diametro</th>
                            <th>Largo</th>
                            <th style="display:none;">Largo</th>
                            <th>Esp</th>
                            <th style="display:none;">Espesor</th>
                            <th>Peso</th>
                            <th style="display:none;">Peso</th>
                            <th>TU</th>
                            <th style="display:none;">TUnion</th>
                            <th>Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th>P Neto Unit</th>
                            <th style="display:none;">Precio Neto Unit</th>
                            <th>V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th>Total Kilos</th>
                            <th style="display:none;">Total Kilos</th>
                            <th>Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                            <th style="display:none;">Sub Total Neto Sin Formato</th>
                            <th style="display:none;">Array Acuerdo Tecnico</th>
                            <th style="display:none;">Tipo Producto</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($aux_sta==2)
                            <?php $aux_nfila = 0; $i = 0;
                            ?>
                            @foreach($cotizacionDetalles as $CotizacionDetalle)
                                <?php 
                                    $aux_nfila++;
                                    $aux_ancho = $CotizacionDetalle->producto->diametro;
                                    $aux_espesor = $CotizacionDetalle->espesor;
                                    $aux_largo = $CotizacionDetalle->largo;
                                    $aux_cla_sello_nombre = $CotizacionDetalle->producto->claseprod->cla_nombre;
                                    $aux_producto_nombre = $CotizacionDetalle->producto->nombre;
                                    $aux_categoria_nombre = $CotizacionDetalle->producto->categoriaprod->nombre;
                                    if ($CotizacionDetalle->acuerdotecnicotemp != null){
                                        $AcuTecTemp = $CotizacionDetalle->acuerdotecnicotemp;
                                        //$aux_producto_nombre = nl2br($AcuTecTemp->at_desc . "\n" . $AcuTecTemp->materiaprima->nombre . " " . $AcuTecTemp->materiaprima->desc . "\n". $AcuTecTemp->at_tiposelloobs);
                                        $aux_producto_nombre = nl2br($CotizacionDetalle->producto->categoriaprod->nombre . ", " . $AcuTecTemp->at_desc);
                                        $aux_ancho = $AcuTecTemp->at_ancho . " " . ($AcuTecTemp->at_ancho ? $AcuTecTemp->anchounidadmedida->nombre : "") ;
                                        $aux_largo = $AcuTecTemp->at_largo . " " . ($AcuTecTemp->at_largo ? $AcuTecTemp->largounidadmedida->nombre : "") ;
                                        $aux_espesor = $AcuTecTemp->at_espesor;
                                        $aux_cla_sello_nombre = $AcuTecTemp->claseprod->cla_nombre;
                                    }
                                    if ($CotizacionDetalle->producto->acuerdotecnico != null){
                                        $AcuTec = $CotizacionDetalle->producto->acuerdotecnico;
                                        //$aux_producto_nombre = nl2br($AcuTec->at_desc . "\n" . $AcuTecTemp->materiaprima->nombre . " " . $AcuTecTemp->materiaprima->desc . "\n". $AcuTec->at_tiposelloobs);
                                        $aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
                                        $aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
                                        $aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
                                        $aux_espesor = $AcuTec->at_espesor;
                                        $aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
                                    }
                                    $aux_mostrarimagenat = "display:none;";
                                ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td name="producto_idTDT{{$aux_nfila}}" id="producto_idTDT{{$aux_nfila}}" style="text-align:center;" categoriaprod_id="{{$CotizacionDetalle->producto->categoriaprod_id}}">
                                        @if ($CotizacionDetalle->producto->tipoprod == 1)
                                            <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTecTemp({{$CotizacionDetalle->acuerdotecnicotempunoauno->id}},{{$data->cliente_id}},1)" data-original-title="Acuerdo Técnico PDF">
                                                {{$CotizacionDetalle->producto_id}}
                                            </a>
                                        @else
                                            @if ($CotizacionDetalle->producto->acuerdotecnico)
                                                <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec({{$CotizacionDetalle->producto->acuerdotecnico->id}},{{$data->cliente_id}},1)" data-original-title="Acuerdo Técnico PDF">
                                                    {{$CotizacionDetalle->producto_id}}
                                                </a>
                                            @else
                                                {{$CotizacionDetalle->producto_id}}
                                            @endif
                                        @endif

                                        @if ($CotizacionDetalle->producto->tipoprod == 1)
                                            <a class="btn-accion-tabla tooltipsC" title="Editar Acuerdo tecnico" onclick="crearEditarAcuTec({{$aux_nfila}})">
                                            @if ($CotizacionDetalle->acuerdotecnicotemp == null)
                                                <i id="icoat{{$aux_nfila}}" class="fa fa-cog text-red girarimagen"></i>
                                            @else
                                                <i id="icoat{{$aux_nfila}}" class="fa fa-cog text-aqua girarimagen"></i>
                                                <?php 
                                                    if($CotizacionDetalle->acuerdotecnicotemp->at_impreso==1){
                                                        $aux_mostrarimagenat = "display:inline;";
                                                    }
                                                ?>
                                            @endif
                                            <div id="divMostrarImagenat{{$aux_nfila}}" name="divMostrarImagenat{{$aux_nfila}}" style={{$aux_mostrarimagenat}}>
                                                <a class="btn-accion-tabla tooltipsC" title="Arte Acuerdo Técnico" onclick="ocultarMostrarFiltro({{$aux_nfila}})">
                                                    <i id="btnmostrarocultar{{$aux_nfila}}" class="fa fa-plus"></i>
                                                </a>
                                                <?php 
                                                    if($CotizacionDetalle->acuerdotecnicotemp == null){
                                                        $aux_imagen = "";
                                                    }else{
                                                        $aux_at_impresofoto = $CotizacionDetalle->acuerdotecnicotemp->at_impresofoto;
                                                        $data_initial_preview=isset($aux_at_impresofoto) ? Storage::url("imagenes/attemp/$aux_at_impresofoto") : "";
                                                    }
                                                ?>
                                                <div id="div_at_imagen{{$aux_nfila}}" name="div_at_imagen{{$aux_nfila}}" style="display: none;">
                                                    <input type="file" name="at_imagen{{$aux_nfila}}" id="at_imagen{{$aux_nfila}}" class="form-control at_imagen" data-initial-preview='{{$data_initial_preview}}' accept="*"/>
                                                    <input type="hidden" name="imagen{{$aux_nfila}}" id="imagen{{$aux_nfila}}" value="{{old("imagen$aux_nfila", $aux_at_impresofoto ?? '')}}">
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td style="display:none;" name="cotdet_idTD{{$aux_nfila}}" id="cotdet_idTD{{$aux_nfila}}">
                                        {{$CotizacionDetalle->id}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="cotdet_id[]" id="cotdet_id{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->id}}" style="display:none;"/>
                                    </td>
                                    <td name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}" style="display:none;">
                                        <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto_id}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;" name="codintprodTD{{$aux_nfila}}" id="codintprodTD{{$aux_nfila}}">
                                        {{$CotizacionDetalle->producto->codintprod}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="codintprod[]" id="codintprod{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->codintprod}}" style="display:none;"/>
                                    </td>
                                    <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:center">
                                        {{$CotizacionDetalle->cant}}
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->cant}}" style="display:none;"/>
                                    </td>
                                    <td name="unidadmedida_nombreTD{{$aux_nfila}}" id="unidadmedida_nombreTD{{$aux_nfila}}" style="text-align:center">
                                        {{$CotizacionDetalle->unidadmedida->nombre}}
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}" categoriaprod_nombre="{{$aux_categoria_nombre}}">
                                        {!!$aux_producto_nombre!!}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->unidadmedida_id}}" style="display:none;"/>
                                    </td>
                                    <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}">
                                        {{$aux_cla_sello_nombre}}
                                    </td>
                                    <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_ancho}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$aux_ancho}}" style="display:none;"/>
                                    </td>
                                    <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_largo}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$aux_largo}}" style="display:none;"/>
                                    </td>
                                    <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($aux_espesor, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$aux_espesor}}" style="display:none;"/>
                                        <input type="text" name="ancho[]" id="ancho{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->ancho}}" style="display:none;"/>
                                        <input type="text" name="obs[]" id="obs{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->obs}}" style="display:none;"/>
                                    </td>
                                    <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                        {{number_format($CotizacionDetalle->peso, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->peso}}" style="display:none;"/>
                                    </td>
                                    <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}"> 
                                        {{$CotizacionDetalle->producto->tipounion}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->tipounion}}" style="display:none;"/>
                                    </td>
                                    <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right">
                                        <?php $aux_descPorc = $CotizacionDetalle->descuento * 100; ?>
                                        {{$aux_descPorc}}%
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->descuento}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <?php $aux_descVal = 1 - $CotizacionDetalle->descuento; ?>
                                        <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                    </td>
                                    <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($CotizacionDetalle->preciounit, 0, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->preciounit}}" style="display:none;"/>
                                    </td>
                                    <td name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($CotizacionDetalle->precioxkilo, 0, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->precioxkilo}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->precioxkiloreal}}" style="display:none;"/>
                                    </td>
                                    <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($CotizacionDetalle->totalkilos, 2, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->totalkilos}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCF" style="text-align:right"> 
                                        {{number_format($CotizacionDetalle->subtotal, 0, ',', '.')}}
                                    </td>
                                    <td class="subtotalCF" style="text-align:right;display:none;">
                                        <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->subtotal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                        {{$CotizacionDetalle->subtotal}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="acuerdotecnico[]" id="acuerdotecnico{{$aux_nfila}}" class="form-control" value="{{json_encode($CotizacionDetalle->acuerdotecnicotemp)}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="tipoprod[]" id="tipoprod{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->tipoprod}}" style="display:none;"/>
                                    </td>
                                    <td>
                                        @if(session('aux_aprocot')=='0')
                                            @if ($CotizacionDetalle->producto->tipoprod != 1)
                                                <a  id="editarRegistro{{$aux_nfila}}" name="editarRegistro{{$aux_nfila}}"  class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro({{$aux_nfila}})">
                                                    <i class="fa fa-fw fa-pencil"></i>
                                                </a>
                                            @endif
                                            <a class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro({{$aux_nfila}})">
                                                <i class="fa fa-fw fa-trash text-danger"></i>
                                            </a>
                                        @else
                                            <a class="btn-accion-tabla tooltipsC" title="Editar Observación" onclick="ObsItemCot({{$CotizacionDetalle->id}},{{$aux_nfila}})">
                                                <i class="fa fa-fw fa-comment-o"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <?php $i++;?>
                            @endforeach
                            <tr id="trneto" name="trneto">
                                <td colspan="14" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="14" style="text-align:right"><b>IVA {{$tablas['empresa']->iva}}%</b></td>
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
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
    <input type="hidden" name="total" id="total" value="{{old('total', $data->total ?? '')}}"class="form-control" style="text-align:right;" readonly required>
</div>
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label name="lblitemcompletos" id="lblitemcompletos" for="itemcompletos" class="control-label requerido" data-toggle='tooltip' title="Complete valores item">Complete valores item 1</label>
    <input type="hidden" name="itemcompletos" id="itemcompletos" value="" class="form-control" style="text-align:right;" readonly required>
</div>

@include('generales.calcprecioprodsn')
@if (session('aux_aprocot')=='1' or session('aux_aprocot')=='5') <!--(strpos("15", session('aux_aprocot'))) -->
    @include('generales.aprobarcotnv')
@else
    @include('generales.buscarclientebd')
    @include('generales.buscarproductobd')
@endif
@include('generales.acuerdotecnico')
@include('generales.modalpdf')

    <!-- Modal -->
    <!-- FORMULARIO DE CLIENTE TEMPORAL CON TODOS LOS CAMPOS 
    <div class="modal fade" id="myModalClienteTemp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
        
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Cliente Temporal</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="razonsocialCTM" class="control-label" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                        <input type="text" name="razonsocialCTM" id="razonsocialCTM" class="form-control requeridos" tipoval="texto" value="{{old('razonsocialCTM')}}" placeholder="Razón Social"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="direccionCTM" class="control-label" data-toggle='tooltip' title="Dirección">Dirección</label>
                        <input type="text" name="direccionCTM" id="direccionCTM" class="form-control requeridos" tipoval="texto"  maxlength="200" value="{{old('direccionCTM')}}" placeholder="Dirección"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="telefonoCTM" class="control-label" data-toggle='tooltip' title="Teléfono">Teléfono</label>
                        <input type="text" name="telefonoCTM" id="telefonoCTM" class="form-control requeridos" tipoval="numerico" maxlength="50" value="{{old('telefonoCTM')}}" placeholder="Teléfono"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="giro_idCTM" class="control-label" data-toggle='tooltip' title="Giro">Giro</label>
                        <select name="giro_idCTM" id="giro_idCTM" class="selectpicker form-control requeridos" tipoval="combobox" value="{{old('giro_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($tablas['giros'] as $giro)
                                <option
                                    value="{{$giro->id}}"
                                    >
                                    {{$giro->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-8" classorig="col-xs-12 col-sm-8">
                        <label for="giroCTM" class="control-label" data-toggle='tooltip' title="Descripción Giro">Descripción Giro</label>
                        <input type="text" name="giroCTM" id="giroCTM" class="form-control requeridos" tipoval="texto" value="{{old('giroCTM')}}" placeholder="Descripción Giro"/>
                        <span class="help-block"></span>
                    </div>

                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="emailCTM" class="control-label" data-toggle='tooltip' title="email">Email</label>
                        <input type="text" name="emailCTM" id="emailCTM" class="form-control requeridos" tipoval="email" maxlength="50" value="{{old('emailCTM')}}" placeholder="Email"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="formapago_idCTM" class="control-label">Forma de Pago</label>
                        <select name="formapago_idCTM" id="formapago_idCTM" class="selectpicker form-control requeridos" tipoval="combobox" data-live-search='true' title='Seleccione...' value="{{old('formapago_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($tablas['formapagos'] as $formapago)
                                <option
                                    value="{{$formapago->id}}"
                                    >
                                    {{$formapago->descripcion}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="plazopago_idCTM" class="control-label">Plazo de Pago</label>
                        <select name="plazopago_idCTM" id="plazopago_idCTM" class="selectpicker form-control requeridos" tipoval="combobox" data-live-search='true' title='Seleccione...' value="{{old('plazopago_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($tablas['plazopagos'] as $plazopago)
                                <option
                                    value="{{$plazopago->id}}"
                                    >
                                    {{$plazopago->descripcion}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="comunap_idCTM" class="control-label">Comuna</label>
                        <select name="comunap_idCTM" id="comunap_idCTM" class="selectpicker form-control requeridos" tipoval="combobox" data-live-search='true' title='Seleccione...' value="{{old('comunap_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($tablas['comunas'] as $comuna)
                                <option
                                    value="{{$comuna->id}}"
                                    region_id="{{$comuna->provincia->region_id}}"
                                    provincia_id="{{$comuna->provincia_id}}"
                                    >
                                    {{$comuna->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="provinciap_idCTM" class="control-label">Provincia</label>
                        <select name="provinciap_idCTM" id="provinciap_idCTM" class="selectpicker form-control provinciap_id" tipoval="combobox" title='Seleccione...' disabled readonly value="{{old('provinciap_idCTM')}}">
                            @foreach($tablas['provincias'] as $provincia)
                                <option
                                    value="{{$provincia->id}}"
                                    >
                                    {{$provincia->nombre}}
                                </option>
                            @endforeach  
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="regionp_idCTM" class="control-label">Región</label>
                        <select name="regionp_idCTM" id="regionp_idCTM" class="selectpicker form-control regionp_id" tipoval="combobox" title='Seleccione...' disabled readonly value="{{old('regionp_idCTM')}}">
                            @foreach($tablas['regiones'] as $region)
                                <option
                                    value="{{$region->id}}"
                                    >
                                    {{$region->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="contactonombreCTM" class="control-label" data-toggle='tooltip' title="Nombre Contacto">Nombre Contacto</label>
                        <input type="text" name="contactonombreCTM" id="contactonombreCTM" class="form-control requeridos" tipoval="texto" placeholder="Nombre Contacto" value="{{old('contactonombreCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="contactoemailCTM" class="control-label" data-toggle='tooltip' title="Email Contacto">Email Contacto</label>
                        <input type="text" name="contactoemailCTM" id="contactoemailCTM" class="form-control requeridos" tipoval="email" placeholder="Email Contacto" value="{{old('contactoemailCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="contactotelefCTM" class="control-label" data-toggle='tooltip' title="Teléfono Contacto">Teléfono Contacto</label>
                        <input type="text" name="contactotelefCTM" id="contactotelefCTM" class="form-control requeridos" tipoval="numerico" placeholder="Teléfono Contacto" value="{{old('contactotelefCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="finanzascontactoCTM" class="control-label" data-toggle='tooltip' title="Contacto Finanzas">Contacto Finanzas</label>
                        <input type="text" name="finanzascontactoCTM" id="finanzascontactoCTM" class="form-control requeridos" tipoval="texto" placeholder="Contacto Finanzas" value="{{old('finanzascontactoCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="finanzanemailCTM" class="control-label" data-toggle='tooltip' title="Email Finanzas">Email Finanzas</label>
                        <input type="text" name="finanzanemailCTM" id="finanzanemailCTM" class="form-control requeridos" tipoval="email" placeholder="Email Finanzas" value="{{old('finanzanemailCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="finanzastelefonoCTM" class="control-label" data-toggle='tooltip' title="Teléfono Finanzas">Teléfono Finanzas</label>
                        <input type="text" name="finanzastelefonoCTM" id="finanzastelefonoCTM" class="form-control requeridos" tipoval="numerico" placeholder="Teléfono Finanzas" value="{{old('finanzastelefonoCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                </div>                
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="sucursal_idCTM" class="control-label">Sucursal</label>
                        <select name="sucursal_idCTM" id="sucursal_idCTM" class="selectpicker form-control requeridos" tipoval="combobox" title='Seleccione...'>
                            @foreach($tablas['sucursales'] as $sucursal)
                                <option
                                    value="{{$sucursal->id}}"
                                    >
                                    {{$sucursal->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>

                    <div class="col-xs-12 col-sm-8" classorig="col-xs-12 col-sm-8">
                        <label for="observacionesCTM" class="control-label" data-toggle='tooltip' title="Observaciones">Observación</label>
                        <textarea class="form-control requeridos" name="observacionesCTM" id="observacionesCTM" placeholder="Observación" value="{{old('contactotelefCTM')}}"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCTM" name="btnGuardarCTM" title="Guardar">Guardar</button>
            </div>
            </div>
            
        </div>
    </div>
    -->
    <!-- NUEVO FORMULARIO DE CLIENTE TEMPORAL SOLO ALGUNOS DATOS SOLICITADO POR CGORIGOITIA 27/04/2023 -->
    <div class="modal fade" id="myModalClienteTemp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
        
            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Cliente Temporal</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="razonsocialCTM" class="control-label" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                        <input type="text" name="razonsocialCTM" id="razonsocialCTM" class="form-control requeridos" tipoval="texto" value="{{old('razonsocialCTM')}}" placeholder="Razón Social"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="direccionCTM" class="control-label" data-toggle='tooltip' title="Dirección">Dirección</label>
                        <input type="text" name="direccionCTM" id="direccionCTM" class="form-control requeridos" tipoval="texto"  maxlength="200" value="{{old('direccionCTM')}}" placeholder="Dirección"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="telefonoCTM" class="control-label" data-toggle='tooltip' title="Teléfono">Teléfono</label>
                        <input type="text" name="telefonoCTM" id="telefonoCTM" class="form-control requeridos" tipoval="numerico" maxlength="50" value="{{old('telefonoCTM')}}" placeholder="Teléfono"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="contactonombreCTM" class="control-label" data-toggle='tooltip' title="Nombre Contacto">Nombre Contacto</label>
                        <input type="text" name="contactonombreCTM" id="contactonombreCTM" class="form-control requeridos" tipoval="texto" placeholder="Nombre Contacto" value="{{old('contactonombreCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="comunap_idCTM" class="control-label">Comuna</label>
                        <select name="comunap_idCTM" id="comunap_idCTM" class="selectpicker form-control requeridos" tipoval="combobox" data-live-search='true' title='Seleccione...' value="{{old('comunap_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($tablas['comunas'] as $comuna)
                                <option
                                    value="{{$comuna->id}}"
                                    region_id="{{$comuna->provincia->region_id}}"
                                    provincia_id="{{$comuna->provincia_id}}"
                                    >
                                    {{$comuna->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                        <label for="provinciap_idCTM" class="control-label">Provincia</label>
                        <select name="provinciap_idCTM" id="provinciap_idCTM" class="selectpicker form-control provinciap_id" tipoval="combobox" title='Seleccione...' disabled readonly value="{{old('provinciap_idCTM')}}">
                            @foreach($tablas['provincias'] as $provincia)
                                <option
                                    value="{{$provincia->id}}"
                                    >
                                    {{$provincia->nombre}}
                                </option>
                            @endforeach  
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                        <label for="regionp_idCTM" class="control-label">Región</label>
                        <select name="regionp_idCTM" id="regionp_idCTM" class="selectpicker form-control regionp_id" tipoval="combobox" title='Seleccione...' disabled readonly value="{{old('regionp_idCTM')}}">
                            @foreach($tablas['regiones'] as $region)
                                <option
                                    value="{{$region->id}}"
                                    >
                                    {{$region->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>             
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="emailCTM" class="control-label" data-toggle='tooltip' title="email">Email</label>
                        <input type="text" name="emailCTM" id="emailCTM" class="form-control requeridos" tipoval="email" maxlength="50" value="{{old('emailCTM')}}" placeholder="Email"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="sucursal_idCTM" class="control-label">Sucursal</label>
                        <select name="sucursal_idCTM" id="sucursal_idCTM" class="selectpicker form-control requeridos" tipoval="combobox" title='Seleccione...'>
                            @foreach($tablas['sucursales'] as $sucursal)
                                <option
                                    value="{{$sucursal->id}}"
                                    >
                                    {{$sucursal->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="observacionesCTM" class="control-label" data-toggle='tooltip' title="Observaciones">Observación</label>
                        <textarea class="form-control requeridos" name="observacionesCTM" id="observacionesCTM" placeholder="Observación" value="{{old('contactotelefCTM')}}"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCTM" name="btnGuardarCTM" title="Guardar">Guardar</button>
            </div>
            </div>
        </div>
    </div>
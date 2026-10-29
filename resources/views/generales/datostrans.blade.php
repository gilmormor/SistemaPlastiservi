{{-- resources/views/general/datostrans.blade.php --}}
<div class="box box-info" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Datos de Transporte (Res. Ex. SII N°144)</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            {{-- Tipo de Transporte (Ahora obligatorio y en blanco por defecto) --}}
            <div class="form-group col-xs-12 col-sm-3">
                <label for="tipo_transporte" class="control-label requerido">Tipo de Transporte</label>
                <select name="tipo_transporte" id="tipo_transporte" class="form-control" required>
                    <option value="">Seleccione tipo de transporte...</option>
                    <option value="INTERNO">Transporte Interno (Propio)</option>
                    <option value="EXTERNO">Transporte Externo (Tercero)</option>
                </select>
                <span class="help-block" id="error_tipo_transporte" style="color: #dd4b39; font-size: 11px; display: none;">Debe seleccionar un tipo de transporte</span>
            </div>
            
            {{-- Fecha y Hora de Salida --}}
            <div class="form-group col-xs-12 col-sm-3">
                <label for="fchsalida" class="control-label requerido">Fecha de Salida</label>
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" name="fchsalida" id="fchsalida" class="form-control datepicker" 
                           placeholder="dd/mm/aaaa" autocomplete="off" readonly required>
                </div>
                <span class="help-block" id="error_fchsalida" style="color: #dd4b39; font-size: 11px; display: none;">La fecha no puede ser menor a hoy</span>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="hrasalida" class="control-label requerido">Hora de Salida</label>
                <input type="text" name="hrasalida" id="hrasalida" class="form-control" 
                       placeholder="HH:MM" autocomplete="off" maxlength="5" required>
                <span class="help-block" id="error_hrasalida" style="color: #dd4b39; font-size: 11px; display: none;">Formato inválido (HH:MM)</span>
            </div>

            {{-- Fecha de Llegada --}}
            <div class="form-group col-xs-12 col-sm-3">
                <label for="fchllegada" class="control-label">Fecha de Llegada</label>
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div>
                    <input type="text" name="fchllegada" id="fchllegada" class="form-control datepicker" 
                           placeholder="dd/mm/aaaa" autocomplete="off" readonly required>
                    </div>
                </div>
                <span class="help-block" id="error_fchllegada" style="color: #dd4b39; font-size: 11px; display: none;">Fecha inválida</span>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-3">
                <label for="indtraslado" class="control-label requerido">Tipo Traslado</label>
                <?php
                    $aux_tipotrasladodisabled = '';
                    if(isset($data->despachosol->tipoguiadesp)){
                        $aux_tipotrasladodisabled = 'disabled';
                    }
                    /* En las facturas (directa y exenta) el tipo de traslado es siempre 1,
                       "Operacion constituye venta", porque la factura documenta la venta
                       misma. Se deja preseleccionado y deshabilitado para que el usuario no
                       pueda cambiarlo. Deshabilitarlo no altera lo que se guarda: los dos
                       controladores ya fijan $dte->indtraslado = 1 sin leer el request.
                       Las vistas de guia de despacho no pasan esta bandera, asi que ahi el
                       select sigue funcionando igual que antes. */
                    $aux_esfactura = (isset($esfactura) and $esfactura);
                    if($aux_esfactura){
                        $aux_tipotrasladodisabled = 'disabled';
                    }
                ?>
                <select name="indtraslado" id="indtraslado" class="form-control select2  indtraslado" data-live-search='true' value="{{old('indtraslado', isset($data) ? $data->indtraslado : ($data->indtraslado ?? ''))}}" required {{$aux_tipotrasladodisabled}}>
                    <option value="">Seleccione...</option>
                    @foreach($tablas['tipotraslado'] as $tipotraslado)
                        <option value="{{$tipotraslado->indtraslado}}"
                            {{-- En factura queda fija la opcion 1, "Operacion constituye venta" --}}
                            @if($aux_esfactura and $tipotraslado->indtraslado == 1)
                                {{'selected'}}
                            @endif
                            @if(isset($data->despachosol->tipoguiadesp))
                                @if($tipotraslado->indtraslado == $data->despachosol->tipoguiadesp)
                                    {{'selected'}}
                                @endif
                                @if($tipotraslado->indtraslado == 6 and $data->despachosol->tipoguiadesp==30)
                                    {{'selected'}}
                                @endif
                            @endif
                            >{{$tipotraslado->desc}}</option>
                    @endforeach
                    {{-- Se agrega isset() igual que en las condiciones de arriba (lineas 61 y 69):
                         esta era la unica linea que accedia a $data->despachosol->tipoguiadesp sin
                         protegerlo, y reventaba en las rutas donde no existe despachosol
                         (dteguiadespnv, que viene de una NV) o donde no se pasa $data a la vista
                         (dteguiadespdir, dtefacturadir, dtefacturaexenta). --}}
                    @if (isset($data->despachosol->tipoguiadesp) and $data->despachosol->tipoguiadesp==20)
                        <option value="1"
                            {{'selected'}}
                            >Traslado + Precio</option>
                    @endif
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="comunaentrega_id" class="control-label requerido">Comuna Entrega</label>
                <?php
                    $aux_comunaentrega_id = '';
                    $aux_comunaentrega_nom = '';
                    if (!isset($dteguiadesp) and isset($data) and $data->comunaentrega_id){
                        $aux_comunaentrega_id = $data->comunaentrega_id;
                        $aux_comunaentrega_nom = $data->comunaentrega->nombre ?? '';
                    }else{
                        if(isset($dteguiadesp) and $dteguiadesp->dteguiadesp->comunaentrega_id){
                            $aux_comunaentrega_id = $dteguiadesp->dteguiadesp->comunaentrega_id;
                            $aux_comunaentrega_nom = $dteguiadesp->dteguiadesp->comunaentrega->nombre ?? '';
                        }
                    }
                    $aux_lugarentrega = isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->lugarentrega : ($data->lugarentrega ?? '');
                    $aux_readonlylugarentrega = '';
                    if($aux_lugarentrega != ''){
                        $aux_readonlylugarentrega = 'readonly';
                    }
                ?>
                @if ($aux_comunaentrega_id != '')
                    <input type="text" name="comunaentrega_id" id="comunaentrega_id" value="{{$aux_comunaentrega_id}}" class="form-control" required style="display: none;"/>
                    <input type="text" name="comunaentrega_nom" id="comunaentrega_nom" value="{{$aux_comunaentrega_nom}}" class="form-control" required disabled/>

                @else
                    <select name="comunaentrega_id" id="comunaentrega_id" class="form-control select2  comunaentrega_id" data-live-search='true' value="{{old('comunaentrega_id', $data->comunaentrega_id ?? '')}}" required>
                        <option value="">Seleccione...</option>
                        @foreach($tablas['comunas'] as $comuna)
                            <option
                                value="{{$comuna->id}}"
                                @if (!isset($dteguiadesp) and isset($data) and $comuna->id==$data->comunaentrega_id)
                                    {{'selected'}}
                                @else 
                                    @if(isset($dteguiadesp) and $comuna->id==$dteguiadesp->dteguiadesp->comunaentrega_id)
                                        {{'selected'}}
                                    @endif
                                @endif
                                >{{$comuna->nombre}}</option>
                        @endforeach
                    </select>
                @endif
                
            </div>

            <div class="form-group col-xs-12 col-sm-6">
                <label for="lugarentrega" class="control-label requerido">Lugar de Entrega</label>
                <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->lugarentrega : ($data->lugarentrega ?? ''))}}" required placeholder="Lugar de Entrega" {{$aux_readonlylugarentrega}}/>
            </div>
        </div>
        <div class="row">
            {{-- Div para RUT Transportista (Externo) --}}
            <div class="form-group col-xs-12 col-sm-3" id="div_rut_transportista" style="display: none;">
                <label for="ruttrans" class="control-label">RUT Transportista (Empresa)</label>
                <input type="text" name="ruttrans" id="ruttrans" class="form-control rut" placeholder="Ej: 761234567" onkeyup="llevarMayus(this);"  maxlength="12" oninput="validarInputRut(event)" onkeyup="llevarMayus(this);">
                <span id="nombre_transportista" class="help-block" style="font-size: 11px;"></span>
                {{-- <div class="input-group"> --}}
                    {{-- <span class="input-group-btn">
                        <button class="btn btn-primary" type="button" id="btn_buscar_transportista"><i class="fa fa-search"></i></button>
                    </span> --}}
                {{-- </div> --}}
            </div>
        </div>

        <div class="row">
            {{-- Datos del Chofer --}}
            <div class="form-group col-xs-12 col-sm-2">
                <label for="rutchofer" class="control-label requerido">RUT Chofer</label>
                <input type="text" name="rutchofer1" id="rutchofer" class="form-control rut" onkeyup="llevarMayus(this);" placeholder="Ej: 123456789"  maxlength="12" oninput="validarInputRut(event)" onkeyup="llevarMayus(this);" required>
                <span id="nombre_chofer" class="help-block" style="font-size: 11px;"></span>
                {{-- <div class="input-group"> --}}
                    {{-- <input type="text" name="rutchofer" id="rutchofer" class="form-control" placeholder="Ej: 12.345.678-9" required> --}}
                    {{-- <span class="input-group-btn">
                        <button class="btn btn-primary" type="button" id="btn_buscar_chofer"><i class="fa fa-search"></i></button>
                    </span> --}}
                {{-- </div>
                <span id="nombre_chofer" class="help-block" style="font-size: 11px;"></span> --}}
            </div>
            <div class="form-group col-xs-12 col-sm-4">
                <label for="nombrechofer" class="control-label requerido">Nombre Completo Chofer</label>
                <input type="text" name="nombrechofer" id="nombrechofer" class="form-control" placeholder="Nombre y Apellido" required>
            </div>
            {{-- Datos del Vehículo --}}
            <div class="form-group col-xs-12 col-sm-3">
                <label for="patente" class="control-label requerido">Patente Vehículo Principal</label>
                <input type="text" name="patente" id="patente" class="form-control" 
                    placeholder="Ej: ABCD12 o AA1234" maxlength="6" 
                    style="text-transform: uppercase;" required>
                <span id="vehiculo_existe" class="help-block" style="font-size: 11px;"></span>
                <small class="help-block text-muted">Formato: 2 letras + 4 números (AA1234) o 4 letras + 2 números (ABCD12)</small>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="patentecarro" class="control-label">Patente Carro (Remolque/Semirremolque)</label>
                <input type="text" name="patentecarro" id="patentecarro" class="form-control" 
                    placeholder="Ej: ABCD12 o AA1234" maxlength="6"
                    style="text-transform: uppercase;">
                <span id="patentecarro_existe" class="help-block" style="font-size: 11px;"></span>
                <small class="help-block text-muted">Opcional - Solo letras y números, sin espacios</small>
            </div>
        </div>
    </div>
</div>

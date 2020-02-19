<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aux_rut" id="aux_rut" value="{{old('aux_rut', $data->rut ?? '')}}"/>
<div class="row">
    <div class="form-group col-xs-12 col-sm-2">
        <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" required onblur="formato_rut(this)" onfocus="eliminarFormatoRut(this);" placeholder="Ingrese RUT" maxlength="9"/>
    </div>

<!--
    <div class="form-group col-xs-12 col-sm-6">
        <label for="rut" class="col-lg-5 control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
        <div class="col-lg-7">
            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" required onblur="formato_rut(this)" onfocus="eliminarFormatoRut(this);" placeholder="Ingrese RUT"/>
        </div>
    </div>
-->
    <div class="form-group col-xs-12 col-sm-3">
        <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
        <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->razonsocial ?? '')}}" maxlength="70" required/>
    </div>
    <div class="form-group col-xs-12 col-sm-5">
        <label for="direccionprinc" class="control-label requerido" data-toggle='tooltip' title="direccion">Dirección Principal</label>
        <input type="text" name="direccionprinc" id="direccionprinc" class="form-control" value="{{old('direccionprinc', $data->direccionprinc ?? '')}}" required  maxlength="200" placeholder="Ingrese Dirección"/>
    </div>
    <div class="form-group col-xs-12 col-sm-2">
        <label for="telefono" class="control-label requerido" data-toggle='tooltip' title="Teléfono">Teléfono</label>
        <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" maxlength="50" required placeholder="Ingrese Teléfono"/>
    </div>    
</div>
<!--style="text-transform:uppercase;" Llevar a MAYUSCULA-->

<div class="row">
    <div class="form-group col-xs-12 col-sm-3">
        <label for="email" class="control-label requerido" data-toggle='tooltip' title="email">Email</label>
        <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required  maxlength="50" placeholder="Ingrese Email"/>
    </div>
    <div class="form-group col-xs-12 col-sm-2">
        <label for="nombrefantasiaprinc" class="control-label" data-toggle='tooltip' title="Nombre de Fantasia">Fantasia</label>
        <input type="text" name="nombrefantasiaprinc" id="nombrefantasiaprinc" class="form-control" value="{{old('nombrefantasiaprinc', $data->nombrefantasiaprinc ?? '')}}" maxlength="50" placeholder="Nombre de Fantasia"/>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="vendedor_id" class="control-label requerido" data-toggle='tooltip' title="Vendedores">Vendedores</label>
        <select name="vendedor_id[]" id="vendedor_id" class="form-control select2" multiple required>
            @foreach($vendedores as $vendedor)
                <option
                    value="{{$vendedor->id}}"
                    {{is_array(old('vendedor_id')) ? (in_array($vendedor->id, old('vendedor_id')) ? 'selected' : '') : (isset($data) ? ($data->vendedores->firstWhere('id', $vendedor->id) ? 'selected' : '') : '')}}
                    >
                    {{$vendedor->nombre}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        <label for="giro_id" class="control-label requerido" data-toggle='tooltip' title="Giro">Giro</label>
        <select name="giro_id" id="giro_id" class="form-control select2 giro_id" required>
            <option value="">Seleccione...</option>
            @foreach($giros as $giro)
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
        <label for="sucursalp_id" class="control-label requerido" data-toggle='tooltip' title="Sucursal">Sucursal</label>
        <select name="sucursalp_id[]" id="sucursalp_id" class="form-control select2" multiple required>
            @foreach($sucursales as $id => $nombre)
                <option
                    value="{{$id}}"
                    {{is_array(old('sucursalp_id')) ? (in_array($id, old('sucursalp_id')) ? 'selected' : '') : (isset($data) ? ($data->sucursales->firstWhere('id', $id) ? 'selected' : '') : '')}}
                    >
                    {{$nombre}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-2">
        <label for="comunap_id" class="col-form-label requerido">Comuna</label>
        <select name="comunap_id" id="comunap_id" class="select2 form-control comunap_id" title='Seleccione...' required>
            <option value="">Seleccione...</option>
            @foreach($comunas as $comuna)
                <option
                    value="{{$comuna->id}}"
                    region_id="{{$comuna->provincia->region_id}}"
                    provincia_id="{{$comuna->provincia_id}}"
                    @if (($aux_sta==2) and ($data->comunap_id==$comuna->id))
                        {{'selected'}}
                    @endif
                    >
                    <!--{{$comuna->id}} - {{$comuna->nombre}}-->
                    {{$comuna->nombre}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-2">
        <label for="provinciap_id" class="col-form-label requerido">Provincia</label>
        <select name="provinciap_id" id="provinciap_id" class="selectpicker form-control provinciap_id" title='Seleccione...' readonly>
            @foreach($provincias as $provincia)
                <option
                    value="{{$provincia->id}}"
                    @if (($aux_sta==2) and ($data->provinciap_id==$provincia->id))
                        {{'selected'}}
                    @endif
                    >
                    <!--{{$provincia->id}} - {{$provincia->nombre}}-->
                    {{$provincia->nombre}}
                </option>
            @endforeach  
        </select>
        <span class="help-block"></span>
    </div>
    <div class="form-group col-xs-12 col-sm-2">
        <label for="regionp_id" class="col-form-label requerido">Región</label>
        <select name="regionp_id" id="regionp_id" class="selectpicker form-control regionp_id" title='Seleccione...' readonly>
            @foreach($regiones as $region)
                <option
                    value="{{$region->id}}"
                    @if (($aux_sta==2) and ($data->regionp_id==$region->id))
                        {{'selected'}}
                    @endif
                    >
                    <!--{{$region->id}} - {{$region->nombre}}-->
                    {{$region->nombre}}
                </option>
            @endforeach
        </select>
        <span class="help-block"></span>
    </div>

    <!--
    <div class="form-group col-xs-12 col-sm-6">
        <label for="vendedor_id1" class="col-lg-2 control-label requerido">Vendedor</label>
        <div class="col-lg-10">
            <select name="vendedor_id1" id="vendedor_id1" class="form-control select2 vendedor_id1" required>
                <option value="">Seleccione...</option>
                @foreach($vendedores as $vendedor)
                    <option
                        value="{{$vendedor->id}}"
                        @if (($aux_sta==2) and ($data->vendedor_id==$vendedor->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$vendedor->nombre}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    -->
    

</div>

<div class="box box-danger">
    <div class="box-header with-border">
        <h3 class="box-title">Dirección</h3>
        <div class="box-tools pull-right">
            <a id="botonNuevaDirec" href="{{route('crear_cliente')}}" class="btn btn-block btn-success btn-sm">
                <i class="fa fa-fw fa-plus-circle"></i> Nueva Dirección
            </a>
        </div>
        <div class="box-body">
            <table class="table table-striped table-bordered table-hover" id="tabla-data">
                <thead>
                    <tr>
                        <th class="width70">ID</th>
                        <th>Direccion</th>
                        <th style="display:none;">region_id</th>
                        <th style="display:none;">provincia_id</th>
                        <th style="display:none;">comuna_id</th>
                        <th style="display:none;">formapago</th>
                        <th style="display:none;">plazopago</th>
                        <th style="display:none;">contactonombre</th>
                        <th style="display:none;">contactoemail</th>
                        <th style="display:none;">contactotelef</th>
                        <th style="display:none;">nombrefantasia</th>
                        <th style="display:none;">mostrarguiasfacturas</th>
                        <th style="display:none;">finanzascontacto</th>
                        <th style="display:none;">finanzanemail</th>
                        <th style="display:none;">finanzastelefono</th>
                        <th style="display:none;">observaciones</th>
                        <th class="width70"></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($aux_sta==2)
                        <?php $aux_nfila = 0; $i = 0;?>
                        @foreach($clientedirecs as $clientedirec)
                            <?php $aux_nfila++; ?>
                            <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                <td>
                                    {{$clientedirec->dir_id}}
                                    <input type="text" name="direccion_id[]" id="direccion_id{{$aux_nfila}}" class="form-control" value="{{$clientedirec->dir_id}}" style="display:none;"/>
                                </td>
                                <td>
                                    <div name="labeldir{{$aux_nfila}}" id="labeldir{{$aux_nfila}}">{{$clientedirec->direccion}}</div>
                                    <input type="text" name="direccion[]" id="direccion{{$aux_nfila}}" class="form-control" value="{{$clientedirec->direccion}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">
                                    '<input type="text" name="region_id[]" id="region_id{{$aux_nfila}}" class="form-control" value="{{$clientedirec->region_id}}"/>
                                </td>
                                <td style="display:none;">
                                    <input type="text" name="provincia_id[]" id="provincia_id{{$aux_nfila}}" class="form-control" value="{{$clientedirec->provincia_id}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="comuna_id[]" id="comuna_id{{$aux_nfila}}" class="form-control" value="{{$clientedirec->comuna_id}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="formapago_id[]" id="formapago_id{{$aux_nfila}}" class="form-control" value="{{$clientedirec->formapago_id}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="plazopago_id[]" id="plazopago_id{{$aux_nfila}}" class="form-control" value="{{$clientedirec->plazopago_id}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="contactonombre[]" id="contactonombre{{$aux_nfila}}" class="form-control" value="{{$clientedirec->contactonombre}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="contactoemail[]" id="contactoemail{{$aux_nfila}}" class="form-control" value="{{$clientedirec->contactoemail}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="contactotelef[]" id="contactotelef{{$aux_nfila}}" class="form-control" value="{{$clientedirec->contactotelef}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="nombrefantasia[]" id="nombrefantasia{{$aux_nfila}}" class="form-control" value="{{$clientedirec->nombrefantasia}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="mostrarguiasfacturas[]" id="mostrarguiasfacturas{{$aux_nfila}}" class="form-control" value="{{$clientedirec->mostrarguiasfacturas}}" style="display:none;"/>
                                </td>
                                <td style="display:none;"> 
                                    <input type="text" name="finanzascontacto[]" id="finanzascontacto{{$aux_nfila}}" class="form-control" value="{{$clientedirec->finanzascontacto}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="finanzanemail[]" id="finanzanemail{{$aux_nfila}}" class="form-control" value="{{$clientedirec->finanzanemail}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="finanzastelefono[]" id="finanzastelefono{{$aux_nfila}}" class="form-control" value="{{$clientedirec->finanzastelefono}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="sucursal_id[]" id="sucursal_id{{$aux_nfila}}" class="form-control" value="{{$aux_vecsuc[$i]}}" style="display:none;"/>
                                </td>
                                <td style="display:none;">  
                                    <input type="text" name="observaciones[]" id="observaciones{{$aux_nfila}}" class="form-control" value="{{$clientedirec->observaciones}}" style="display:none;"/>
                                </td>
                                <td>
                                    <a href="#" class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro({{$aux_nfila}})">
                                        <i class="fa fa-fw fa-pencil"></i>
                                    </a>
                                    <a href="#" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro({{$aux_nfila}})">
                                        <i class="fa fa-fw fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $i++;?>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
    
      <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title">Dirección Cliente</h3>
        </div>
        <div class="modal-body">
            <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="direccionM" class="col-form-label" data-toggle='tooltip' title="Direccion">Dirección</label>
                        <input type="text" name="direccionM" id="direccionM" class="form-control" value="{{old('direccionM', $data->direccion ?? '')}}" placeholder="Ingrese Dirección" maxlength="150"/>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="comuna_idM" class="col-form-label">Comuna</label>
                        <select name="comuna_idM" id="comuna_idM" class="selectpicker form-control comuna_id" data-live-search='true' title='Seleccione...'>
                            <option value="">Seleccione...</option>
                            @foreach($comunas as $comuna)
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
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="region_idM" class="col-form-label">Región</label>
                        <select name="region_idM" id="region_idM" class="selectpicker form-control region_id" data-live-search='true' title='Seleccione...' disabled readonly>
                            @foreach($regiones as $region)
                                <option
                                    value="{{$region->id}}">
                                    {{$region->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="provincia_idM" class="col-form-label">Provincia</label>
                        <select name="provincia_idM" id="provincia_idM" class="selectpicker form-control provincia_id" data-live-search='true' title='Seleccione...' disabled readonly>
                            @foreach($provincias as $provincia)
                                <option
                                    value="{{$provincia->id}}">
                                    {{$provincia->nombre}}
                                </option>
                            @endforeach  
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="formapago_idM" class="col-form-label">Forma de Pago</label>
                        <select name="formapago_idM" id="formapago_idM" class="selectpicker form-control formapago" data-live-search='true' title='Seleccione...'>
                            @foreach($formapagos as $formapago)
                                <option
                                    value="{{$formapago->id}}">
                                    {{$formapago->descripcion}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="plazopago_idM" class="col-form-label">Plazo de Pago</label>
                        <select name="plazopago_idM" id="plazopago_idM" class="selectpicker form-control plazopago" data-live-search='true' title='Seleccione...'>
                            @foreach($plazopagos as $plazopago)
                                <option
                                    value="{{$plazopago->id}}">
                                    {{$plazopago->descripcion}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 align-bottom">
                    <br>
                    <div class="form-group col-xs-12 col-sm-12">
                        <div class='checkbox'>
                            <label style='font-size: 1.2em' data-toggle='tooltip' title='Permiso Insertar'>
                                <input type='checkbox' id='mostrarguiasfacturasM' name='mostrarguiasfacturasM'>
                                <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                Mostrar Guias Factura
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="contactonombreM" class="col-form-label" data-toggle='tooltip' title="Contacto Finanzas">Nombre Contacto</label>
                        <input type="text" name="contactonombreM" id="contactonombreM" class="form-control" value="{{old('contactonombreM', $data->contactonombre ?? '')}}" placeholder="Nombre Contacto"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="contactoemailM" class="col-form-label" data-toggle='tooltip' title="Contacto Finanzas">Email Contacto</label>
                        <input type="text" name="contactoemailM" id="contactoemailM" class="form-control" value="{{old('contactoemailM', $data->contactoemail ?? '')}}" placeholder="Email Contacto"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="contactotelefM" class="col-form-label" data-toggle='tooltip' title="Contacto Finanzas">Teléfono Contacto</label>
                        <input type="text" name="contactotelefM" id="contactotelefM" class="form-control" value="{{old('contactotelefM', $data->contactotelef ?? '')}}" placeholder="Teléfono Contacto"/>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="finanzascontactoM" class="col-form-label" data-toggle='tooltip' title="Contacto Finanzas">Contacto Finanzas</label>
                        <input type="text" name="finanzascontactoM" id="finanzascontactoM" class="form-control" value="{{old('finanzascontactoM', $data->finanzascontacto ?? '')}}" placeholder="Contacto Finanzas"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="finanzanemailM" class="col-form-label" data-toggle='tooltip' title="Contacto Finanzas">Email Finanzas</label>
                        <input type="text" name="finanzanemailM" id="finanzanemailM" class="form-control" value="{{old('finanzanemailM', $data->finanzanemail ?? '')}}" placeholder="Email Finanzas"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="finanzastelefonoM" class="col-form-label" data-toggle='tooltip' title="Contacto Finanzas">Teléfono Finanzas</label>
                        <input type="text" name="finanzastelefonoM" id="finanzastelefonoM" class="form-control" value="{{old('finanzastelefonoM', $data->finanzastelefono ?? '')}}" placeholder="Teléfono Finanzas"/>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-left: 0px;">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="nombrefantasiaM" class="col-form-label" data-toggle='tooltip' title="Contacto Finanzas">Nombre Fantasia</label>
                        <input type="text" name="nombrefantasiaM" id="nombrefantasiaM" class="form-control" value="{{old('nombrefantasiaM', $data->nombrefantasia ?? '')}}" placeholder="Nombre de Fantasia"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <label for="sucursal_idM" class="control-label">Sucursal</label>
                    <select name="sucursal_idM[]" id="sucursal_idM" class="selectpicker form-control sucursal_id" data-live-search='true' title='Seleccione...' multiple>
                        @foreach($sucursales as $id => $nombre)
                            <option
                                value="{{$id}}"
                                >
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label for="observacionesM" class="col-form-label" data-toggle='tooltip' title="Contacto Finanzas">Observación</label>
                        <textarea class="form-control" name="observacionesM" id="observacionesM" value="{{old('observacionesM', $data->observaciones ?? '')}}" placeholder="Observación"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarM" name="btnGuardarM" title="Guardar">Guardar</button>
        </div>
        </div>
      
    </div>
</div>
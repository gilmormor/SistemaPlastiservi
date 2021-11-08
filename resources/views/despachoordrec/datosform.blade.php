<div class="col-xs-12 col-sm-9">
    <!--
    <div class="box box-danger col-xs-12 col-sm-1" style="margin-bottom: 0px;margin-top: 2px;">
        <div class="box-header with-border">
            <div class="box-body">
            -->
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-1">
                        <label for="despachoord_idD" class="control-label" data-toggle='tooltip' title="ID Orden Despacho">OD ID</label>
                        <input type="text" name="despachoord_idD" id="despachoord_idD" class="form-control" value="{{$data->id}}" required disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="rut" class="control-label" data-toggle='tooltip' title="RUT">RUT</label>
                        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->notaventa->cliente->rut ?? '')}}" maxlength="12" required readonly disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-6">
                        <label for="razonsocial" class="control-label" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                        <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->notaventa->cliente->razonsocial ?? '')}}" readonly disabled/>
                    </div>
                
                    <div class="form-group col-xs-12 col-sm-3">
                        <label for="fechahora" class="control-label">Fecha</label>
                        <input type="text" name="fechahora" id="fechahora" class="form-control" value="{{old('fechahora', $fecha ?? '')}}" required readonly disabled/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-5">
                        <label for="direccion" class="control-label">Dirección Princ</label>
                        <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->notaventa->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="telefono" class="control-label">Telefono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->notaventa->telefono ?? '')}}" required readonly disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-3">
                        <label for="email" class="control-label">Email</label>
                        <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->notaventa->email ?? '')}}" required readonly disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="comuna_idD" class="control-label">Comuna</label>
                        <input type="text" name="comuna_nombreD" id="comuna_nombreD" class="form-control" value="{{old('comuna_nombreD', $data->notaventa->cliente->comuna->nombre ?? '')}}" required readonly disabled/>
                    </div>
                
                </div>
                
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="vendedor_idD" class="control-label">Vendedor</label>
                        <input type="text" name="vendedor_nombreD" id="vendedor_nombreD" class="form-control" value="{{old('vendedor_nombreD', ($data->notaventa->vendedor->persona->nombre . " " . $data->notaventa->vendedor->persona->apellido) ?? '')}}" required readonly disabled/>
                    </div>
                    <!--
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="plazopago_idD" class="control-label">Plazo</label>
                        <input type="text" name="plazopago_descripcionD" id="plazopago_descripcionD" class="form-control" value="{{old('plazopago_descripcionD', $data->notaventa->plazopago->descripcion ?? '')}}" required readonly/>
                    </div>
                
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="formapago_idD" class="control-label">Forma de Pago</label>
                        <input type="text" name="formapago_descripcionD" id="formapago_descripcionD" class="form-control" value="{{old('formapago_descripcionD', $data->notaventa->formapago->descripcion ?? '')}}" required readonly/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="giro_idD" class="control-label">Giro</label>
                        <input type="text" name="giro_nombreD" id="giro_nombreD" class="form-control" value="{{old('giro_nombreD', $data->notaventa->giro->nombre ?? '')}}" required readonly/>
                    </div>
                
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="tipoentrega_id" class="control-label">Tipo Entrega</label>
                        <input type="text" name="tipoentrega_nombre" id="tipoentrega_nombre" class="form-control" value="{{old('tipoentrega_nombre', $data->notaventa->tipoentrega->nombre ?? '')}}" required readonly/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="plazoentrega" class="control-label">Plazo Ent.</label>
                        <input type="text" name="plazoentrega" id="plazoentrega" class="form-control pull-right"  value="{{old('plazoentrega', $data->plazoentrega ?? '')}}" readonly required>
                    </div>
                    -->
                    <div class="form-group col-xs-12 col-sm-3">
                        <label for="lugarentrega" class="control-label">Lugar de Entrega</label>
                        <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', $data->lugarentrega ?? '')}}" required placeholder="Lugar de Entrega" readonly required disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="comunaentrega_id" class="control-label">Comuna Entrega</label>
                        <input type="text" name="comunaentrega_nombre" id="comunaentrega_nombre" class="form-control" value="{{old('comunaentrega_nombre', $data->notaventa->comunaentrega->nombre ?? '')}}" readonly required disabled/>
                    </div>
                
                    <div class="form-group col-xs-12 col-sm-3">
                        <label for="contacto" class="control-label">Contacto</label>
                        <input type="text" name="contacto" id="contacto" class="form-control" value="{{old('contacto', $data->contacto ?? '')}}" required placeholder="Contacto Entrega" readonly required disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="contactotelf" class="control-label">Teléfono</label>
                        <input type="text" name="contactotelf" id="contactotelf" class="form-control" value="{{old('contactotelf', $data->contactotelf ?? '')}}" required placeholder="Teléfono Contacto Entrega"  readonly required disabled/>
                    </div>
                
                
                </div>
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-4">
                        <label for="contactoemail" class="control-label">Email</label>
                        <input type="email" name="contactoemail" id="contactoemail" class="form-control" value="{{old('contactoemail', $data->contactoemail ?? '')}}" required placeholder="Email Contacto Entrega" readonly required disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-4">
                        <label for="observacion" class="control-label">Observaciones</label>
                        <input type="text" name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}" placeholder="Observaciones" readonly disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="fechaestdesp" class="control-label" data-toggle='tooltip' title="Fecha estimada de Despacho">Fec Est Despacho</label>
                        <input type="text" name="fechaestdesp" id="fechaestdesp" class="form-control pull-right" value="{{old('fechaestdesp', $data->fechaestdesp ?? '')}}" required readonly disabled/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="despachoobs_id" class="control-label">Obs</label>
                        <input type="text" name="despachoobs_nombre" id="despachoobs_nombre" class="form-control pull-right" value="{{old('despachoobs_nombre', $data->notaventa->despachoobs->nombre ?? '')}}" readonly disabled/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="despachoordrecmotivo_idD" class="control-label requerido">Motivo Rechazo</label>
                        <select name="despachoordrecmotivo_id" id="despachoordrecmotivo_id" class="form-control select2" data-live-search='true' required>
                            <option value="">Seleccione...</option>
                            @foreach($despachoordrecmotivos as $despachoordrecmotivo)
                                <option
                                    value="{{$despachoordrecmotivo->id}}"
                                    @if (($aux_sta==3) and $despachoordrecmotivo->id==$despachoordrec->despachoordrecmotivo_id)
                                        {{'selected'}}
                                    @endif
                                    >
                                    {{$despachoordrecmotivo->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="solnotacred" class="control-label requerido">Nota de Credito?</label>
                        <select name="solnotacred" id="solnotacred" class="select2 form-control solnotacred"  data-live-search='true' required>
                            <option value="">Seleccione...</option>
                            <option value="1"
                                @if (($aux_sta==3) and ($despachoordrec->solnotacred=="1"))
                                    {{'selected'}}
                                @endif
                            >Si</option>
                            <option value="0"
                                @if (($aux_sta==3) and ($despachoordrec->solnotacred=="0"))
                                    {{'selected'}}
                                @endif            
                            >No</option>
                        </select>
                    </div>
                    <div class="form-group col-xs-12 col-sm-6">
                        <label for="despachoobs_id" class="control-label">Observaciones</label>
                        <input type="text" name="obs" id="obs" class="form-control pull-right" value="{{old('obs', $despachoordrec->obs ?? '')}}"/>
                    </div>
                </div>
<!--            </div>
        </div>
    </div>
-->
</div>
<div class="col-xs-12 col-sm-3">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label id="lbdocumento_id" name="lbdocumento_id" for="documento_id" class="control-label">Nro documento</label>
            <div class="input-group">
                <input type="text" name="documento_id" id="documento_id" class="form-control" value="{{old('documento_id', $despachoordrec->documento_id ?? '')}}" placeholder="Nro Orden de Compra"/>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-12">
            <label id="lbdocumento_id" name="lbdocumento_id" for="documento_file" class="control-label">Adjuntar documento</label>
            <div class="input-group">
                <input type="file" name="documento_file" id="documento_file" class="form-control" data-initial-preview='{{isset($despachoordrec->documento_file) ? Storage::url("imagenes/despachorechazo/$despachoordrec->documento_file") : ""}}' valor="{{old('documento_id', $despachoordrec->documento_file ?? '')}}" accept="image/*"/>
            </div>
        </div>
    </div>
</div>

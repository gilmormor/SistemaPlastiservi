<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            <div class="input-group">
                                <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" onkeyup="llevarMayus(this);" title="F2 Buscar" placeholder="F2 Buscar" maxlength="12" required/>
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->cliente->razonsocial ?? '')}}" readonly/>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-4">
                                <label for="direccion" class="control-label">Dirección Princ</label>
                                <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->cliente->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <label for="telefono" class="control-label requerido">Telefono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->cliente->telefono ?? '')}}" required readonly/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <label for="email" class="control-label requerido">Email</label>
                                <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->cliente->email ?? '')}}" required readonly/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <label for="comuna_nombre" class="control-label requerido">Comuna</label>
                                <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="{{old('comuna_nombre', $data->cliente->comuna->nombre ?? '')}}" required readonly/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <label for="provincia_nombre" class="control-label requerido">Provincia</label>
                                <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" value="{{old('provincia_nombre', $data->cliente->comuna->provincia->nombre ?? '')}}" required readonly/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-2">
                                <label for="vendedor_idD" class="control-label requerido">Vendedor</label>
                                <select name="vendedor_idD" id="vendedor_idD" class="form-control selectpicker vendedor_idD" data-live-search='true'  required readonly disabled>
                                    <option value="">Seleccione...</option>
                                    @foreach($tablas['vendedores'] as $vendedor)
                                        <option
                                            value="{{$vendedor->id}}"
                                            @if ((isset($data->vendedor_id)) and ($data->vendedor_id==$vendedor->id))
                                                {{'selected'}}
                                            @endif
                                            >
                                            {{$vendedor->nombre}} {{$vendedor->apellido}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>                
                </div>
            </div>
        </div>
    </div>    
</div>

@include('generales.modalpdf')
@include('generales.buscarclientebd')

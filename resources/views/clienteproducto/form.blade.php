<input type="hidden" name="aux_rut" id="aux_rut" value="{{old('aux_rut', $data->rut ?? '')}}"/>
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->id ?? '')}}"/>
<input type="hidden" name="sucursal_id" id="sucursal_id" value="{{old('sucursal_id', "1" ?? '')}}"/>
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}"/>

<div class="row">
    <div class="form-group col-xs-12 col-sm-2">
        <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" required onblur="formato_rut(this)" onfocus="eliminarFormatoRut(this);" placeholder="Ingrese RUT" maxlength="9" readonly/>
    </div>

    <div class="form-group col-xs-12 col-sm-3">
        <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
        <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->razonsocial ?? '')}}" maxlength="70" required readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-5">
        <label for="direccion" class="control-label requerido" data-toggle='tooltip' title="direccion">Dirección Principal</label>
        <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->direccion ?? '')}}" required  maxlength="200" placeholder="Ingrese Dirección" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-2">
        <label for="telefono" class="control-label requerido" data-toggle='tooltip' title="Teléfono">Teléfono</label>
        <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" maxlength="50" required placeholder="Ingrese Teléfono" readonly/>
    </div>    
</div>
<!--style="text-transform:uppercase;" Llevar a MAYUSCULA-->

<div class="row">
    <div class="form-group col-xs-12 col-sm-3">
        <label for="email" class="control-label requerido" data-toggle='tooltip' title="email">Email</label>
        <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required  maxlength="50" placeholder="Ingrese Email" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="nombrefantasia" class="control-label" data-toggle='tooltip' title="Nombre de Fantasia">Fantasia</label>
        <input type="text" name="nombrefantasia" id="nombrefantasia" class="form-control" value="{{old('nombrefantasia', $data->nombrefantasia ?? '')}}" maxlength="50" placeholder="Nombre de Fantasia" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-5">
        <label for="vendedor_id" class="control-label requerido" data-toggle='tooltip' title="Vendedores">Vendedores</label>
        <select name="vendedor_id[]" id="vendedor_id" class="form-control select2" multiple required disabled="true">
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
</div>

<div class="row">
    <div class="form-group col-xs-12 col-sm-3">
        <label for="sucursalp_id" class="control-label requerido" data-toggle='tooltip' title="Sucursal">Sucursal</label>
        <select name="sucursalp_id[]" id="sucursalp_id" class="form-control select2" multiple required disabled="true">
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
    <div class="form-group col-xs-12 col-sm-3">
        <label for="comunap_id" class="col-form-label requerido">Comuna</label>
        <select name="comunap_id" id="comunap_id" class="select2 form-control comunap_id" title='Seleccione...' required disabled="true">
            <option value="">Seleccione...</option>
            @foreach($comunas as $comuna)
                <option
                    value="{{$comuna->id}}"
                    region_id="{{$comuna->provincia->region_id}}"
                    provincia_id="{{$comuna->provincia_id}}"
                    @if (($data->comunap_id==$comuna->id))
                        {{'selected'}}
                    @endif
                    >
                    <!--{{$comuna->id}} - {{$comuna->nombre}}-->
                    {{$comuna->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="box box-danger">
    <div class="box-header with-border">
        <h3 class="box-title">Producto</h3>
        @if(can('guardar-cliente',false) == true)
            <div class="box-tools pull-right">
                <a id="btnbuscarproducto" name="btnbuscarproducto" class="btn btn-block btn-success btn-sm">
                    <i class="fa fa-fw fa-plus-circle"></i> Nuevo producto
                </a>
            </div>
        @endif
        <div class="box-body">
            <table class="table table-striped table-bordered table-hover" id="tabla-data">
                <thead>
                    <tr>
                        <th class="width70">ID</th>
                        <th>Descripcion</th>
                        <th class="tooltipsC" title="PDF Acuerdo Tecnico">PDF AT</th>
                        <th class="width70" >Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $aux_nfila = 0; $i = 0;?>
                    @foreach($data->productos as $producto)
                        <?php $aux_nfila++; ?>
                        <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                            <td>
                                {{$producto->id}}
                                <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control producto_id" value="{{$producto->id}}" valor="{{$producto->id}}" style="display:none;"/>
                            </td>
                            <td>
                                {{$producto->nombre}}
                                <input type="text" name="producto_nombre[]" id="producto_nombre{{$aux_nfila}}" class="form-control" value="{{$producto->nombre}}" style="display:none;"/>
                            </td>
                            <td>
                                @if (isset($producto->acuerdotecnico->id))
                                    <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfCOT({{$producto->acuerdotecnico->id}},1)" data-original-title="Acuerdo Técnico: {{$producto->acuerdotecnico->id}}">
                                        <i class="fa fa-fw fa-file-pdf-o"></i>
                                    </a>
                                    <input type="text" name="acuerdotecnico_id[]" id="acuerdotecnico_id{{$aux_nfila}}" class="form-control" value="{{$producto->acuerdotecnico->id}}" style="display:none;"/>
                                @endif
                            </td>
                            <td>
                                @if(can('editar-cliente-producto',false) == true)
                                    <a class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro({{$aux_nfila}})">
                                        <i class="fa fa-fw fa-trash text-danger"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        <?php $i++;?>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('generales.buscarproductobd')

<input type="text" name="ids" id="ids" value="{{$aux_cont ? $aux_cont : 0}}" style="display: none">
<input type="text" name="aux_sta" id="aux_sta" value="{{$aux_sta}}" style="display: none">
<input type="text" name="idsG" id="idsG" value="{{$aux_contG ? $aux_contG : 0}}" style="display: none">

<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="descripcion" class="col-lg-3 control-label requerido">Descripcion</label>
    <div class="col-lg-8">
    <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->descripcion ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="sta_precioxkilo" class="col-lg-3 control-label requerido">Status Precio x Kilo</label>
    <div class="col-lg-8">
        <select name="sta_precioxkilo" id="sta_precioxkilo" class="form-control select2 tipounion" required>
            <option value="">Seleccione...</option>
            <option value="0"
                @if (($aux_sta==2) and ($data->sta_precioxkilo=="0"))
                    {{'selected'}}
                @endif
            >No</option>
            <option value="1"
                @if (($aux_sta==2) and ($data->sta_precioxkilo=="1"))
                    {{'selected'}}
                @endif            
            >Si</option>
            <option value="2"
                @if (($aux_sta==2) and ($data->sta_precioxkilo=="2"))
                    {{'selected'}}
                @endif            
            >Asignar Precio al Vender</option>
        </select>
    </div>
</div>
<div class="form-group">
    <label for="precio" class="col-lg-3 control-label requerido">Precio</label>
    <div class="col-lg-8">
    <input type="text" name="precio" id="precio" class="form-control camponumerico" value="{{old('precio', $data->precio ?? '')}}" placeholder="9999999999.99" required pattern="[0-9]{0,10}.[0-9]{0,2}" maxlength="13" required/>
    </div>
</div>
<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-8">
        <select name="sucursal_id[]" id="sucursal_id" class="form-control select2" multiple required>
            @foreach($sucursales as $id => $nombre)
                <option
                    value="{{$id}}"
                    {{is_array(old('sucursal_id')) ? (in_array($id, old('sucursal_id')) ? 'selected' : '') : (isset($data) ? ($data->sucursales->firstWhere('id', $id) ? 'selected' : '') : '')}}
                    >
                    {{$nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="areaproduccion_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Area Producción">Area Producción</label>
    <div class="col-lg-8">
        <select name="areaproduccion_id" id="areaproduccion_id" class="form-control select2 areaproduccion_id" required>
            <option value="">Seleccione...</option>
            @foreach($areaproduccions as $id => $nombre)
                <option
                    value="{{$id}}"
                    @if (($aux_sta==2) and ($data->areaproduccion_id==$id))
                        {{'selected'}}
                    @endif
                    >
                    {{$nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="unidadmedida_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Unidad de Medida">Unidad de Medida</label>
    <div class="col-lg-8">
        <select name="unidadmedida_id" id="unidadmedida_id" class="form-control select2 unidadmedida_id" required>
            <option value="">Seleccione...</option>
            @foreach($unidadmedidas as $id => $descripcion)
                <option
                    value="{{$id}}"
                    @if (($aux_sta==2) and ($data->unidadmedida_id==$id))
                        {{'selected'}}
                    @endif
                    >
                    {{$descripcion}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="unidadmedidafact_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Unidad de Medida">Unidad de Medida</label>
    <div class="col-lg-8">
        <select name="unidadmedidafact_id" id="unidadmedidafact_id" class="form-control select2 unidadmedidafact_id" required>
            <option value="">Seleccione...</option>
            @foreach($unidadmedidas as $id => $descripcion)
                <option
                    value="{{$id}}"
                    @if (($aux_sta==2) and ($data->unidadmedidafact_id==$id))
                        {{'selected'}}
                    @endif
                    >
                    {{$descripcion}}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Clases</h3>
        </div>
        <table class="table table-striped table-bordered table-hover" id="dataTables">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Longitud</th>
                    <th></th>
                    <th style="display: none">id</th>
                </tr>
            </thead>
            <tbody id="tbody">
                @if ($aux_sta==2)
                    <?php $aux_nfila = 0; ?>
                    @foreach ($claseprods as $claseprod)
                        <?php $aux_nfila++; ?>
                        <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                            <td><input type="text" name="cla_nombre[]" id="cla_nombre{{$aux_nfila}}" class="form-control" value="{{$claseprod->cla_nombre}}"/></td>
                            <td><input type="text" name="cla_descripcion[]" id="cla_descripcion{{$aux_nfila}}" class="form-control" value="{{$claseprod->cla_descripcion}}"/></td>
                            <td><input type="text" name="cla_longitud[]" id="cla_longitud{{$aux_nfila}}" class="form-control camponumerico" value="{{$claseprod->cla_longitud}}"/></td>
                            <td> 
                                <a onclick="agregarEliminar('{{$aux_nfila}}')" class="btn-accion-tabla" title="Eliminar" data-original-title="Eliminar" id="agregar_reg{{$aux_nfila}}" name="agregar_reg{{$aux_nfila}}" valor="fa-plus">
                                    <i class="fa fa-fw fa-minus"></i>
                                </a>
                            </td>
                            <td style="display: none"><input type="text" name="cla_id[]" id="cla_id{{$aux_nfila}}" class="form-control camponumerico" value="{{$claseprod->cla_id}}"/></td>
                        </tr>
                    @endforeach            
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-6">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Grupos</h3>
        </div>
        <table class="table table-striped table-bordered table-hover" id="tablagrupos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th></th>
                    <th style="display: none">id</th>
                </tr>
            </thead>
            <tbody id="tbody">
                @if ($aux_sta==2)
                    <?php $aux_nfila = 0; ?>
                    @foreach ($grupoprods as $grupoprod)
                        <?php $aux_nfila++; ?>
                        <tr name="filaG{{$aux_nfila}}" id="filaG{{$aux_nfila}}">
                            <td><input type="text" name="gru_nombre[]" id="cla_nombre{{$aux_nfila}}" class="form-control" value="{{$grupoprod->gru_nombre}}"/></td>
                            <td><input type="text" name="gru_descripcion[]" id="gru_descripcion{{$aux_nfila}}" class="form-control" value="{{$grupoprod->gru_descripcion}}"/></td>
                            <td> 
                                <a onclick="agregarEliminarG('{{$aux_nfila}}')" class="btn-accion-tabla" title="Eliminar" data-original-title="Eliminar" id="agregar_regG{{$aux_nfila}}" name="agregar_regG{{$aux_nfila}}" valor="fa-plus">
                                    <i class="fa fa-fw fa-minus"></i>
                                </a>
                            </td>
                            <td style="display: none"><input type="text" name="gru_id[]" id="gru_id{{$aux_nfila}}" class="form-control camponumerico" value="{{$grupoprod->gru_id}}"/></td>
                        </tr>
                    @endforeach            
                @endif
            </tbody>
        </table>
    </div>
</div>
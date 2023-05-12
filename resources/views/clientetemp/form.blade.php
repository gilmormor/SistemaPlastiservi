<div class="form-group">
    <label for="rut" class="col-lg-3 control-label requerido">RUT</label>
    <div class="col-lg-9">
        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" required readonly/>
    </div>
</div>
<div class="form-group">
    <label for="razonsocial" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-9">
        <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->razonsocial ?? '')}}" required readonly/>
    </div>
</div>

<div class="form-group">
    <label id="lblvendedor_id" name="lblvendedor_id" for="vendedor_id" class="col-lg-3 control-label requerido">Vendedor</label>
    <div class="col-lg-9">
        <select name="vendedor_id" id="vendedor_id" class="form-control select2 vendedor_id" data-live-search='true' required>
            <option value=''>Seleccione...</option>
                @foreach($tablas['vendedores'] as $vendedor)
                    <option
                        value="{{$vendedor->id}}"
                        @if (isset($data) and ($data->vendedor_id==$vendedor->id))
                            {{'selected'}}
                        @endif
                        >{{$vendedor->nombre . " " . $vendedor->apellido}}</option>
                @endforeach                    
        </select>
    </div>
</div>
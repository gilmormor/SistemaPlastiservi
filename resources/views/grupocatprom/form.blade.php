<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="100" required/>
    </div>
</div>

<div class="form-group">
    <label for="categoriaprod_id" class="col-lg-3 control-label requerido">Categoria</label>
    <div class="col-lg-4">
        <select name="categoriaprod_id[]" id="categoriaprod_id" class="form-control select2" multiple required>
            @foreach($categoriaprods as $categoriaprod)
                <option
                    value="{{$categoriaprod->id}}"
                    {{is_array(old('categoriaprod_id')) ? (in_array($categoriaprod->id, old('categoriaprod_id')) ? 'selected' : '') : (isset($data) ? ($data->categoriaprods->firstWhere('id', $categoriaprod->id) ? 'selected' : '') : '')}}
                    >{{$categoriaprod->nombre}}</option>
            @endforeach
        </select>
    </div>
</div>

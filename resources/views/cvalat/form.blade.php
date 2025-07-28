<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="50" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripcion</label>
    <div class="col-lg-4">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" maxlength="100" required/>
    </div>
</div>
<div class="form-group">
    <label for="orden" class="col-lg-3 control-label requerido">Orden</label>
    <div class="col-lg-4">
        <input type="text" name="orden" id="orden" class="form-control" value="{{old('orden', $data->orden ?? '')}}" maxlength="4" required/>
    </div>
</div>

<hr>
<div class="form-group">
    <label class="col-lg-3 control-label">Detalles</label>
    <div class="col-lg-9">
        <table class="table table-bordered" id="tabla-detalles">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Orden</th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($data) && $data->cvalatdets)
                    @foreach($data->cvalatdets as $det)
                        <tr>
                            <td>
                                <input type="hidden" name="detalles[{{$loop->index}}][id]" value="{{ $det->id }}">
                                <input type="text" name="detalles[{{$loop->index}}][nombredet]" class="form-control" value="{{ old('detalles.'.$loop->index.'.nombredet', $det->nombre) }}" required>
                            </td>
                            <td>
                                <input type="text" name="detalles[{{$loop->index}}][descdet]" class="form-control" value="{{ old('detalles.'.$loop->index.'.descdet', $det->desc) }}" required>
                            </td>
                            <td>
                                <input type="text" name="detalles[{{$loop->index}}][ordendet]" class="form-control" value="{{ old('detalles.'.$loop->index.'.ordendet', $det->orden) }}" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-eliminar-detalle"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <button type="button" class="btn btn-success btn-sm" id="btn-agregar-detalle"><i class="fa fa-plus"></i> Agregar detalle</button>
    </div>
</div>
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-9">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" maxlength="60" required/>
    </div>
</div>

{{-- <hr>
<div class="form-group">
    <label class="col-lg-3 control-label">Detalles</label>
    <div class="col-lg-9">
        <table class="table table-bordered" id="tabla-detalles">
            <thead>
                <tr>
                    <th>Descripción Detalle</th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($data) && $data->codigodet)
                    @foreach($data->codigodet as $det)
                        <tr>
                            <td>
                                <input type="text" name="detalles[][descdet]" class="form-control" value="{{ old('detalles.'.$loop->index.'.descdet', $det->descdet) }}" required>
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
</div> --}}

<hr>
<div class="form-group">
    <label class="col-lg-3 control-label">Detalles</label>
    <div class="col-lg-9">
        <table class="table table-bordered" id="tabla-detalles">
            <thead>
                <tr>
                    <th>Descripción Detalle</th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($data) && $data->codigodet)
                    @foreach($data->codigodet as $det)
                        <tr>
                            <td>
                                <input type="hidden" name="detalles[{{$loop->index}}][id]" value="{{ $det->id }}">
                                <input type="text" name="detalles[{{$loop->index}}][descdet]" class="form-control" value="{{ old('detalles.'.$loop->index.'.descdet', $det->descdet) }}" required>
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
<div class="form-group">
    <label for="notaventa_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Nota Venta ID">Nota Venta ID</label>
        @if ($aux_editar == 0)
            <div class="col-lg-2">
                <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{old('notaventa_id', $data->notaventa_id ?? '')}}" maxlength="12" required placeholder="F2 Buscar"/>
            </div>
            <div class="col-lg-1">
                <button class="btn btn-default" type="button" id="btnbuscarNotaVenta" name="btnbuscarNotaVenta" data-toggle='tooltip' title="Buscar">Buscar</button>
            </div>
        @else
            <div class="col-lg-2">
                <input type="hidden" name="notaventa_id" id="notaventa_id" value="{{old('notaventa_id', $data->notaventa_id ?? '')}}">
                <input type="text" name="notaventa_id1" id="notaventa_id1" class="form-control" value="{{old('notaventa_id1', $data->notaventa_id ?? '')}}" maxlength="12" required disabled readonly/>
            </div>
        @endif
        <div class="col-lg-2" id="vistaprevNV" style="display:none">
            <a id="vpnv1" class='btn-accion-tabla btn-sm tooltipsC' title='Nota de venta' onclick="genpdfNV(2720,1)">
                <i class='fa fa-fw fa-file-pdf-o'></i>
            </a>
            <a id="vpnv2" class='btn-accion-tabla btn-sm tooltipsC' title='Precio x Kg' onclick="genpdfNV(2720,2)">
                <i class='fa fa-fw fa-file-pdf-o'></i>
            </a>
        </div>
</div>

<div class="form-group">
    <label for="observacion" class="col-lg-3 control-label requerido">Observacion</label>
    <div class="col-lg-8">
        <textarea name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}" required>{{old('observacion', $data->observacion ?? '')}}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="codigodet_id" class="col-lg-3 control-label requerido">Motivo</label>
    <div class="col-lg-8">
        <select name="codigodet_id" id="codigodet_id" class="form-control select2 codigodet_id" data-live-search='true' required>
            <option value="">Seleccione...</option>
            @foreach($codigodets as $codigodet)
                <option
                    value="{{$codigodet->id}}"
                    @if (isset($data->codigodet_id) and $codigodet->id==$data->codigodet_id)
                        {{'selected'}}
                    @endif
                    >{{$codigodet->descdet}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-xs-12 col-md-8 col-sm-8">
    
</div>

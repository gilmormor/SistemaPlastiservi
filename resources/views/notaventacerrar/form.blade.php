<div class="form-group">
    <label for="notaventa_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Nota Venta ID">Nota Venta ID</label>
    @if ($aux_editar == 0)
        <div class="col-lg-3">
            <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{old('notaventa_id', $data->notaventa_id ?? '')}}" maxlength="12" required placeholder="F2 Buscar"/>
        </div>
        <div class="col-lg-3">
            <button class="btn btn-default" type="button" id="btnbuscarNotaVenta" name="btnbuscarNotaVenta" data-toggle='tooltip' title="Buscar">Buscar</button>
        </div>
        @else
        <div class="col-lg-3">
            <input type="hidden" name="notaventa_id" id="notaventa_id" value="{{old('notaventa_id', $data->notaventa_id ?? '')}}">
            <input type="text" name="notaventa_id1" id="notaventa_id1" class="form-control" value="{{old('notaventa_id1', $data->notaventa_id ?? '')}}" maxlength="12" required disabled readonly/>
        </div>

    @endif
</div>

<div class="form-group">
    <label for="observacion" class="col-lg-3 control-label requerido">Observacion</label>
    <div class="col-lg-8">
        <textarea name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}" required>{{old('observacion', $data->observacion ?? '')}}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="motcierre_id" class="col-lg-3 control-label requerido">Motivo</label>
    <div class="col-lg-8">
        <!--<select name="motcierre_id" id="motcierre_id" class="selectpicker form-control motcierre_id" required>-->
        <?php
            $aux_selected0 = "";
            $aux_selected1 = "";
            $aux_selected2 = "";
            $aux_selected3 = "";
            if($aux_editar == 0){
                $aux_selected0 = "selected";
            }else{
                switch ($data->motcierre_id) {
                case 1:
                    $aux_selected1 = "selected";
                    break;
                case 2:
                    $aux_selected2 = "selected";
                    break;
                case 3:
                    $aux_selected3 = "selected";
                    break;
                }
            }
        ?>
        <select name="motcierre_id" id="motcierre_id" class="form-control select2 motcierre_id" required>
            <option value="" {{$aux_selected0}}>Seleccione...</option>
            <option value="1" {{$aux_selected1}}>Por Fecha</option>
            <option value="2" {{$aux_selected2}}>Por Precio</option>
            <option value="3" {{$aux_selected3}}>Por solicitud cliente</option>
        </select>
    </div>
</div>
<div class="col-xs-12 col-md-8 col-sm-8">
    
</div>

@include('generales.buscarnotaventa')
@include('generales.buscarcliente')
@include('generales.modalpdf')
@include('generales.verpdf')
@include('generales.listarorddesp')
@include('generales.actualizarFileOC')
@include('generales.buscarproducto')

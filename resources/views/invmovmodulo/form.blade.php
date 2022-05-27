<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required maxlength="100"/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required maxlength="300"/>
    </div>
</div>
<div class="form-group">
    <label for="cod" class="col-lg-3 control-label requerido">Código</label>
    <div class="col-lg-8">
        <input type="text" name="cod" id="cod" class="form-control" value="{{old('cod', $data->cod ?? '')}}" required maxlength="10"/>
    </div>
</div>
<!--
<div class="form-group">
    <label for="invmovmodulobodsal_id" class="col-lg-3 control-label requerido">Bodegas Salida</label>
    <div class="col-lg-8">
        <select name="invmovmodulobodsal_id[]" id="invmovmodulobodsal_id" class="form-control select2" multiple required>
            @foreach($invbodegas as $invbodega)
                <option
                    value="{{$invbodega->id}}"
                    {{is_array(old('invmovmodulobodsal_id')) ? (in_array($invbodega->id, old('invmovmodulobodsal_id')) ? 'selected' : '') : (isset($data) ? ($data->invmovmodulobodsals->firstWhere('id', $invbodega->id) ? 'selected' : '') : '')}}
                    >
                    {{$invbodega->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
-->
<div class="form-group">
    <label for="invmovmodulobodsal_id" class="col-lg-3 control-label requerido">Bodegas Salida</label>
    <div class="col-lg-8">
        <select name="invmovmodulobodsal_id[]" id="invmovmodulobosal_id" class="selectpicker form-control invmovmodulobodsal_id" data-live-search='true' multiple data-actions-box='true'>
        <!--<select name="invmovmodulobodsal_id[]" id="invmovmodulobodsal_id" class="form-control select2" multiple required>-->
            @foreach($invbodegas as $invbodega)
                <option
                    value="{{$invbodega->id}}"
                    {{is_array(old('invmovmodulobodsal_id')) ? (in_array($invbodega->id, old('invmovmodulobodsal_id')) ? 'selected' : '') : (isset($data) ? ($data->invmovmodulobodsals->firstWhere('id', $invbodega->id) ? 'selected' : '') : '')}}
                    >
                    {{$invbodega->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>


<!--
<div class="form-group">
    <label for="invmovmodulobodent_id" class="col-lg-3 control-label">Bodegas Entrada</label>
    <div class="col-lg-8">
        <select name="invmovmodulobodent_id[]" id="invmovmodulobodent_id" class="form-control select2" multiple>
            @foreach($invbodegas as $invbodega)
                <option
                    value="{{$invbodega->id}}"
                    {{is_array(old('invmovmodulobodent_id')) ? (in_array($invbodega->id, old('invmovmodulobodent_id')) ? 'selected' : '') : (isset($data) ? ($data->invmovmodulobodents->firstWhere('id', $invbodega->id) ? 'selected' : '') : '')}}
                    >
                    {{$invbodega->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
-->
<div class="form-group">
    <label for="invmovmodulobodent_id" class="col-lg-3 control-label">Bodegas Entrada</label>
    <div class="col-lg-8">
        <select name="invmovmodulobodent_id[]" id="invmovmodulobodent_id" class="selectpicker form-control invmovmodulobodent_id" data-live-search='true' multiple data-actions-box='true'>
        <!--<select name="invmovmodulobodent_id[]" id="invmovmodulobodent_id" class="form-control select2" multiple>-->
            @foreach($invbodegas as $invbodega)
                <option
                    value="{{$invbodega->id}}"
                    {{is_array(old('invmovmodulobodent_id')) ? (in_array($invbodega->id, old('invmovmodulobodent_id')) ? 'selected' : '') : (isset($data) ? ($data->invmovmodulobodents->firstWhere('id', $invbodega->id) ? 'selected' : '') : '')}}
                    >
                    {{$invbodega->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>


<input type='hidden' id='activo' name='activo' value="{{old('activo', $data->activo ?? '')}}">

<div class="form-group">
    <label for="doc" class="col-lg-3 control-label requerido">Documento</label>
    <div class="col-lg-9">
        <input type="text" name="doc" id="doc" class="form-control" value="{{old('doc', $data->doc ?? '')}}" required maxlength="4"/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-9">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required maxlength="50"/>
    </div>
</div>
<div class="form-group">
    <label for="ultfoliouti" class="col-lg-3 control-label requerido" data-toggle='tooltip' title='Ultimo Folio Usado'>Ultimo Folio</label>
    <div class="col-lg-9">
        <input type="text" name="ultfoliouti" id="ultfoliouti" class="form-control" value="{{old('ultfoliouti', $data->ultfoliouti ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="ultfoliohab" class="col-lg-3 control-label requerido" data-toggle='tooltip' title='Ultimo Folio Habilitado'>Ultimo Folio Hab</label>
    <div class="col-lg-9">
        <input type="text" name="ultfoliohab" id="ultfoliohab" class="form-control" value="{{old('ultfoliohab', $data->ultfoliohab ?? '')}}" required/>
    </div>
</div>

<div class="form-group">
    <label for="maxitemxdoc" class="col-lg-3 control-label requerido" data-toggle='tooltip' title='Maximo de items x Dom4cumento'>Maximo item x Doc</label>
    <div class="col-lg-9">
        <input type="text" name="maxitemxdoc" id="maxitemxdoc" class="form-control" value="{{old('maxitemxdoc', $data->maxitemxdoc ?? '')}}" required/>
    </div>
</div>

<div class="form-group">
    <label for="folmindisp" class="col-lg-3 control-label requerido" data-toggle='tooltip' title='Maximo de items x Dom4cumento'>Maximo item x Doc</label>
    <div class="col-lg-9">
        <input type="text" name="folmindisp" id="folmindisp" class="form-control" value="{{old('folmindisp', $data->folmindisp ?? '')}}" required/>
    </div>
</div>

<div class="row">
    <div class='checkbox col-md-3 col-md-offset-3'>
        <label style='font-size: 1.2em' data-toggle='tooltip' title='Activo o Inactivo'>
            <input type='checkbox' id='activoT' name='activoT'>
            <span class='cr'><i class='cr-icon fa fa-check'></i></span>
            Activo
        </label>
    </div>
</div>
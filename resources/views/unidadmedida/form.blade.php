<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="descripcion" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
        <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->descripcion ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <div class="checkbox">
        <label class="col-sm-offset-3" style="font-size: 1.2em;display:flex;align-items: center;">
            <input type="checkbox" id="aux_mostrarfact" name="aux_mostrarfact">
            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            Mostrar en Factura
        </label>
    </div>
</div>
<input type="hidden" name="mostrarfact" id="mostrarfact" value="{{old('mostrarfact', $data->mostrarfact ?? '0')}}">

<div class="form-group">
    <div class="checkbox">
        <label class="col-sm-offset-3" style="font-size: 1.2em;display:flex;align-items: center;">
            <input type="checkbox" id="aux_agrupado" name="aux_agrupado">
            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            Agrupado?
        </label>
    </div>
</div>
<input type="hidden" name="agrupado" id="agrupado" value="{{old('agrupado', $data->agrupado ?? '0')}}">

<div class="form-group">
    <div class="checkbox">
        <label class="col-sm-offset-3" style="font-size: 1.2em;display:flex;align-items: center;">
            <input type="checkbox" id="aux_usa_produccion" name="aux_usa_produccion">
            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            Usar en Producción
        </label>
        {{-- Marca las unidades que pueden ser salida de una etapa de produccion.
             Las dimensionales -pulgadas, milimetros- no deben marcarse: miden el
             producto, no cuentan cantidad, y no sirven como unidad de inventario. --}}
        <p class="help-block col-sm-offset-3" style="margin-top:0;">
            Permite elegirla como unidad de salida de una etapa de producción.
        </p>
    </div>
</div>
<input type="hidden" name="usa_produccion" id="usa_produccion" value="{{old('usa_produccion', $data->usa_produccion ?? '0')}}">
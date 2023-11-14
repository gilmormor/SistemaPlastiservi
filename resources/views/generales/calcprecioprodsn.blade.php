<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title">Producto</h3>
        </div>
        <div class="modal-body">
            <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
            <input type="hidden" name="precioxkilorealM" id="precioxkilorealM" value="0">
            <input type="hidden" name="stakilos" id="stakilos" value="0">
            <input type="hidden" name="categoriaprod_id" id="categoriaprod_id">
            <input type="hidden" name="acuerdotecnico_id" id="acuerdotecnico_id">
            <input type="hidden" name="at_unidadmedida_idM" id="at_unidadmedida_idM">
            <input type="hidden" name="tipoprodM" id="tipoprodM">
            <div class="row">
                <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                    <label for="producto_idM" class="control-label" title="F2 Buscar">Producto</label>
                    <div class="input-group">
                        <input type="text" name="producto_idM" id="producto_idM" class="form-control" tipoval="numericootro" value="{{old('producto_idM', $clienteselec[0]->producto_idM ?? '')}}" placeholder="F2 Buscar"/>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="btnbuscarproducto" name="btnbuscarproducto" data-toggle='tooltip' title="Buscar">Buscar</button>
                        </span>
                    </div>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2" style="display:none;">
                    <label for="codintprodM" class="control-label" data-toggle='tooltip'>Cod Inerno</label>
                    <input type="text" name="codintprodM" id="codintprodM" class="form-control" value="{{old('codintprodM', $data->codintprodM ?? '')}}" placeholder="Cod Interno Prducto" disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-5">
                    <label for="nombreprodM" class="control-label" data-toggle='tooltip'>Nombre Prod</label>
                    <input type="text" name="nombreprodM" id="nombreprodM" class="form-control" value="{{old('nombreprodM', $data->nombreprodM ?? '')}}" placeholder="Nombre Producto" disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="stockM" class="control-label" data-toggle='tooltip'>Stock</label>
                    <input type="text" name="stockM" id="stockM" class="form-control" style="text-align:right" value="{{old('stockM', $data->stockM ?? '')}}" placeholder="Stock" disabled/>
                    <span class="help-block"></span>
                </div>

                <div class="col-xs-12 col-sm-2" id="mostunimed1" classorig="col-xs-12 col-sm-2" style="display:none;">
                    <label for="unidadmedida_idM" class="control-label unidadmedidacombo" >UniMedida</label>
                    <select name="unidadmedida_idM" id="unidadmedida_idM"  class="selectpicker form-control unidadmedida_idM" data-live-search='true'>
                        <option value="">Seleccione...</option>
                        @foreach($tablas['unidadmedida'] as $unidadmedida)
                            <option
                                value="{{$unidadmedida->id}}"
                                >
                                {{$unidadmedida->nombre}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-12 col-sm-2" id="mostunimed0" classorig="col-xs-12 col-sm-2" style="display:none;">
                    <label for="unidadmedida_textoM" class="control-label unidadmedidatexto" >UniMedida</label>
                    <input type="text" name="unidadmedida_textoM" id="unidadmedida_textoM" class="form-control unidadmedidatexto" disabled readonly/>
                </div>

            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                    <label for="cantM" class="control-label" data-toggle='tooltip'>Cant</label>
                    <input type="text" name="cantM" id="cantM" class="form-control requeridos" tipoval="numerico" value="{{old('cantM', $data->cantM ?? '')}}" placeholder="Cant"/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                    <label for="descuentoM" class="control-label" data-toggle='tooltip'>Descuento</label>
                    <select name="descuentoM" id="descuentoM" class="selectpicker form-control descuentoM" tipoval="combobox" data-live-search='true'>
                        <option porc="0" value=1>0%</option>
                        <option porc="0.005" value=0.95>0.5%</option>
                        <option porc="0.010" value=0.90>1.0%</option>
                        <option porc="0.015" value=0.85>1.5%</option>
                        <option porc="0.020" value=0.80>2.0%</option>
<!--
                        <option porc="0.25" value=0.75>25%</option>
                        <option porc="0.30" value=0.70>30%</option>
                        <option porc="0.35" value=0.65>35%</option>
                        <option porc="0.40" value=0.60>40%</option>
                        <option porc="0.45" value=0.55>45%</option>
                        <option porc="0.50" value=0.50>50%</option>

                        <option porc="0.55" value=0.45>55%</option>
                        <option porc="0.60" value=0.40>60%</option>
                        <option porc="0.65" value=0.35>65%</option>
                        <option porc="0.70" value=0.30>70%</option>
                        <option porc="0.75" value=0.25>75%</option>
                        <option porc="0.80" value=0.20>80%</option>
                        <option porc="0.85" value=0.15>85%</option>
                        <option porc="0.90" value=0.10>90%</option>
                        <option porc="0.95" value=0.05>95%</option>
                        <option porc="0.100" value=0.0>100%</option>
-->
                    </select>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                    <label for="precioM" class="control-label" data-toggle='tooltip'>Precio Kg</label>
                    <input type="text" name="precioM" id="precioM" class="form-control requeridos" tipoval="numerico" style="text-align:right" value="{{old('precioM', $data->precio ?? '')}}" valor="0.00" placeholder="Precio Kg"/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                    <label for="precionetoM" class="control-label" data-toggle='tooltip'>PrecioUnit</label>
                    <input type="text" name="precionetoM" style="text-align:right" id="precionetoM" class="form-control numericoblanco requeridos" tipoval="texto" value="{{old('precionetoM', $data->precioneto ?? '')}}" placeholder="PrecioUnit" valor="0.00"/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                    <label for="totalkilosM" class="control-label" data-toggle='tooltip'>Total Kg</label>
                    <input type="text" name="totalkilosM" style="text-align:right" id="totalkilosM" class="form-control numericoblanco" value="{{old('totalkilosM', $data->totalkilosM ?? '')}}" valor="0.00" tipoval="texto" placeholder="Total Kilos" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="subtotalM" class="control-label" data-toggle='tooltip'>SubTotal</label>
                    <input type="text" name="subtotalM" style="text-align:right" id="subtotalM" class="form-control" value="{{old('subtotalM', $data->subtotalM ?? '')}}" valor="0.00" placeholder="SubTotal" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="row mostdatosad0" style="display:none;">
                <div class="col-xs-12 col-sm-2">
                    <label for="diamextmmM" class="control-label" data-toggle='tooltip'>Diam/Ancho</label>
                    <input type="text" name="diamextmmM" id="diamextmmM" class="form-control" value="{{old('diamextmmM', $data->diametro ?? '')}}" placeholder="Diametro" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="longM" class="control-label" data-toggle='tooltip'>Largo</label>
                    <input type="text" name="longM" id="longM" class="form-control" value="{{old('longM', $data->longM ?? '')}}" placeholder="Largo" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="espesorM" class="control-label" data-toggle='tooltip'>Espesor</label>
                    <input type="text" name="espesorM" id="espesorM" class="form-control" value="{{old('espesorM', $data->espesorM ?? '')}}" placeholder="Espesor" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="cla_nombreM" class="control-label" data-toggle='tooltip'>Clase/Sello</label>
                    <input type="text" name="cla_nombreM" id="cla_nombreM" class="form-control" value="{{old('cla_nombreM', $data->cla_nombreM ?? '')}}" placeholder="Clase" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="tipounionM" class="control-label" data-toggle='tooltip'>TUnión</label>
                    <input type="text" name="tipounionM" id="tipounionM" class="form-control" value="{{old('tipounionM', $data->tipounionM ?? '')}}" placeholder="TUnión" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="pesoM" class="control-label" data-toggle='tooltip'>Peso</label>
                    <input type="text" name="pesoM" id="pesoM" class="form-control" value="{{old('pesoM', $data->pesoM ?? '')}}" placeholder="Peso" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="row mostdatosad1" style="display:none;">
                <div class="col-xs-12 col-sm-2">
                    <label for="anchoM" class="control-label" data-toggle='tooltip'>Ancho</label>
                    <input type="text" name="anchoM" id="anchoM" style="text-align:right" valor="" class="form-control numerico" value="{{old('anchoM', $data->anchoM ?? '')}}" placeholder="0.00"/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="largoM" class="control-label" data-toggle='tooltip'>Largo</label>
                    <input type="text" name="largoM" id="largoM" style="text-align:right" valor="" class="form-control numerico" value="{{old('largoM', $data->largoM ?? '')}}" placeholder="0.00"/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="espesor1M" class="control-label" data-toggle='tooltip'>Espesor</label>
                    <input type="text" name="espesor1M" id="espesor1M" style="text-align:right" valor="" class="form-control numerico4d" value="{{old('espesor1M', $data->espesor1M ?? '')}}" placeholder="0.0000"/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <label for="obsM" class="control-label" data-toggle='tooltip' title="Observación">Observación</label>
                    <textarea name="obsM" id="obsM" class="form-control" value="{{old('obsM', $data->obsM ?? '')}}">{{old('obsM', $data->obsM ?? '')}}</textarea>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarM" name="btnGuardarM" title="Guardar">Guardar</button>
        </div>
        </div>
        
    </div>
</div>
<input type="hidden" name="dte_id" id="dte_id">
<input type="hidden" name="dte_id" id="aux_fechaphp">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id">
<input type='hidden' id="aux_obs" name="aux_obs">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{old('aux_iva', $tablas['empresa']->iva ?? '')}}">
<input type="hidden" name="updated_at" id="updated_at">

<?php 
    $aux_rut = "";
    if(isset($data)){
        $aux_rut = number_format( substr ( $data->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->cliente->rut, strlen($data->cliente->rut) -1 , 1 );
    }
?>

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="nrodocto" class="control-label requerido">Factura</label>
                            <input type="text" name="nrodocto" id="nrodocto" class="form-control" style="padding-left: 4px;padding-right: 4px;" required/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            <input type="text" name="rut" id="rut" class="form-control" readonly disabled/>
                            <!--
                            @if (isset($data))
                                <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $aux_rut ?? '')}}" readonly disabled/>
                            @else
                                <div class="input-group">
                                    <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" onkeyup="llevarMayus(this);" title="F2 Buscar" placeholder="F2 Buscar" maxlength="12" required/>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                                    </span>
                                </div>
                                
                            @endif
                            -->
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="direccion" class="control-label">Dirección Princ</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" required placeholder="Dirección principal" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="telefono" class="control-label requerido">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" style="padding-left: 4px;padding-right: 4px;" required readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="email" class="control-label requerido">Email</label>
                            <input type="text" name="email" id="email" class="form-control" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="comuna_nombre" class="control-label requerido">Comuna</label>
                            <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="provincia_nombre" class="control-label requerido">Provincia</label>
                            <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="formapago_desc" class="control-label requerido">Forma de Pago</label>
                            <input type="text" name="formapago_desc" id="formapago_desc" class="form-control" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="plazopago" class="control-label requerido">Plazo Pago</label>
                            <input type="text" name="plazopago" id="plazopago" class="form-control" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fchemis" class="control-label requerido">Fecha Emision</label>
                            <input type="text" name="fchemis" id="fchemis" class="form-control pull-right datepicker" readonly required>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fchvenc" class="control-label requerido">F. Venc</label>
                            <input type="text" name="fchvenc" id="fchvenc" class="form-control" required readonly/>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="vendedor_id" class="control-label requerido">Vendedor</label>
                            <select name="vendedor_id" id="vendedor_id" class="form-control select2 vendedor_id" data-live-search='true' required readonly disabled>
                                <option value="">Seleccione...</option>
                                @foreach($tablas['vendedores'] as $vendedor)
                                    <option
                                        value="{{$vendedor->id}}"
                                        >{{$vendedor->nombre}} {{$vendedor->apellido}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="centroeconomico_id" class="control-label requerido">Centro Economico</label>
                            <select name="centroeconomico_id" id="centroeconomico_id" class="form-control select2 centroeconomico_id" data-live-search='true' required readonly disabled>
                                <option value="">Seleccione...</option>
                                @foreach($centroeconomicos as $centroeconomico)
                                    <option
                                        value="{{$centroeconomico->id}}"
                                        >{{$centroeconomico->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="hep" class="control-label">Num Atencion o Hep</label>
                            <input type="text" name="hep" id="hep" class="form-control" maxlength="12" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label for="obs" class="control-label">Observaciones</label>
                            <textarea class="form-control" name="obs" id="obs" placeholder="Observación" maxlength="90" readonly></textarea>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </div>    
</div>
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
    <input type="hidden" name="total" id="total" class="form-control" style="text-align:right;" readonly required>
    <input type="hidden" name="totalini" id="totalini" value="0" valor="0" style="display:none;">
</div>
<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="text-align:center;" class="width30">item</th>
                            <th class="width30 tooltipsC" title="Código Producto">CodProd</th>
                            <th class="width80" style="text-align:right;">Cant</th>
                            <th class="tooltipsC width100" title="Unidad de Medida">UniMed</th>
                            <th>Nombre</th>
                            <th class="width100" style="text-align:right;">Kilos</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th class="width100" style="text-align:right;">PUnit</th>
                            <th style="display:none;">V Kilo</th>
                            <th class="width100" style="text-align:right;">Sub Total</th>
                            <th style="display:none;">Sub Total</th>
                            <th class="width30" >Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux_nfila = 0; $i = 0;
                            $aux_Tsubtotal = 0;
                            $aux_Tcant = 0;
                            $aux_Tkilos = 0;
                            $aux_Tiva = 0;
                        ?>
                    </tbody>
                    <tfoot style="display:none;" id="foottotal" name="foottotal">
                        <div id="foot">
                            <tr id="trneto" name="trneto">
                                <th colspan="2" style="text-align:right">
                                    <b>Totales:</b>
                                </th>
                                <th id="Tcant" name="Tcant" style="text-align:right">
                                    {{$aux_Tcant}}
                                </th>
                                <th colspan="2" style="text-align:right"><b>Total Kg</b></th>
                                <th id="totalkg" name="totalkg" style="text-align:right" valor="{{$aux_Tkilos}}">{{number_format($aux_Tkilos, 2, ',', '.')}}</th>
                                <th style="text-align:right"><b>Neto</b></th>
                                <th id="tdneto" name="tdneto" style="text-align:right">{{number_format($aux_Tsubtotal, 0, ',', '.')}}</th>
                            </tr>
                            <?php 
                                $aux_Tiva = round(($tablas['empresa']->iva * $aux_Tsubtotal/100));
                                $aux_total = round($aux_Tsubtotal + $aux_Tiva);
                            ?>
                            <tr id="triva" name="triva">
                                <th colspan="7" style="text-align:right"><b>IVA {{$tablas['empresa']->iva}}%</b></th>
                                <th id="tdiva" name="tdiva" style="text-align:right">{{number_format($aux_Tiva, 0, ',', '.')}}</th>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <th colspan="7" style="text-align:right"><b>Total</b></th>
                                <th id="tdtotal" name="tdtotal" style="text-align:right">{{number_format($aux_total, 0, ',', '.')}}</th>
                            </tr>
                        </div>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@include('generales.modalpdf')
@include('generales.buscarclientebd')
@include('generales.buscardteguiadesp')


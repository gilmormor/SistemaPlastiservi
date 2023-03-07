<?php
    use App\Models\CategoriaGrupoValMes;
    use App\Models\InvMov;
    $aux_mesanno = CategoriaGrupoValMes::mesanno(date("Y") . date("m"));
?>

<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="nombre" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Nombre">Nombre</label>
        <div class="col-lg-9">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="descripcion" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Descripción">Descripción</label>
        <div class="col-lg-9">
        <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->descripcion ?? '')}}" required/>
        </div>
    </div>
</div>
<div class="row">
    @if ($aux_sta==1)
        <div class="form-group col-xs-12 col-sm-6">
            <label for="categoriaprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Categoría">Categoría</label>
            <div class="col-lg-9">
                <select name="categoriaprod_id" id="categoriaprod_id" class="form-control select2 categoriaprod_id" required>
                    <option value="" precio="0">Seleccione...</option>
                    @foreach($categoriaprods as $categoriaprod)
                        <option
                            value="{{$categoriaprod->id}}" precio="{{$categoriaprod->precio}}">
                            {{$categoriaprod->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="claseprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Clase">Clase</label>
            <div class="col-lg-9">
                <select name="claseprod_id" id="claseprod_id" class="form-control select2 claseprod_id" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
        </div>
    @else
        <div class="form-group col-xs-12 col-sm-6">
            <label for="categoriaprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Categoría">Categoría</label>
            <div class="col-lg-9">
                <select name="categoriaprod_id" id="categoriaprod_id" class="form-control select2 categoriaprod_id" required>
                    <option value="" precio="0">Seleccione...</option>
                    @foreach($categoriaprods as $categoriaprod)
                        <option
                            value="{{$categoriaprod->id}}" precio="{{$categoriaprod->precio}}"
                            @if ($data->categoriaprod_id==$categoriaprod->id)
                                {{'selected'}}
                            @endif
                            >
                            {{$categoriaprod->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="claseprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Clase">Clase</label>
            <div class="col-lg-9">
                <select name="claseprod_id" id="claseprod_id" class="form-control select2 claseprod_id" required>
                    <option value="">Seleccione...</option>
                    @foreach($claseprods as $claseprod)
                        <option value="{{$claseprod->id}}"
                            @if ($data->claseprod_id==$claseprod->id)
                                {{'selected'}}
                            @endif
                            >
                            {{$claseprod->cla_nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
</div>
<!--
<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="codintprod" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Códigp interno de producto">Cód Int Prod</label>
        <div class="col-lg-9">
        <input type="text" name="codintprod" id="codintprod" class="form-control" value="{{old('codintprod', $data->codintprod ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="codbarra" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Código de Barra">Código Barra</label>
        <div class="col-lg-9">
        <input type="text" name="codbarra" id="codbarra" class="form-control" value="{{old('codbarra', $data->codbarra ?? '')}}" required/>
        </div>
    </div>
</div>
-->
<div class="row">
    @if ($aux_sta==1)
        <div class="form-group col-xs-12 col-sm-6">
            <label for="grupoprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Campo de agrupación">Grupo</label>
            <div class="col-lg-9">
                <select name="grupoprod_id" id="grupoprod_id" class="form-control select2 grupoprod_id" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
        </div>
    @else
        <div class="form-group col-xs-12 col-sm-6">
            <label for="grupoprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Campo de agrupación">Grupo</label>
            <div class="col-lg-9">
                <select name="grupoprod_id" id="grupoprod_id" class="form-control select2 grupoprod_id" required>
                    <option value="">Seleccione...</option>
                    @foreach($grupoprods as $grupoprod)
                        <option value="{{$grupoprod->id}}"
                            @if ($data->grupoprod_id==$grupoprod->id)
                                {{'selected'}}
                            @endif
                            >
                            {{$grupoprod->gru_nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
    <div class="form-group col-xs-12 col-sm-6">
        <label for="diametro" class="col-lg-3 control-label requerido" title="Diámetro" data-toggle='tooltip' title="Diámetro">Diámetro</label>
        <div class="col-lg-9">
        <input type="text" name="diametro" id="diametro" class="form-control" value="{{old('diametro', $data->diametro ?? '')}}" required/>
        </div>
    </div>

<!--
    <div class="form-group col-xs-12 col-sm-6">
        <label for="diamextmm" class="col-lg-3 control-label requerido" title="Diámetro Ext mm" data-toggle='tooltip' title="Diámetro">Diámetro mm</label>
        <div class="col-lg-9">
        <input type="text" name="diamextmm" id="diamextmm" class="form-control numerico" value="{{old('diamextmm', $data->diamextmm ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="diamextpg" class="col-lg-3 control-label requerido" title="Diámetro Ext mm" data-toggle='tooltip' title="Diámetro Pulgadas">Diámetro Pg</label>
        <div class="col-lg-9">
        <input type="text" name="diamextpg" id="diamextpg" class="form-control" value="{{old('diamextpg', $data->diamextpg ?? '')}}" required/>
        </div>
    </div>
-->
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="espesor" class="col-lg-3 control-label requerido" title="Espesor mm" data-toggle='tooltip'>Espesor</label>
        <div class="col-lg-9">
        <input type="text" name="espesor" id="espesor" class="form-control numerico" value="{{old('espesor', $data->espesor ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="long" class="col-lg-3 control-label requerido" title="Largo Mts" data-toggle='tooltip'>Largo</label>
        <div class="col-lg-9">
        <input type="text" name="long" id="long" class="form-control numerico" value="{{old('long', $data->long ?? '')}}" required/>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="peso" class="col-lg-3 control-label requerido" title="Peso Kgs" data-toggle='tooltip'>Peso</label>
        <div class="col-lg-9">
        <input type="text" name="peso" id="peso" class="form-control numerico" value="{{old('peso', $data->peso ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="tipounion" class="col-lg-3 control-label requerido" title="Tipo de Union" data-toggle='tooltip'>Tipo Unión</label>
        <div class="col-lg-9">
        <!--<input type="text" name="tipounion" id="tipounion" class="form-control" value="{{old('tipounion', $data->tipounion ?? '')}}" required/>-->
            @if ($aux_sta==1)
                <select name="tipounion" id="tipounion" class="form-control select2 tipounion" required>
                    <option value="">Seleccione...</option>
                    <option value="A">Anger</option>
                    <option value="C">Cementar</option>
                    <option value="S/C">S/C</option>
                    <option value="S/U">S/U</option>
                    <option value="R600">R-600</option>
                    <option value="R2100">R-2100</option>
                </select>
            @else
                <select name="tipounion" id="tipounion" class="form-control select2 tipounion" required>
                    <option value="">Seleccione...</option>
                    <option value="A"
                        @if ($data->tipounion=="A")
                            {{'selected'}}
                        @endif
                    >Anger</option>
                    <option value="C"
                        @if ($data->tipounion=="C")
                            {{'selected'}}
                        @endif            
                    >Cementar</option>
                    <option value="S/C"
                        @if ($data->tipounion=="S/C")
                            {{'selected'}}
                        @endif            
                    >S/N</option>
                    <option value="S/U"
                        @if ($data->tipounion=="S/U")
                            {{'selected'}}
                        @endif            
                    >S/U</option>
                    <option value="R600"
                        @if ($data->tipounion=="R600")
                            {{'selected'}}
                        @endif            
                    >R-600</option>
                    <option value="R2100"
                        @if ($data->tipounion=="R2100")
                            {{'selected'}}
                        @endif            
                    >R-2100</option>
                </select>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="precioneto" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Precio Neto">Precio Neto</label>
        <div class="col-lg-9">
        <input type="text" name="precioneto" id="precioneto" class="form-control numerico" value="{{old('precioneto', $data->precioneto ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="color_id" class="col-lg-3 control-label" data-toggle='tooltip' title="Color">Color</label>
        <div class="col-lg-9">
            <select name="color_id" id="color_id" class="selectpicker form-control color_id" data-live-search='true' title='Seleccione...'>
                @foreach($colores as $color)
                    <option data-content="<span class='badge' style='background: {{$color->codcolor}}; color: #fff;'>{{$color->nombre}}</span>"
                        value="{{$color->id}}"
                        @if (($aux_sta==2) and ($data->color_id==$color->id))
                            {{'selected'}}
                        @endif
                        >
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="stockmin" class="col-lg-3 control-label requerido" title="Stock Mínimo Kgs" data-toggle='tooltip'>Stock Min</label>
        <div class="col-lg-9">
        <input type="text" name="stockmin" id="stockmin" class="form-control numerico" value="{{old('stockmin', $data->stockmin ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="stockmax" class="col-lg-3 control-label requerido" title="Stock Máximo Kgs" data-toggle='tooltip'>Stock Max</label>
        <div class="col-lg-9">
        <input type="text" name="stockmax" id="stockmax" class="form-control numerico" value="{{old('stockmax', $data->stockmax ?? '')}}" required/>
        </div>
    </div>
</div>

<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="tipoprod" class="col-lg-3 control-label requerido" title="Tipo Producto" data-toggle='tooltip'>Tipo Producto</label>
        <div class="col-lg-9">
            <select name="tipoprod" id="tipoprod" class="form-control select2 tipoprod" required>
                <option value="">Seleccione...</option>
                <option value="0"
                    @if (isset($data) and ($data->tipoprod=='0'))
                        {{'selected'}}
                    @endif
                >Producto</option>
                <option value="1"
                    @if (isset($data) and ($data->tipoprod=='1'))
                        {{'selected'}}
                    @endif
                >Transicional (Para Hacer Acuerdo Técnico)</option>
                <option value="2"
                @if (isset($data) and ($data->tipoprod=='2'))
                    {{'selected'}}
                @endif
                >Factura Directa</option>
            </select>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="estado" class="col-lg-3 control-label requerido" title="Activo o Inactivo.">Estado</label>
        <div class="col-lg-9">
            <select name="estado" id="estado" class="form-control select2 estado" required>
                <option value="">Seleccione...</option>
                <option value="1"
                    @if (isset($data) and ($data->estado=="1"))
                        {{'selected'}}
                    @endif
                >Activo</option>
                <option value="0"
                    @if (isset($data) and ($data->estado=="0"))
                        {{'selected'}}
                    @endif    
                >Inactivo</option>
            </select>
        </div>    
    </div>

</div>

<div class="col-md-8 col-md-offset-2">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Bodegas</h3>
        </div>
        <table class="table table-striped table-bordered table-hover" id="dataTables">
            <thead>
                <tr>
                    <th>Sucursal</th>
                    <th>Bodega</th>
                    <th style='text-align:center'>Stock</th>
                    <th style="display: none">id</th>
                </tr>
            </thead>
            <tbody id="tbody">
                <?php 
                    $aux_nfila = 0; 
                    $totalStockinvbodega = 0;
                ?>
                @if ($aux_sta==2)
                    @foreach ($invbodegaproductos->get() as $invbodegaproducto)
                        <?php 
                            $aux_nfila++; 
                            $aux_request = new Request();
                            $aux_request->mesanno = $aux_mesanno;
                            $aux_request->sucursal_id = $invbodegaproducto->invbodega->sucursal_id;
                            $aux_request->producto_id = $invbodegaproducto->producto_id;
                            $aux_request->invbodega_id = $invbodegaproducto->invbodega_id;
                            $aux_stock = InvMov::stock($aux_request);
                            $aux_stock = $aux_stock->get();
                            $aux_stockValor = sizeof($aux_stock) > 0 ? $aux_stock[0]->stock : 0;
                            $totalStockinvbodega += $aux_stockValor;
                        ?>
                        <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                            <td>
                                {{$invbodegaproducto->invbodega->sucursal->nombre}}
                            </td>
                            <td>
                                {{$invbodegaproducto->invbodega->nombre}}
                            </td>
                            <td style='text-align:center'>
                                {{$aux_stockValor}}
                            </td>
                        </tr>
                    @endforeach            
                @endif
            </tbody>
            <tfoot>
                <tr>
                </tr>
                <tr>
                    <th colspan='2' style='text-align:right'>Total Stock</th>
                    <th id='totalstockinvbodega' name='totalstockinvbodega' style='text-align:center'>{{number_format($totalStockinvbodega, 0, ",", ".")}}</th>
                </tr>
            </tfoot>

        </table>
    </div>
</div>
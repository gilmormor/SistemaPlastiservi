<?php
    use App\Models\CategoriaGrupoValMes;
    use App\Models\InvMov;
    $aux_mesanno = CategoriaGrupoValMes::mesanno(date("Y") . date("m"));
?>
@include('generales.buscarproductobd')
@include('generales.buscarinsumobd')
<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
@if (isset($data))
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6">
            <label for="sku" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="SKU">SKU</label>
            <div class="col-lg-9">
                <input type="text" name="sku" id="sku" class="form-control" value="{{old('sku', $data->sku ?? '')}}" required maxlength="35"/>
            </div>
        </div>
    </div>    
@endif
<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="glosa" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Glosa">Glosa</label>
        <div class="col-lg-9">
            <textarea name="glosa" id="glosa" class="form-control validar-texto-xml" value="{{old('glosa', $data->glosa ?? '')}}"  maxlength="250" required
                    @if(isset($data) && $data->glosaaut == 1)
                        readonly
                    @endif
                >{{old('glosa', $data->glosa ?? '')}}</textarea>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="glosaaut" class="col-lg-3 control-label requerido" title="Glosa Automatica">Glosa Automatica?</label>
        <div class="col-lg-9">
            <select name="glosaaut" id="glosaaut" class="form-control select2 glosaaut" required>
                <option value="">Seleccione...</option>
                <option value="1"
                    @if (isset($data) and ($data->glosaaut=="1"))
                        {{'selected'}}
                    @endif
                >Si</option>
                <option value="0"
                    @if (isset($data) and ($data->glosaaut=="0"))
                        {{'selected'}}
                    @endif    
                >No</option>
            </select>
        </div>    
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="nombre" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Nombre">Nombre</label>
        <div class="col-lg-9">
            {{-- <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/> --}}
            <textarea name="nombre" id="nombre" class="form-control validar-texto-xml" value="{{old('nombre', $data->nombre ?? '')}}"  maxlength="300" required>{{old('nombre', $data->nombre ?? '')}}</textarea>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="descripcion" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Descripción">Descripción</label>
        <div class="col-lg-9">
            {{-- <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->descripcion ?? '')}}" required/> --}}
            <textarea name="descripcion" id="descripcion" class="form-control validar-texto-xml" value="{{old('descripcion', $data->descripcion ?? '')}}" maxlength="300" required>{{old('descripcion', $data->descripcion ?? '')}}</textarea>
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
                            value="{{$categoriaprod->id}}" precio="{{$categoriaprod->precio}}"
                            >{{$categoriaprod->nombre}}</option>
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
                            >{{$categoriaprod->nombre}}</option>
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
                            >{{$claseprod->cla_nombre}}</option>
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
                            >{{$grupoprod->gru_nombre}}</option>
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
                <option value="3"
                    @if (isset($data) and ($data->tipoprod=='3'))
                        {{'selected'}}
                    @endif
                >Servicio</option>
                <option value="4"
                    @if (isset($data) and ($data->tipoprod=='4'))
                        {{'selected'}}
                    @endif
                >Embalaje</option>
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
                    $totalStockinvbodega = 0;
                ?>
                @if ($aux_sta==2)
                    @foreach ($invbodegaproductos->get() as $invbodegaproducto)
                        <?php 
                            $totalStockinvbodega += $invbodegaproducto->stock;
                        ?>
                        <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}">
                            <td>
                                {{$invbodegaproducto->invbodega->sucursal->nombre}}
                            </td>
                            <td>
                                {{$invbodegaproducto->invbodega->nombre}}
                            </td>
                            <td style='text-align:center'>
                                {{$invbodegaproducto->stock}}
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
{{-- <hr>
<div class="form-group">
    <label class="col-lg-3 control-label">Componentes</label>
    <div class="col-lg-9">
        <table class="table table-bordered" id="tabla-detalles">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Glosa</th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($data) && $data->productocomps)
                    @foreach($data->productocomps as $det)
                        <tr>
                            <td>
                                <input type="hidden" name="detalles[{{$loop->index}}][id]" value="{{ $det->id }}">
                                <input type="text" name="detalles[{{$loop->index}}][productocompiddet]" class="form-control" value="{{ old('detalles.'.$loop->index.'.productocompiddet', $det->productocomp_id) }}" required>
                            </td>
                            <td>
                                <input type="text" name="detalles[{{$loop->index}}][nombredet]" class="form-control" value="{{ old('detalles.'.$loop->index.'.nombredet', $det->productocomp->nombre) }}" required>
                            </td>
                            <td>
                                <input type="text" name="detalles[{{$loop->index}}][glosadet]" class="form-control" value="{{ old('detalles.'.$loop->index.'.glosadet', $det->productocomp->glosa) }}" required>
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
<div class="col-md-8 col-md-offset-2">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Componentes</h3>
        </div>
        <table class="table table-striped table-bordered table-hover" id="tabla-detalles">
            <thead>
                <tr>
                    <th style="width: 120px">Codigo</th>
                    <th style="width: 100px">Cant</th>
                    <th>Glosa</th>
                    <th>Obs</th>
                    <th style="width: 120px">Precio</th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody id="tbody">
                @if(isset($data) && $data->productocomps)
                    @foreach($data->productocomps as $det)
                        <tr>
                            <td>
                                <input type="hidden" name="detalles[{{$loop->index}}][id]" value="{{ $det->id }}">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        name="detalles[{{$loop->index}}][productocompiddet]"
                                        id="producto_id{{$loop->index}}"
                                        item="{{$loop->index}}"
                                        class="form-control numerico"
                                        required
                                        onblur="onBlurProducto_id(this)"
                                        onkeyup="buscarProdKeyUp(this,event)"
                                        value="{{ old('detalles.'.$loop->index.'.productocompiddet', $det->productocomp_id) }}"
                                        maxlength="4"
                                        style="text-align:right;"
                                        valor=""
                                    >
                                    <span class="input-group-btn">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary btn-buscar-producto"
                                            title="Buscar Producto"
                                            item="{{$loop->index}}"
                                            data-toggle="modal"
                                            data-target="#buscarProductoBDModal"
                                            onclick="buscarproductoGenNew(this,event)"
                                            nomCampProducto="producto_id{{$loop->index}}"
                                            >
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="detalles[{{$loop->index}}][cantdet]" id="cantdet{{$loop->index}}" class="form-control numerico" value="{{ old('detalles.'.$loop->index.'.cantdet', $det->cant) }}" required style="text-align:right">
                            </td>
                            <td>
                                <input type="text" name="detalles[{{$loop->index}}][glosadet]" id="glosadet{{$loop->index}}" class="form-control" value="{{ old('detalles.'.$loop->index.'.glosadet', $det->productocomp->glosa) }}" required readonly>
                            </td>
                            <td>
                                <input type="text" name="detalles[{{$loop->index}}][obsdet]" id="obsdet{{$loop->index}}" class="form-control" value="{{ old('detalles.'.$loop->index.'.obsdet', $det->obs) }}" required>
                            </td>
                            <td>
                                <input type="text" name="detalles[{{$loop->index}}][precionetodet]" id="precionetodet{{$loop->index}}" class="form-control" value="{{ old('detalles.'.$loop->index.'.precionetodet', number_format($det->productocomp->precioneto, 2, ',', '.')) }}" required readonly style="text-align:right">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-eliminar-detalle"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <button type="button" class="btn btn-success btn-sm" id="btn-agregar-detalle"><i class="fa fa-plus" title="Agregar Complemento"></i> Agregar</button>
    </div>
</div>


<div class="col-md-8 col-md-offset-2">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Insumos</h3>
        </div>
        <table class="table table-striped table-bordered table-hover" id="tabla-detalles-insumos">
            <thead>
                <tr>
                    <th style="width: 120px">Codigo</th>
                    <th style="width: 100px">Cant</th>
                    <th>Nombre</th>
                    <th>Unidades</th>
                    <th>Obs</th>
                    <th style="width: 120px">Precio</th>
                    <th style="width: 50px"></th>
                </tr>
            </thead>
            <tbody id="tbody">
                @if(isset($data) && $data->productoinsumos)
                    @foreach($data->productoinsumos as $det)
                        <tr>
                            <td>
                                <input type="hidden" name="detalleinsumos[{{$loop->index}}][id]" value="{{ $det->id }}">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        name="detalleinsumos[{{$loop->index}}][insumo_iddet]"
                                        id="insumo_id{{$loop->index}}"
                                        item="{{$loop->index}}"
                                        class="form-control numerico"
                                        required
                                        onblur="onBlurInsumo_id(this)"
                                        onkeyup="buscarInsumoKeyUp(this,event)"
                                        value="{{ old('detalles.'.$loop->index.'.insumoiddet', $det->insumo_id) }}"
                                        maxlength="4"
                                        style="text-align:right;"
                                        valor=""
                                    >
                                    <span class="input-group-btn">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary btn-buscar-insumo"
                                            title="Buscar Insumo"
                                            item="{{$loop->index}}"
                                            data-toggle="modal"
                                            data-target="#buscarInsumoBDModal"
                                            onclick="buscarInsumoGenNew(this,event)"
                                            nomCampProducto="insumo_id{{$loop->index}}"
                                            >
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="detalleinsumos[{{$loop->index}}][insumocantdet]" id="insumocantdet{{$loop->index}}" class="form-control numerico" value="{{ old('detalles.'.$loop->index.'.cantdet', $det->cant) }}" required style="text-align:right">
                            </td>
                            <td>
                                <input type="text" name="detalleinsumos[{{$loop->index}}][insumonombredet]" id="insumonombredet{{$loop->index}}" class="form-control" value="{{ old('detalles.'.$loop->index.'.insumonombredet', $det->insumo->nombre) }}" required readonly>
                            </td>
                            <td>
                                <input type="text" name="detalleinsumos[{{$loop->index}}][insumounidadesproductodet]" id="insumounidadesproductodet{{$loop->index}}" class="form-control" value="{{ old('detalles.'.$loop->index.'.insumounidadesproductodet', $det->unidadesproducto) }}" required readonly>
                            </td>
                            <td>
                                <input type="text" name="detalleinsumos[{{$loop->index}}][insumoobsdet]" id="insumoobsdet{{$loop->index}}" class="form-control" value="{{ old('detalles.'.$loop->index.'.insumoobsdet', $det->obs) }}" required>
                            </td>
                            <td>
                                <input type="text" name="detalleinsumos[{{$loop->index}}][insumocostounitariodet]" id="insumocostounitariodet{{$loop->index}}" class="form-control" value="{{ old('detalles.'.$loop->index.'.insumocostounitariodet', number_format($det->insumo->costounitario, 2, ',', '.')) }}" required readonly style="text-align:right">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-eliminar-detalle"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <button type="button" class="btn btn-success btn-sm" id="btn-agregar-detalle-insumo"><i class="fa fa-plus" title="Agregar Complemento"></i> Agregar</button>
    </div>
</div>
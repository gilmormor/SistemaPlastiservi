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

<div class="row">
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
    <div class="form-group col-xs-12 col-sm-6">
        <label for="precioneto" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Precio Neto">Precio Neto</label>
        <div class="col-lg-9">
        <input type="text" name="precioneto" id="precioneto" class="form-control numerico" value="{{old('precioneto', $data->precioneto ?? '')}}" required/>
        </div>
    </div>

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
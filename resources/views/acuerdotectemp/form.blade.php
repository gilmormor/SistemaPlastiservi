<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="clientedirec_id" class="col-lg-3 control-label requerido">Cliente</label>
        <div class="col-lg-9">
            <select name="clientedirec_id" id="clientedirec_id" class="selectpicker form-control clientedirec_id" data-live-search='true' title='Seleccione...'  required>
                @foreach($clientedirecs as $clientedirec)
                    <option
                        value="{{$clientedirec->id}}"
                        nomcli="{{$clientedirec->contactonombre}}"
                        @if (($aux_sta==2) and ($data->clientedirec_id==$clientedirec->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$clientedirec->cliente['razonsocial']}} - {{$clientedirec->direccion}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-6">
        <label for="nomcli" class="col-lg-3 control-label requerido">Nombre Cliente</label>
        <div class="col-lg-9">
            <input type="text" name="nomcli" id="nomcli" class="form-control" value="{{old('nomcli', $data->nomcli ?? '')}}" required/>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="categoriaprod_id" class="col-lg-3 control-label requerido">Categoria Prod</label>
        <div class="col-lg-9">
            <select name="categoriaprod_id" id="categoriaprod_id" class="selectpicker form-control categoriaprod_id" data-live-search='true' title='Seleccione...'  required>
                @foreach($categoriaprods as $categoriaprod)
                    <option
                        value="{{$categoriaprod->id}}"
                        @if (($aux_sta==2) and ($data->categoriaprod_id==$categoriaprod->id))
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
        <label for="nombreprod" class="col-lg-3 control-label requerido">Nombre Prod</label>
        <div class="col-lg-9">
            <input type="text" name="nombreprod" id="nombreprod" class="form-control" value="{{old('nombreprod', $data->nombreprod ?? '')}}" required/>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-6">
        <label for="entmuestra" class="col-lg-3 control-label requerido">Entrega Muestra</label>
        <div class="col-lg-9">
            <select name="entmuestra" id="entmuestra" class="selectpicker form-control entmuestra" data-live-search='true' title='Seleccione...'  required>
                @foreach($nosis as $posicion=>$nosi)
                    <option
                        value="{{$posicion}}"
                        @if (($aux_sta==2) and ($data->entmuestra==$posicion))
                            {{'selected'}}
                        @endif
                        >
                        {{$nosi}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


</div>

<div class="box box-success" style="padding-left: 10px;padding-right: 10px;">
    <div class="box-header with-border">
        <h3 class="box-title">Caracteristicas de Extrusión</h3>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6">
            <label for="matfabr_id" class="col-lg-3 control-label requerido">Material de Fabricación</label>
            <div class="col-lg-9">
                <select name="matfabr_id" id="matfabr_id" class="selectpicker form-control matfabr_id" data-live-search='true' title='Seleccione...'  required>
                    @foreach($matfabrs as $matfabr)
                        <option
                            value="{{$matfabr->id}}"
                            @if (($aux_sta==2) and ($data->matfabr_id==$matfabr->id))
                                {{'selected'}}
                            @endif
                            >
                            {{$matfabr->nombre}} - {{$matfabr->descripcion}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="usoprevisto" class="col-lg-3 control-label requerido">Uso Previsto</label>
            <div class="col-lg-9">
                <input type="text" name="usoprevisto" id="usoprevisto" class="form-control" value="{{old('usoprevisto', $data->usoprevisto ?? '')}}" required/>
            </div>
        </div>
    </div>

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Aditivos</h3>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="uv" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="uv">UV</label>
                <div class="col-lg-9">
                    <select name="uv" id="uv" class="selectpicker form-control uv" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->uv==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="uvobs" id="uvobs" class="form-control" value="{{old('uvobs', $data->uvobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="antideslizante" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Antideslizante">Antideslizante</label>
                <div class="col-lg-9">
                    <select name="antideslizante" id="antideslizante" class="selectpicker form-control antideslizante" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->antideslizante==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="antideslizanteobs" id="antideslizanteobs" class="form-control" value="{{old('antideslizanteobs', $data->antideslizanteobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="antiestatico" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Antideslizante">Antiestatico</label>
                <div class="col-lg-9">
                    <select name="antiestatico" id="antiestatico" class="selectpicker form-control antiestatico" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->antiestatico==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="antideslizanteobs" id="antideslizanteobs" class="form-control" value="{{old('antideslizanteobs', $data->antideslizanteobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="antiblock" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Antiblock">Antiblock</label>
                <div class="col-lg-9">
                    <select name="antiblock" id="antiblock" class="selectpicker form-control antiblock" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->antiblock==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="antiblockobs" id="antiblockobs" class="form-control" value="{{old('antiblockobs', $data->antiblockobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="aditivootro" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Aditivo Otro">Aditivo Otro</label>
                <div class="col-lg-9">
                    <select name="aditivootro" id="aditivootro" class="selectpicker form-control aditivootro" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->aditivootro==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="aditivootroobs" id="aditivootroobs" class="form-control" value="{{old('aditivootroobs', $data->aditivootroobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Dimensiones</h3>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="ancho" class="col-lg-3 control-label" data-toggle='tooltip' title="Ancho">Ancho</label>
                <div class="col-lg-5">
                    <input type="text" name="ancho" id="ancho" class="form-control" value="{{old('ancho', $data->ancho ?? '')}}"/>
                </div>
                <div class="col-lg-4">
                    <select name="anchoum_id" id="anchoum_id" class="selectpicker form-control anchoum_id" data-live-search='true' title='Seleccione...'  required>
                        <option value="">Seleccione...</option>
                        @foreach($unidadmedidas as $unidadmedida)
                            <option
                                value="{{$unidadmedida->id}}"
                                @if (($aux_sta==2) and ($data->anchoum_id==$unidadmedida->id))
                                    {{'selected'}}
                                @endif
                                >
                                {{$unidadmedida->nombre}}
                            </option>
                        @endforeach
                    </select>
                    </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="anchodesv" id="anchodesv" class="form-control" value="{{old('anchodesv', $data->anchodesv ?? '')}}" placeholder="Desviación"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="largo" class="col-lg-3 control-label" data-toggle='tooltip' title="Largo">Largo</label>
                <div class="col-lg-5">
                    <input type="text" name="largo" id="largo" class="form-control" value="{{old('largo', $data->largo ?? '')}}"/>
                </div>
                <div class="col-lg-4">
                    <select name="largoum_id" id="largoum_id" class="selectpicker form-control largoum_id" data-live-search='true' title='Seleccione...'  required>
                        <option value="">Seleccione...</option>
                        @foreach($unidadmedidas as $unidadmedida)
                            <option
                                value="{{$unidadmedida->id}}"
                                @if (($aux_sta==2) and ($data->largoum_id==$unidadmedida->id))
                                    {{'selected'}}
                                @endif
                                >
                                {{$unidadmedida->nombre}}
                            </option>
                        @endforeach
                    </select>
                    </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="largodesv" id="largodesv" class="form-control" value="{{old('largodesv', $data->largodesv ?? '')}}" placeholder="Desviación"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="fuelle" class="col-lg-3 control-label" data-toggle='tooltip' title="Fuelle">Fuelle</label>
                <div class="col-lg-5">
                    <input type="text" name="fuelle" id="fuelle" class="form-control" value="{{old('fuelle', $data->fuelle ?? '')}}"/>
                </div>
                <div class="col-lg-4">
                    <select name="fuelleum_id" id="fuelleum_id" class="selectpicker form-control fuelleum_id" data-live-search='true' title='Seleccione...'  required>
                        <option value="">Seleccione...</option>
                        @foreach($unidadmedidas as $unidadmedida)
                            <option
                                value="{{$unidadmedida->id}}"
                                @if (($aux_sta==2) and ($data->fuelleum_id==$unidadmedida->id))
                                    {{'selected'}}
                                @endif
                                >
                                {{$unidadmedida->nombre}}
                            </option>
                        @endforeach
                    </select>
                    </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="fuelledesv" id="fuelledesv" class="form-control" value="{{old('fuelledesv', $data->fuelledesv ?? '')}}" placeholder="Desviación"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="espesor" class="col-lg-3 control-label" data-toggle='tooltip' title="espesor">espesor</label>
                <div class="col-lg-5">
                    <input type="text" name="espesor" id="espesor" class="form-control" value="{{old('espesor', $data->espesor ?? '')}}"/>
                </div>
                <div class="col-lg-4">
                    <select name="espesorum_id" id="espesorum_id" class="selectpicker form-control espesorum_id" data-live-search='true' title='Seleccione...'  required>
                        <option value="">Seleccione...</option>
                        @foreach($unidadmedidas as $unidadmedida)
                            <option
                                value="{{$unidadmedida->id}}"
                                @if (($aux_sta==2) and ($data->espesorum_id==$unidadmedida->id))
                                    {{'selected'}}
                                @endif
                                >
                                {{$unidadmedida->nombre}}
                            </option>
                        @endforeach
                    </select>
                    </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="espesordesv" id="espesordesv" class="form-control" value="{{old('espesordesv', $data->espesordesv ?? '')}}" placeholder="Desviación"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-4">
            <label for="color_id" class="col-lg-3 control-label" data-toggle='tooltip' title="Color">Color</label>
            <div class="col-lg-9">
                <select name="color_id" id="color_id" class="selectpicker form-control color_id" data-live-search='true' title='Seleccione...'  required>
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
        <div class="form-group col-xs-12 col-sm-4">
            <label for="npantone" class="col-lg-3 control-label requerido">N°.Pantone</label>
            <div class="col-lg-9">
                <input type="text" name="npantone" id="npantone" class="form-control" value="{{old('npantone', $data->npantone ?? '')}}" required/>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-4">
            <label for="translucidez" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Translúcido">Translúcido</label>
            <div class="col-lg-9">
                <select name="translucidez" id="translucidez" class="selectpicker form-control translucidez" data-live-search='true' title='Seleccione...'  required>
                    @foreach($transparencias as $posicion=>$transparencia)
                        <option
                            value="{{$transparencia['id']}}"
                            @if (($aux_sta==2) and ($data->translucidez==$transparencia['id']))
                                {{'selected'}}
                            @endif
                            >
                            {{$transparencia['nombre']}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<div class="box box-success" style="padding-left: 10px;padding-right: 10px;">
    <div class="box-header with-border">
        <h3 class="box-title">Impresión</h3>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6">
            <label for="impreso" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Impreso">Impreso</label>
            <div class="col-lg-9">
                <select name="impreso" id="impreso" class="selectpicker form-control impreso" data-live-search='true' title='Seleccione...'  required>
                    @foreach($nosis as $posicion=>$nosi)
                        <option
                            value="{{$posicion}}"
                            @if (($aux_sta==2) and ($data->impreso==$posicion))
                                {{'selected'}}
                            @endif
                            >
                            {{$nosi}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <div class="col-lg-12">
                <input type="text" name="impresoobs" id="impresoobs" class="form-control" value="{{old('impresoobs', $data->impresoobs ?? '')}}" placeholder="Observaciones"/>
            </div>
        </div>
    </div>
    <div class="row" id="divfoto" name="divfoto">
        <div class="form-groupcol-xs-12 col-sm-6">
            <label for="impresofoto" class="col-lg-3 control-label">Foto</label>
            <div class="col-lg-9">
                <input type="file" name="foto_up" id="impresofoto" data-initial-preview="{{isset($data->imagen) ? Storage::url("imagenes/usuario/$data->imagen") : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Usuario"}}" accept="image/*"/>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-4">
            <label for="impresocolor" class="col-lg-3 control-label" data-toggle='tooltip' title="Color">Color</label>
            <div class="col-lg-9">
                <select name="impresocolor" id="impresocolor" class="selectpicker form-control impresocolor" data-live-search='true' title='Seleccione...'  required>
                    @foreach($colores as $color)
                        <option data-content="<span class='badge' style='background: {{$color->codcolor}}; color: #fff;'>{{$color->nombre}}</span>"
                            value="{{$color->id}}"
                            @if (($aux_sta==2) and ($data->impresocolor==$color->id))
                                {{'selected'}}
                            @endif
                            >
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<div class="box box-success" style="padding-left: 10px;padding-right: 10px;padding-bottom: 1px;">
    <div class="box-header with-border">
        <h3 class="box-title">Sellado y Embalaje</h3>
    </div>
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Tipo de Sello</h3>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="sfondo" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Fondo">Fondo</label>
                <div class="col-lg-9">
                    <select name="sfondo" id="sfondo" class="selectpicker form-control sfondo" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->sfondo==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="sfondoobs" id="sfondoobs" class="form-control" value="{{old('sfondoobs', $data->sfondoobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="slateral" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Lateral">Lateral</label>
                <div class="col-lg-9">
                    <select name="slateral" id="slateral" class="selectpicker form-control slateral" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->slateral==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="slateralobs" id="slateralobs" class="form-control" value="{{old('slateralobs', $data->slateralobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="sprepicado" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Prepicado">Prepicado</label>
                <div class="col-lg-9">
                    <select name="sprepicado" id="sprepicado" class="selectpicker form-control sprepicado" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->slateral==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="sprepicadoobs" id="sprepicadoobs" class="form-control" value="{{old('sprepicadoobs', $data->sprepicadoobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="slamina" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Lamina">Lamina</label>
                <div class="col-lg-9">
                    <select name="slamina" id="slamina" class="selectpicker form-control slamina" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->slateral==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="slaminaobs" id="slaminaobs" class="form-control" value="{{old('slaminaobs', $data->slaminaobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="sfunda" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Lamina">Lamina</label>
                <div class="col-lg-9">
                    <select name="sfunda" id="sfunda" class="selectpicker form-control sfunda" data-live-search='true' title='Seleccione...'  required>
                        @foreach($nosis as $posicion=>$nosi)
                            <option
                                value="{{$posicion}}"
                                @if (($aux_sta==2) and ($data->slateral==$posicion))
                                    {{'selected'}}
                                @endif
                                >
                                {{$nosi}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="sfundaobs" id="sfundaobs" class="form-control" value="{{old('sfundaobs', $data->sfundaobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Forma de Embalaje</h3>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="feunidxpaq" class="col-lg-3 control-label requerido">Unid x Paquete</label>
                <div class="col-lg-9">
                    <input type="text" name="feunidxpaq" id="feunidxpaq" class="form-control" value="{{old('feunidxpaq', $data->feunidxpaq ?? '')}}" required/>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="feunidxpaqobs" id="feunidxpaqobs" class="form-control" value="{{old('feunidxpaqobs', $data->feunidxpaqobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="feunidxcont" class="col-lg-3 control-label requerido">Unid x Contenedor</label>
                <div class="col-lg-9">
                    <input type="text" name="feunidxcont" id="feunidxcont" class="form-control" value="{{old('feunidxcont', $data->feunidxcont ?? '')}}" required/>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="feunidxcontobs" id="feunidxcontobs" class="form-control" value="{{old('feunidxcontobs', $data->feunidxcontobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="fecolorcont" class="col-lg-3 control-label requerido">Color Contenedor</label>
                <div class="col-lg-9">
                    <input type="text" name="fecolorcont" id="fecolorcont" class="form-control" value="{{old('fecolorcont', $data->fecolorcont ?? '')}}" required/>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="fecolorcontobs" id="fecolorcontobs" class="form-control" value="{{old('fecolorcontobs', $data->fecolorcontobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="feunitxpalet" class="col-lg-3 control-label requerido">Unid x Palet</label>
                <div class="col-lg-9">
                    <input type="text" name="feunitxpalet" id="feunitxpalet" class="form-control" value="{{old('feunitxpalet', $data->feunitxpalet ?? '')}}" required/>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="feunitxpaletobs" id="feunitxpaletobs" class="form-control" value="{{old('feunitxpaletobs', $data->feunitxpaletobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Etiquetado</h3>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="etiqplastiservi" class="col-lg-3 control-label requerido">Plastiservi</label>
                <div class="col-lg-9">
                    <input type="text" name="etiqplastiservi" id="etiqplastiservi" class="form-control" value="{{old('etiqplastiservi', $data->etiqplastiservi ?? '')}}" required/>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="etiqplastiserviobs" id="etiqplastiserviobs" class="form-control" value="{{old('etiqplastiserviobs', $data->etiqplastiserviobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="etiqotro" class="col-lg-3 control-label requerido">Otro</label>
                <div class="col-lg-9">
                    <input type="text" name="etiqotro" id="etiqotro" class="form-control" value="{{old('etiqotro', $data->etiqotro ?? '')}}" required/>
                </div>
            </div>
            <div class="form-group col-xs-12 col-sm-6">
                <div class="col-lg-12">
                    <input type="text" name="etiqotroobs" id="etiqotroobs" class="form-control" value="{{old('etiqotroobs', $data->etiqotroobs ?? '')}}" placeholder="Observaciones"/>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box box-success" style="padding-left: 10px;padding-right: 10px;">
    <div class="box-header with-border">
        <h3 class="box-title">Certificados</h3>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-8">
            <label for="certificados_id" class="col-lg-2 control-label requerido">Certificados</label>
            <div class="col-lg-10">
                <select name="certificados_id[]" id="certificados_id" class="selectpicker form-control sucursal_id" data-live-search="true" title="Seleccione..." multiple required>
                    @foreach($certificados as $id => $descripcion)
                        <option
                            value="{{$id}}"
                            {{is_array(old('certificados_id')) ? (in_array($id, old('certificados_id')) ? 'selected' : '') : (isset($data) ? ($data->certificados->firstWhere('id', $id) ? 'selected' : '') : '')}}
                            >
                            {{$descripcion}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-4">
            <div class="col-lg-12">
                <input type="text" name="otrocertificado" id="otrocertificado" class="form-control" value="{{old('otrocertificado', $data->otrocertificado ?? '')}}" placeholder="Otro certificado (Especifique)"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6">
            <label for="plazopago_id" class="col-lg-3 control-label requerido">Plazo Pago</label>
            <div class="col-lg-9">
                <select name="plazopago_id" id="plazopago_id" class="selectpicker form-control plazopago_id" data-live-search='true' title='Seleccione...'  required>
                    @foreach($plazopagos as $plazopago)
                        <option
                            value="{{$plazopago->id}}"
                            @if (($aux_sta==2) and ($data->plazopago==$plazopago->id))
                                {{'selected'}}
                            @endif
                            >
                            {{$plazopago->descripcion}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-6">
            <label for="despacharA" class="col-lg-3 control-label requerido">Despachar a</label>
            <div class="col-lg-9">
                <input type="text" name="despacharA" id="despacharA" class="form-control" value="{{old('despacharA', $data->despacharA ?? '')}}" required/>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModalAcuerdoTecnico" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="lbltitAT1" name="lbltitAT1">Acuerdo Tecnico</h3>
            </div>
            <div class="modal-body form">
                <input type="hidden" name="aux_numfilaAT" id="aux_numfilaAT" value="0">
                <input type="hidden" name="at_id" id="at_id" class="form_acutec">
                <div class="scrollg">
                    <div class="col-xs-11">
                        <div class="row">
                            <div class="box box-primary">
                                <!--
                                <div class="box-header with-border">
                                    <h3 class="box-title" id="lbltitAT2" name="lbltitAT2">Acuerdo tecnico</h3>
                                </div>
                                -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-7" classorig="col-xs-12 col-sm-7">
                                            <label for="at_desc" class="control-label tooltipsC requerido" data-original-title="Descripción Producto Acuerdo Técnico">Descripción</label>
                                            <input type="text" name="at_desc" id="at_desc" class="form-control form_acutec valorrequerido" placeholder="Descripción Producto Acuerdo Técnico" tipoval="texto" maxlength="100"/>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-5" classorig="col-xs-12 col-sm-5">
                                            <label for="at_complementonomprod" class="control-label tooltipsC" data-original-title="Complemento nombre Producto para Factura">Complemento nombre Producto</label>
                                            <a class="btn-sm tooltipsC" data-original-title="Descripción que complementa el nombre del producto para la Factura" aria-describedby="tooltip202998"><i class="fa fa-fw fa-question-circle text-aqua"></i></a>
                                            <input type="text" name="at_complementonomprod" id="at_complementonomprod" class="form-control form_acutec" placeholder="Complemento nombre Producto" maxlength="70"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-5" classorig="col-xs-12 col-sm-5">
                                            <label for="at_entmuestra" class="control-label requerido" data-toggle='tooltip' title="Entrega Muestra?">Entrega muestra?</label>
                                            <select name="at_entmuestra" id="at_entmuestra"  class="selectpicker form-control entmuestra form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Caracteristicas Extrusión</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                            <label for="at_materiaprima_id" class="control-label requerido" data-toggle='tooltip' title="Materia Prima">Materia Prima</label>
                                            <select name="at_materiaprima_id" id="at_materiaprima_id" class="selectpicker form-control materiaprima_id form_acutec valorrequerido" data-live-search='true' tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                @foreach($tablas['materiPrima'] as $materiprima)
                                                    <option
                                                        value="{{$materiprima->id}}" data-subtext="{{$materiprima->desc}}"
                                                        desc="{{$materiprima->desc}}"
                                                        >
                                                        {{$materiprima->nombre}}
                                                    </option>
                                                @endforeach            
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                                            <label for="at_color_id" class="control-label color_id requerido" data-toggle='tooltip' title="Color">Color</label>
                                            <select name="at_color_id" id="at_color_id" class="selectpicker form-control color_id form_acutec valorrequerido" data-live-search='true' title='Seleccione...' tipoval="combobox" size="3">
                                                @foreach($tablas['color'] as $color)
                                                    <option data-content="<span class='badge' style='background: {{$color->codcolor}}; color: #fff;'>{{$color->nombre}}</span>"
                                                        value="{{$color->id}}"
                                                        @if (($aux_sta==2) and ($data->color_id==$color->id))
                                                            {{'selected'}}
                                                        @endif
                                                        >
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                                            <label for="at_pigmentacion" class="control-label requerido" data-toggle='tooltip' title="% Pigmentacion entre 0 y 100%">%Pigmento</label>
                                            <input type="text" name="at_pigmentacion" id="at_pigmentacion" class="form-control form_acutec valorrequerido numerico" placeholder="%" tipoval="number" min1="0" max1="100"/>
                                            <span class="help-block"></span>
                                        </div>
<!--
                                        <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                                            <label for="at_colordesc" class="control-label requerido" data-toggle='tooltip' title="Descripción Color">Desc. Color</label>
                                            <input type="text" name="at_colordesc" id="at_colordesc" class="form-control form_acutec valorrequerido" placeholder="Descripción Color" tipoval="texto"/>
                                            <span class="help-block"></span>
                                        </div>
-->
                                        <div class="col-xs-12 col-sm-3">
                                            <label for="at_npantone" class="control-label" data-toggle='tooltip' title="Número Pantone">Número Pantone</label>
                                            <input type="text" name="at_npantone" id="at_npantone" class="form-control form_acutec" placeholder="Número Pantone"/>
                                            <span class="help-block"></span>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                                            <label for="at_translucidez" class="control-label translucidez requerido" data-toggle='tooltip' title="translucidez">Translucidez</label>
                                            <select name="at_translucidez" id="at_translucidez" class="selectpicker form-control translucidez form_acutec valorrequerido" data-live-search='true' title='Seleccione...' tipoval="combobox">
                                                <option value="1">No translucido</option>
                                                <option value="2">Opaco semi translucido</option>
                                                <option value="3">Alta Transparencia</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-5">
                                            <label for="at_materiaprimaobs" class="control-label" data-toggle='tooltip' title="Observación Materia Prima">Observación</label>
                                            <input type="text" name="at_materiaprimaobs" id="at_materiaprimaobs" class="form-control form_acutec" placeholder="Observacion" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                            <label for="at_usoprevisto" class="control-label requerido" data-toggle='tooltip' title="Uso Previsto">Uso Previsto</label>
                                            <input type="text" name="at_usoprevisto" id="at_usoprevisto" class="form-control form_acutec valorrequerido" placeholder="Uso Previsto" tipoval="texto" maxlength="50"/>
                                            <span class="help-block"></span>
                                        </div>
    
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Aditivos</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                                            <label for="at_uv" class="control-label requerido" data-toggle='tooltip' title="UV">UV</label>
                                            <select name="at_uv" id="at_uv"  class="selectpicker form-control uv form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-9">
                                            <label for="at_uvobs" class="control-label" data-toggle='tooltip' title="Observación UV">Observación</label>
                                            <input type="text" name="at_uvobs" id="at_uvobs" class="form-control form_acutec" placeholder="Observacion UV" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                                            <label for="at_antideslizante" class="control-label requerido" data-toggle='tooltip' title="Antideslizante">Antideslizante</label>
                                            <select name="at_antideslizante" id="at_antideslizante"  class="selectpicker form-control antideslizante form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-9">
                                            <label for="at_antideslizanteobs" class="control-label" data-toggle='tooltip' title="Observación Antideslizante">Observación</label>
                                            <input type="text" name="at_antideslizanteobs" id="at_antideslizanteobs" class="form-control form_acutec" placeholder="Observacion Antideslizante" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                                            <label for="at_antiestatico" class="control-label requerido" data-toggle='tooltip' title="Antiestatico">Antiestatico</label>
                                            <select name="at_antiestatico" id="at_antiestatico"  class="selectpicker form-control antiestatico form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-9">
                                            <label for="at_antiestaticoobs" class="control-label" data-toggle='tooltip' title="Observación antiestatico">Observación</label>
                                            <input type="text" name="at_antiestaticoobs" id="at_antiestaticoobs" class="form-control form_acutec" placeholder="Observacines Antiestatico" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                                            <label for="at_antiblock" class="control-label requerido" data-toggle='tooltip' title="Antiblock">Antiblock</label>
                                            <select name="at_antiblock" id="at_antiblock"  class="selectpicker form-control antiblock form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-9">
                                            <label for="at_antiblockobs" class="control-label" data-toggle='tooltip' title="Observación Antiblock">Observación</label>
                                            <input type="text" name="at_antiblockobs" id="at_antiblockobs" class="form-control form_acutec" placeholder="Observacines Antiblock" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                                            <label for="at_aditivootro" class="control-label requerido" data-toggle='tooltip' title="Aditovo Otro">Aditivos Otro</label>
                                            <select name="at_aditivootro" id="at_aditivootro"  class="selectpicker form-control aditivootro form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-9">
                                            <label for="at_aditivootroobs" class="control-label" data-toggle='tooltip' title="Observación Aditivos otros">Observación</label>
                                            <input type="text" name="at_aditivootroobs" id="at_aditivootroobs" class="form-control form_acutec" placeholder="Observacines Aditivos otro" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Dimensiones</h3>
                                </div>
                                <div class="box-body">
                                    <div class="col-md-6">
                                        <div class="box box-default">
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_ancho" class="control-label requerido" data-toggle='tooltip' title="Ancho">Ancho</label>
                                                        <input type="text" name="at_ancho" id="at_ancho" class="form-control form_acutec numerico valorrequerido" placeholder="Ancho" tipoval="texto"/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_anchoum_text" class="control-label anchoum_id requerido" data-toggle='tooltip' title="Unidad Medida Ancho">UM</label>
                                                        <select name="at_anchoum_id" id="at_anchoum_id"  class="selectpicker form-control anchoum_id form_acutec valorrequerido" data-live-search='true' tipoval="combobox">
                                                            <option value="">Seleccione...</option>
                                                            @foreach($tablas['unidadmedidaAT'] as $unidadmedida)
                                                                @if ($unidadmedida->id == 1)
                                                                    <option
                                                                        value="{{$unidadmedida->id}}"
                                                                        {{$unidadmedida->id == 1 ? 'selected' : ''}}
                                                                        >
                                                                        {{$unidadmedida->nombre}}
                                                                    </option>
                                                                    
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_anchodesv" class="control-label requerido" data-toggle='tooltip' title="Desviación Ancho">Desv</label>
                                                        <input type="text" name="at_anchodesv" id="at_anchodesv" class="form-control form_acutec valorrequerido" placeholder="Desviación" tipoval="texto" style="padding-right: 0px;padding-left: 2px;" readonly/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                                <div class="row">                    
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_largo" class="control-label requerido" data-toggle='tooltip' title="Largo">Largo</label>
                                                        <input type="text" name="at_largo" id="at_largo" class="form-control form_acutec numerico valorrequerido" placeholder="Largo" tipoval="texto"/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_largoum_text" class="control-label largoum_id" data-toggle='tooltip' title="Unidad Medida Largo">UM</label>
                                                        <select name="at_largoum_id" id="at_largoum_id"  class="selectpicker form-control largoum_id form_acutec" data-live-search='true'>
                                                            <option value="">Seleccione...</option>
                                                            @foreach($tablas['unidadmedidaAT'] as $unidadmedida)
                                                                @if ($unidadmedida->id == 1)
                                                                    <option
                                                                        value="{{$unidadmedida->id}}"
                                                                        {{$unidadmedida->id == 1 ? 'selected' : ''}}
                                                                        >
                                                                        {{$unidadmedida->nombre}}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_largodesv" class="control-label" data-toggle='tooltip' title="Desviación Largo">Desv</label>
                                                        <input type="text" name="at_largodesv" id="at_largodesv" class="form-control form_acutec" placeholder="Desviación" style="padding-right: 0px;padding-left: 2px;" readonly/>
                                                        <span class="help-block" style="margin-top: -2px; display: none;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="box box-default">
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_fuelle" class="control-label requerido" data-toggle='tooltip' title="Fuelle">Fuelle</label>
                                                        <input type="text" name="at_fuelle" id="at_fuelle" class="form-control form_acutec numerico valorrequerido" placeholder="Fuelle" tipoval="texto"/>
                                                        <span class="help-block" style="margin-top: -2px; display: none;"></span>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_fuelleum_text" class="control-label fuelleum_id" data-toggle='tooltip' title="Unidad Medida Fuelle">UM</label>
                                                        <select name="at_fuelleum_id" id="at_fuelleum_id" class="selectpicker form-control fuelleum_id form_acutec" data-live-search='true'>
                                                            <option value="">Seleccione...</option>
                                                            @foreach($tablas['unidadmedidaAT'] as $unidadmedida)
                                                                @if ($unidadmedida->id == 1)
                                                                    <option
                                                                        value="{{$unidadmedida->id}}"
                                                                        {{$unidadmedida->id == 1 ? 'selected' : ''}}
                                                                        >
                                                                        {{$unidadmedida->nombre}}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!--
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_fuelledesv" class="control-label" data-toggle='tooltip' title="Desviación Fuelle">Desv</label>
                                                        <input type="text" name="at_fuelledesv" id="at_fuelledesv" class="form-control form_acutec" placeholder="Desviación"/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                    -->
                                                </div>
                                                <div class="row">
                
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_espesor" class="control-label requerido" data-toggle='tooltip' title="Espesor">Espesor</label>
                                                        <input type="text" name="at_espesor" id="at_espesor" class="form-control form_acutec numerico4d valorrequerido" placeholder="Espesor" tipoval="texto" style="padding-right: 2px;padding-left: 6px;"/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_espesorum_text" class="control-label espesorum_id requerido" data-toggle='tooltip' title="Unidad Medida Espesor">UM</label>
                                                        <select name="at_espesorum_id" id="at_espesorum_id" class="selectpicker form-control espesorum_id form_acutec valorrequerido" data-live-search='true' tipoval="combobox">
                                                            <option value="">Seleccione...</option>
                                                            @foreach($tablas['unidadmedidaAT'] as $unidadmedida)
                                                                @if ($unidadmedida->id == 2)
                                                                    <option
                                                                        value="{{$unidadmedida->id}}"
                                                                        {{$unidadmedida->id == 2 ? 'selected' : ''}}
                                                                        >
                                                                        {{$unidadmedida->nombre}}
                                                                    </option>                                                                    
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                                        <label for="at_espesordesv" class="control-label requerido" data-toggle='tooltip' title="Desviación Espesor">Desv</label>
                                                        <input type="text" name="at_espesordesv" id="at_espesordesv" class="form-control form_acutec valorrequerido" placeholder="Desviación" tipoval="texto" style="padding-right: 0px;padding-left: 2px;" readonly/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="box box-default">
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12" classorig="col-xs-12 col-sm-12">
                                                        <label for="at_unidadmedida_id" class="control-label at_unidadmedida_id requerido" data-toggle='tooltip' title="Unidad Medida Base">Unid Medida Base</label>
                                                        <a class="btn-sm tooltipsC" data-original-title="Unidad de medida como va ser vendido el producto" aria-describedby="tooltip202998"><i class="fa fa-fw fa-question-circle text-aqua"></i></a>
                                                        <input type="text" name="at_unidadmedida_nombre" id="at_unidadmedida_nombre" class="form-control form_acutec valorrequerido" tipoval="texto" disabled/>
                                                        <div style="display:none">
                                                            <select name="at_unidadmedida_id" id="at_unidadmedida_id" class="selectpicker form-control at_unidadmedida_id form_acutec valorrequerido" data-live-search='true' tipoval="combobox">
                                                                <option value="">Seleccione...</option>
                                                                @foreach($tablas['unidadmedidaAT'] as $unidadmedida)
                                                                    <option
                                                                        value="{{$unidadmedida->id}}"
                                                                        >{{$unidadmedida->nombre}}</option>
                                                                @endforeach
                                                            </select>    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="div_at_formatofilm" name="div_at_formatofilm" style="display: none;">
                                        <div class="box box-default">
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12" classorig="col-xs-12 col-sm-12">
                                                        <label for="at_formatofilm" class="control-label at_formatofilm requerido" data-toggle='tooltip' title="Formato Film Strech en Kg">Formato Film Strech Kg.</label>
                                                        <a class="btn-sm tooltipsC" data-original-title="Formato Film Strech en Kg" aria-describedby="tooltip202998"><i class="fa fa-fw fa-question-circle text-aqua"></i></a>
                                                        <input type="text" name="at_formatofilm" id="at_formatofilm" class="form-control form_acutec numerico" tipoval="texto"/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Impresión</h3>
                                </div>
                                <div class="box-body">
                                    <div class="col-xs-12 col-sm-5" classorig="col-xs-12 col-sm-5">
                                        <label for="at_impreso" class="control-label requerido" data-toggle='tooltip' title="Impreso">Impreso</label>
                                        <select name="at_impreso" id="at_impreso"  class="selectpicker form-control impreso form_acutec valorrequerido" tipoval="combobox">
                                            <option value="">Seleccione...</option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                        <span class="help-block"></span>
                                    </div>            
                                    <div class="col-xs-12 col-sm-7">
                                        <label for="at_impresoobs" class="control-label" data-toggle='tooltip' title="Observación impreso">Observación</label>
                                        <input type="text" name="at_impresoobs" id="at_impresoobs" class="form-control form_acutec" placeholder="Observación impreso" maxlength="60"/>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Tipo de Sello</h3>
                                </div>
                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                            <label for="at_claseprod_id" class="control-label requerido" data-toggle='tooltip' title="Clase">Tipo Sello</label>
                                            <select name="at_claseprod_id" id="at_claseprod_id" class="selectpicker form-control at_claseprod_id form_acutec valorrequerido" data-live-search='true' tipoval="combobox">
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
    
                                        <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3" style="display: none;">
                                            <label for="at_tiposello_id" class="control-label requerido" data-toggle='tooltip' title="Tipo de Sello">Tipo Sello</label>
                                            <select name="at_tiposello_id" id="at_tiposello_id" class="selectpicker form-control tiposello_id form_acutec valorrequerido" data-live-search='true' tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                @foreach($tablas['tipoSello'] as $tiposello)
                                                    <option
                                                        value="{{$tiposello->id}}"
                                                        @if ($tiposello->id == 1)
                                                            {{'selected'}}
                                                        @endif
                                                        >{{$tiposello->desc}}</option>
                                                @endforeach            
                                            </select>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-8">
                                            <label for="at_tiposelloobs" class="control-label" data-toggle='tooltip' title="Observación fondo">Observación</label>
                                            <input type="text" name="at_tiposelloobs" id="at_tiposelloobs" class="form-control form_acutec" placeholder="Observación" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
<!--
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-5" classorig="col-xs-12 col-sm-5">
                                            <label for="at_sfondo" class="control-label requerido" data-toggle='tooltip' title="Fondo">Fondo</label>
                                            <select name="at_sfondo" id="at_sfondo"  class="selectpicker form-control sfondo form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>            
                                        <div class="col-xs-12 col-sm-7">
                                            <label for="at_sfondoobs" class="control-label" data-toggle='tooltip' title="Observación fondo">Observación</label>
                                            <input type="text" name="at_sfondoobs" id="at_sfondoobs" class="form-control form_acutec" placeholder="Observación"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-5" classorig="col-xs-12 col-sm-5">
                                            <label for="at_slateral" class="control-label requerido" data-toggle='tooltip' title="Lateral">Lateral</label>
                                            <select name="at_slateral" id="at_slateral"  class="selectpicker form-control slateral form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>            
                                        <div class="col-xs-12 col-sm-7">
                                            <label for="at_slateralobs" class="control-label" data-toggle='tooltip' title="Observación lateral">Observación</label>
                                            <input type="text" name="at_slateralobs" id="at_slateralobs" class="form-control form_acutec" placeholder="Observación"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-5" classorig="col-xs-12 col-sm-5">
                                            <label for="at_sprepicado" class="control-label requerido" data-toggle='tooltip' title="Prepicado">Prepicado</label>
                                            <select name="at_sprepicado" id="at_sprepicado"  class="selectpicker form-control sprepicado form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>            
                                        <div class="col-xs-12 col-sm-7">
                                            <label for="at_sprepicadoobs" class="control-label" data-toggle='tooltip' title="Observación Prepicado">Observación</label>
                                            <input type="text" name="at_sprepicadoobs" id="at_sprepicadoobs" class="form-control form_acutec" placeholder="Observación"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-5" classorig="col-xs-12 col-sm-5">
                                            <label for="at_slamina" class="control-label requerido" data-toggle='tooltip' title="Lamina">Lamina</label>
                                            <select name="at_slamina" id="at_slamina"  class="selectpicker form-control slamina form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>            
                                        <div class="col-xs-12 col-sm-7">
                                            <label for="at_slaminaobs" class="control-label" data-toggle='tooltip' title="Observación Lamina">Observación</label>
                                            <input type="text" name="at_slaminaobs" id="at_slaminaobs" class="form-control form_acutec" placeholder="Observación"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-5" classorig="col-xs-12 col-sm-5">
                                            <label for="at_sfunda" class="control-label requerido" data-toggle='tooltip' title="Funda">Funda</label>
                                            <select name="at_sfunda" id="at_sfunda"  class="selectpicker form-control sfunda form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>            
                                        <div class="col-xs-12 col-sm-7">
                                            <label for="at_sfundaobs" class="control-label" data-toggle='tooltip' title="Observación Funda">Observación</label>
                                            <input type="text" name="at_sfundaobs" id="at_sfundaobs" class="form-control form_acutec" placeholder="Observación"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
-->
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Forma de Embalaje</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                                            <label for="at_embalajeplastservi" class="control-label requerido" data-toggle='tooltip' title="Embalaje Plastiservi">Embalaje Plastiservi</label>
                                            <select name="at_embalajeplastservi" id="at_embalajeplastservi"  class="selectpicker form-control impreso form_acutec valorrequerido" tipoval="combobox"  onchange="embalajePlastiservi()">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>            
                                    </div>
                                    <div class="col-xs-12 col-sm-4">
                                        <label for="at_feunidxpaq" class="control-label" data-toggle='tooltip' title="Unidades por empaque">Unid x empaque</label>
                                        <input type="text" name="at_feunidxpaq" id="at_feunidxpaq" class="form-control form_acutec numerico embalaje" placeholder="Unidades por empaque" disabled/>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="col-xs-12 col-sm-8">
                                        <label for="at_feunidxpaqobs" class="control-label" data-toggle='tooltip' title="Observacion Unidades por empaque">Observación</label>
                                        <input type="text" name="at_feunidxpaqobs" id="at_feunidxpaqobs" class="form-control form_acutec embalaje" placeholder="Observación Unidades por empaque" maxlength="60" disabled/>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="col-xs-12 col-sm-4">
                                        <label for="at_feunidxcont" class="control-label" data-toggle='tooltip' title="Unidades por contenedor">Unid x contenedor</label>
                                        <input type="text" name="at_feunidxcont" id="at_feunidxcont" class="form-control form_acutec numerico embalaje" placeholder="Unidades por contenedor" disabled/>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="col-xs-12 col-sm-8">
                                        <label for="at_feunidxcontobs" class="control-label" data-toggle='tooltip' title="Observacion Unidades por contenedor">Observación</label>
                                        <input type="text" name="at_feunidxcontobs" id="at_feunidxcontobs" class="form-control form_acutec embalaje" placeholder="Observación Unidades por contenedor" maxlength="60" disabled/>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="col-xs-12 col-sm-4">
                                        <label for="at_fecolorcont" class="control-label" data-toggle='tooltip' title="Color contenedor">Color contenedor</label>
                                        <input type="text" name="at_fecolorcont" id="at_fecolorcont" class="form-control form_acutec embalaje" placeholder="Color contenedor" disabled/>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="col-xs-12 col-sm-8">
                                        <label for="at_fecolorcontobs" class="control-label" data-toggle='tooltip' title="Observacion Color contenedor">Observación</label>
                                        <input type="text" name="at_fecolorcontobs" id="at_fecolorcontobs" class="form-control form_acutec embalaje" placeholder="Observación Color contenedor" maxlength="60" disabled/>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="col-xs-12 col-sm-4">
                                        <label for="at_feunitxpalet" class="control-label" data-toggle='tooltip' title="Unidades por palet">Unid x palet</label>
                                        <input type="text" name="at_feunitxpalet" id="at_feunitxpalet" class="form-control form_acutec numerico embalaje" placeholder="Unidades por palet" disabled/>
                                        <span class="help-block"></span>
                                    </div>
                                    <div class="col-xs-12 col-sm-8">
                                        <label for="at_feunitxpaletobs" class="control-label" data-toggle='tooltip' title="Observacion Unidades por palet">Observación</label>
                                        <input type="text" name="at_feunitxpaletobs" id="at_feunitxpaletobs" class="form-control form_acutec embalaje" placeholder="Observación Unidades por palet" maxlength="60" disabled/>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Etiquetado</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6" classorig="col-xs-12 col-sm-6">
                                            <label for="at_etiqplastiservi" class="control-label requerido" data-toggle='tooltip' title="Etiqueta Plastiservi">Etiqueta Plastiservi</label>
                                            <select name="at_etiqplastiservi" id="at_etiqplastiservi"  class="selectpicker form-control etiqplastiservi form_acutec valorrequerido" tipoval="combobox">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                            </select>
                                            <span class="help-block"></span>
                                        </div>            
                                        <div class="col-xs-12 col-sm-6">
                                            <label for="at_etiqplastiserviobs" class="control-label" data-toggle='tooltip' title="Observación Etiqueta Plastiservi">Observación</label>
                                            <input type="text" name="at_etiqplastiserviobs" id="at_etiqplastiserviobs" class="form-control form_acutec" placeholder="Observación" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <label for="at_etiqotro" class="control-label" data-toggle='tooltip' title="Etiqueta Otro">Etiqueta otro</label>
                                            <input type="text" name="at_etiqotro" id="at_etiqotro" class="form-control form_acutec" placeholder="Etiqueta otro" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <label for="at_etiqotroobs" class="control-label" data-toggle='tooltip' title="Observación Etiqueta Otro">Observación</label>
                                            <input type="text" name="at_etiqotroobs" id="at_etiqotroobs" class="form-control form_acutec" placeholder="Observación" maxlength="60"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Certificados</h3>
                                </div>
                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6" classorig="col-xs-12 col-sm-6">
                                            <label for="at_certificados" class="control-label certificados requerido" data-toggle='tooltip' title="Certificado">Certificado</label>
                                            <!--<select name="at_certificados" id="at_certificados"  class="selectpicker form-control etiqplastiservi form_acutec valorrequerido1" tipoval="combobox" value="" multiple>-->
                                            <select name="at_certificados" id="at_certificados"  class="selectpicker form-control form_acutec at_certificados valorrequerido" data-live-search='true' tipoval="combobox" multiple>
                                                @foreach($tablas['certificado'] as $certificado)
                                                    <option value="{{$certificado->id}}">{{$certificado->descripcion}}</option>
                                                @endforeach
                                            </select>
                                            <span class="help-block"></span>
                                        </div>

                                        <div class="col-xs-12 col-sm-6" classorig="col-xs-12 col-sm-6">
                                            <label for="at_otrocertificado" class="control-label" data-toggle='tooltip' title="Otro certificado">Otro certificado</label>
                                            <input type="text" name="at_otrocertificado" id="at_otrocertificado" class="form-control form_acutec" placeholder="Otro certificado" maxlength="100"/>
                                            <span class="help-block"></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                @if (session('editaracutec') == '1')
                    <button type="button" class="btn btn-primary" id="btnAceptarAcuTecTemp" name="btnAceptarAcuTecTemp" title="Guardar">Aceptar</button>
                @endif
                <!--<input type="button" id="create_pdf1" value="Generate PDF">  -->
            </div>
        </div>
        
    </div>
</div>
<!--
<div id="contenedorCanvas" style="border: 1px solid red;">
</div>
-->

<input type="hidden" name="funcvalidarai" id="funcvalidarai" value="{{old('funcvalidarai', $funcvalidarai ?? '')}}">
<input type="hidden" name="idhide" id="idhide" value="{{old('puntonormativo', $data->id ?? '')}}">

<section class="content" style="display:none;" id="paso2time">
    <!-- row -->
    <div class="row">
      <div class="col-md-12">
        <!-- The time line -->
        <ul class="timeline">
            <li class="time-label">
                <span class="bg-red" id="fechanc" name="fechanc">
                </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li>
                <i class="fa fa-envelope bg-blue"></i>
                <div class="timeline-item">
                    <span class="time" id="horanc" name="horanc"></span>
                    <h3 class="timeline-header" id="motivonc_id" name="motivonc_id"></h3>
                    <h3 class="timeline-header" id="puntonormativo" name="puntonormativo"></h3>
                    <h3 class="timeline-header" id="formadeteccionnc" name="formadeteccionnc"></h3>
                    <h3 class="timeline-header" id="hallazgo" name="hallazgo"></h3>
                    <h3 class="timeline-header" id="jefaturas" name="jefaturas"></h3>
                    <h3 class="timeline-header" id="certificados" name="certificados"></h3>
                    <h3 class="timeline-header" id="puntonorma" name="puntonorma"></h3>
                    <h3 class="timeline-header" id="responsables" name="responsables"></h3>
                </div>
            </li>
            <!-- END timeline item -->
            <!-- timeline item -->



            <!-- timeline time label -->
            <li class="time-label">
                    <span class="bg-aqua" id="fechaai" name="fechaai">
                    </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li>
                <i class="fa fa-edit bg-blue" id="circuloedidAI" name="circuloedidAI"></i>

                <div class="timeline-item">
                <span class="time" id="horaai" name="horaai"></span>

                <h3 class="timeline-header" name="accioninmediatatxt" id="accioninmediatatxt"><a href="validarai()">Acción Inmediata</a></h3>

                <div class="timeline-body" id="linebodyai1">
                    <textarea name="accioninmediata" id="accioninmediata" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                </div>
                <div class="timeline-footer" id="linebodyai2">
                    <a id="guardarAI" name="guardarAI" class="btn btn-primary btn-xs">Guardar</a>
                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                </div>
                </div>
            </li>
            <!-- END timeline item -->

            <!-- timeline time label -->
            <li class="time-label obsvalai" style="display:none;">
                <span class="bg-aqua" id="fechavalai" name="fechavalai">
                </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li class="obsvalai" style="display:none;">
                <i class="fa fa-edit bg-blue"></i>

                <div class="timeline-item">
                <span class="time" id="horavalai" name="horavalai"></span>

                <h3 class="timeline-header" name="obsvalaitxt" id="obsvalaitxt"><a href="validarai()">Validación No conformidad</a></h3>

                <div class="timeline-body" id="linebodyvalai1">
                    <textarea name="obsvalai" id="obsvalai" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                </div>
                <div class="timeline-footer" id="linebodyvalai2">
                    <a id="guardarvalAIAp" name="guardarvalAIAp" class="btn btn-primary btn-xs" onclick="apre(1)">Aprobar</a>
                    <a id="guardarvalAIRe" name="guardarvalAIRe" class="btn btn-danger btn-xs" onclick="apre(0)">Rechazar</a>
                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                </div>
                </div>
            </li>
            <!-- END timeline item -->


            <!-- timeline item -->
            <!-- timeline time label -->
            <li class="time-label acausa" style="display:none;">
                <span class="bg-green" id="fechaac" name="fechaac">
                
                </span>
            </li>
                <!-- /.timeline-label -->
                <!-- timeline item -->
            <li class="acausa" style="display:none;">
                <i class="fa fa-edit bg-blue"></i>

                <div class="timeline-item">
                <span class="time" id="horaac" name="horaac"></span>

                <h3 class="timeline-header" name="analisisdecausatxt" id="analisisdecausatxt"><a href="#">Análisis de causa</a></h3>

                <div class="timeline-body" id="linebodyac1">
                    <textarea name="analisisdecausa" id="analisisdecausa" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                </div>
                <div class="timeline-footer" id="linebodyac2">
                    <a id="guardarACausa" name="guardarACausa" class="btn btn-primary btn-xs">Guardar</a>
                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                </div>
                </div>
            </li>
            <!-- END timeline item -->
            <!-- timeline item -->

            <!-- timeline item -->
            <!-- timeline time label -->
            <li class="time-label acorrect" style="display:none;">
                <span class="bg-purple" id="fechaacorr" name="fechaacorr">
                
                </span>
            </li>
                <!-- /.timeline-label -->
                <!-- timeline item -->
            <li class="acorrect" style="display:none;">
                <i class="fa fa-edit bg-blue"></i>

                <div class="timeline-item">
                <span class="time" id="horaacorr" name="horaacorr"></span>

                <h3 class="timeline-header" name="accorrectxt" id="accorrectxt"><a href="#">Acción Correctiva</a></h3>

                <div class="timeline-body" id="linebodyacorr1">
                    <textarea name="accorrec" id="accorrec" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                </div>
                <div class="timeline-footer" id="linebodyacorr2">
                    <a id="guardarACorr" name="guardarACorr" class="btn btn-primary btn-xs">Guardar</a>
                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                </div>
                </div>
            </li>
            <!-- END timeline item -->
            <!-- timeline item -->


            <!-- END timeline item -->
            <!-- timeline time label -->
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li class="acorrect" style="display:none;">
                <i class="fa fa-camera bg-purple"></i>

                <div class="timeline-item">
                <span class="time"></span>

                <h3 class="timeline-header"><a href="#">Adjuntar Archivos <i class="glyphicon glyphicon-paperclip"></i></a></h3>

                <div class="timeline-body">
                    <!--
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    -->
                    <DIV id="PANEL_0" class="panel panel-primary text-justify">
                        <DIV class="panel-heading">
                            <H3 class="panel-title">Seleccionar Archivos</H3>
                        </DIV>
                        <DIV class="panel-body">
                            <FORM id="form-general" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                                <input id="file-essAC" name="imagenesAC[]" type="file" multiple>
                                <SMALL id="textmensajearc" class="form-text text-muted">Seleccionar archivos: pdf, jpg, bmp, png.</SMALL>
                            </form>
                            <p>&nbsp;</p>
                            <div class="alert alert-success" role="alert"></div>
                        </DIV>
                    </DIV>

                </div>
                </div>
            </li>
            <!-- END timeline item -->

            <!-- timeline item -->
            <!-- timeline time label -->
            <li class="time-label fechacompromiso" style="display:none;">
                <span class="bg-yellow" id="fechafechacompromiso" name="fechafechacompromiso">
                
                </span>
            </li>
                <!-- /.timeline-label -->
                <!-- timeline item -->
            <li class="fechacompromiso" style="display:none;">
                <i class="fa fa-edit bg-aqua" id="circuloedidFC" name="circuloedidFC"></i>

                <div class="timeline-item">
                    <span class="time" id="horafechacompromiso" name="horafechacompromiso"></span>

                    <h3 class="timeline-header" name="fechacompromisotxt" id="fechacompromisotxt"><a href="#">Fecha de compromiso</a></h3>

                    <div class="timeline-body" id="linebodyfeccomp1">
                        <!--<textarea name="fechacompromiso" id="fechacompromiso" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>-->
                        <input type="text" bsDaterangepicker class="form-control datepicker requeridos" name="fechacompromiso" id="fechacompromiso" placeholder="DD/MM/AAAA" readonly>
                    </div>
                    <div class="timeline-footer" id="linebodyfeccomp2">
                        <a id="guardarfechacompromiso" name="guardarfechacompromiso" class="btn btn-primary btn-xs">Guardar</a>
                        <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                    </div>
                </div>
            </li>
            <li class="fechaguardado" style="display:none;">
                <i class="fa fa-cloud-upload bg-aqua" id="circuloedidFC" name="circuloedidFC"></i>

                <div class="timeline-item">
                    <span class="time" id="horafechaguardado" name="horafechaguardado"></span>

                    <h3 class="timeline-header" name="fechaguardadotxt" id="fechaguardadotxt"><a href="#">Fecha Guardado</a></h3>

                    <div class="timeline-body" id="linebodyfechaguardado1">
                        <!--<textarea name="fechaguardado" id="fechaguardado" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>-->
                        <input type="text" class="form-control requeridos" name="fechaguardado" id="fechaguardado" placeholder="DD/MM/AAAA" readonly>
                    </div>
                    <div class="timeline-footer" id="linebodyfechaguardado2">
                        <a id="guardarfechaguardado" name="guardarfechaguardado" class="btn btn-primary btn-xs">Guardar</a>
                        <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                    </div>
                </div>
            </li>

            <!-- END timeline item -->
            <!-- timeline item -->

            <!-- timeline time label -->
            <li class="time-label cumplimiento" style="display:none;">
                <span class="bg-aqua" id="fechacumplimiento" name="fechacumplimiento">
                </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li class="cumplimiento" style="display:none;">
                <i class="fa fa-edit bg-blue"></i>

                <div class="timeline-item">
                <span class="time" id="horacumplimiento" name="horacumplimiento"></span>

                <h3 class="timeline-header" name="cumplimientotxt" id="cumplimientotxt"><a href="validarai()">Validar Cumplimiento</a></h3>
                <div class="timeline-body" id="linebodycumplimiento1">
                    <textarea name="cumplimiento" id="cumplimiento" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                </div>
                <div class="timeline-footer" id="linebodycumplimiento2">
                    <a id="guardarcumplimientoSi" name="guardarcumplimientoSi" class="btn btn-primary btn-xs" onclick="cumplimientoSN(1)">Si </a>
                    <a id="guardarcumplimientoNo" name="guardarcumplimientoNo" class="btn btn-danger btn-xs" onclick="cumplimientoSN(0)">No</a>
                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                </div>
                </div>
            </li>
            <!-- END timeline item -->

            <!-- timeline time label -->
            <li class="time-label aprobpaso2" style="display:none;">
                <span class="bg-maroon" id="fechaaprobpaso2" name="fechaaprobpaso2">
                </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li class="aprobpaso2" style="display:none;">
                <i class="fa fa-edit bg-yellow"></i>

                <div class="timeline-item">
                <span class="time" id="horaaprobpaso2" name="horaaprobpaso2"></span>

                <h3 class="timeline-header" name="aprobpaso2txt" id="aprobpaso2txt"><a href="#" id="lblaprobpaso2" name="lblaprobpaso2">En revisión SGI</a></h3>
                <div class="timeline-body" id="linebodyaprobpaso21">
                    <h4 class="timeline-header" name="aprubaprobpaso2" id="aprubaprobpaso2">Aprueba?</h3>
                    <textarea name="aprobpaso2" id="aprobpaso2" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                </div>
                <div class="timeline-footer" id="linebodyaprobpaso22">
                    <a id="guardaraprobpaso2Si" name="guardaraprobpaso2Si" class="btn btn-primary btn-xs" onclick="aprobpaso2(1)">Si </a>
                    <a id="guardaraprobpaso2No" name="guardaraprobpaso2No" class="btn btn-danger btn-xs" onclick="aprobpaso2(0)">No</a>
                    <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                </div>
                </div>
            </li>
            <!-- END timeline item -->


            <!-- timeline time label -->
            <li class="time-label paso4" style="display:none;">
                <span class="bg-maroon" id="fechapaso4" name="fechapaso4">
                </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li class="paso4" style="display:none;">
                <i class="fa fa-edit bg-yellow"></i>

                <div class="timeline-item">
                    <span class="time" id="horapaso4" name="horapaso4"></span>

                    <h3 class="timeline-header" name="paso4txt" id="paso4txt"><a href="#" id="lblpaso4" name="lblpaso4">Resultado de medidas tomadas</a></h3>
                    <div class="timeline-body" id="linebodypaso41">
                        <textarea name="paso4" id="paso4" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                        <h4 class="timeline-header" name="aprubpaso4" id="aprubpaso4">Aprueba?</h3>
                        <a id="guardarpaso4Si" name="guardarpaso4Si" class="btn btn-primary btn-xs" onclick="paso4(1)">Si </a>
                        <a id="guardarpaso4No" name="guardarpaso4No" class="btn btn-danger btn-xs" onclick="paso4(0)">No</a>
                        <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                    </div>
                </div>
            </li>

            <!-- timeline item -->
            <li class="paso4" style="display:none;">
                <i class="fa fa-camera bg-purple"></i>

                <div class="timeline-item">
                <span class="time"></span>

                <h3 class="timeline-header"><a href="#">Adjuntar Archivos <i class="glyphicon glyphicon-paperclip"></i></a></h3>

                <div class="timeline-body">
                    <!--
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    -->
                    <DIV id="PANEL_0MT" class="panel panel-primary text-justify">
                        <DIV class="panel-heading">
                            <H3 class="panel-title">Seleccionar Archivos</H3>
                        </DIV>
                        <DIV class="panel-body">
                            <FORM id="form-generalMT" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                                <input id="file-essMT" name="imagenesMT[]" type="file" multiple>
                                <SMALL id="textmensajearc" class="form-text text-muted">Seleccionar archivos: pdf, jpg, bmp, png.</SMALL>
                            </form>
                            <p>&nbsp;</p>
                            <div class="alert alert-success" role="alert"></div>
                        </DIV>
                    </DIV>

                </div>
                </div>
            </li>
            <!-- END timeline item -->
            <!-- END timeline item -->


            <!-- timeline time label -->
            <li class="time-label paso5" style="display:none;">
                <span class="bg-maroon" id="fechapaso5" name="fechapaso5">
                </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li class="paso5" style="display:none;">
                <i class="fa fa-edit bg-yellow"></i>

                <div class="timeline-item">
                    <span class="time" id="horapaso5" name="horapaso5"></span>

                    <h3 class="timeline-header" name="paso5txt" id="paso5txt"><a href="#" id="lblpaso5" name="lblpaso5">Cierre y verificación de la eficacia de la acción correctiva</a></h3>
                    <div class="timeline-body" id="linebodypaso51">
                        <textarea name="paso5" id="paso5" class="form-control requeridos" tipoval="texto" value="" placeholder="Descripción"></textarea>
                        <a id="guardarpaso5Si" name="guardarpaso5Si" class="btn btn-primary btn-xs" onclick="paso5(1)">Guardar</a>
                        <!--<a class="btn btn-danger btn-xs">Delete</a>-->
                    </div>
                </div>
            </li>

            <!-- timeline item -->
            <li class="paso5" style="display:none;">
                <i class="fa fa-camera bg-purple"></i>

                <div class="timeline-item">
                <span class="time"></span>

                <h3 class="timeline-header"><a href="#">Adjuntar Archivos <i class="glyphicon glyphicon-paperclip"></i></a></h3>

                <div class="timeline-body">
                    <!--
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    <img src="http://placehold.it/150x100" alt="..." class="margin">
                    -->
                    <DIV id="PANEL_0CV" class="panel panel-primary text-justify PANEL_0CV">
                        <DIV class="panel-heading">
                            <H3 class="panel-title">Seleccionar Archivos</H3>
                        </DIV>
                        <DIV class="panel-body">
                            <FORM id="form-generalMT" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                                <input id="file-essCV" name="imagenesCV[]" type="file" class="file-essCV" multiple>
                                <SMALL id="textmensajearc" class="form-text text-muted">Seleccionar archivos: pdf, jpg, bmp, png.</SMALL>
                            </form>
                            <p>&nbsp;</p>
                            <div class="alert alert-success" role="alert"></div>
                        </DIV>
                    </DIV>

                </div>
                </div>
            </li>
            <!-- END timeline item -->


            <!-- timeline item -->
            <li>
                <i class="fa fa-clock-o bg-gray"></i>
            </li>
        </ul>
      </div>
      <!-- /.col -->
    </div>
    <!--<a id="Prueba" name="Prueba" class="btn btn-primary btn-xs">Prueba</a>-->
    <!-- /.row -->
    @include('generales.obsrechazoaprobpaso2')
</section>
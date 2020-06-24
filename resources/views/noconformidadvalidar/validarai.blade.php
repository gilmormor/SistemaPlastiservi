<div class="modal fade" id="myModalValidarai" tabindex="-1" role="dialog" aria-labelledby="ValidaraiLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="ValidaraiLabel">Validar acción Inmediata</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                        <label for="obsvalai" class="control-label">Observación</label>
                            <textarea name="obsvalai" id="obsvalai" class="form-control requeridos" tipoval="texto" value="" placeholder="Observación"></textarea>
                        <span class="help-block"></span>
                        <!--<input type="textarea" name="aprobobs" id="aprobobs" class="form-control" value="{{old('aprobobs', $data->aprobobs ?? '')}}" required placeholder="Observación"/>-->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnaprobarAI" name="btnaprobarAI" onclick="guardarvalai(1)" class="btn btn-primary">Aprobar</button>
                <button type="button" id="btnrechazarAI" name="btnrechazarAI" onclick="guardarvalai(0)" class="btn btn-danger">Rechazar</button>
            </div>
        </div>
    </div>
</div>
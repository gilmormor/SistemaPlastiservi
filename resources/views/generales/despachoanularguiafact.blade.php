<div class="modal fade" id="myModalanularguiafact" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="mdialTamanio">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Anular Guia despacho</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="nfilaanul" id="nfilaanul">
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="idanul" class="control-label">OD</label>
                        <input type="text" name="idanul" id="idanul" class="form-control" required placeholder="ID" disabled readonly/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-10" classorig="form-group col-xs-12 col-sm-10">
                        <label for="guiadespachoanul" class="control-label">Guia despacho</label>
                        <input type="text" name="guiadespachoanul" id="guiadespachoanul" class="form-control requeridos numerico" required placeholder="Guia despacho" disabled readonly/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                        <label for="observacionanul" class="control-label">Observación</label>
                        <textarea name="observacionanul" id="observacionanul" class="form-control requeridos"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnGuardarGanul" name="btnGuardarGanul" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        
    </div>
</div>
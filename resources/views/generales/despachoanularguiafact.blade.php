<div class="modal fade" id="myModalanularguiafact" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="mdialTamanio1">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 id="tituloAGFAC" name="tituloAGFAC" class="modal-title">Anular Guia despacho</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="guiadesp_id" id="guiadesp_id" value="">
                <input type="hidden" name="updated_at" id="updated_at" value="">
                <div class="row">
                    <input type="hidden" name="nfilaanul" id="nfilaanul">
                    <div class="form-group col-xs-12 col-sm-2">
                        <label id="id1" name="id1" for="idanul" class="control-label">OD</label>
                        <input type="text" name="idanul" id="idanul" class="form-control" required placeholder="ID" disabled readonly/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-6" classorig="form-group col-xs-12 col-sm-6">
                        <label id="id2" name="id2" for="guiadespachoanul" class="control-label">Guia despacho</label>
                        <input type="text" name="guiadespachoanul" id="guiadespachoanul" class="form-control numerico" required placeholder="Guia despacho" disabled readonly/>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group col-xs-12 col-sm-4" classorig="form-group col-xs-12 col-sm-4">
                        <label for="statusM" class="col-form-label">Status</label>
                        <select name="statusM" id="statusM" class="selectpicker form-control requeridos" title='Seleccione...' tipoval="combobox">
                            @if ($aux_vista=="G")
                                <option value="2" {{'selected'}}>Orden Despacho</option>                            
                            @endif
                            @if ($aux_vista=="F")
                                <option value="1">Guia</option>
                                <option value="2">Orden Despacho</option>                            
                            @endif
                        </select>
                        <span class="help-block"></span>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                        <label for="observacionanul" class="control-label">Observación</label>
                        <textarea name="observacionanul" id="observacionanul" class="form-control requeridos" tipoval="texto"></textarea>
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
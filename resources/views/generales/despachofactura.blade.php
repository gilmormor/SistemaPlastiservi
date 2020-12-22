<div class="modal fade" id="myModalnumfactura" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="mdialTamanio">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Número Factura</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="nfilaf" id="nfilaf">
                    <input type="hidden" name="status" id="status">
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="idf" class="control-label">OD</label>
                        <input type="text" name="idf" id="idf" class="form-control" required placeholder="ID" disabled readonly/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-5" classorig="form-group col-xs-12 col-sm-5">
                        <label for="numfacturam" class="control-label">Número Factura</label>
                        <input type="text" name="numfacturam" id="numfacturam" class="form-control requeridos numerico" tipoval="texto" required placeholder="Número de Factura"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group col-xs-12 col-sm-5" classorig="form-group col-xs-12 col-sm-5">
                        <label for="fechafacturam" class="control-label" data-toggle='tooltip' title="Fecha Factura">Fecha Factura</label>
                        <input type="text" name="fechafacturam" id="fechafacturam" class="form-control pull-right datepicker requeridos" tipoval="texto" required readonly/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnGuardarF" name="btnGuardarF" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        
    </div>
</div>
<div class="modal fade" id="myModalactualizarFileOC" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Actializar File OC</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                                            <label id="lboc_id" name="lboc_id" for="oc_id" class="control-label">Nro OrdenCompra</label>
                                            <div class="input-group">
                                                <input type="text" name="oc_id1" id="oc_id1" class="form-control" value="{{old('oc_id1', $data->oc_id ?? '')}}" placeholder="Nro Orden de Compra"/>
                                            </div>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                                            <label id="lboc_id" name="lboc_id" for="oc_file" class="control-label">Adjuntar OC</label>
                                            <div class="input-group">
                                                <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->oc_file) ? Storage::url("imagenes/notaventa/$data->oc_file") : ""}}' accept="image/*"/>
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
                <button type="button" class="btn btn-primary" id="btnGuardarFileOC" name="btnGuardarFileOC" title="Guardar">Guardar</button>
            </div>
        </div>
        
    </div>
</div>
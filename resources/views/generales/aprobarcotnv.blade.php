<div class="modal fade" id="myModalaprobcot" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="mdialTamanio1">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Aprobar</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="id" class="control-label">ID</label>
                        <input type="text" name="id" id="id" class="form-control" value="{{old('id', $data->id ?? '')}}" required placeholder="ID" {{$disabledReadOnly}}/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="aprobusu_id" class="control-label">Cod Usu</label>
                        <input type="text" name="aprobusu_id" id="aprobusu_id" class="form-control" value="{{old('aprobusu_id', auth()->id() ?? '')}}" required placeholder="Cod Usuario" {{$disabledReadOnly}}/>
                    </div>
                    <div class="form-group col-xs-12 col-sm-8">
                        <label for="aprobusu_nom" class="control-label">Nombre Usuario</label>
                        <input type="text" name="aprobusu_nom" id="aprobusu_nom" class="form-control" value="{{old('aprobusu_nom', session()->get('nombre_usuario') ?? '')}}" required placeholder="Nombre Usuario" {{$disabledReadOnly}}/>
                    </div>
                </div>
                    <!--
                    <div class="form-group col-xs-12 col-sm-2">
                        <label for="aprobfechahora" class="control-label requerido">Fecha aprobación</label>
                        <input type="text" name="aprobfechahora" id="aprobfechahora" class="form-control" value="{{old('aprobfechahora', $data->aprobfechahora ?? '')}}" required placeholder="Fecha Aprobación" {{$disabledReadOnly}}/>
                    </div>-->
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                        <label for="aprobobs" class="control-label">Observación</label>
                        <textarea name="aprobobs" id="aprobobs" class="form-control requeridos" tipoval="texto" value="{{old('aprobobs', $data->aprobobs ?? '')}}" placeholder="Observación"></textarea>
                        <span class="help-block"></span>
                        <!--<input type="textarea" name="aprobobs" id="aprobobs" class="form-control" value="{{old('aprobobs', $data->aprobobs ?? '')}}" required placeholder="Observación"/>-->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnaprobarM" name="btnaprobarM" class="btn btn-primary">Aprobar</button>
                <button type="button" id="btnrechazarM" name="btnrechazarM" class="btn btn-danger">Rechazar</button>
            </div>
        </div>
        
    </div>
</div>
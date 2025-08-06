<div class="form-group col-xs-12 col-sm-2">
    <label for="producto_idTDT" class="control-label requerido" title="Producto">Producto</label>
    <input type="text" name="producto_idTDT{{$data->acuerdotecnico->id}}" id="producto_idTDT{{$data->acuerdotecnico->id}}" class="form-control" value="{{old('producto_idTDT', $data->id ?? '')}}" categoriaprod_id="{{$data->categoriaprod_id}}" readonly/>
    <input type="text" name="unidadmedida_id{{$data->acuerdotecnico->id}}" id="unidadmedida_id{{$data->acuerdotecnico->id}}" class="form-control" value="{{old('unidadmedida_id', $data->acuerdotecnico->at_unidadmedida_id ?? '')}}" style="display:none;"/>
    <input type="text" name="producto_id" id="producto_id" class="form-control" value="{{old('producto_id', $data->id ?? '')}}" style="display:none;"/>
</div>

<div class="form-group col-xs-12 col-sm-2">
    <label for="acuerdotecnico_id" class="control-label requerido" title="Id Acuerdo Tecnico">Id At</label>
    <input type="text" name="acuerdotecnico_id" id="acuerdotecnico_id" class="form-control" value="{{old('acuerdotecnico_id', $data->acuerdotecnico->id ?? '')}}" readonly/>
</div>

<div class="form-group col-xs-12 col-sm-4">
    <label for="nombreProdTD" class="control-label">Nombre Producto</label>
    <input type="text" name="nombreProdTD{{$data->acuerdotecnico->id}}" id="nombreProdTD{{$data->acuerdotecnico->id}}" class="form-control" value="{{old('nombreProdTD', $tablas["atributoProd"]["nombre"] ?? '')}}" required placeholder="Nombre Producto" categoriaprod_nombre={{$data->categoriaprod->nombre}} readonly/>
</div>
<div class="form-group col-xs-12 col-sm-4">
    <label for="acuerdotecnico" class="control-label requerido" title="Editar Acuerdo Tecnico">Editar AT</label>
    <input type="text" name="acuerdotecnico{{$data->acuerdotecnico->id}}" id="acuerdotecnico{{$data->acuerdotecnico->id}}" class="form-control" value="{{json_encode($data->acuerdotecnico)}}" style="display:none;"/>
    <div name="acuerdotecnico" id="acuerdotecnico" class="form-control">
        <a class="btn-accion-tabla tooltipsC" title="Editar Acuerdo tecnico" onclick="crearEditarAcuTec({{$data->acuerdotecnico->id}})">
            @if ($data->acuerdotecnico != null)
                <i id="icoat{{$data->acuerdotecnico->id}}" class="fa fa-cog text-aqua girarimagen"></i>
                <?php 
                    $aux_mostrarimagenat = "";
                    if($data->acuerdotecnico->at_impreso==1){
                        $aux_mostrarimagenat = "display:inline;";
                    }
                ?>
            @endif
        </a>
    </div>
</div>
<div id="group_at_firmado" class="form-group col-xs-12 col-sm-4">
    <label id="lboc_at_firmado" name="lboc_at_firmado" for="at_filefirmado" class="control-label">AT firmado</label>
    <div class="input-group">
        <?php 
            $aux_at_firmado = $data->acuerdotecnico->at_firmado ?? null;
        ?>
        <input type="hidden" name="at_filefirmado_deleted" id="at_filefirmado_deleted" value="0">
        <input type="file" name="at_filefirmado" id="at_filefirmado" class="form-control" data-initial-preview='{{isset($aux_at_firmado) ? Storage::url("imagenes/atfirm/$aux_at_firmado") : ""}}' accept=".jpg,.jpeg,.png,.pdf, application/pdf"/>
    </div>
</div>
@if ($data->acuerdotecnico->at_impreso == 1)
    <div id="group_at_impresofoto" class="form-group col-xs-12 col-sm-4">
        <label id="lboc_at_impresofoto" name="lboc_at_impresofoto" for="at_fileimpresofoto" class="control-label">Arte impresion</label>
        <div class="input-group">
            <?php 
                $aux_at_impresofoto = $data->acuerdotecnico->at_impresofoto ?? null;
            ?>
            <input type="hidden" name="at_fileimpresofoto_deleted" id="at_fileimpresofoto_deleted" value="0">
            <input type="file" name="at_fileimpresofoto" id="at_fileimpresofoto" class="form-control" data-initial-preview='{{isset($aux_at_impresofoto) ? Storage::url("imagenes/at/$aux_at_impresofoto") : ""}}' accept=".jpg,.jpeg,.png,.pdf, application/pdf"/>
        </div>
    </div>
@endif

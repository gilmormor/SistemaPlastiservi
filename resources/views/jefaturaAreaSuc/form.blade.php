<div class="form-group">
    <label for="suc_nombre" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-8">
    <input type="text" name="suc_nombre" id="suc_nombre" class="form-control" value="{{old('suc_nombre', $sucursales[0]->suc_nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="are_nombre" class="col-lg-3 control-label requerido">Nombre Area</label>
    <div class="col-lg-8">
    <input type="text" name="are_nombre" id="are_nombre" class="form-control" value="{{old('are_nombre', $sucursales[0]->are_nombre ?? '')}}" required/>
    </div>
</div>

<div class="form-group">
    <label for="jefatura_id" class="col-lg-3 control-label requerido">Jefatura</label>
    <div class="col-lg-8">
        <div class="input-group">    
            <select name="jefatura_id[]" id="jefatura_id" class="form-control select2" multiple required>
                @foreach($jefaturas as $id => $nombre)
                    <option
                        value="{{$id}}"
                        {{is_array(old('jefatura_id')) ? (in_array($id, old('jefatura_id')) ? 'selected' : '') : (isset($sucursales[0]) ? ($sucursales[0]->jefaturas->firstWhere('id', $id) ? 'selected' : '') : '')}}
                        >
                        {{$nombre}}
                    </option>
                @endforeach
            </select>
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="btnjefe" name="btnjefe" data-toggle='tooltip' title="Asignar Jefe Departamento">Jefe</button>
            </span>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalJefe" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Jefe de Departamento</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                                <thead>
                                    <tr>
                                        <th>Jefatura</th>
                                        <th>Jefe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0;?>
                                    @foreach($jefaturasucursalareas as $jefaturasucursalarea)
                                        <tr name="fila{{$i}}" id="fila{{$i}}">
                                            <td name="jefatura{{$i}}" id="jefatura{{$i}}" jefatura_id={{$jefaturasucursalarea->id}}>
                                                {{$jefaturasucursalarea->jefatura->nombre}}
                                            </td>
                                            <td name="persona_id{{$i}}" id="persona_id{{$i}}">
                                                <select name="personal_idD{{$i}}" id="personal_idD{{$i}}" class="selectpicker form-control" data-live-search='true'>
                                                    <option value="">Seleccione...</option>
                                                    @foreach($personas as $persona)
                                                        <option
                                                            value="{{$persona->id}}"
                                                            @if ($jefaturasucursalarea->persona_id==$persona->id)
                                                                {{'selected'}}
                                                            @endif
                                                            >
                                                            {{$persona->nombre}} {{$persona->apellido}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <?php $i++;?>
                                    @endforeach
                                </tbody>
                            </table>
            
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="guardarJefe" name="guardarJefe" class="btn btn-primary" items={{$i}}>Guardar</button>
            </div>
        </div>
        
    </div>
</div>
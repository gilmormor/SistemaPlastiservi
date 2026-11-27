<div class="modal fade" id="myModalEditarCampoTex" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="mdialTamanio1">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="titeditarcampo" name="titeditarcampo">Editar Campo</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12">
                        <label id="lbleditarcampo" name="lbleditarcampo" for="id" class="control-label">Campo</label>
                        {{-- Campo de varias lineas SOLO si la pantalla lo pide. Este modal lo
                             comparten cinco pantallas y en las de guia de despacho y factura
                             edita el nombre del producto y la unidad de medida que viajan al
                             XML del DTE: alli un salto de linea no tiene nada que hacer.
                             Sin la variable se pinta el input de siempre, sin ningun cambio.

                             El textarea conserva id, name, class, tipoval, required y
                             placeholder, asi que verificarDato() lo valida igual: usa
                             serializeArray() y getElementById().value, que no distinguen
                             entre input y textarea. --}}
                        @if(!empty($auxEditCampoMultilinea))
                            <textarea name="auxeditcampoT" id="auxeditcampoT" class="form-control requeridos valorrequerido" tipoval="texto" required placeholder="Ingrese valor" fila_id="" aux_nomcampot="" rows="3" style="resize:vertical;"></textarea>
                        @else
                            <input type="text" name="auxeditcampoT" id="auxeditcampoT" class="form-control requeridos valorrequerido" tipoval="texto" required placeholder="Ingrese valor" fila_id="" aux_nomcampot=""/>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="btnaceptarMT" name="btnaceptarMT" class="btn btn-primary">Aceptar</button>
            </div>
        </div>
        
    </div>
</div>
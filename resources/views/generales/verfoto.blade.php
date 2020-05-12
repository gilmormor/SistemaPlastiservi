<div class="modal fade" id="myModalverfoto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Orden de Compra</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="card" style="width: 87rem">
                        <?php
                            $info = new SplFileInfo($nameFile);
                            $ext = $info->getExtension();
                            if($ext=='pdf'){
                                ?>
                                <object data="{{Storage::url("$ruta$nameFile")}}" type="application/pdf" width="100%" height="100%"></object>
                            <?php }
                            else{
                            ?>
                                <img id="id_img_fondo" src="{{Storage::url("$ruta$nameFile")}}" alt="User Image" >
                            <?php }
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        
    </div>
</div>
{{-- Modal etiqueta de etapa (Reg. Prod.), usado por verEtiquetaEtapaConPermiso() en general.js.
     Requiere solo que la página incluya general.js. --}}
<div class="modal fade modal-etiqueta" id="modalEtiquetaEtapa" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:440px;">
        <div class="modal-content" style="border-radius:6px; overflow:hidden;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" style="font-size:14px;">
                    <i class="fa fa-tag"></i>&nbsp; Etiqueta de etapa &mdash; Reg. <span id="modalEtiquetaId"></span>
                </h4>
            </div>
            <div class="modal-body" style="padding:0; height:340px;">
                <iframe id="ifrEtiquetaEtapa" name="ifrEtiquetaEtapa" src="about:blank"
                        style="width:100%; height:340px; border:none;"
                        sandbox="allow-scripts allow-same-origin allow-popups allow-modals">
                </iframe>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e8edf3; padding:10px 16px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="imprimirEtiquetaEtapa()">
                    <i class="fa fa-print"></i> Imprimir etiqueta
                </button>
            </div>
        </div>
    </div>
</div>

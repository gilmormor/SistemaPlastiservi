@extends("theme.$theme.layout")
@section('titulo')
Reporte CC por Lote
@endsection
@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script>
    // Abre el PDF en modal; limpia el iframe al cerrar para liberar memoria
    // aux_venmodant: id de un modal anterior a ocultar (usado cuando este partial
    // se embebe dentro de otra pantalla, ej. despachosol/form.blade.php). En esta
    // página no aplica (no hay modal anterior), se ignora si no existe.
    function verPdfCcLote(url, aux_venmodant) {
        if (aux_venmodant && $('#' + aux_venmodant).length) {
            $('#' + aux_venmodant).modal('hide');
        }
        $('#iframePdfCcLote').attr('src', url);
        $('#modalPdfCcLote').modal('show');
    }
    $('#modalPdfCcLote').on('hidden.bs.modal', function () {
        $('#iframePdfCcLote').attr('src', '');
    });

    $(document).ready(function () {
        function consultarLote() {
            var loteId = $('#inp-lote-id').val().trim();
            if (!loteId || isNaN(loteId)) {
                Biblioteca.notificaciones('Ingrese un número de lote válido.', 'Reporte CC', 'error');
                return;
            }
            $('#div-resultado').hide();
            $('#div-error-lote').hide().html('');
            $('#div-cargando').show();

            $.ajax({
                url  : '/reportcclote/consultar',
                data : { opdetregprod_id: loteId },
                type : 'GET',
                success: function (html) {
                    $('#div-cargando').hide();
                    $('#div-detalle-lote').html(html);
                    $('#div-resultado').show();
                    $('[data-toggle="tooltip"], .tooltipsC').tooltip();
                },
                error: function (xhr) {
                    $('#div-cargando').hide();
                    var msg = xhr.responseText || 'Error al consultar el lote.';
                    $('#div-error-lote').html(msg).show();
                }
            });
        }

        $('#btn-consultar-lote').on('click', consultarLote);
        $('#inp-lote-id').on('keypress', function (e) {
            if (e.which === 13) consultarLote();
        });
        $('#inp-lote-id').on('input', function () {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });
    });
    </script>
@endsection

@section('contenido')

{{-- Modal PDF muestra CC (usado por genpdfCC en general.js) --}}
@include('generales.modalpdf')

{{-- Modal etiqueta de etapa (usado por verEtiquetaEtapaConPermiso en general.js) --}}
@include('generales.modaletiquetaetapa')

{{-- Modal PDF Reporte CC por Lote --}}
<div class="modal fade" id="modalPdfCcLote" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:90%;max-width:1100px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-file-pdf-o text-danger"></i> Reporte CC — Cobertura por Lote
                </h4>
            </div>
            <div class="modal-body" style="padding:0;height:80vh;">
                <iframe id="iframePdfCcLote" src="" style="width:100%;height:100%;border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-search"></i> Reporte CC — Cobertura por Lote / Etapas
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-4">
                        <label>N° de Lote / Registro de Producción:</label>
                        <div class="input-group">
                            <input type="text" id="inp-lote-id" class="form-control" placeholder="Ej: 131" maxlength="10"
                                   style="font-size:16px;" autofocus>
                            <span class="input-group-btn">
                                <button id="btn-consultar-lote" class="btn btn-success" type="button">
                                    <i class="fa fa-search"></i> Consultar
                                </button>
                            </span>
                        </div>
                        <p class="help-block" style="font-size:11px;">
                            Ingrese cualquier lote de la cadena de producción — se mostrarán todas las etapas del producto.
                        </p>
                    </div>
                </div>

                <div id="div-resultado" style="margin-top:16px;display:none;">
                    <div id="div-detalle-lote"></div>
                </div>

                <div id="div-cargando" style="display:none;text-align:center;padding:30px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i><br>
                    <span style="color:#888;">Cargando...</span>
                </div>

                <div id="div-error-lote" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

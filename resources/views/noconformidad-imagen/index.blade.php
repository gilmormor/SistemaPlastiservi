@extends("theme.$theme.layout")
@section('titulo')
Cotización
@endsection
<!--
https://plugins.krajee.com/file-preview-management-demo#top
http://minubeinformatica.com/articulos/programacion/como-utilizar-el-plugin-fileinput-bootstrap-para-la-subida-masiva-de-archivos
-->
@section("styles")
    <link rel="stylesheet" href="{{asset("assets/js/bootstrap-fileinput/css/fileinput.min.css")}}">
@endsection

@section("scriptsPlugins")
    <script src="{{asset("assets/js/bootstrap-fileinput/js/fileinput.min.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/js/bootstrap-fileinput/js/locales/es.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/js/bootstrap-fileinput/themes/fas/theme.min.js")}}" type="text/javascript"></script>
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/noconformidad-imagen/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">No Conformidad</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
<!--                    <p>&nbsp;</p>
                    <p>&nbsp;</p>    
-->
                    <div class="form-group col-xs-12 col-sm-12"> 
                        <DIV id="PANEL_0" class="panel panel-primary text-justify">
                            <DIV class="panel-heading">
                                <H3 class="panel-title">Envio de solicitud</H3>
                            </DIV>
                            <DIV class="panel-body">
                                <FORM id="form-general" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                                    <label for="file-es" role="button">Seleccionar Archivos</label>
                                    <input id="file-es" name="file-es[]" type="file" multiple>
                                    <SMALL class="form-text text-muted">Seleccionar archivos de Office 201X: docx, xlsx, pptx y pdf hasta un máximo de 5.</SMALL>
                                </form>
                                <p>&nbsp;</p>
                                <div class="alert alert-success" role="alert"></div>
                            </DIV>
                        </DIV>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="myModalguiadespacho" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="mdialTamanio">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="titulomodal" name="titulomodal">Guias Despacho</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="notaventa_idhide" id="notaventa_idhide" value="">
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                        <label for="guiasdespacho" class="control-label">Guias</label>
                        <textarea name="guiasdespacho" id="guiasdespacho" class="form-control requeridos" tipoval="texto" value="" placeholder="Guias Despacho"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="guardarGD" name="guardarGD" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        
    </div>
</div>

@endsection

<div class="modal fade" id="myModalBuscarProd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Productos</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="row" id="staprodxcli" style="display: none">
                        <div class="col-lg-6" id="DivVerTodosProd" name="DivVerTodosProd">
                            <label class="switch tooltipsC" id="lblVerTodosProd" name="lblVerTodosProd" title="Ver todos los Productos">
                                <input id="VerTodosProd" name="VerTodosProd" type="checkbox" >
                                <div class="slider round"></div>
                            </label>
                            <label id="lblTitVerTosdosProd" name="lblTitVerTosdosProd">Productos X Cliente</label>
                        </div>
                        <div class="col-lg-6" id="DivchVerAcuTec" name="DivchVerAcuTec" style="display:none;">
                            <label class="switch tooltipsC" id="lblVerAcuTec" name="lblVerAcuTec" title="Ver Productos Base para crear Acuerdo Técnico">
                                <input id="VerAcuTec" name="VerAcuTec" type="checkbox" >
                                <div class="slider round"></div>
                            </label>
                            <label id="lbltipoprod" name="lbltipoprod">Productos</label>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
                                    <div class="table-responsive">
                                        <!--<table class="table table-striped table-bordered table-hover display tablas" id="tabla-data-productos" style="width:100%">-->
                                        <table id="tabla-data-productos" class="table-hover display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Clase<br>Sello</th>
                                                    <th>Diamet/<br>Ancho</th>
                                                    <th style="text-align:right">Largo</th>
                                                    <th style="text-align:right">Peso/<br>Espesor</th>
                                                    <th style="text-align:center">TipU</th>
                                                    <th style="text-align:right">PrecN</th>
                                                    <th style="text-align:right">Precio</th>
                                                    <th class="ocultar">tipoprod</th>
                                                    <th class="ocultar">acuerdotecnico_id</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Clase</th>
                                                    <th>Diametro</th>
                                                    <th>Largo</th>
                                                    <th>Peso</th>
                                                    <th>TipU</th>
                                                    <th>PrecN</th>
                                                    <th>Prec</th>
                                                    <th class="ocultar">tipoprod</th>
                                                    <th class="ocultar">acuerdotecnico_id</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    @if (isset($selecmultprod) and $selecmultprod == true) <!-- Valiable creada en el controlador para validar si se puede hacer multiple seleccion -->
                                        <div class="col-xs-12" id="divprodselec" name="divprodselec">
                                            <div class="col-xs-12 col-md-1 col-sm-1 text-left" title="Productos seleccionados">
                                                <label for="productos">Selección:</label>
                                            </div>
                                            <div class="col-xs-12 col-md-11 col-sm-11" title="Productos seleccionados">
                                                <select id="productos" name="productos" class="form-control select2" multiple>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                @if (isset($selecmultprod) and $selecmultprod == true) <!-- Valiable creada en el controlador para validar si se puede hacer multiple seleccion -->
                    <button id="aceptarmbp" name="aceptarmbp" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                @endif
            </div>
            <input type="hidden" name="totalreg" id="totalreg">
        </div>
    </div>
</div>
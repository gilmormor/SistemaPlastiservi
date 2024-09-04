<div class="modal fade" id="myModalBuscarProdSelectMult" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                    {{-- <div class="row" id="staprodxcli" style="display: none">
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
                    </div> --}}
                    <div class="col-lg-12">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <input type="hidden" name="aux_numfilasm" id="aux_numfilasm" value="0">
                                    <div class="table-responsive">
                                        <!--<table class="table table-striped table-bordered table-hover display tablas" id="tabla-data-productos" style="width:100%">-->
                                        <table id="tabla-data-productos-selectmult" class="table-hover display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th id="thselectAll" name="thselectAll" class="width100" style="text-align:center">
                                                        <div class="checkbox" style="padding-top: 0px;" title="Marcar todo">
                                                            <div>
                                                                <label style="font-size: 1.0em">
                                                                    <input type="checkbox" class="checkllenarCantSol" id="SelecAll" name="SelecAll">
                                                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </th>
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
                                                    <th>check</th>
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12" id="divprodselecsm" name="divprodselecsm">
                        <input type="hidden" name="producto_idsm" id="producto_idsm" class="form-control"/>
                        <div class="col-xs-12 col-md-2 col-sm-2 text-right">
                            <label for="tipoprecio" >Tipo Precio</label>
                        </div>
                        <div class="col-xs-12 col-md-4 col-sm-4"  classorig="col-xs-12 col-md-4 col-sm-4">
                            {{-- <select name="tipoprecio" id="tipoprecio" class="form-control select2 tipoprecio requeridopantprodselmult" data-live-search='true'  required> --}}
                            <select name="tipoprecio" id="tipoprecio" class="selectpicker form-control requeridopantprodselmult" tipoval="combobox" required>
                                <option value="">Seleccione...</option>
                                <option value="1">Por Unidad</option>
                                <option value="2">Por Kilo</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-2 col-sm-2 text-right" title="Precio">
                            <label for="preciosm">Precio:</label>
                        </div>
                        <div class="col-xs-12 col-md-2 col-sm-2" title="Precio"  classorig="col-xs-12 col-md-2 col-sm-2">
                            <input type="text" name="preciosm" id="preciosm" class="form-control requeridopantprodselmult numericoblanco" tipoval="texto" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" id="aceptarmbpsm" name="aceptarmbpsm" class="btn btn-primary">Aceptar</button>
            </div>
            <input type="hidden" name="totalreg" id="totalreg">
        </div>
    </div>
</div>
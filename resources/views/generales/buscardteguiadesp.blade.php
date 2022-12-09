<div class="modal fade" id="myModalBuscardteguiadesp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Guia Despacho SII</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
                                    <?php 
                                        $aux_selecguiadesp = "";
                                        if(isset($data)){
                                            $aux_selecguiadesp = implode(",", $data->dtedtes->pluck('dter_id')->toArray()) ;
                                        }
                                    ?>
                                    <input type="hidden" name="selectguiadesp" id="selectguiadesp" value={{$aux_selecguiadesp}}>
                                    <input type="hidden" name="selectguiadesp_update" id="selectguiadesp_update">
                                    <div class="table-responsive">
                                        <!--<table class="table table-striped table-bordered table-hover display tablas" id="tabla-data-productos" style="width:100%">-->
                                        <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-dteguiadesp" name="tabla-data-dteguiadesp"  data-page-length="10">
                                            <thead>
                                                <tr>
                                                    <th class='tooltipsC' title="ID">ID</th>
                                                    <th>Fecha</th>
                                                    <th class='tooltipsC' title="Cotizacion">Cot</th>
                                                    <th class='tooltipsC' title="Orden de Compra">OC</th>
                                                    <th class='tooltipsC' title="Nota Venta">NV</th>
                                                    <th class='tooltipsC' title="Solicitud Despacho">SD</th>
                                                    <th class='tooltipsC' title="Orden Despacho">OD</th>
                                                    <th class='tooltipsC' title="Guia Despacho SII">GD</th>
                                                    <th>Comuna</th>
                                                    <th>TE</th>
                                                    <th></th>
                                                    <th class='ocultar'>TE</th>
                                                    <th class="ocultar">Icono</th>
                                                    <th class="ocultar">Obs Bloqueo</th>
                                                    <th class="ocultar">oc_file</th>
                                                    <th class="ocultar">updated_at</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnaceptarGD" name="btnaceptarGD" disabled>Aceptar</button>
            </div>
            <input type="hidden" name="totalreg" id="totalreg">
        </div>
    </div>
</div>
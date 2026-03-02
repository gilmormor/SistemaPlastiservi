<div class="modal fade" id="buscarInsumoBDModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content-->        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Insumos</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="row">
                                    <input type="hidden" name="aux_numfila_insumo" id="aux_numfila_insumo" value="0">
                                    <div class="table-responsive">
                                        <table id="tabla-data-insumos" class="table-hover display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Obs</th>
                                                    {{-- <th>Costo</th>
                                                    <th>Unidad</th>
                                                    <th>Tipo costo</th>
                                                    <th>Activo</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            {{-- <tfoot>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Obs</th>
                                                    <th>Costo</th>
                                                    <th>Unidad</th>
                                                    <th>Tipo costo</th>
                                                    <th>Activo</th>
                                                </tr>
                                            </tfoot> --}}
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
            </div>
            <input type="hidden" name="totalreg_insumo" id="totalreg_insumo">
        </div>
    </div>
</div>
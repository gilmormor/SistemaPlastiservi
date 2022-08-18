<div class="modal fade" id="myModalBusqueda" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="display:none">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Buscar Cliente</h3>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <!--<table class="table table-striped table-bordered table-hover tablas" id="tabla-data-clientes">-->
                    <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-clientes"  data-page-length="10">
                    <!--<table id="tabla-data-clientes" class="table-hover display" style="width:100%">-->
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th>RUT</th>
                                <th>Razón Social</th>
                                <th>Dirección</th>
                                <th>Telefono</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        
    </div>
</div>
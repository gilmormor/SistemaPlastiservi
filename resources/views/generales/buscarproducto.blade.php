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
                                                    <th style="width:100px;">Nombre</th>
                                                    <th>Diámetro</th>
                                                    <th>Clase</th>
                                                    <th style="text-align:right">Largo</th>
                                                    <th style="text-align:right">Peso</th>
                                                    <th style="text-align:center">TipU</th>
                                                    <th style="text-align:right">PrecN</th>
                                                    <th style="text-align:right">Prec</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $aux_nfila = 0; $i = 0;?>
                                                @foreach($productos as $producto)
                                                    <?php $aux_nfila++; 
                                                        $aux_onclick = "";
                                                        if (isset($selecmultprod)){ //Valiable creada en el controlador para validar si se puede hacer multiple seleccion -->
                                                            $aux_onclick = "onclick=llenarlistaprod(" . $aux_nfila . "," . $producto->id . ")";
                                                        }else{
                                                            $aux_onclick = "onclick=copiar_codprod(" . $producto->id . ",'" . $producto->codintprod . "')";
                                                            //<a href="#" class="copiar_id" onclick="copiar_codprod({{$producto->id}},'{{$producto->codintprod}}')"> {{$producto->nombre}} </a>
                                                        }
                                                    ?>
                                                    <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}" prodid="{{$producto->id}}" {{$aux_onclick}} class="btn-accion-tabla copiar_id" data-toggle='tooltip' title="Click para seleccionar producto">
                                                        <td name="producto_idBtd{{$aux_nfila}}" id="producto_idBtd{{$aux_nfila}}">
                                                            {{$producto->id}}
                                                        </td>
                                                        <td name="productonombreBtd{{$aux_nfila}}" id="productonombreBtd{{$aux_nfila}}">
                                                            {{$producto->nombre}}
                                                        </td>
                                                        <td name="productodiamextmmBtd{{$aux_nfila}}" id="productodiamextmmBtd{{$aux_nfila}}">
                                                            {{$producto->diametro}}
                                                        </td>
                                                        <td name="productocla_nombreBtd{{$aux_nfila}}" id="productocla_nombreBtd{{$aux_nfila}}">
                                                            {{$producto->cla_nombre}}
                                                        </td>
                                                        <td name="productolongBtd{{$aux_nfila}}" id="productolongBtd{{$aux_nfila}}" style="text-align:center">
                                                            {{$producto->long}}
                                                        </td>
                                                        <td name="productopesoBtd{{$aux_nfila}}" id="productopesoBtd{{$aux_nfila}}" style="text-align:right">
                                                            {{number_format($producto->peso, 2, ",", ".")}}
                                                        </td>
                                                        <td name="productotipounionBtd{{$aux_nfila}}" id="productotipounionBtd{{$aux_nfila}}" style="text-align:center">
                                                            {{$producto->tipounion}}
                                                        </td>
                                                        <td name="productoprecionetoBtd{{$aux_nfila}}" id="productoprecionetoBtd{{$aux_nfila}}" style="text-align:right">
                                                            {{number_format($producto->precioneto, 2, ",", ".")}}
                                                        </td>
                                                        <td name="productoprecioBtd{{$aux_nfila}}" id="productoprecioBtd{{$aux_nfila}}" style="text-align:right">
                                                            {{number_format($producto->precio, 2, ",", ".")}}
                                                        </td>
                                                    </tr>
                                                    <?php $i++;?>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Diámetro</th>
                                                    <th>Clase</th>
                                                    <th>Largo</th>
                                                    <th>Peso</th>
                                                    <th>TipU</th>
                                                    <th>PrecN</th>
                                                    <th>Prec</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    @if (isset($selecmultprod)) <!-- Valiable creada en el controlador para validar si se puede hacer multiple seleccion -->
                                        <div class="col-xs-12" id="divprodselec" name="divprodselec">
                                            <div class="col-xs-12 col-md-1 col-sm-1 text-left" data-toggle='tooltip' title="Productos seleccionados">
                                                <label for="productos">Selección:</label>
                                            </div>
                                            <div class="col-xs-12 col-md-11 col-sm-11" data-toggle='tooltip' title="Productos seleccionados">
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
                @if (isset($selecmultprod)) <!-- Valiable creada en el controlador para validar si se puede hacer multiple seleccion -->
                    <button id="aceptarmbp" name="aceptarmbp" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                @endif
            </div>
            <input type="hidden" name="totalreg" id="totalreg" value="{{$aux_nfila}}">
        </div>
    </div>
</div>
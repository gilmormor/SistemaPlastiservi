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
                <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover tablas" id="tabla-data-productos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Clase</th>
                                <th>Codigo</th>
                                <th>Diametro</th>
                                <th>Esp</th>
                                <th>Long</th>
                                <th>Peso</th>
                                <th>TipU</th>
                                <th>PrecN</th>
                                <th>Prec</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $aux_nfila = 0; $i = 0;?>
                            @foreach($productos as $producto)
                                <?php $aux_nfila++; ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td name="producto_idBtd{{$aux_nfila}}" id="producto_idBtd{{$aux_nfila}}">
                                        {{$producto->id}}
                                    </td>
                                    <td name="productonombreBtd{{$aux_nfila}}" id="productonombreBtd{{$aux_nfila}}">
                                        <a href="#" class="copiar_id" onclick="copiar_codprod({{$producto->id}},'{{$producto->codintprod}}')"> {{$producto->nombre}} </a>
                                    </td>
                                    <td name="productocla_nombreBtd{{$aux_nfila}}" id="productocla_nombreBtd{{$aux_nfila}}">
                                        {{$producto->cla_nombre}}
                                    </td>
                                    <td name="productocodintprodBtd{{$aux_nfila}}" id="productocodintprodBtd{{$aux_nfila}}">
                                        {{$producto->codintprod}}
                                    </td>
                                    <td name="productodiamextmmBtd{{$aux_nfila}}" id="productodiamextmmBtd{{$aux_nfila}}">
                                        {{$producto->diamextmm}}mm - {{$producto->diamextpg}}
                                    </td>
                                    <td name="productoespesorBtd{{$aux_nfila}}" id="productoespesorBtd{{$aux_nfila}}">
                                        {{$producto->espesor}}
                                    </td>
                                    <td name="productolongBtd{{$aux_nfila}}" id="productolongBtd{{$aux_nfila}}">
                                        {{$producto->long}}
                                    </td>
                                    <td name="productopesoBtd{{$aux_nfila}}" id="productopesoBtd{{$aux_nfila}}">
                                        {{$producto->peso}}
                                    </td>
                                    <td name="productotipounionBtd{{$aux_nfila}}" id="productotipounionBtd{{$aux_nfila}}">
                                        {{$producto->tipounion}}
                                    </td>
                                    <td name="productoprecionetoBtd{{$aux_nfila}}" id="productoprecionetoBtd{{$aux_nfila}}">
                                        {{$producto->precioneto}}
                                    </td>
                                    <td name="productoprecioBtd{{$aux_nfila}}" id="productoprecioBtd{{$aux_nfila}}">
                                        {{$producto->precio}}
                                    </td>
                                </tr>
                                <?php $i++;?>
                            @endforeach
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
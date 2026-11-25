@extends("theme.$theme.layout")
@section('titulo')
Saldo por Producir
@endsection

@section("scripts")
    <script>
        const SP_RUTA_PAGE    = "{{ route('reportsaldoproducirpage') }}";
        const SP_RUTA_DETALLE = "{{ url('reportsaldoproducir/detalle') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscarnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportsaldoproducir/index.js")}}" type="text/javascript"></script>
@endsection
<?php
    $selecmultprod = true;
?>
@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Saldo por Producir</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                {{-- Explica para que sirve esta pantalla: es la respuesta a "de donde
                     sale el numero que ve el vendedor", que no se puede reconstruir
                     desde el reporte de stock. --}}
                <div class="callout callout-info" style="padding:8px 12px;margin-bottom:14px;">
                    <p style="margin-bottom:0;font-size:12px;">
                        El vendedor ve en la nota de venta <strong>cuánto falta fabricar</strong>, no el stock.
                        Un valor <strong class="text-red">negativo</strong> significa que el producto está
                        <strong>sobrevendido</strong>. Aquí se ve de dónde sale ese número:
                        <em>stock vendible − pendiente por despachar − reservado</em>.
                    </p>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-4">
                        <label for="sucursal_id">Sucursal</label>
                        <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" required>
                            @foreach($tablashtml['sucursales'] as $sucursal)
                                <option value="{{$sucursal->id}}">{{$sucursal->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xs-12 col-md-5">
                        <label for="producto_idPxP">Producto</label>
                        {{-- Acepta varios codigos separados por coma: comparar es justo
                             lo que hace falta para ver cuales estan sobrevendidos. --}}
                        <div class="input-group">
                            <input type="text" name="producto_idPxP" id="producto_idPxP" class="form-control"
                                   tipoval="numericootro" placeholder="Un código, o varios separados por coma"/>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="btnbuscarproducto"
                                        name="btnbuscarproducto">Buscar</button>
                            </span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3" style="margin-top:24px;">
                        <button type="button" id="btnconsultar" class="btn btn-success">
                            <i class="fa fa-search"></i> Consultar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-default">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-saldo">
                        <thead>
                            <tr>
                                <th class="width70">Cod</th>
                                <th>Producto</th>
                                <th class="text-right width70" title="Suma de todas las bodegas de venta y picking">Stock vendible</th>
                                <th class="text-right width70" title="Lo vendido y aún no despachado">Pendiente</th>
                                <th class="text-right width70" title="Apartado para un cliente o una nota de venta">Reservado</th>
                                <th class="text-right width70" title="Negativo = sobrevendido">Saldo</th>
                                <th class="width70">Detalle</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Desglose de un producto: bodega por bodega, venta por venta --}}
<div class="modal fade" id="modalDetalleSaldo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-list-ul"></i> <span id="sp-det-titulo"></span></h4>
            </div>
            <div class="modal-body" id="sp-det-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@include('generales.buscarproductobd')
@endsection

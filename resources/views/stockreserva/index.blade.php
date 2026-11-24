@extends("theme.$theme.layout")
@section('titulo')
Reserva de Stock
@endsection

@section("scripts")
    <script>
        const SR_RUTA_STOCK = "{{ url('stockreserva/stockproducto') }}";
        const SR_RUTA_PAGE  = "{{ route('stockreservapage') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/stockreserva/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')

        {{-- ------------------------------------------------------------------
             Reservar. La pantalla sigue el orden de la decision real: primero
             QUE producto, luego DONDE esta y cuanto queda libre de verdad, y
             recien ahi PARA QUIEN y HASTA CUANDO.
             ------------------------------------------------------------------ --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Reservar stock</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('stockreserva_guardar') }}" method="post" id="form-reserva">
                    @csrf

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <label for="producto_idPxP">Producto</label>
                            {{-- Mismo patron de busqueda que ccdesbloqueo y los demas:
                                 el codigo se escribe o se elige en el modal. --}}
                            <div class="input-group">
                                <input type="text" name="producto_idPxP" id="producto_idPxP" class="form-control"
                                       placeholder="Código del producto">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="btnbuscarproducto"
                                            name="btnbuscarproducto">Buscar</button>
                                </span>
                            </div>
                            <small class="text-muted" id="sr-producto-nombre"></small>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <label for="sr-lote">Lote <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="opdetregprod_id" id="sr-lote" class="form-control"
                                   placeholder="N° de lote, si quiere apartar uno en particular">
                            <small class="text-muted">
                                El stock que entró por carga de inventario no tiene lote: deje el campo vacío.
                            </small>
                        </div>
                    </div>

                    {{-- Bodegas con stock del producto. No basta con saber cuanto hay:
                         lo que decide es cuanto queda LIBRE despues de lo ya apartado. --}}
                    <div class="row" style="margin-top:18px;">
                        <div class="col-xs-12">
                            <div id="sr-bodegas" style="display:none;">
                                <label>¿De qué bodega se aparta?</label>
                                <div class="table-responsive">
                                    <table class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;"></th>
                                                <th>Bodega</th>
                                                <th class="text-right" style="width:120px;">Stock</th>
                                                <th class="text-right" style="width:130px;">Ya reservado</th>
                                                <th class="text-right" style="width:120px;">Libre</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sr-bodegas-body"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="sr-sinstock" class="alert alert-warning" style="display:none;">
                                Ese producto no tiene stock disponible en sus bodegas.
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-12 col-md-3">
                            <label for="sr-cant">Cantidad</label>
                            <input type="text" name="cant" id="sr-cant" class="form-control text-right"
                                   placeholder="0">
                            <small class="text-muted" id="sr-libre-aviso"></small>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <label for="sr-cliente">Cliente</label>
                            <select name="cliente_id" id="sr-cliente" class="selectpicker form-control"
                                    data-live-search="true" title="Sin cliente">
                                @foreach($tablas['clientes'] as $cliente)
                                    <option value="{{$cliente->id}}">{{$cliente->razonsocial}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-2">
                            <label for="sr-nvdet">Ítem NV <small class="text-muted">(opc.)</small></label>
                            <input type="text" name="notaventadetalle_id" id="sr-nvdet" class="form-control"
                                   placeholder="id">
                        </div>
                        <div class="col-xs-12 col-md-3">
                            {{-- Obligatoria por diseno: sin fecha tope las reservas se
                                 acumulan y nadie recuerda para que estaban. --}}
                            <label for="sr-vence">Reservado hasta</label>
                            <input type="text" name="fechavence" id="sr-vence" class="form-control fecha"
                                   value="{{$tablas['fechavence']}}">
                        </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                        <div class="col-xs-12 col-md-9">
                            <label for="sr-motivo">Motivo</label>
                            <input type="text" name="motivo" id="sr-motivo" class="form-control" maxlength="300"
                                   placeholder="Por qué se aparta">
                        </div>
                        <div class="col-xs-12 col-md-3" style="margin-top:24px;">
                            <input type="hidden" name="invbodegaproducto_id" id="sr-ibp">
                            <button type="submit" class="btn btn-primary btn-block" id="sr-guardar" disabled>
                                <i class="fa fa-lock"></i> Reservar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ------------------------------------------------------------------
             Listado
             ------------------------------------------------------------------ --}}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Reservas</h3>
                <div class="box-tools pull-right">
                    <label style="font-weight:normal;">
                        <input type="checkbox" id="sr-vertodas">
                        Ver también liberadas, vencidas y cumplidas
                    </label>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-reservas">
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th>Producto</th>
                                <th>Bodega</th>
                                <th class="width70">Lote</th>
                                <th class="text-right width70">Cant</th>
                                <th>Para</th>
                                <th class="width70">Vence</th>
                                <th>Motivo</th>
                                <th class="width70">Usuario</th>
                                <th class="width70">Estado</th>
                                <th class="width70">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de busqueda de producto, el mismo que usan las demas pantallas --}}
@include('generales.buscarproductobd')
@endsection

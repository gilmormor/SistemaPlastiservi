@extends("theme.$theme.layout")
@section('titulo')
Reasignar Producción
@endsection

@section("scripts")
    <script>
        const PR_RUTA_LOTE     = "{{ url('prodreasignacion/consultar') }}";
        const PR_RUTA_DESTINOS = "{{ url('prodreasignacion/destinos') }}";
        const PR_RUTA_PAGE     = "{{ route('prodreasignacionpage') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/prodreasignacion/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')

        {{-- ------------------------------------------------------------------
             Reasignar. Nada se borra: el lote se fabrico para su nota de venta
             de origen y eso sigue siendo cierto. Lo que se registra aqui es a
             quien se le entrega finalmente.
             ------------------------------------------------------------------ --}}
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Entregar una producción a otro cliente o nota de venta</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('prodreasignacion_guardar') }}" method="post" id="form-reasig">
                    @csrf

                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <label for="pr-lote">Número de lote</label>
                            <input type="text" name="opdetregprod_id" id="pr-lote" class="form-control"
                                   placeholder="Ej: 69" maxlength="10" autocomplete="off" autofocus>
                        </div>
                    </div>

                    {{-- Ficha del lote: para quien se fabrico y que queda en bodega.
                         Es la confirmacion de que es el lote correcto antes de
                         quitarselo a nadie. --}}
                    <div class="row" style="margin-top:14px;">
                        <div class="col-xs-12 col-md-8">
                            <div id="pr-ficha" style="display:none;">
                                <table class="table table-condensed table-bordered">
                                    <tr><th style="width:34%;">Lote</th><td id="pr-f-lote"></td></tr>
                                    <tr><th>Producto</th><td id="pr-f-producto"></td></tr>
                                    <tr><th>En bodega</th><td id="pr-f-bodega"></td></tr>
                                    <tr><th>Se fabricó para</th><td id="pr-f-origen"></td></tr>
                                </table>
                            </div>
                            <div id="pr-error" class="alert alert-danger" style="display:none;"></div>
                        </div>
                    </div>

                    {{-- A quien se entrega. Solo items del MISMO producto: darle a
                         un cliente algo distinto de lo que pidio no es una
                         reasignacion, es un error. --}}
                    <div id="pr-destino-box" style="display:none;">
                        <div class="row" style="margin-top:10px;">
                            <div class="col-xs-12">
                                <label>¿A quién se le entrega?</label>
                                <div class="table-responsive">
                                    <table class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;"></th>
                                                <th style="width:90px;">N° NV</th>
                                                <th>Cliente</th>
                                                <th class="text-right" style="width:110px;">Cantidad</th>
                                                {{-- Lo que todavia le falta recibir: es el dato que decide --}}
                                                <th class="text-right" style="width:110px;">Pendiente</th>
                                                <th style="width:120px;">Fabricación</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pr-destinos-body"></tbody>
                                    </table>
                                </div>
                                <small class="text-muted">
                                    Solo notas de venta <strong>aprobadas</strong> del mismo producto que
                                    todavía esperan material: se descartan las no aprobadas, las cerradas,
                                    las anuladas, las ya despachadas por completo y las que ya tienen
                                    todo solicitado.
                                </small>
                            </div>
                        </div>

                        <div class="row" style="margin-top:10px;">
                            <div class="col-xs-12 col-md-9">
                                <label for="pr-motivo">Motivo <span class="text-red">*</span></label>
                                <input type="text" name="motivo" id="pr-motivo" class="form-control" maxlength="300"
                                       placeholder="Por qué se le entrega a otro">
                                <small class="text-muted">
                                    Obligatorio: alguien va a preguntar por qué su pedido se atrasó.
                                </small>
                            </div>
                            <div class="col-xs-12 col-md-3" style="margin-top:24px;">
                                <input type="hidden" name="nvdet_destino_id" id="pr-nvdet-destino">
                                <button type="submit" class="btn btn-warning btn-block" id="pr-guardar" disabled>
                                    <i class="fa fa-exchange"></i> Reasignar
                                </button>
                            </div>
                        </div>

                        {{-- Lo que el usuario tiene que entender ANTES de confirmar:
                             la nota de venta de origen queda esperando. --}}
                        <div class="row" style="margin-top:10px;">
                            <div class="col-xs-12">
                                <div class="alert alert-warning" style="margin-bottom:0;">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>La nota de venta que pierde esta producción queda descubierta.</strong>
                                    Sigue requiriendo fabricación, y quien haga su solicitud de despacho
                                    verá el aviso de que este lote se entregó a otro.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ------------------------------------------------------------------
             Historial
             ------------------------------------------------------------------ --}}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Reasignaciones</h3>
                <div class="box-tools pull-right">
                    <label style="font-weight:normal;">
                        <input type="checkbox" id="pr-vertodas"> Ver también las deshechas
                    </label>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-reasig">
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th class="width70">Lote</th>
                                <th class="width70" title="Código del producto">CodProd</th>
                                <th>Producto</th>
                                <th>Se fabricó para</th>
                                <th>Se entregó a</th>
                                <th>Motivo</th>
                                <th class="width70">Fecha</th>
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
@endsection

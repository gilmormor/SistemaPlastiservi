@extends("theme.$theme.layout")
@section('titulo')
Etapa Externa
@endsection

@section("scripts")
    <script>
        /* Rutas que consume el JS. Se declaran aqui porque el .js es estatico y
           no pasa por Blade. */
        const EXT_RUTA_LOTE     = "{{ url('etapaexterna/lote') }}";
        const EXT_RUTA_PLANILLA = "{{ route('etapaexterna_guardar_planilla') }}";
        const EXT_RUTA_ETIQUETA = "{{ route('etapaexterna_guardar_etiqueta') }}";
        const EXT_IMP_PLANILLA  = "{{ url('etapaexterna/planilla') }}";
        const EXT_IMP_ETIQUETA  = "{{ url('etapaexterna/etiqueta') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/etapaexterna/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')

        {{-- ============================================ BUSQUEDA DEL LOTE --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Planilla y Etiquetas — Proceso Externo</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-4">
                        <label for="opdetregprod_id">N° de Lote (registro de producción aprobado)</label>
                        <div class="input-group">
                            <input type="text" id="opdetregprod_id" name="opdetregprod_id"
                                   class="form-control" maxlength="10" placeholder="Ej: 45" autofocus>
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="button" id="btnbuscarlote">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </span>
                        </div>
                        <small class="text-muted">
                            Es el lote de la etapa anterior que el operario va a procesar.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================ FICHA DEL LOTE --}}
        {{-- Oculta hasta que haya una busqueda con resultado. --}}
        <div class="box box-info" id="boxlote" style="display:none;">
            <div class="box-header with-border">
                <h3 class="box-title">Lote <span id="lbl_lote"></span></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-success btn-sm" id="btnplanilla">
                        <i class="fa fa-print"></i> Imprimir Planilla
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnetiquetas">
                        <i class="fa fa-tags"></i> Imprimir Etiquetas
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <table class="table table-condensed table-bordered">
                            <tr><th style="width:38%;">Cliente</th><td id="lbl_cliente"></td></tr>
                            <tr><th>Producto</th><td id="lbl_producto"></td></tr>
                            <tr><th>Material</th><td id="lbl_materiaprima"></td></tr>
                            <tr><th>Forma de embalar</th><td id="lbl_formaembalar"></td></tr>
                        </table>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <table class="table table-condensed table-bordered">
                            <tr><th style="width:38%;">OT / OP / OpDet</th><td id="lbl_otop"></td></tr>
                            <tr><th>Producción del lote</th><td id="lbl_kg"></td></tr>
                            <tr><th>Viene de</th><td id="lbl_origen"></td></tr>
                            <tr><th>Se procesa en</th><td id="lbl_etapa"></td></tr>
                        </table>
                    </div>
                </div>

                {{-- Aviso cuando el lote es de la ultima etapa y no hay etapa
                     siguiente que deducir desde la OP. --}}
                <div class="alert alert-warning" id="avisosinetapa" style="display:none;">
                    <i class="fa fa-warning"></i>
                    Este lote es de la última etapa de la OP, así que no hay una etapa siguiente
                    que deducir. No se puede generar la planilla ni las etiquetas para él.
                </div>
            </div>
        </div>

        {{-- =================================================== HISTORIAL --}}
        <div class="box box-default" id="boxhistorial" style="display:none;">
            <div class="box-header with-border">
                <h3 class="box-title">Ya impreso para este lote</h3>
            </div>
            <div class="box-body">
                {{-- Cierra el circulo: con que material se termino produciendo.
                     No se guarda en las tablas del modulo, se deriva del FIFO
                     entre etapas (ver destinosLote en el controlador). --}}
                <h5><strong>Registros de producción que consumieron este lote</strong></h5>
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Registro</th><th>Etapa</th><th>Operario</th>
                            <th>Kg tomados</th><th>Fecha</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tbodydestinos"></tbody>
                </table>

                <h5 style="margin-top:18px;"><strong>Planillas</strong></h5>
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>N°</th><th>Fecha</th><th>Máquina</th><th>Responsable</th>
                            <th>N° Bolsas</th><th>Usuario</th><th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyplanillas"></tbody>
                </table>

                <h5 style="margin-top:18px;"><strong>Tiradas de etiquetas</strong></h5>
                <table class="table table-condensed table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>N°</th><th>Fecha</th><th>Planilla</th><th>Máquina</th>
                            <th>Operario</th><th>Turno</th><th>Kg/Paq</th><th>Unid/Paq</th>
                            <th>Cantidad</th><th>Usuario</th><th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyetiquetas"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- =================================================== MODAL PLANILLA --}}
<div class="modal fade" id="modalplanilla" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Datos de la Planilla</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="pl_maquina_id">Máquina <span class="text-danger">*</span></label>
                    <select id="pl_maquina_id" class="form-control">
                        <option value="">Seleccione...</option>
                    </select>
                    <small class="text-muted">Máquina de la etapa en la que se va a procesar.</small>
                </div>
                <div class="form-group">
                    <label for="pl_operario_id">Responsable del equipo <span class="text-danger">*</span></label>
                    <select id="pl_operario_id" class="form-control">
                        <option value="">Seleccione...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pl_nrobolsas">N° de Bolsas</label>
                    <input type="text" id="pl_nrobolsas" class="form-control" maxlength="10">
                    <small class="text-muted">Si lo deja en blanco, la celda sale vacía para anotarla a mano.</small>
                </div>
                <div class="form-group">
                    <label for="pl_formaembalar">Detalle de embalaje</label>
                    <input type="text" id="pl_formaembalar" class="form-control" maxlength="200"
                           placeholder="Ej: BULTOS DE 4,00">
                    {{-- Se guarda en el detalle de la OT y en el acuerdo del cliente para
                         ese producto, asi la proxima OT ya lo trae cargado. --}}
                    <small class="text-muted">
                        Queda guardado para este producto y cliente; la próxima vez sale solo.
                    </small>
                </div>
                <div class="form-group">
                    <label for="pl_obs">Observación</label>
                    <input type="text" id="pl_obs" class="form-control" maxlength="300">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnguardarplanilla">
                    <i class="fa fa-print"></i> Guardar e Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

{{-- =================================================== MODAL ETIQUETAS --}}
<div class="modal fade" id="modaletiqueta" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Etiquetas de Paquete</h4>
            </div>
            <div class="modal-body">
                {{-- Aviso cuando se imprimen etiquetas sin planilla previa. No
                     bloquea: ver la nota en la migracion de etapaextetiqueta. --}}
                <div class="alert alert-warning" id="avisosinplanilla" style="display:none;">
                    <i class="fa fa-warning"></i>
                    Este lote no tiene planilla impresa. Puede imprimir las etiquetas igual.
                </div>

                <div class="form-group" id="grupoplanilla" style="display:none;">
                    <label for="et_planilla_id">Planilla</label>
                    <select id="et_planilla_id" class="form-control"></select>
                    <small class="text-muted">Al elegir una planilla se precargan su máquina y responsable.</small>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="et_maquina_id">Máquina <span class="text-danger">*</span></label>
                            <select id="et_maquina_id" class="form-control">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="et_operario_id">Operario <span class="text-danger">*</span></label>
                            <select id="et_operario_id" class="form-control">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="et_fecha">Fecha Producción <span class="text-danger">*</span></label>
                            <input type="text" id="et_fecha" class="form-control datepicker"
                                   placeholder="DD/MM/AAAA">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="et_turno">Turno</label>
                            <input type="text" id="et_turno" class="form-control" maxlength="20">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="et_kilos">Kilos <small>(por paquete)</small></label>
                            <input type="text" id="et_kilos" class="form-control" maxlength="12">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="et_unidades">Unidades <small>(por paquete)</small></label>
                            <input type="text" id="et_unidades" class="form-control" maxlength="12">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="et_obs">Observación</label>
                    <input type="text" id="et_obs" class="form-control" maxlength="300">
                </div>
                <div class="form-group">
                    <label for="et_cantidad">Cantidad de Etiquetas <span class="text-danger">*</span></label>
                    <input type="text" id="et_cantidad" class="form-control" maxlength="5">
                    <small class="text-muted">Una por cada paquete que se va a armar.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnguardaretiqueta">
                    <i class="fa fa-print"></i> Guardar e Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ================================================ IMPRESION DIRECTA --}}
{{-- El documento se carga aqui y se manda a imprimir de inmediato: la vista
     previa de la impresora ya lo muestra, asi que una vista previa propia
     sobraba.

     El iframe NO va con display:none ni con tamaño cero: la planilla y las
     etiquetas se autoajustan midiendo su propio alto (filas en blanco y tamaño
     de letra), y sin dimensiones reales esas medidas salen mal. Por eso se le
     da el ancho util de la hoja y se lo saca de la pantalla con left. --}}
<iframe id="contimpresion" src="" title="Documento para imprimir"
        style="position:fixed; left:-10000px; top:0; width:996px; height:900px; border:0;"></iframe>
@endsection

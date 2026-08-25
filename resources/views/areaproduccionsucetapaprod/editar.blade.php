@extends("theme.$theme.layout")
@section('titulo')
    Area Producción
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/areaproduccionsucetapaprod/crear.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/areaproduccionsucetapaprod/campos.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/areaproduccionsucetapaprod/ccparam.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/areaproduccionsucetapaprod/bodegas.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Area Producción Asignar Etapas de produccion</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('areaproduccionsucetapaprod')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_areaproduccionsucetapaprod', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="box-body">
                    @include('areaproduccionsucetapaprod.form')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    @include('includes.boton-form-editar')
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>

{{-- ============================================================
     Sección: Campos adicionales por etapa
     Muestra todas las etapas del área con botón para gestionar
     sus campos adicionales. AJAX, sin afectar el form principal.
     ============================================================ --}}
<div class="row" style="margin-top:20px;">
    <div class="col-lg-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-list-ul"></i> Campos adicionales por etapa
                </h3>
                <small class="text-muted" style="margin-left:10px;">
                    Define qué datos extra debe ingresar el operario en cada etapa
                </small>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-condensed table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Orden</th>
                            <th>Campos configurados</th>
                            <th style="width:120px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->areaproduccionsucetapaprods as $apsuc)
                        <tr>
                            <td>{{ $apsuc->etapaprod->nombre ?? '—' }}</td>
                            <td style="text-align:center;">{{ $apsuc->orden }}</td>
                            <td id="resumen-campos-{{ $apsuc->id }}">
                                @if($apsuc->campos->isEmpty())
                                    <span class="text-muted">Sin campos</span>
                                @else
                                    <span class="badge" style="background:#00a65a;">{{ $apsuc->campos->count() }}</span>
                                    {{ $apsuc->campos->pluck('etiqueta')->implode(', ') }}
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-default"
                                        onclick="abrirModalCampos({{ $apsuc->id }})"
                                        title="Gestionar campos adicionales">
                                    <i class="fa fa-cog"></i> Campos
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     Sección: Parámetros CC por etapa
     Permite configurar qué parámetros de Control de Calidad
     se miden en cada etapa, con sus rangos mín/máx.
     ============================================================ --}}
<div class="row" style="margin-top:20px;">
    <div class="col-lg-12">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-check-circle-o"></i> Parámetros Control de Calidad por etapa
                </h3>
                <small class="text-muted" style="margin-left:10px;">
                    Define qué parámetros mide CC en cada etapa y sus rangos mín/máx
                </small>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-condensed table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Orden</th>
                            <th>Parámetros CC configurados</th>
                            <th style="width:130px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->areaproduccionsucetapaprods as $apsuc)
                        <tr>
                            <td>{{ $apsuc->etapaprod->nombre ?? '—' }}</td>
                            <td style="text-align:center;">{{ $apsuc->orden }}</td>
                            <td id="resumen-ccparams-{{ $apsuc->id }}">
                                @php $ccparams = $apsuc->ccparamApsucetapaprod()->whereNull('deleted_at')->with('ccparam')->orderBy('orden')->get(); @endphp
                                @if($ccparams->isEmpty())
                                    <span class="text-muted">Sin parámetros CC</span>
                                @else
                                    <span class="badge" style="background:#f39c12;">{{ $ccparams->count() }}</span>
                                    {{ $ccparams->map(function($c){ return $c->ccparam->etiqueta ?? ''; })->implode(', ') }}
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-warning"
                                        onclick="abrirModalCcParams({{ $apsuc->id }}, '{{ addslashes($apsuc->etapaprod->nombre ?? '') }}')"
                                        title="Gestionar parámetros CC de esta etapa">
                                    <i class="fa fa-check-circle-o"></i> Params CC
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     Sección: Bodegas de inventario por etapa
     Permite configurar qué bodegas tiene disponible cada etapa
     para generar movimientos de inventario al aprobar producción.
     ============================================================ --}}
<div class="row" style="margin-top:20px;">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-archive"></i> Bodegas de inventario por etapa
                </h3>
                <small class="text-muted" style="margin-left:10px;">
                    Al aprobar un registro de producción, el sistema generará un movimiento en la bodega asignada
                </small>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-condensed table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Orden</th>
                            <th>Bodegas asignadas</th>
                            <th style="width:120px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->areaproduccionsucetapaprods as $apsuc)
                        <tr>
                            <td>{{ $apsuc->etapaprod->nombre ?? '—' }}</td>
                            <td style="text-align:center;">{{ $apsuc->orden }}</td>
                            <td id="resumen-bodegas-{{ $apsuc->id }}">
                                @if($apsuc->bodegas->isEmpty())
                                    <span class="text-muted">Sin bodegas (no genera movimiento de inv.)</span>
                                @else
                                    <span class="badge" style="background:#3c8dbc;">{{ $apsuc->bodegas->count() }}</span>
                                    {{ $apsuc->bodegas->map(function($b){ return $b->invbodega->nombre ?? '?'; })->implode(', ') }}
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-primary"
                                        onclick="abrirModalBodegas({{ $apsuc->id }}, '{{ addslashes($apsuc->etapaprod->nombre ?? '') }}')"
                                        title="Gestionar bodegas de inventario de esta etapa">
                                    <i class="fa fa-archive"></i> Bodegas
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal gestión de bodegas por etapa --}}
<div class="modal fade" id="modalBodegasEtapa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc; color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-archive"></i>
                    Bodegas — <span id="modalBodegasEtapaNombre"></span>
                </h4>
            </div>
            <div class="modal-body">
                {{-- Tabla de bodegas asignadas --}}
                <table class="table table-bordered table-condensed" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>Bodega</th>
                            <th style="width:60px;">Quitar</th>
                        </tr>
                    </thead>
                    <tbody id="tablaBodegasBody">
                        <tr><td colspan="2" class="text-center text-muted">Cargando...</td></tr>
                    </tbody>
                </table>
                <hr>
                <h5><i class="fa fa-plus"></i> Agregar bodega</h5>
                <input type="hidden" id="bodega_apsucetapaprod_id" value="">
                <div class="row">
                    <div class="form-group col-sm-9">
                        <label>Bodega disponible <span class="text-danger">*</span></label>
                        <select id="bodega_invbodega_id" class="form-control">
                            <option value="">-- Seleccione --</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-3" style="padding-top:25px;">
                        <button type="button" class="btn btn-primary btn-block" onclick="guardarBodegaEtapa()">
                            <i class="fa fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal gestión de parámetros CC por etapa --}}
<div class="modal fade" id="modalCcParamsEtapa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#f39c12; color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-check-circle-o"></i>
                    Parámetros CC — <span id="modalCcParamsEtapaNombre"></span>
                </h4>
            </div>
            <div class="modal-body">
                {{-- Tabla de parámetros configurados --}}
                <table class="table table-bordered table-condensed" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>Ord</th>
                            <th>Parámetro</th>
                            <th>Tipo</th>
                            <th>Unidad</th>
                            <th>Mínimo</th>
                            <th>Máximo</th>
                            <th>Req</th>
                            <th style="width:80px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCcParamsBody">
                        <tr><td colspan="8" class="text-center text-muted">Cargando...</td></tr>
                    </tbody>
                </table>
                <hr>
                {{-- Formulario agregar/editar --}}
                <h5 id="formCcParamTitulo"><i class="fa fa-plus"></i> Agregar parámetro CC</h5>
                <input type="hidden" id="ccparam_ap_id_editar" value="">
                <input type="hidden" id="ccparam_apsucetapaprod_id" value="">
                <div class="row">
                    <div class="form-group col-sm-5">
                        <label>Parámetro <span class="text-danger">*</span></label>
                        <select id="ccparam_ccparam_id" class="form-control">
                            <option value="">-- Seleccione --</option>
                        </select>
                        <small class="text-muted">Si no aparece el parámetro, créalo primero en
                            <a href="/ccparam" target="_blank">Parámetros CC</a>.
                        </small>
                    </div>
                    {{-- Origen del rango: fijo (valor_min/valor_max) o calculado desde el
                         acuerdo técnico del producto (objetivo ± tolerancia del propio AT).
                         Al elegir el AT se ocultan los campos de rango fijo, porque el rango
                         pasa a ser distinto para cada producto. --}}
                    <div class="form-group col-sm-4">
                        <label>¿Contra qué se compara?</label>
                        <select id="ccparam_at_campo" class="form-control" onchange="toggleRangoCcParam()">
                            <option value="">Rango fijo (mínimo y máximo)</option>
                            <option value="at_espesor">Espesor del acuerdo técnico</option>
                            <option value="at_ancho">Ancho del acuerdo técnico</option>
                            <option value="at_largo">Largo del acuerdo técnico</option>
                            <option value="at_fuelle">Fuelle del acuerdo técnico</option>
                        </select>
                        <small class="text-muted">Con el acuerdo técnico, el rango sale del valor
                            del producto y su tolerancia.</small>
                    </div>
                    <div class="form-group col-sm-2 rango-fijo">
                        <label>Valor mínimo</label>
                        <input type="number" step="any" id="ccparam_valor_min" class="form-control" placeholder="Ej: 80"/>
                    </div>
                    <div class="form-group col-sm-2 rango-fijo">
                        <label>Valor máximo</label>
                        <input type="number" step="any" id="ccparam_valor_max" class="form-control" placeholder="Ej: 100"/>
                    </div>
                    <div class="form-group col-sm-1">
                        <label>Orden</label>
                        <input type="number" id="ccparam_orden" class="form-control" value="0" min="0"/>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Requerido</label>
                        <div style="margin-top:8px;">
                            <input type="checkbox" id="ccparam_requerido" checked> Sí
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="cancelarEdicionCcParam()">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="guardarCcParam()">
                    <i class="fa fa-save"></i> Guardar parámetro CC
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal gestión de campos adicionales --}}
<div class="modal fade" id="modalCamposEtapa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-list-ul"></i>
                    Campos adicionales — <span id="modalCamposEtapaNombre"></span>
                </h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-condensed" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>Ord</th><th>Etiqueta</th><th>Nombre</th><th>Tipo</th>
                            <th>Fórmula</th><th>Unidad</th><th>Dec</th><th>Req</th>
                            <th>Mapea</th><th style="width:90px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCamposBody">
                        <tr><td colspan="10" class="text-center text-muted">Cargando...</td></tr>
                    </tbody>
                </table>
                <hr>
                <h5 id="formCampoTitulo"><i class="fa fa-plus"></i> Agregar campo</h5>
                <input type="hidden" id="campoid_editar" value="">
                <input type="hidden" id="campo_apsucetapaprod_id" value="">
                <div class="row">
                    <div class="form-group col-sm-2">
                        <label>Orden</label>
                        <input type="number" id="campo_orden" class="form-control" value="0" min="0"/>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Etiqueta <span class="text-danger">*</span></label>
                        <input type="text" id="campo_etiqueta" class="form-control" maxlength="100" placeholder="Ej: Peso Neto Pallet"/>
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Nombre interno <span class="text-danger">*</span></label>
                        <input type="text" id="campo_nombre" class="form-control" maxlength="50" placeholder="Ej: peso_neto_pallet"
                               oninput="this.value=this.value.toLowerCase()"/>
                        <small class="text-muted">Minúsculas, sin espacios (ej: <code>peso_neto</code>)</small>
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Tipo <span class="text-danger">*</span></label>
                        <select id="campo_tipo" class="form-control" onchange="toggleFormula()">
                            <option value="number">Número</option>
                            <option value="text">Texto</option>
                            <option value="calculated">Calculado</option>
                        </select>
                    </div>
                </div>
                <div class="row" id="filaFormula" style="display:none;">
                    <div class="form-group col-sm-12">
                        <label>Fórmula <span class="text-danger">*</span></label>
                        <input type="text" id="campo_formula" class="form-control" maxlength="300"
                               placeholder="Ej: cantidad_sacos * unidades_por_saco"/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-2">
                        <label>Unidad</label>
                        <input type="text" id="campo_unidad" class="form-control" maxlength="20" placeholder="kg, un..."/>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Decimales</label>
                        <input type="number" id="campo_decimales" class="form-control" value="2" min="0" max="6"/>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Mapea a campo estándar</label>
                        <select id="campo_mapea_campo" class="form-control">
                            <option value="">— Ninguno —</option>
                            <option value="cantprod">cantprod (Cantidad producida)</option>
                            <option value="kgprod">kgprod (Kg producción)</option>
                            <option value="kgscrap">kgscrap (Kg scrap)</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Requerido</label>
                        <div style="margin-top:8px;">
                            <input type="checkbox" id="campo_requerido"> Sí
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="cancelarEdicionCampo()">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarCampo()">
                    <i class="fa fa-save"></i> Guardar campo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
{{--
    Partial: campos adicionales por etapa de producción.
    Se incluye en opdetregprodtemp/form.blade.php.

    Variables esperadas (pasadas desde el controlador vía $tablas):
      $tablas['campos']    — Collection<EtapaProdCampo> (puede estar vacía)
      $tablas['campovals'] — Collection keyed por etapaprod_campo_id → valor
                             (vacío al crear, poblado al editar)

    Si $tablas['campos'] está vacío, este partial no renderiza nada.
    No afecta a etapas sin campos configurados.
--}}
@if(isset($tablas['campos']) && $tablas['campos']->isNotEmpty())
<hr>
<div class="row">
    <div class="col-xs-12">
        <h4 style="color:#3c8dbc; margin-bottom:10px;">
            <i class="fa fa-list-ul"></i> Datos adicionales de la etapa
        </h4>
    </div>
</div>
<div class="row" id="campos-adicionales-etapa">
    @foreach($tablas['campos'] as $campo)
    <div class="form-group col-xs-12 col-sm-2">
        <label for="campo_val_{{$campo->id}}" class="control-label {{$campo->requerido ? 'requerido' : ''}}">
            {{$campo->etiqueta}}
            @if($campo->unidad)
                <small class="text-muted">({{$campo->unidad}})</small>
            @endif
        </label>

        @if($campo->tipo === 'calculated')
            {{-- Campo calculado: readonly, se llena por JS.
                 data-mapea-campo: si está definido, el JS también actualiza
                 el input estándar del formulario (ej: aux_cantprod). --}}
            <input type="text"
                   id="campo_val_{{$campo->id}}"
                   name="campo_val[{{$campo->id}}]"
                   class="form-control campo-adicional campo-calculado"
                   data-campo-id="{{$campo->id}}"
                   data-campo-nombre="{{$campo->nombre}}"
                   data-formula="{{$campo->formula}}"
                   data-decimales="{{$campo->decimales}}"
                   data-mapea-campo="{{$campo->mapea_campo ?? ''}}"
                   value="{{old('campo_val.'.$campo->id, $tablas['campovals'][$campo->id] ?? '')}}"
                   style="text-align:right; background:#f5f5f5;"
                   readonly/>
        @elseif($campo->tipo === 'number')
            {{-- Campo numérico: data-mapea-campo propaga el valor al input estándar
                 cuando el usuario escribe. --}}
            <input type="text"
                   id="campo_val_{{$campo->id}}"
                   name="campo_val[{{$campo->id}}]"
                   class="form-control numerico campo-adicional campo-numerico"
                   data-campo-id="{{$campo->id}}"
                   data-campo-nombre="{{$campo->nombre}}"
                   data-decimales="{{$campo->decimales}}"
                   data-mapea-campo="{{$campo->mapea_campo ?? ''}}"
                   valor="{{old('campo_val.'.$campo->id, $tablas['campovals'][$campo->id] ?? '0')}}"
                   value="{{old('campo_val.'.$campo->id, $tablas['campovals'][$campo->id] ?? '')}}"
                   style="text-align:right;"
                   {{$campo->requerido ? 'required' : ''}}/>
        @else
            {{-- tipo = text --}}
            <input type="text"
                   id="campo_val_{{$campo->id}}"
                   name="campo_val[{{$campo->id}}]"
                   class="form-control campo-adicional"
                   data-campo-id="{{$campo->id}}"
                   data-campo-nombre="{{$campo->nombre}}"
                   value="{{old('campo_val.'.$campo->id, $tablas['campovals'][$campo->id] ?? '')}}"
                   {{$campo->requerido ? 'required' : ''}}/>
        @endif
    </div>
    @endforeach
</div>

{{-- Script de fórmulas: evalúa campos calculados cuando cambia un campo numérico.
     Se usa window.addEventListener('load') para garantizar que jQuery ya cargó,
     ya que este partial está en @section('contenido') y jQuery carga al final. --}}
<script>
// Datos de campos para las fórmulas (se leen aquí donde Blade puede renderizarlos)
window._etapaCampos = window._etapaCampos || {};
@foreach($tablas['campos'] as $campo)
window._etapaCampos['{{$campo->nombre}}'] = '{{$campo->id}}';
@endforeach

// Inicialización diferida: 'load' se dispara cuando todos los scripts están listos
window.addEventListener('load', function () {
    var $ = window.jQuery;
    if (!$) return;

    var campoNombreAId = window._etapaCampos || {};

    // Mapa de campo estándar → inputs del formulario principal que deben actualizarse.
    // visible: input que ve el usuario; hidden: input oculto que envía el valor al servidor.
    // El trigger keyup en el visible activa las validaciones existentes (validarsaldokg, etc.)
    var mapaStandard = {
        cantprod : { visible: '#aux_cantprod', hidden: '#cantprod' },
        kgprod   : { visible: '#aux_kgprod',   hidden: null },
        kgscrap  : { visible: '#aux_kgscrap',  hidden: null }
    };

    /**
     * Propaga un valor numérico al input estándar del formulario
     * de forma silenciosa (sin trigger de eventos) para no robar el foco.
     * Actualiza: valor attr + value visible + hidden si existe.
     * El backend maneja la escritura real via mapea_campo en el controlador.
     */
    function propagarAlCampoEstandar(mapea, valor) {
        if (!mapea || !mapaStandard[mapea]) return;
        var mapa = mapaStandard[mapea];
        var $visible = $(mapa.visible);
        if ($visible.length) {
            $visible.attr('valor', valor);
            $visible.val(valor);
            // Sin trigger('keyup') — evita que el plugin numerico robe el foco
        }
        if (mapa.hidden) {
            $(mapa.hidden).val(valor);
        }
    }

    function valorCampo(nombre) {
        var id = campoNombreAId[nombre];
        if (!id) return 0;
        var $input = $('#campo_val_' + id);
        var v = $input.attr('valor') || $input.val() || '0';
        return parseFloat(v.toString().replace(',', '.')) || 0;
    }

    function evaluarCalculados() {
        $('.campo-calculado').each(function () {
            var formula   = $(this).data('formula');
            var decimales = parseInt($(this).data('decimales')) || 2;
            var mapea     = $(this).data('mapea-campo') || '';
            if (!formula) return;

            var expr = formula;
            $.each(campoNombreAId, function (nombre) {
                expr = expr.replace(new RegExp('\\b' + nombre + '\\b', 'g'), valorCampo(nombre));
            });

            try {
                var resultado = (new Function('return (' + expr + ')'))();
                if (!isFinite(resultado)) resultado = 0;
                resultado = Math.round(resultado * Math.pow(10, decimales)) / Math.pow(10, decimales);
                $(this).val(resultado.toFixed(decimales).replace('.', ','));
                $(this).attr('valor', resultado);

                // Si el campo calculado mapea a un campo estándar, propagarlo
                if (mapea) {
                    propagarAlCampoEstandar(mapea, resultado);
                }
            } catch (e) { /* fórmula inválida */ }
        });
    }

    // Al cambiar un campo numérico: actualizar valor, recalcular fórmulas
    // y propagar si tiene mapeo directo (campos number con mapea_campo)
    $(document).on('keyup change', '.campo-numerico', function () {
        var v = $(this).val().replace(',', '.') || '0';
        var num = parseFloat(v) || 0;
        $(this).attr('valor', num);

        // Propagación directa para campos number con mapea_campo
        var mapea = $(this).data('mapea-campo') || '';
        if (mapea) {
            propagarAlCampoEstandar(mapea, num);
        }

        evaluarCalculados();
    });

    // Calcular valores iniciales (modo editar con valores previos)
    evaluarCalculados();
});
</script>
@endif

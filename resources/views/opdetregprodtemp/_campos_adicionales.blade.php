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
            {{-- Campo calculado: readonly, se llena por JS --}}
            <input type="text"
                   id="campo_val_{{$campo->id}}"
                   name="campo_val[{{$campo->id}}]"
                   class="form-control campo-adicional campo-calculado"
                   data-campo-id="{{$campo->id}}"
                   data-campo-nombre="{{$campo->nombre}}"
                   data-formula="{{$campo->formula}}"
                   data-decimales="{{$campo->decimales}}"
                   value="{{old('campo_val.'.$campo->id, $tablas['campovals'][$campo->id] ?? '')}}"
                   style="text-align:right; background:#f5f5f5;"
                   readonly/>
        @elseif($campo->tipo === 'number')
            <input type="text"
                   id="campo_val_{{$campo->id}}"
                   name="campo_val[{{$campo->id}}]"
                   class="form-control numerico campo-adicional campo-numerico"
                   data-campo-id="{{$campo->id}}"
                   data-campo-nombre="{{$campo->nombre}}"
                   data-decimales="{{$campo->decimales}}"
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

{{-- Script de fórmulas: evalúa campos calculados cuando cambia un campo numérico --}}
<script>
(function () {
    // Mapa nombre -> id del campo para resolver fórmulas
    var campoNombreAId = {};
    @foreach($tablas['campos'] as $campo)
    campoNombreAId['{{$campo->nombre}}'] = '{{$campo->id}}';
    @endforeach

    /**
     * Obtiene el valor numérico actual de un campo por su nombre.
     */
    function valorCampo(nombre) {
        var id = campoNombreAId[nombre];
        if (!id) return 0;
        var $input = $('#campo_val_' + id);
        // 'valor' guarda el número limpio; value puede tener formato con comas
        var v = $input.attr('valor') || $input.val() || '0';
        return parseFloat(v.toString().replace(',', '.')) || 0;
    }

    /**
     * Evalúa todos los campos calculados (tipo=calculated).
     * La fórmula usa nombres de campos (ej. "cantidad_sacos * unidades_por_saco").
     * Se reemplaza cada nombre por su valor numérico antes de evaluar.
     */
    function evaluarCalculados() {
        $('.campo-calculado').each(function () {
            var formula = $(this).data('formula');
            var decimales = parseInt($(this).data('decimales')) || 2;
            if (!formula) return;

            // Reemplazar cada nombre de campo conocido por su valor
            var expr = formula;
            $.each(campoNombreAId, function (nombre) {
                // reemplaza todas las ocurrencias del nombre por el valor numérico
                expr = expr.replace(new RegExp('\\b' + nombre + '\\b', 'g'), valorCampo(nombre));
            });

            try {
                // eslint-disable-next-line no-new-func
                var resultado = (new Function('return (' + expr + ')'))();
                if (!isFinite(resultado)) resultado = 0;
                resultado = Math.round(resultado * Math.pow(10, decimales)) / Math.pow(10, decimales);
                $(this).val(resultado.toFixed(decimales).replace('.', ','));
                $(this).attr('valor', resultado);
            } catch (e) {
                // fórmula inválida: dejar en blanco
            }
        });
    }

    // Recalcular al cambiar cualquier campo numérico del bloque de campos adicionales
    $(document).on('keyup change', '.campo-numerico', function () {
        // Actualizar 'valor' con el valor limpio (sin comas de formato)
        var v = $(this).val().replace(',', '.') || '0';
        $(this).attr('valor', parseFloat(v) || 0);
        evaluarCalculados();
    });

    // Calcular al cargar la página (en modo editar pueden haber valores previos)
    $(document).ready(function () {
        evaluarCalculados();
    });
})();
</script>
@endif

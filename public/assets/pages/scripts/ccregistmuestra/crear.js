/**
 * Cálculo en tiempo real del resultado por parámetro CC y semáforo general.
 */
$(document).ready(function () {

    var params = window._ccParams || [];

    // Mapa id → config
    var paramMap = {};
    params.forEach(function (p) { paramMap[p.id] = p; });

    function calcularResultado(valor, p) {
        if (p.tipo !== 'number') return 1; // texto/boolean: siempre ok
        var v = parseFloat(String(valor).replace(',', '.'));
        if (isNaN(v)) return null; // sin valor
        var min = p.valor_min !== null && p.valor_min !== '' ? parseFloat(p.valor_min) : null;
        var max = p.valor_max !== null && p.valor_max !== '' ? parseFloat(p.valor_max) : null;
        if (min !== null && v < min) return 3;
        if (max !== null && v > max) return 3;
        return 1;
    }

    function actualizarResultado(paramId) {
        var p    = paramMap[paramId];
        if (!p) return;
        var $inp = $('[name="valor_param[' + paramId + ']"]');
        var val  = $inp.val();
        var $lbl = $('#resultado-param-' + paramId);

        if (val === '' || val === null || val === undefined) {
            $lbl.removeClass('label-success label-danger').addClass('label-default').text('—');
        } else {
            var res = calcularResultado(val, p);
            if (res === 1) {
                $lbl.removeClass('label-default label-danger').addClass('label-success').text('OK ✓');
            } else if (res === 3) {
                $lbl.removeClass('label-default label-success').addClass('label-danger').text('Fuera rango ✗');
            } else {
                $lbl.removeClass('label-success label-danger').addClass('label-default').text('—');
            }
        }
        actualizarResumen();
    }

    function actualizarResumen() {
        var peor = 1;
        var alguno = false;
        params.forEach(function (p) {
            var $inp = $('[name="valor_param[' + p.id + ']"]');
            var val  = $inp.val();
            if (val !== '' && val !== null && val !== undefined) {
                alguno = true;
                var res = calcularResultado(val, p);
                if (res !== null && res > peor) peor = res;
            }
        });

        var $res = $('#status-resumen');
        if (!alguno) {
            $res.removeClass('label-success label-warning label-danger').addClass('label-default').text('Sin evaluar');
        } else if (peor === 1) {
            $res.removeClass('label-default label-warning label-danger').addClass('label-success').text('Aprobado');
        } else if (peor === 2) {
            $res.removeClass('label-default label-success label-danger').addClass('label-warning').text('Aprobado c/obs');
        } else {
            $res.removeClass('label-default label-success label-warning').addClass('label-danger').text('Rechazado');
        }
    }

    // Bloquear caracteres no numéricos en campos tipo number
    // type="number" igual permite 'e', '+', '-' en Chrome — los forzamos a solo dígitos (y punto si hay decimales)
    $(document).on('keydown', '.cc-param-input[data-tipo="number"]', function (e) {
        var decimales  = parseInt($(this).data('decimales') || 0, 10);
        var teclasPerm = [
            8,   // Backspace
            9,   // Tab
            13,  // Enter
            27,  // Escape
            37, 38, 39, 40, // flechas
            46,  // Delete
            110, 190  // punto (numpad y teclado)
        ];
        // Si el campo no admite decimales, bloquear el punto también
        if (decimales === 0) {
            teclasPerm = teclasPerm.filter(function (k) { return k !== 110 && k !== 190; });
        }
        // Dígitos 0-9 (teclado normal y numpad)
        var esDigito = (e.which >= 48 && e.which <= 57) || (e.which >= 96 && e.which <= 105);
        var esTeclaPerm = teclasPerm.indexOf(e.which) !== -1;
        var esCtrl = e.ctrlKey || e.metaKey; // permitir Ctrl+C, Ctrl+V, Ctrl+A
        if (!esDigito && !esTeclaPerm && !esCtrl) {
            e.preventDefault();
        }
    });

    // Limpiar pegado de texto con caracteres inválidos en campos numéricos
    $(document).on('paste', '.cc-param-input[data-tipo="number"]', function () {
        var $el = $(this);
        var decimales = parseInt($el.data('decimales') || 0, 10);
        setTimeout(function () {
            var patron = decimales > 0 ? /[^0-9.]/g : /[^0-9]/g;
            $el.val($el.val().replace(patron, ''));
            $el.trigger('input');
        }, 0);
    });

    // Trigger en cambio de cualquier input de param
    $(document).on('input change', '.cc-param-input', function () {
        var paramId = $(this).data('param-id');
        actualizarResultado(paramId);
    });

    // ── Checkbox "Aprobado con observaciones" ────────────────────────────
    $('#chk-aprobado-obs').on('change', function () {
        var marcado = $(this).is(':checked');
        $('#forzar_status_2').val(marcado ? '1' : '0');

        if (marcado) {
            // Forzar semáforo a amarillo
            $('#status-resumen')
                .removeClass('label-default label-success label-danger')
                .addClass('label-warning')
                .text('Aprobado c/obs');
            // Resaltar label de observación como requerido
            $('#lbl-observacion').html('Observación <span class="text-danger">*</span>:');
            $('#hint-observacion').show();
            $('#campo-observacion').attr('placeholder', 'Observación obligatoria al aprobar con observaciones...');
            // Borde amarillo en el checkbox label
            $('#lbl-aprobado-obs').css('color', '#f39c12');
        } else {
            // Restaurar semáforo al estado calculado
            actualizarResumen();
            $('#lbl-observacion').text('Observación:');
            $('#hint-observacion').hide();
            $('#campo-observacion').attr('placeholder', 'Observaciones opcionales sobre la muestra...');
            $('#lbl-aprobado-obs').css('color', '');
        }
    });

    // Validación antes de enviar
    $('#form-crear-muestra').on('submit', function (e) {
        var vacio = false;
        $('.cc-param-input[required]').each(function () {
            if (!$(this).val()) {
                vacio = true;
                $(this).closest('tr').css('background', '#fff3cd');
            }
        });
        if (vacio) {
            e.preventDefault();
            Biblioteca.notificaciones('Complete los parámetros requeridos antes de guardar.', 'CC Muestra', 'error');
            return;
        }
        // Si está marcado como Aprobado c/obs, la observación es obligatoria
        if ($('#chk-aprobado-obs').is(':checked') && !$.trim($('#campo-observacion').val())) {
            e.preventDefault();
            $('#campo-observacion').focus();
            $('#hint-observacion').show();
            Biblioteca.notificaciones('Debe ingresar la observación al marcar "Aprobado con observaciones".', 'CC Muestra', 'error');
        }
    });
});

/**
 * Abre el PDF del acuerdo técnico del producto en el modal de la pantalla.
 *
 * Es la misma función que general.js, replicada acá a propósito: esta pantalla no
 * carga general.js y no conviene que lo haga, porque esa librería instala
 * manejadores globales de máscara numérica que interfieren con los campos de
 * medición (el espesor necesita 3 decimales). Se define solo si no existe, para
 * no pisar la original si algún día general.js llegara a cargarse aquí.
 */
if (typeof window.genpdfAcuTec !== 'function') {
    window.genpdfAcuTec = function (id, cliente_id, sta_formamostrar, aux_venmodant) {
        var data = '?id=' + id +
                   '&cliente_id=' + (cliente_id || 0) +
                   '&sta_formamostrar=' + (sta_formamostrar || 1); // 1 = PDF, 2 = HTML

        // Patrón de modal anidado: si se abre desde otro modal, se oculta y se
        // recuerda para restaurarlo al cerrar el PDF.
        $('#venmodant').val('');
        if (aux_venmodant) {
            $('#' + aux_venmodant).modal('hide');
            $('#venmodant').val(aux_venmodant);
        }

        $('#contpdf').attr('src', '/acuerdotecnico/exportPdf/' + data);
        $('#myModalpdf').modal('show');
    };
}

/**
 * Cálculo en tiempo real del resultado por parámetro CC y semáforo general.
 */
$(document).ready(function () {

    // Altura del modal del PDF: el iframe usa height="100%", asi que sin una altura
    // explicita en el .modal-body se queda en los 150px por defecto del iframe y el
    // PDF se ve en una franja. general.js hace esto mismo al abrir #myModalpdf, pero
    // esta pantalla no lo carga (ver nota en genpdfAcuTec). Se usa un namespace propio
    // para no duplicar el manejador si general.js llegara a cargarse aqui.
    $('#myModalpdf').off('show.bs.modal.ccmuestra').on('show.bs.modal.ccmuestra', function () {
        $('#myModalpdf .modal-body').css('height', $(window).height() * 0.75);
    });

    var params = window._ccParams || [];

    // Mapa id → config
    var paramMap = {};
    params.forEach(function (p) { paramMap[p.id] = p; });

    function calcularResultado(valor, p) {
        // Cumple / No Cumple: "No Cumple" (0) es un rechazo. Debe coincidir con
        // CcRegistMuestraDet::evaluar() del servidor, que es quien decide al guardar.
        if (p.tipo === 'boolean') {
            if (valor === null || valor === '') return null; // sin seleccionar
            return (String(valor) === '0') ? 3 : 1;
        }
        if (p.tipo !== 'number') return 1; // texto libre: sin criterio, no se evalua
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

    // ── Toggle R2: "No se pudo tomar la muestra" ────────────────────────
    $('#btn-sin-params').on('click', function () {
        $('#sin_parametros').val('1');
        $('#div-sin-params-banner').show();
        $('#btn-sin-params').hide();
        $('#btn-con-params').show();
        // Ocultar sección de parámetros y semáforo
        $('#div-params-normal').hide();
        $('#alerta-sin-config').hide();
        // Marcar observación como obligatoria (motivo)
        $('#lbl-observacion').html('Motivo <span class="text-danger">*</span>:');
        $('#campo-observacion').attr('placeholder', 'Ingrese el motivo por el que no se pudo tomar la muestra...');
        $('#hint-observacion').hide();
        // Quitar required de inputs de params para que no bloqueen el submit
        $('.cc-param-input').removeAttr('required');
    });

    $('#btn-con-params').on('click', function () {
        $('#sin_parametros').val('0');
        $('#div-sin-params-banner').hide();
        $('#btn-con-params').hide();
        $('#btn-sin-params').show();
        // Mostrar sección de parámetros
        $('#div-params-normal').show();
        $('#alerta-sin-config').show();
        // Restaurar label observación
        if ($('#chk-aprobado-obs').is(':checked')) {
            $('#lbl-observacion').html('Observación <span class="text-danger">*</span>:');
        } else {
            $('#lbl-observacion').text('Observación:');
        }
        $('#campo-observacion').attr('placeholder', 'Observaciones opcionales sobre la muestra...');
        // Restaurar required según data-original
        $('.cc-param-input[data-param-id]').each(function () {
            if ($(this).closest('tr').find('.label-danger').length) {
                $(this).attr('required', 'required');
            }
        });
    });

    // Validación antes de enviar
    $('#form-crear-muestra').on('submit', function (e) {
        // R2: modo sin parámetros — solo requiere observación (motivo)
        if ($('#sin_parametros').val() === '1') {
            if (!$.trim($('#campo-observacion').val())) {
                e.preventDefault();
                $('#campo-observacion').focus();
                Biblioteca.notificaciones('Debe ingresar el motivo por el que no se pudo tomar la muestra.', 'CC Muestra', 'error');
            }
            return;
        }

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

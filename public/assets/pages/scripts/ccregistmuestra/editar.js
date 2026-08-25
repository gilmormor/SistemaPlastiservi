/**
 * Cálculo en tiempo real del resultado por parámetro CC en la vista editar.
 * Usa window._ccParams con clave det_id en lugar de ccparam_ap_id.
 */
$(document).ready(function () {

    var params = window._ccParams || [];

    // Mapa det_id → config
    var paramMap = {};
    params.forEach(function (p) { paramMap[p.det_id] = p; });

    function calcularResultado(valor, p) {
        // Cumple / No Cumple: "No Cumple" (0) es un rechazo. Debe coincidir con
        // CcRegistMuestraDet::evaluar() del servidor, que es quien decide al guardar.
        if (p.tipo === 'boolean') {
            if (valor === null || valor === '') return null; // sin seleccionar
            return (String(valor) === '0') ? 3 : 1;
        }
        if (p.tipo !== 'number') return 1; // texto libre: sin criterio, no se evalua
        var v = parseFloat(String(valor).replace(',', '.'));
        if (isNaN(v)) return null;
        var min = p.valor_min !== null && p.valor_min !== '' ? parseFloat(p.valor_min) : null;
        var max = p.valor_max !== null && p.valor_max !== '' ? parseFloat(p.valor_max) : null;
        if (min !== null && v < min) return 3;
        if (max !== null && v > max) return 3;
        return 1;
    }

    function actualizarResultado(detId) {
        var p    = paramMap[detId];
        if (!p) return;
        var $inp = $('[name="valor_param[' + detId + ']"]');
        var val  = $inp.val();
        var $lbl = $('#resultado-det-' + detId);

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
            var $inp = $('[name="valor_param[' + p.det_id + ']"]');
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

    // Trigger al cambiar cualquier input
    $(document).on('input change', '.cc-param-input', function () {
        var detId = $(this).data('det-id');
        actualizarResultado(detId);
    });

    // Inicializar semáforo con los valores actuales
    params.forEach(function (p) {
        actualizarResultado(p.det_id);
    });

    // Validación antes de enviar
    $('#form-editar-muestra').on('submit', function (e) {
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
        }
    });
});

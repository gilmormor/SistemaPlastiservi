/* ====================================================================
   ETAPA EXTERNA — listado de trazabilidad.

   Muestra las tiradas de etiquetas impresas (una fila por tirada, con su
   cantidad de copias) y de que planilla salio cada una. Es el rastro del
   proceso que se ejecuta fuera del sistema.
   ==================================================================== */

var EXT_TABLA = null;

$(document).ready(function () {

    $('.date-picker').datepicker({
        language      : 'es',
        format        : 'dd/mm/yyyy',
        autoclose     : true,
        todayHighlight: true
    });

    EXT_TABLA = $('#tabla-data').DataTable({
        processing: true,
        ajax: {
            url : EXT_RUTA_LISTADO,
            type: 'GET',
            /* Los filtros se leen en cada recarga, no al construir la tabla:
               asi el boton Consultar solo tiene que llamar a ajax.reload(). */
            data: function (d) {
                d.fechad          = $('#fechad').val();
                d.fechah          = $('#fechah').val();
                d.opdetregprod_id = $('#f_opdetregprod_id').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'created_at', render: function (d) { return formatoFechaHora(d); } },
            { data: 'opdetregprod_id', render: function (d) { return '#' + d; } },
            /* Lotes que se produjeron con este material. Vacio = todavia no se
               registro produccion con el lote. */
            { data: 'destinos', render: function (d) { return d ? d : '<span class="text-muted">—</span>'; } },
            { data: 'etapaextplanilla_id', render: function (d) { return d ? '#' + d : '—'; } },
            { data: 'etapa',    defaultContent: '—' },
            { data: 'maquina',  defaultContent: '—' },
            { data: 'operario', defaultContent: '—' },
            { data: 'fecha',    render: function (d) { return formatoFecha(d); } },
            { data: 'turno',    defaultContent: '—' },
            { data: 'kilos',    className: 'text-right', defaultContent: '—' },
            { data: 'unidades', className: 'text-right', defaultContent: '—' },
            { data: 'cantidad', className: 'text-right' },
            { data: 'usuario',  defaultContent: '—' },
            {
                data: 'id',
                orderable: false,
                render: function (d) {
                    return '<button type="button" class="btn btn-xs btn-default btnverdoc" ' +
                           'data-url="' + EXT_IMP_ETIQUETA + '/' + d + '/imprimir" ' +
                           'data-titulo="Etiquetas — Tirada N° ' + d + '">' +
                           '<i class="fa fa-print"></i></button>';
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            // Mismo origen que usa general.js para el resto de las tablas
            url: 'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json'
        }
    });

    $('#btnconsultar').on('click', function () {
        EXT_TABLA.ajax.reload();
    });

    /* Reimprimir: va directo al dialogo de impresion, sin vista previa propia.
       Delegado porque las filas las pinta DataTables despues de cargar. */
    $(document).on('click', '.btnverdoc', function () {
        imprimirDocumento($(this).attr('data-url'));
    });
});

/**
 * Carga el documento en el iframe oculto y abre el dialogo de impresion.
 * Misma mecanica que en la pantalla principal (ver etapaexterna/index.js).
 */
function imprimirDocumento(url) {
    var aux_frame = document.getElementById('contimpresion');
    if (!aux_frame) { return; }

    aux_frame.onload = function () {
        // Espera a que el documento termine de autoajustar su contenido
        setTimeout(function () {
            try {
                aux_frame.contentWindow.focus();
                aux_frame.contentWindow.print();
            } catch (e) {
                alertify.error('No se pudo abrir el diálogo de impresión.');
            }
        }, 200);
    };

    aux_frame.src = url + '?modal=1';
}

/* Se formatean sin Date() para que la zona horaria no corra el dia. */
function formatoFechaHora(valor) {
    if (!valor) { return '—'; }
    var aux = valor.split(' ');
    var f   = aux[0].split('-');
    if (f.length !== 3) { return valor; }
    return f[2] + '/' + f[1] + '/' + f[0] + (aux[1] ? ' ' + aux[1].substring(0, 5) : '');
}

function formatoFecha(valor) {
    if (!valor) { return '—'; }
    var f = String(valor).split(' ')[0].split('-');
    if (f.length !== 3) { return valor; }
    return f[2] + '/' + f[1] + '/' + f[0];
}

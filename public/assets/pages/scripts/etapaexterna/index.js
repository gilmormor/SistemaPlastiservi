/* ====================================================================
   ETAPA EXTERNA — planilla y etiquetas de paquete.

   El supervisor busca un lote aprobado, y desde ahi imprime la planilla que
   le entrega al operario y las etiquetas que van pegadas a cada paquete.
   El modulo no registra produccion: solo deja rastro de lo impreso.

   Las rutas llegan como constantes desde la vista (index.blade.php), porque
   este archivo es estatico y no pasa por Blade.
   ==================================================================== */

/* Datos del lote actualmente cargado. Se llena en buscarLote() y lo consumen
   los dos modales, para no volver a consultar el servidor al abrirlos. */
var EXT_LOTE = null;

$(document).ready(function () {

    $('#et_fecha').datepicker({
        language      : 'es',
        format        : 'dd/mm/yyyy',
        autoclose     : true,
        todayHighlight: true
    });

    $('#btnbuscarlote').on('click', function () {
        buscarLote();
    });

    /* Enter en el campo del lote busca, en vez de recargar la pagina. */
    $('#opdetregprod_id').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            buscarLote();
        }
    });

    $('#btnplanilla').on('click', function () {
        abrirModalPlanilla();
    });

    $('#btnetiquetas').on('click', function () {
        abrirModalEtiqueta();
    });

    $('#btnguardarplanilla').on('click', function () {
        guardarPlanilla();
    });

    $('#btnguardaretiqueta').on('click', function () {
        guardarEtiqueta();
    });

    /* Al cambiar de planilla en el modal de etiquetas se precargan su maquina
       y su responsable: las etiquetas deben salir acordes a lo que se entrego
       al operario. Igual se pueden cambiar a mano. */
    $('#et_planilla_id').on('change', function () {
        precargarDesdePlanilla($(this).val());
    });

    /* Reimpresion desde el historial. Se delega porque las filas se pintan
       despues de cargar la pagina. */
    $(document).on('click', '.btnverdoc', function () {
        imprimirDocumento($(this).attr('data-url'), '');
    });
});

/* ====================================================================
   IMPRESION DIRECTA
   ==================================================================== */
/**
 * Carga el documento en el iframe oculto y abre el dialogo de impresion.
 * No se muestra vista previa propia: la del navegador ya la hace.
 *
 * modalPadre: id del modal desde el que se llama, para cerrarlo. Vacio si se
 * llama desde la pantalla (reimpresion del historial).
 */
function imprimirDocumento(url, modalPadre) {
    if (modalPadre) {
        $('#' + modalPadre).modal('hide');
    }

    var aux_frame = document.getElementById('contimpresion');
    if (!aux_frame) { return; }

    aux_frame.onload = function () {
        /* Pequeña espera antes de imprimir: el documento ajusta su contenido
           al cargar (filas en blanco de la planilla, tamaño de letra de las
           etiquetas) y conviene que ese reajuste termine antes de que el
           navegador tome la medida para la vista previa. */
        setTimeout(function () {
            try {
                aux_frame.contentWindow.focus();
                aux_frame.contentWindow.print();
            } catch (e) {
                alertify.error('No se pudo abrir el diálogo de impresión.');
            }
        }, 200);
    };

    /* modal=1: el documento no dibuja su propio boton Imprimir, que aqui no se
       ve ni hace falta. */
    aux_frame.src = url + '?modal=1';
}

/* ====================================================================
   BUSQUEDA DEL LOTE
   ==================================================================== */
function buscarLote() {
    var aux_id = $.trim($('#opdetregprod_id').val());

    if (aux_id === '') {
        alertify.error('Escriba el número de lote.');
        return;
    }

    $.ajax({
        url : EXT_RUTA_LOTE + '/' + aux_id,
        type: 'GET',
        success: function (resp) {
            if (!resp.ok) {
                /* El lote no existe o no esta aprobado. Se limpia la pantalla
                   para que no quede visible la ficha de una busqueda anterior,
                   que llevaria a imprimir el lote equivocado. */
                limpiarPantalla();
                alertify.error(resp.mensaje);
                return;
            }
            EXT_LOTE = resp;
            pintarLote(resp);
        },
        error: function () {
            limpiarPantalla();
            alertify.error('No se pudo consultar el lote.');
        }
    });
}

function limpiarPantalla() {
    EXT_LOTE = null;
    $('#boxlote').hide();
    $('#boxhistorial').hide();
}

function pintarLote(resp) {
    var l = resp.lote;

    $('#lbl_lote').text('#' + l.id);
    $('#lbl_cliente').text(l.cliente);
    $('#lbl_producto').text('id ' + l.producto_id + ' — ' + l.producto);
    $('#lbl_materiaprima').text(l.materiaprima);
    $('#lbl_formaembalar').text(l.formaembalar ? l.formaembalar : '(sin definir)');
    $('#lbl_otop').text('OT-' + l.ot_id + ' / OP-' + l.op_id + ' / OpDet ' + l.opdet_id);
    $('#lbl_kg').text(l.cantprod + ' unid. / ' + l.kgprod + ' kg');
    $('#lbl_origen').text(l.etapaorigen + ' — máquina ' + l.maquinaorigen);
    $('#lbl_etapa').text(resp.etapaprod_nombre ? resp.etapaprod_nombre : '—');

    /* Sin etapa siguiente (lote de la ultima etapa) no hay nada que imprimir:
       se avisa y se desactivan los dos botones. */
    if (!resp.etapaprod_id) {
        $('#avisosinetapa').show();
        $('#btnplanilla, #btnetiquetas').prop('disabled', true);
    } else {
        $('#avisosinetapa').hide();
        $('#btnplanilla, #btnetiquetas').prop('disabled', false);
    }

    pintarHistorial(resp);

    $('#boxlote').show();
    $('#boxhistorial').show();
}

function pintarHistorial(resp) {
    var html = '';

    /* Registros que consumieron el lote. Un lote puede alimentar a varios
       (dos turnos, entregas parciales), por eso es una lista y no un dato
       unico. Los pendientes se marcan para distinguirlos de los aprobados. */
    if (!resp.destinos || resp.destinos.length === 0) {
        html = '<tr><td colspan="6" class="text-center text-muted">' +
               'Todavía no se ha registrado producción con este lote</td></tr>';
    } else {
        $.each(resp.destinos, function (i, d) {
            var aux_etiq = (d.tipo === 'lote')
                ? '<span class="label label-success">Lote #' + d.id + '</span>'
                : '<span class="label label-default">Registro temp #' + d.id + '</span>';
            var aux_est = (d.estado === 'Aprobado')
                ? '<span class="text-green">' + d.estado + '</span>'
                : (d.estado === 'Rechazado'
                    ? '<span class="text-red">' + d.estado + '</span>'
                    : '<span class="text-muted">' + d.estado + '</span>');
            html += '<tr>' +
                '<td>' + aux_etiq + '</td>' +
                '<td>' + (d.etapa || '—') + '</td>' +
                '<td>' + (d.operario || '—') + '</td>' +
                '<td class="text-right">' + d.kg + '</td>' +
                '<td>' + formatoFechaHora(d.fecha) + '</td>' +
                '<td>' + aux_est + '</td>' +
                '</tr>';
        });
    }
    $('#tbodydestinos').html(html);

    html = '';
    if (resp.planillas.length === 0) {
        html = '<tr><td colspan="7" class="text-center text-muted">Sin planillas impresas</td></tr>';
    } else {
        $.each(resp.planillas, function (i, p) {
            html += '<tr>' +
                '<td>' + p.id + '</td>' +
                '<td>' + formatoFechaHora(p.created_at) + '</td>' +
                '<td>' + (p.maquina || '—') + '</td>' +
                '<td>' + (p.operario || '—') + '</td>' +
                '<td class="text-right">' + (p.nrobolsas !== null ? p.nrobolsas : '') + '</td>' +
                '<td>' + (p.usuario || '—') + '</td>' +
                '<td><button type="button" class="btn btn-xs btn-default btnverdoc" ' +
                'data-url="' + EXT_IMP_PLANILLA + '/' + p.id + '/imprimir" ' +
                'data-titulo="Planilla N° ' + p.id + '">' +
                '<i class="fa fa-print"></i> Reimprimir</button></td>' +
                '</tr>';
        });
    }
    $('#tbodyplanillas').html(html);

    html = '';
    if (resp.etiquetas.length === 0) {
        html = '<tr><td colspan="11" class="text-center text-muted">Sin etiquetas impresas</td></tr>';
    } else {
        $.each(resp.etiquetas, function (i, e) {
            html += '<tr>' +
                '<td>' + e.id + '</td>' +
                '<td>' + formatoFechaHora(e.created_at) + '</td>' +
                '<td>' + (e.etapaextplanilla_id ? '#' + e.etapaextplanilla_id : '—') + '</td>' +
                '<td>' + (e.maquina || '—') + '</td>' +
                '<td>' + (e.operario || '—') + '</td>' +
                '<td>' + (e.turno || '—') + '</td>' +
                '<td class="text-right">' + (e.kilos !== null ? e.kilos : '—') + '</td>' +
                '<td class="text-right">' + (e.unidades !== null ? e.unidades : '—') + '</td>' +
                '<td class="text-right">' + e.cantidad + '</td>' +
                '<td>' + (e.usuario || '—') + '</td>' +
                '<td><button type="button" class="btn btn-xs btn-default btnverdoc" ' +
                'data-url="' + EXT_IMP_ETIQUETA + '/' + e.id + '/imprimir" ' +
                'data-titulo="Etiquetas — Tirada N° ' + e.id + '">' +
                '<i class="fa fa-print"></i> Reimprimir</button></td>' +
                '</tr>';
        });
    }
    $('#tbodyetiquetas').html(html);
}

/* Las fechas llegan del servidor como "AAAA-MM-DD HH:MM:SS". Se muestran en
   formato chileno sin usar Date(), que interpreta la zona horaria y puede
   correr el dia. */
function formatoFechaHora(valor) {
    if (!valor) { return '—'; }
    var aux = valor.split(' ');
    var f   = aux[0].split('-');
    if (f.length !== 3) { return valor; }
    return f[2] + '/' + f[1] + '/' + f[0] + (aux[1] ? ' ' + aux[1].substring(0, 5) : '');
}

/* ====================================================================
   SELECTS
   ==================================================================== */
function cargarSelect(selector, items, valorSel) {
    var html = '<option value="">Seleccione...</option>';
    $.each(items, function (i, it) {
        var sel = (valorSel && String(valorSel) === String(it.id)) ? ' selected' : '';
        html += '<option value="' + it.id + '"' + sel + '>' + it.nombre + '</option>';
    });
    $(selector).html(html);
}

/* ====================================================================
   MODAL PLANILLA
   ==================================================================== */
function abrirModalPlanilla() {
    if (!EXT_LOTE) { return; }

    cargarSelect('#pl_maquina_id', EXT_LOTE.maquinas, null);
    cargarSelect('#pl_operario_id', EXT_LOTE.operarios, null);

    $('#pl_nrobolsas').val('');
    $('#pl_obs').val('');
    // La forma de embalar se precarga con lo ya definido, si lo hay
    $('#pl_formaembalar').val(EXT_LOTE.lote.formaembalar || '');

    /* Si la etapa no tiene maquinas en la sucursal del usuario no se puede
       continuar: se avisa en vez de abrir un modal con el select vacio. */
    if (EXT_LOTE.maquinas.length === 0) {
        alertify.error('La etapa ' + EXT_LOTE.etapaprod_nombre +
                       ' no tiene máquinas configuradas en su sucursal.');
        return;
    }

    $('#modalplanilla').modal('show');
}

function guardarPlanilla() {
    var data = {
        opdetregprod_id: EXT_LOTE.lote.id,
        etapaprod_id   : EXT_LOTE.etapaprod_id,
        maquina_id     : $('#pl_maquina_id').val(),
        operario_id    : $('#pl_operario_id').val(),
        nrobolsas      : $.trim($('#pl_nrobolsas').val()),
        formaembalar   : $.trim($('#pl_formaembalar').val()),
        obs            : $.trim($('#pl_obs').val()),
        _token         : $('input[name=_token]').val()
    };

    if (data.maquina_id === '' || data.operario_id === '') {
        alertify.error('Seleccione la máquina y el responsable.');
        return;
    }

    $('#btnguardarplanilla').prop('disabled', true);

    $.ajax({
        url : EXT_RUTA_PLANILLA,
        type: 'POST',
        data: data,
        success: function (resp) {
            $('#btnguardarplanilla').prop('disabled', false);
            if (!resp.ok) {
                alertify.error(resp.mensaje);
                return;
            }
            alertify.success('Planilla N° ' + resp.id + ' generada.');
            imprimirDocumento(EXT_IMP_PLANILLA + '/' + resp.id + '/imprimir', 'modalplanilla');
            buscarLote();   // refresca el historial
        },
        error: function () {
            $('#btnguardarplanilla').prop('disabled', false);
            alertify.error('No se pudo guardar la planilla.');
        }
    });
}

/* ====================================================================
   MODAL ETIQUETAS
   ==================================================================== */
function abrirModalEtiqueta() {
    if (!EXT_LOTE) { return; }

    if (EXT_LOTE.maquinas.length === 0) {
        alertify.error('La etapa ' + EXT_LOTE.etapaprod_nombre +
                       ' no tiene máquinas configuradas en su sucursal.');
        return;
    }

    /* Select de planillas del lote. Si no hay ninguna se avisa, pero NO se
       bloquea: imprimir etiquetas no exige planilla previa (si se bloqueara,
       un dia sin impresora frenaria la produccion). */
    var html = '<option value="">(sin planilla)</option>';
    $.each(EXT_LOTE.planillas, function (i, p) {
        html += '<option value="' + p.id + '">Planilla #' + p.id + ' — ' +
                (p.maquina || '—') + ' / ' + (p.operario || '—') + '</option>';
    });
    $('#et_planilla_id').html(html);

    if (EXT_LOTE.planillas.length === 0) {
        $('#avisosinplanilla').show();
        $('#grupoplanilla').hide();
    } else {
        $('#avisosinplanilla').hide();
        $('#grupoplanilla').show();
    }

    cargarSelect('#et_maquina_id', EXT_LOTE.maquinas, null);
    cargarSelect('#et_operario_id', EXT_LOTE.operarios, null);

    $('#et_fecha').val(fechaHoy());
    $('#et_turno').val('');
    $('#et_kilos').val('');
    $('#et_unidades').val('');
    $('#et_obs').val('');
    $('#et_cantidad').val('');

    /* Si hay planillas, se preselecciona la ultima (la mas reciente, que es la
       que se acaba de entregar) y se heredan sus datos. */
    if (EXT_LOTE.planillas.length > 0) {
        $('#et_planilla_id').val(EXT_LOTE.planillas[0].id);
        precargarDesdePlanilla(EXT_LOTE.planillas[0].id);
    }

    $('#modaletiqueta').modal('show');
}

function precargarDesdePlanilla(planilla_id) {
    if (!planilla_id || !EXT_LOTE) { return; }

    var aux_pla = null;
    $.each(EXT_LOTE.planillas, function (i, p) {
        if (String(p.id) === String(planilla_id)) { aux_pla = p; }
    });
    if (!aux_pla) { return; }

    /* La planilla guarda los nombres, no los ids, porque viene de una consulta
       con join. Se buscan los ids en los catalogos ya cargados. */
    $.each(EXT_LOTE.maquinas, function (i, m) {
        if (m.nombre === aux_pla.maquina) { $('#et_maquina_id').val(m.id); }
    });
    $.each(EXT_LOTE.operarios, function (i, o) {
        if (o.nombre === aux_pla.operario) { $('#et_operario_id').val(o.id); }
    });
}

function fechaHoy() {
    var d = new Date();
    var dd = ('0' + d.getDate()).slice(-2);
    var mm = ('0' + (d.getMonth() + 1)).slice(-2);
    return dd + '/' + mm + '/' + d.getFullYear();
}

function guardarEtiqueta() {
    var data = {
        etapaextplanilla_id: $('#et_planilla_id').val(),
        opdetregprod_id    : EXT_LOTE.lote.id,
        etapaprod_id       : EXT_LOTE.etapaprod_id,
        maquina_id         : $('#et_maquina_id').val(),
        operario_id        : $('#et_operario_id').val(),
        fecha              : $.trim($('#et_fecha').val()),
        turno              : $.trim($('#et_turno').val()),
        kilos              : $.trim($('#et_kilos').val()),
        unidades           : $.trim($('#et_unidades').val()),
        obs                : $.trim($('#et_obs').val()),
        cantidad           : $.trim($('#et_cantidad').val()),
        _token             : $('input[name=_token]').val()
    };

    if (data.maquina_id === '' || data.operario_id === '') {
        alertify.error('Seleccione la máquina y el operario.');
        return;
    }
    if (data.fecha === '') {
        alertify.error('Indique la fecha de producción.');
        return;
    }
    /* La cantidad se valida aqui ademas de en el servidor: un cero o un texto
       mandaria a imprimir cero etiquetas despues de haber guardado el registro. */
    if (data.cantidad === '' || isNaN(data.cantidad) || parseInt(data.cantidad, 10) < 1) {
        alertify.error('Indique cuántas etiquetas imprimir (mínimo 1).');
        return;
    }

    $('#btnguardaretiqueta').prop('disabled', true);

    $.ajax({
        url : EXT_RUTA_ETIQUETA,
        type: 'POST',
        data: data,
        success: function (resp) {
            $('#btnguardaretiqueta').prop('disabled', false);
            if (!resp.ok) {
                alertify.error(resp.mensaje);
                return;
            }
            alertify.success('Tirada N° ' + resp.id + ' generada (' + data.cantidad + ' etiquetas).');
            imprimirDocumento(EXT_IMP_ETIQUETA + '/' + resp.id + '/imprimir', 'modaletiqueta');
            buscarLote();   // refresca el historial
        },
        error: function () {
            $('#btnguardaretiqueta').prop('disabled', false);
            alertify.error('No se pudo guardar la tirada de etiquetas.');
        }
    });
}

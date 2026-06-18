/**
 * Pantalla de búsqueda de registros opdetregprod para crear muestra CC.
 * La tabla permanece oculta hasta que el usuario haga clic en "Consultar".
 */
var _tablaInit = false;
var _tabla;

$(document).ready(function () {

    // Inicializar datepickers
    if ($.fn.datepicker) {
        $('#fechad, #fechah').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            language: 'es'
        });
    }

    // Bloquear caracteres no numéricos en los filtros de ID entero (e, +, -, .)
    var selectoresEntero = '#opdetregprod_id, #op_id, #ot_id';
    $(document).on('keydown', selectoresEntero, function (e) {
        var teclasBloqueadas = ['e', 'E', '+', '-', '.', ','];
        if (teclasBloqueadas.indexOf(e.key) !== -1) {
            e.preventDefault();
        }
    });
    $(document).on('input', selectoresEntero, function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // F2 en el campo RUT abre el modal de búsqueda de cliente
    $('#rut').on('keyup', function (e) {
        if (e.which === 113) { // F2
            $('#rut').val('');
            $('#myModalBusqueda').modal('show');
        }
    });

    // Validación blur del RUT: formatea de inmediato y luego valida existencia del cliente
    $('#rut').on('blur', function () {
        var codigo = $(this).val();
        if (codigo === null || codigo.length === 0 || /^\s+$/.test(codigo)) {
            return;
        }
        // Formatear con puntos y guion antes de la consulta AJAX
        formato_rut($('#rut'));
        var data = {
            rut: eliminarFormatoRutret(codigo),
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/cliente/buscarCli',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                if (respuesta.length === 0) {
                    swal({
                        title: 'Cliente no encontrado.',
                        text: 'Verifique el RUT ingresado.',
                        icon: 'warning',
                        buttons: { confirm: 'Aceptar' }
                    }).then(function () {
                        $('#rut').val('').focus();
                    });
                }
            }
        });
    });

    // Botón Buscar abre el modal de selección de cliente
    $('#btnbuscarcliente').on('click', function () {
        $('#rut').val('');
        $('#myModalBusqueda').modal('show');
    });

    // Botón Consultar
    $('#btnconsultar').on('click', function () {
        consultar();
    });
});

// Función llamada desde el modal de clientes al seleccionar uno
function copiar_rut(id, rut) {
    $('#myModalBusqueda').modal('hide');
    $('#rut').val(rut);
    formato_rut($('#rut'));
}

function consultar() {
    var fechad           = $('#fechad').val();
    var fechah           = $('#fechah').val();
    var etapaprod        = $('#etapaprod_id').val();
    var maquina_id       = $('#maquina_id').val();
    var op_id            = $('#op_id').val();
    var ot_id            = $('#ot_id').val();
    var con_muestra      = $('#con_muestra').val();
    var opdetregprod_id  = $('#opdetregprod_id').val();
    var rut              = eliminarFormatoRutret($('#rut').val());

    var params = {
        fechad:           fechad,
        fechah:           fechah,
        etapaprod_id:     etapaprod,
        maquina_id:       maquina_id,
        op_id:            op_id,
        ot_id:            ot_id,
        con_muestra:      con_muestra,
        opdetregprod_id:  opdetregprod_id,
        rut:              rut
    };

    $('#msg-inicial').hide();
    $('#contenedor-tabla').show();

    var urlFiltrada = '/listaropdetregprodpage_ccregistmuestra?' + $.param(params);

    if (!_tablaInit) {
        _tabla = $('#tabla-opdetregprod').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: urlFiltrada,
                dataSrc: 'data'
            },
            columns: [
                { data: 'id' },
                { data: 'created_at', render: function(d){ return d ? d.substring(0,16).replace('T',' ') : '—'; } },
                { data: null, render: function(d){ return 'OP ' + d.op_id + ' / OT ' + d.ot_id; } },
                { data: 'producto_nombre' },
                { data: 'etapaprod_nombre', render: function(d, t, row) {
                    var txt = d || '—';
                    if (row.maquina_nombre) {
                        txt += '<br><small class="text-muted"><i class="fa fa-cog"></i> ' + row.maquina_nombre + '</small>';
                    }
                    return txt;
                }},
                { data: 'kgprod', render: function(d){ return d ? parseFloat(d).toLocaleString('es-CL',{minimumFractionDigits:2}) : '—'; } },
                { data: 'cantprod', render: function(d, t, row){ return d ? parseFloat(d).toLocaleString('es-CL',{minimumFractionDigits:2}) + ' ' + (row.unidadmedidasal_nombre || '') : '—'; } },
                {
                    data: null,
                    render: function (d) {
                        var n = d.cant_muestras || 0;
                        var color = {1: '#00a65a', 2: '#f39c12', 3: '#dd4b39'};
                        var icono = '';
                        if (n > 0 && d.peor_status_cc) {
                            icono = ' <i class="fa fa-circle" style="color:' + (color[d.peor_status_cc] || '#ccc') + ';"></i>';
                        }
                        return '<span class="badge" style="background:#777;">' + n + '</span>' + icono;
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function (id, t, row) {
                        var rutaCrear = '/ccregistmuestra/crear/' + id;
                        var rutaVer   = '/ccregistmuestra/verxopdetregprod/' + id;
                        var btnCrear  = '<a href="' + rutaCrear + '" class="btn btn-xs btn-success" title="Nueva muestra CC">'
                                      + '<i class="fa fa-plus"></i> Muestra</a>';
                        var btnVer    = '';
                        if (row.cant_muestras > 0) {
                            btnVer = ' <a href="' + rutaVer + '" class="btn btn-xs btn-primary" title="Ver muestras CC">'
                                   + '<i class="fa fa-eye"></i> Ver</a>';
                        }
                        return btnCrear + btnVer;
                    }
                }
            ],
            order: [[0, 'desc']],
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
        _tablaInit = true;
    } else {
        // Actualizar parámetros y recargar con URL fresca (evita mezcla con params del init)
        _tabla.ajax.url(urlFiltrada).load();
    }
}

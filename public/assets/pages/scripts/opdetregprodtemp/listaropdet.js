$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    //alert(aux_nfila);
    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");
    
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
    });

    configurarTabla('.tablas');

    data = datosopdet();
    let table =$('#tabla-data-consulta').DataTable({
        'paging'      : true,
        'scrollX'     : true,
        'scrollY'     : 'calc(100vh - 320px)',
        'scrollCollapse': true,
        'lengthChange': true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "/opdetregprodtemp/listaropdetpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>'
            },
            {data: 'id'},
            {data: 'created_at'},
            {data: 'op_id'},
            {data: 'razonsocial'},
            {data: 'maquina_nombre'},
            {data: 'producto_id'},
            {data: 'nombre_producto'},
            {data: 'cantrec'},
            {data: 'kgrec'},
            {data: 'cantprod'},
            {data: 'kgprod'},
            {data: 'kgscrap'},
            {data: 'saldokg'},
            {defaultContent : ``}
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at',data.updated_at);
            
            /* aux_text = 
                `<a id="otdet_id${data.id}" name="otdet_id${data.id}" class="btn-accion-tabla btn-sm" title="Ver OT: ${data.ot_id}" onclick='genpdf(${data.ot_id},"","ver-pdf-ot","/ot/exportPdf/${data.ot_id}")'>
                    ${data.ot_id}
                </a>
                <a id="otdet_id${data.id}" name="otdet_id${data.id}" class="btn-accion-tabla btn-sm" title="Ver OP: ${data.op_id}" onclick='genpdf(${data.op_id},"","ver-pdf-op","/op/exportPdf/${data.op_id}")'>
                    ${data.op_id}-${data.id}
                </a>`;

            $('td', row).eq(1).html(aux_text); */
            $('td', row).eq(1).attr('data-order',data.id);

            $('td', row).eq(2).attr('data-order',data.created_at);
            aux_fecha = new Date(data.created_at);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha) + " " + data.created_at.substr(11, 8));
            $('td', row).eq(2).attr("style","font-size:12px");

            aux_textNV = "";
            if(data.notaventa_id){
                aux_textNV = `<a class="btn-accion-tabla btn-sm tooltipsC" title="Nota Venta" onclick="genpdfNV(${data.notaventa_id},1)" style="padding-left: 0px;">
                    ${data.notaventa_id}
                </a>`;
            }
            aux_text = aux_textNV + `
                <a class="btn-accion-tabla btn-sm tooltipsC" title="Orden Trabajo" onclick='genpdf(${data.ot_id},"","ver-pdf-ot","/ot/exportPdf/${data.ot_id}")' style="padding-left: 0px;">
                    ${data.ot_id}
                </a>
                <a class="btn-accion-tabla btn-sm tooltipsC" title="Orden Trabajo Detalle" onclick='genpdf(${data.ot_id},"","ver-pdf-ot","/ot/exportPdf/${data.ot_id}")' style="padding-left: 0px;">
                    ${data.otdet_id}
                </a>
                <a class="btn-accion-tabla btn-sm tooltipsC" title="Orden Produccion" ${data.op_id}" onclick='genpdf(${data.op_id},"","ver-pdf-op","/op/exportPdf/${data.op_id}")' style="padding-left: 0px;">
                    ${data.op_id}
                </a>
                <a class="btn-accion-tabla btn-sm tooltipsC" title="Orden Produccion Detalle ${data.opdet_id}" onclick='genpdf(${data.op_id},"","ver-pdf-op","/op/exportPdf/${data.op_id}")' style="padding-left: 0px;">
                    ${data.id}
                </a>`
            $('td', row).eq(3).html(aux_text);


			if(data.acuerdotecnico_id != null){
				//$('td', row).eq(0).html(aux_text);
				if (data.hasOwnProperty('cliente_id')) {
					aux_cliente_id = data.cliente_id;
				} else {
					aux_cliente_id = 0;
				}
				aux_text = 
				`<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick='genpdfAcuTec(${data.acuerdotecnico_id},${aux_cliente_id},"")'>
					${data.producto_id}
				</a>`;
				if(data.at_impresofoto != "" && data.at_impresofoto != null){
					aux_text += 
						`<a class="btn-accion-tabla btn-sm tooltipsC" title="Ver Imagen" onclick='verpdf2(\"at/${data.at_impresofoto}\",2,"","ver-arte-acuerdo-tecnico")'>
							<i class="fa fa-fw fa-photo"></i>
						</a>`;
				}
				$('td', row).eq(6).html(aux_text);
				//$('td', row).eq(0).attr('onClick', 'genpdfAcuTec(' + data.acuerdotecnico_id + ',' + aux_cliente_id +',"");');
			}
            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).html(MASKLA(data.cantrec,2));
            $('td', row).eq(9).attr('style','text-align:right');
            $('td', row).eq(9).html(MASKLA(data.kgrec,2));
            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).html(MASKLA(data.cantprod,2));
            $('td', row).eq(11).attr('style','text-align:right');
            $('td', row).eq(11).html(MASKLA(data.kgprod,2));
            $('td', row).eq(12).attr('style','text-align:right');
            $('td', row).eq(12).html(MASKLA(data.kgscrap,2));
            $('td', row).eq(13).attr('style','text-align:right');
            $('td', row).eq(13).html(MASKLA(data.saldokg,2));

            //href="${nuevaaux_rutadespsol}"
            aux_text = 
            `<a class="btn-accion-tabla tooltipsC enlace-soldesp botonac${data.id}" title="Procesar Registro Produccion" href="/opdetregprodtemp/crearini/${data.id}/${data.updatednum_at}">
                <button type="button" class="btn btn-default btn-xs">
                    <i class="fa fa-fw fa-gear text-primary"></i>
                </button>
            </a>`;
            $('td', row).eq(14).html(aux_text);


            /* $('td', row).eq(5).attr("style","font-size:13px");

            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm' title='Ver Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(5).html(aux_text);
            }

            aux_text = 
            `<a class="btn-accion-tabla btn-sm tooltipsC" title="Precio x Kg" onclick="genpdfNV(${data.id},2)">
                <i class="fa fa-fw fa-file-pdf-o"></i>
            </a>`;
            $('td', row).eq(6).html(aux_text);

            aux_kgpend = data.totalkilos - data.totalkgsoldesp;
            aux_dinpend = data.subtotal - data.totalsubtotalsoldesp;
            $('td', row).eq(8).attr('class', "kgpend");
            $('td', row).eq(8).attr('data-order',aux_kgpend);
            $('td', row).eq(8).attr('data-search',aux_kgpend);
            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).html(MASKLA(aux_kgpend,2));

            $('td', row).eq(9).attr('class', "dinpend");
            $('td', row).eq(9).attr('data-order',aux_dinpend);
            $('td', row).eq(9).attr('data-search',aux_dinpend);
            $('td', row).eq(9).attr('style','text-align:right');
            $('td', row).eq(9).html(MASKLA(aux_dinpend,0));

            aux_text = 
            `<a class="btn-accion-tabla btn-sm tooltipsC" title="Vista Previa SD" onclick="pdfSolDespPrev(${data.id},2)">
                <i class='fa fa-fw fa-file-pdf-o'></i>                                    
            </a>`;
            let aux_rutadespsol = $("#aux_ruta_creardespsol").val();
            let nuevaaux_rutadespsol = aux_rutadespsol.replace("/0/", "/"+data.id+"/");
            $('td', row).eq(9).attr('class','action-buttons');
            aux_clienteBloqueado = validarClienteBloqueadoxModulo(data);
            aux_displaybtnac = ``;
            aux_displaybtnbl = ``;
            aux_mensajebloqueo = `Condición financiera en revisión: ${aux_clienteBloqueado}`;
            aux_iconobloqueo = "fa-lock text-danger";
            if(aux_clienteBloqueado == ""){
                aux_displaybtnac = ``;
                aux_displaybtnbl = `style="display:none;"`;
            }else{
                aux_displaybtnac = `style="display:none;"`;
                aux_displaybtnbl = ``;
                if(data.modulo_id_orddesp  !== null){
                    aux_iconobloqueo = "fa-unlock text-yellow";
                    aux_mensajebloqueo = `Habilitado para Despacho`;
                }

            }
            aux_displaybtnac = ``;
            data_icono = data.icono;
            aux_titlehacersoldesp = `Hacer solicitud despacho: ${data.tipentnombre}`;

            aux_text += 
                `<a ${aux_displaybtnac} href="${nuevaaux_rutadespsol}" class="btn-accion-tabla tooltipsC enlace-soldesp botonac${data.id}" title="${aux_titlehacersoldesp}">
                    <button type="button" class="btn btn-default btn-xs">
                        <i class="fa fa-fw ${data_icono}"></i>
                    </button>
                </a>`;

            if(data.ot_aprobstatus == 1){
                data_icono = 'fa fa-fw fa-cog text-aqua';
                aux_titlehacersoldesp += `. OT Nro. ${data.ot_id} aprobada`;
            }

            aux_text += 
            `<a ${aux_displaybtnbl} class="btn-accion-tabla tooltipsC botonbloq${data.id}" title="${aux_mensajebloqueo}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},${data.id},0)">
                <button type="button" class="btn btn-default btn-xs">
                    <i id="iac${data.id}" name="iac${data.id}" class="fa fa-fw ${aux_iconobloqueo}"></i>
                </button>
            </a>`;
            if(data.anulada != null && data.anulada != ""){
                aux_text = "";
                colorFila = 'background-color: #87CEEB;';
                aux_data_toggle = "tooltip";
                aux_title = "Anulada Fecha: " + data.anulada;
            }
            if (data.ot_aprobstatus == 0){
                aux_text += 
                `<a class="btn-accion-tabla tooltipsC" title="OT Nro. ${data.ot_id} esta pendiente x aprobacion">
                    <i class="fa fa-fw fa-cog text-yellow"></i>
                </a>`;
            }

            if(data.ot_aprobstatus == 1){
                aux_text += 
                `<a class="btn-accion-tabla tooltipsC enlace-soldesp botonac${data.id}" title="OT Nro. ${data.ot_id} enviada a aprobacion">
                    <i class="fa fa-fw fa-cog text-yellow"></i>
                </a>`;
            }
            if(data.ot_aprobstatus == 2){
                aux_text += 
                `<a class="btn-accion-tabla tooltipsC enlace-soldesp botonac${data.id}" title="OT Nro. ${data.ot_id} aprobada">
                    <i class="fa fa-fw fa-cog text-aqua"></i>
                </a>`;
            }

            $('td', row).eq(10).html(aux_text);
            // Aplicar estilo "white-space: nowrap" al td específico
            $('td', row).eq(10).css('white-space', 'nowrap'); */
        }
    });

    //totalizar();

    $("#btnconsultar").click(function()
    {
        data = datosopdet();
        $('#tabla-data-consulta').DataTable().ajax.url( "/opdetregprodtemp/listaropdetpage/" + data.data2 ).load();
        //totalizar();
    });

    // Add event listener for opening and closing details
    table.on('click', 'td.dt-control', function (e) {
        let tr = e.target.closest('tr');
        let row = table.row(tr);
    
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>');
        }
        else {
            // Open this row
            row.child(format(row.data())).show();
            $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-bottom text-aqua" title="Mostrar Detalle"></i>');
        }
    });
    
});

// Función para decodificar entidades HTML con jQuery
function decodeHtml(html) {
    return $('<textarea/>').html(html).text();
}

// Formatting function for row details - modify as you need
function format(d) {
    // Descomponer el campo nvdetalle
    //console.log(d);
    //console.log(d.nvdetalle);
    const detalleArray = decodeHtml(d.nvdetalle).split(';').map(detalle => {
        const [producto_id, cant, precio,subtotal,cantsoldesp,producto_nombre,requiere_fabricacion,id,totalkilos] = detalle.split('|');
        return {
            producto_id: parseInt(producto_id),
            cant: parseInt(cant),
            cantsoldesp: parseInt(cantsoldesp),
            precio: parseFloat(precio),
            subtotal: parseInt(subtotal),
            producto_nombre: producto_nombre,
            requiere_fabricacion: parseInt(requiere_fabricacion),
            acuerdotecnico_id: id,
            totalkilos: parseFloat(totalkilos)
        };
    });
    // Generar tabla HTML
    let tableHtml = `<div style="display: flex; align-items: flex-start;"> <!-- Contenedor Flex (flecha al principio) -->
            <div style="margin-left: 20px;">&#8627;</div> <!-- Flecha desplazada un poco a la derecha -->
            <div class="table-responsive">
            <table class="table table-bordered table-striped AllDataTables table-hover table-condensed" style="width: auto; margin-left: 5px;">
                <thead>
                    <tr>
                        <th style="text-align: center;" title="ID Producto">ID Prod</th>
                        <th>Nombre Producto</th>
                        <th style="text-align: center;" title="Cantidad">Cant</th>
                        <th style="text-align: center;" title="Despachado">Desp</th>
                        <th style="text-align: center;">Saldo</th>
                        <th style="text-align: right;">Precio</th>
                        <th style="text-align: right;">Subtotal</th>
                        <th style="text-align: right;" title="Total kilos">Total Kg</th>
                        <th style="text-align: center;" title="Requiere Fabricación">ReqFab</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Recorrer los detalles y agregar filas a la tabla
    detalleArray.forEach(detalle => {
        aux_producto_id = detalle.producto_id;
        if(detalle.acuerdotecnico_id != 0){
            aux_producto_id = 
            `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick='genpdfAcuTec(${detalle.acuerdotecnico_id},${d.cliente_id},"")'>
                ${detalle.producto_id}
            </a>`;    
        }
        tableHtml += `
            <tr>
                <td style="text-align: center;">${aux_producto_id}</td>
                <td>${detalle.producto_nombre}</td>
                <td style="text-align: center;">${detalle.cant}</td>
                <td style="text-align: center;">${detalle.cantsoldesp}</td>
                <td style="text-align: center;">${detalle.cant - detalle.cantsoldesp}</td>
                <td style="text-align: right;">${detalle.precio.toFixed(2)}</td>
                <td style="text-align: right;">${detalle.subtotal.toFixed(2)}</td>
                <td style="text-align: right;">${detalle.totalkilos.toFixed(2)}</td>
                <td style="text-align: center;">${detalle.requiere_fabricacion === 1 ? 'Sí' : 'No'}</td>
            </tr>
        `;
    });

    // Cerrar la tabla
    tableHtml += `
                    </tbody>
                </table>
            </div>
        </div> <!-- Fin del contenedor Flex -->
    `;

    // Devolver la tabla HTML
    return tableHtml;
    
}

function totalizar(){
    let  table = $('#tabla-data-consulta').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datosopdet();
    $.ajax({
        url: '/despachosol/totalizarlistarnvpage/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalgenkg").html(MASKLA(datos.aux_kgpend,2));
            $("#totalgendin").html(MASKLA(datos.aux_dinpend,2));
        }
    });
}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-consulta tr .kgpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totalkg").html(MASKLA(total,2))
	total = 0;
	$("#tabla-data-consulta tr .dinpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totaldinero").html(MASKLA(total,0))
}

function configurarTabla(aux_tabla){
    $(aux_tabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        "order"       : [[ 0, "desc" ]],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}
function configurarTabla2(aux_tabla){
    $(aux_tabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}


function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarcotvend'){
				if (respuesta.mensaje == "ok") {
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
            if(funcion=='vistonotaventa'){
				if (respuesta.mensaje == "ok") {
					//$("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}

		},
		error: function () {
		}
	});
}

function datoslnv(){
    var data = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        plazoentrega      : $("#plazoentrega").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 0,
        sucursal_id       : $("#sucursal_id").val(),
        _token            : $('input[name=_token]').val()
    };
    return data;
}

function datosopdet(){
    aux_titulo ="";
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        notaventa_id      : $("#notaventa_id").val(),
        ot_id             : $("#ot_id").val(),
        op_id             : $("#op_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        sucursal_id       : $("#sucursal_id").val(),
        filtro            : 0,
        modulo_id         : 4,
        statusBloqueo     : $("#statusBloqueo").val(),
        _token            : $('input[name=_token]').val()
    };

    data2 = "?fechad="+data1.fechad+
            "&fechah="+data1.fechah +
            "&rut=" + data1.rut +
            "&op_id=" + data1.op_id +
            "&notaventa_id=" + data1.notaventa_id +
            "&ot_id=" + data1.ot_id +
            "&filtro=" + data1.filtro +
            "&producto_id=" + data1.producto_id +
            "&sucursal_id=" + data1.sucursal_id +
            "&modulo_id=" + data1.modulo_id +
            "&statusBloqueo=" + data1.statusBloqueo +
            "&aux_titulo=" + aux_titulo;
    
    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

function consultar(data){
    $.ajax({
        url: '/despachosol/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                $("#tablaconsulta2").html(datos['tabla2']);
                $("#tablaconsulta3").html(datos['tabla3']);

                configurarTabla('#tabla-data-listar1');
                let  table = $('#tabla-data-listar1').DataTable();
                //console.log(table);
                table
                    .on('draw', function () {
                        eventFired( 'Page' );
                    });
                configurarTabla('.tablascons');
                configurarTabla2('.tablascons2');
            }
        }
    });
}

function consultarpdf(data){
    $.ajax({
        url: '/notaventaconsulta/exportPdf',
        type: 'GET',
        data: data,
        success: function (datos) {
            $("#midiv").html(datos);
            /*
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla();
            }
            */
        }
    });
}

$("#rut").blur(function(){
	codigo = $("#rut").val();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
		if(!dgv(codigo.substr(0, codigo.length-1))){
			swal({
				title: 'Dígito verificador no es Válido.',
				text: "",
				icon: 'error',
				buttons: {
					confirm: "Aceptar"
				},
			}).then((value) => {
				if (value) {
					//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
					$("#rut").focus();
				}
			});
			//$(this).val('');
		}else{
			var data = {
				rut: $("#rut").val(),
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				url: '/cliente/buscarCli',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.length>0){
						formato_rut($("#rut"));
					}else{
                        formato_rut($("#rut"));
                        swal({
                            title: 'Cliente no existe.',
                            text: "Aceptar para crear cliente temporal",
                            icon: 'error',
                            buttons: {
                                confirm: "Aceptar",
                                cancel: "Cancelar"
                            },
                        }).then((value) => {
                            if (value) {
                                limpiarclientemp();
                                
                                $("#myModalClienteTemp").modal('show');
                            }else{
                                $("#rut").focus();
                                //$("#rut").val('');
                            }
                        });		
					}
				}
			});
		}
	}
});

$("#btnbuscarcliente").click(function(event){
    $("#rut").val("");
    $("#myModalBusqueda").modal('show');
});

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
}

function btnpdf(numrep){
    data = datoslnv();
    cadena = "?fechad="+data.fechad+
            "&fechah="+data.fechah +
            "&rut=" + data.rut +
            "&vendedor_id=" + data.vendedor_id +
            "&oc_id=" + data.oc_id +
            "&giro_id=" + data.giro_id + 
            "&areaproduccion_id=" + data.areaproduccion_id +
            "&tipoentrega_id=" + data.tipoentrega_id +
            "&notaventa_id=" + data.notaventa_id +
            "&aprobstatus=" + data.aprobstatus +
            "&comuna_id=" + data.comuna_id +
            "&plazoentrega=" + data.plazoentrega +
            "&producto_id=" + data.producto_id +
            "&filtro=" + data.filtro +
            "&statusBloqueo=" + data.statusBloqueo +
            "&numrep=" + numrep;
    if(numrep==1){
        aux_titulo = 'Nota de Venta Pendientes';
        cadena = cadena +
            "&aux_titulo=" + aux_titulo +
            "&aux_sql=1" + 
            "&aux_orden=1";
    }
    if(numrep==2){
        aux_titulo = 'Pendiente por Cliente y comuna';
        cadena = cadena +
            "&aux_titulo=" + aux_titulo +
            "&aux_sql=1" + 
            "&aux_orden=2";
    }
    if(numrep==3){
        aux_titulo = 'Pendiente por Producto';
        cadena = cadena +
            "&aux_titulo=" + aux_titulo +
            "&aux_sql=2" + 
            "&aux_orden=1";
    }
    if(data.statusBloqueo == "1"){
        aux_titulo = 'Clientes bloqueados';
    }
    $('#contpdf').attr('src', '/despachosol/pdfnotaventapendiente/'+cadena);
    $("#myModalpdf").modal('show');
}

/* $(document).on("click", ".enlace-soldesp", function(event){
    // Detenemos el comportamiento predeterminado del enlace
    event.preventDefault();
    // Aquí puedes obtener el href del enlace actual
    var href = $(this).attr("href");

    // Ejecutamos tu consulta jQuery aquí
    // Por ejemplo, aquí podrías hacer una consulta AJAX
    
    // Simulando una consulta AJAX
    swal({
        title: '¿Desea continuar?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        // Si el usuario hace clic en "Aceptar", redirigimos
        if (value) {
            window.location.href = href;
        }
    });
}); */
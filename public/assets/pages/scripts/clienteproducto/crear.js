$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $( "#rut" ).focus();
    //$("#rut").numeric();
	$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	configTablaProd();
});

function formato_rut(rut)
{
    var sRut1 = rut.value;      //contador de para saber cuando insertar el . o la -
    var nPos = 0; //Guarda el rut invertido con los puntos y el guión agregado
    var sInvertido = ""; //Guarda el resultado final del rut como debe ser
    var sRut = "";
    for(var i = sRut1.length - 1; i >= 0; i-- )
    {
        sInvertido += sRut1.charAt(i);
        if (i == sRut1.length - 1 )
            sInvertido += "-";
        else if (nPos == 3)
        {
            sInvertido += ".";
            nPos = 0;
        }
        nPos++;
    }
    for(var j = sInvertido.length - 1; j>= 0; j-- )
    {
        if (sInvertido.charAt(sInvertido.length - 1) != ".")
            sRut += sInvertido.charAt(j);
        else if (j != sInvertido.length - 1 )
            sRut += sInvertido.charAt(j);
    }
    //Pasamos al campo el valor formateado
    //rut.value = sRut.toUpperCase();
}

function eliminarFormatoRut(rut){
    var rut1 = rut.value;
    var rutR = "";
    for(i=0; i<=rut1.length ; i++){
        if(!isNaN(rut1[i])){
            rutR = rutR + rut1[i]
        }
    }
    //$("#rut").val(rutR);
}

$("#botonNuevaDirec").click(function(event)
{
    event.preventDefault();
    limpiarInputOT();
	quitarverificar();
	$("#aux_sta").val('1')
    $("#myModal").modal('show');
    $("#direcciondetalleM").focus();
});
$("#btnGuardarM").click(function(event)
{
    event.preventDefault();
	if(verificar())
	{
		//alert('Guardar');
		if($("#aux_sta").val()=="1"){
			insertarTabla();
		}else{
			modificarTabla($("#aux_numfila").val());
		}
		
		$("#myModal").modal('hide');
	}else{
		alertify.error("Falta incluir informacion");
	}
});

function modificarTabla(i){
	//alert($("#sucursal_idM").val());
	$("#aux_sta").val('0')
	$("#labeldir"+i).html($("#direcciondetalleM").val());
	$("#direcciondetalle"+i).val($("#direcciondetalleM").val());
	$("#region_id"+i).val($("#region_idM").val());
	$("#provincia_id"+i).val($("#provincia_idM").val());
	$("#comuna_id"+i).val($("#comuna_idM").val());
}

function insertarTabla(producto_id,producto_nombre,acuerdotecnico_id){
	//aux_nfila = 1; 
	var aux_nfila = $("#tabla-data tbody tr").length;
	aux_nfila++;
	//alert(aux_nfila);
	txtacuerdotecnico_id= '';
	if(acuerdotecnico_id){
		txtacuerdotecnico_id = '<a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfCOT(' + acuerdotecnico_id + ',1)" data-original-title="Acuerdo Técnico: ' + acuerdotecnico_id + '">' +
									'<i class="fa fa-fw fa-file-pdf-o"></i>' +
								'<input type="text" name="acuerdotecnico_id[]" id="acuerdotecnico_id'+ aux_nfila + '" class="form-control" value="'+ acuerdotecnico_id +'" style="display:none;"/>'
	}
    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
		'<td>'+ 
			producto_id +
			'<input type="text" name="producto_id[]" id="producto_id'+ aux_nfila + '" class="form-control producto_id" value="' + producto_id + '" style="display:none;" valor="' + producto_id +  '"/>'+
		'</td>'+
		'<td>'+ 
			producto_nombre+
            '<input type="text" name="producto_nombre[]" id="producto_nombre'+ aux_nfila + '" class="form-control" value="'+ producto_nombre +'" style="display:none;"/>'+
        '</td>'+
		'<td>' + 
			txtacuerdotecnico_id +
		'</td>'+
		'<td>' +
			'<a class="btn-accion-tabla eliminar tooltipsC" title="" onclick="eliminarRegistro(' + aux_nfila + ')" data-original-title="Eliminar este registro">' +
                '<i class="fa fa-fw fa-trash text-danger"></i>' +
            '</a>' +
        '</td>'+
    '</tr>';
    $('#tabla-data tbody').append(htmlTags);
	$('#myModalBuscarProd').modal('hide');
}


function eliminarRegistro(i){
	//alert($('input[name=_token]').val());
	var data = {
		direccion_id: $("#direccion_id"+i).val(),
		nfila : i
	};
	var ruta = '/cliente/eliminarClienteDirec/'+i;
	swal({
		title: '¿ Está seguro que desea eliminar el registro ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			$("#fila"+data['nfila']).remove();
			//ajaxRequest(data,ruta,'eliminar');
		}
	});
}
function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='eliminar'){
				if (respuesta.mensaje == "ok") {
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue eliminado correctamente', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso para eliminar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
			if(funcion=='verUsuario'){
				$('#myModal .modal-body').html(respuesta);
				$("#myModal").modal('show');
			}
		},
		error: function () {
		}
	});
}

$("#btnbuscarproducto").click(function(event){
	$(this).val("");
	$(".input-sm").val('');
	data = datos();
	ArrayProdId = [];
	$("#tabla-data tr .producto_id").each(function() {
		valor = $(this).attr("valor");
		ArrayProdId.push(valor);
	});
	//console.log(ArrayProdId.toString());
	txtProdId = ArrayProdId.toString();
	if(txtProdId === ""){
		txtProdId = "0";
	}
	//$("#tabla-data-productos").DataTable().fnDestroy();
	$("#tabla-data-productos > tbody").empty();
	$('#tabla-data-productos').DataTable().ajax.url( "productobuscarpage/" + data.data2 + "&producto_id=" + txtProdId).load();
	//$("#myModal").modal('hide');
	//$("#myModalBuscarProd").modal('show');
	$('#myModalBuscarProd').modal('show');
/*
	$('#myModal')
		.modal('hide')
		.on('hidden.bs.modal', function (e) {
			$('#myModalBuscarProd').modal('show');

			$(this).off('hidden.bs.modal'); // Remove the 'on' event binding
		});
*/
});

function datos(){
    var data1 = {
        cliente_id  : $("#cliente_id").val(),
        sucursal_id : $("#sucursal_id").val(),
        _token      : $('input[name=_token]').val()
    };

    var data2 = "?cliente_id="+data1.cliente_id +
    "&sucursal_id="+data1.sucursal_id

    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

function configTablaProd(){
    aux_nfila = 0;
    data = datos();
    $("#tabla-data-productos").attr('style','');
    $("#tabla-data-productos").dataTable().fnDestroy();
    $('#tabla-data-productos').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "productobuscarpage/" + data.data2 + "&producto_id=",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {data: 'diametro'},
            {data: 'cla_nombre'},
            {data: 'long'},
            {data: 'peso'},
            {data: 'tipounion'},
            {data: 'precioneto'},
            {data: 'precio'},
            {data: 'tipoprod',className:"ocultar"},
			{data: 'acuerdotecnico_id',className:"ocultar"}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_nfila++;
            selecmultprod = true;
            //aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            aux_onclick = "insertarTabla(" + data.id + ",'" + data.nombre + "'," + data.acuerdotecnico_id + ")";

            $(row).attr('name', 'fila' + aux_nfila);
            $(row).attr('id', 'fila' + aux_nfila);
            $(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla copiar_id");
            $(row).attr('data-toggle', data.id);
            $(row).attr('title', "Click para seleccionar producto");
            $(row).attr('onClick', aux_onclick + ';');

            //$(row).attr('id','fila' + data.id);


            if(data.tipoprod == 1){
                aux_text = 
                    data.nombre +
                " <i id='icoat1' class='fa fa-cog text-red girarimagen'></i>";
                $('td', row).eq(1).html(aux_text);
            }

            $('td', row).eq(5).attr('data-order',data.peso);
            $('td', row).eq(5).attr('data-search',data.peso);
            $('td', row).eq(5).html(MASKLA(data.peso,3));

            $('td', row).eq(7).attr('data-order',data.precioneto);
            $('td', row).eq(7).attr('data-search',data.precioneto);
            $('td', row).eq(7).attr('style','text-align:right');
            $('td', row).eq(7).html(MASKLA(data.precioneto,0));

            $('td', row).eq(8).attr('data-order',data.precio);
            $('td', row).eq(8).attr('data-search',data.precio);
            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).html(MASKLA(data.precio,0));

            /*
            $('td', row).eq(8).attr('data-order',data.precioneto);
            $('td', row).eq(8).attr('data-search',data.precioneto);
            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).html(MASKLA(data.precioneto,2));
            */
            $("#totalreg").val(aux_nfila);

        },
        initComplete: function () {
            // Apply the search
            this.api()
                .columns()
                .every(function () {
                    var that = this;
 
                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
        },
    });
}
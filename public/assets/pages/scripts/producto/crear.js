$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $( "#glosa" ).focus();
    /*
    $("#diamextmm").blur(function(){
        //$("#diamextpg").val($(this).val()*0.039370);
        $("#diamextpg").val(mmAPg($(this).val()));
    });
    */
    
    $(".numerico").numeric();

    aux_nfilas=parseInt($("#dataTables >tbody >tr").length);
    //alert(aux_nfilas);
    if($("#aux_sta").val() == 2){
        //agregarFila(aux_nfilas);
    }

    // Ejecutar al cargar la página
    validarGlosa();

    // Ejecutar al cambiar el select
    $('#glosaaut').on('change', function () {
        validarGlosa();
    });

    // Ejecutar al cargar la página
    validarCosto();
    // Ejecutar al cambiar el select
    $('#costobloqueado').on('change', function () {
        validarCosto();
    });


    $('#btn-agregar-detalle').click(function() {
        var index = $('#tabla-detalles tbody tr').length;
        var newRow = `<tr>
            <td>
                <input type="hidden" name="detalles[${index}][id]" value="">
                <div class="input-group">
                    <input
                        type="text"
                        name="detalles[${index}][productocompiddet]"
                        id="producto_id${index}"
                        item="${index}"
                        class="form-control numerico"
                        required
                        onblur="onBlurProducto_id(this)"
                        value=""
                        maxlength="4"
                        style="text-align:right;"
                        valor=""
                    >
                    <span class="input-group-btn">
                        <button
                            type="button"
                            class="btn btn-sm btn-primary btn-buscar-producto"
                            title="Buscar Producto"
                            item="${index}"
                            data-toggle="modal"
                            data-target="#buscarProductoBDModal"
                            onclick="buscarproductoGenNew(this,event)"
                            nomCampProducto="producto_id${index}"
                            >
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                
            </td>
            <td>
                <input type="text" name="detalles[${index}][cantdet]" id="cantdet${index}" class="form-control numerico" required style="text-align:right">
            </td>
            <td>
                <input type="text" name="detalles[${index}][glosadet]" id="glosadet${index}" class="form-control" required readonly>
            </td>
            <td>
                <input type="text" name="detalles[${index}][obsdet]" id="obsdet${index}" class="form-control" required>
            </td>
            <td>
                <input type="text" name="detalles[${index}][precionetodet]" id="precionetodet${index}" class="form-control" required readonly style="text-align:right">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btn-eliminar-detalle"><i class="fa fa-trash"></i></button>
            </td>
        </tr>`;
        
        $('#tabla-detalles tbody').append(newRow);
        $(".numerico").numeric();

    });

    // Eliminar detalle con confirmación
    $(document).on('click', '.btn-eliminar-detalle', function() {
        var fila = $(this).closest('tr');
        swal({
            title: '¿Está seguro?',
            text: 'El detalle será eliminado y no podrá recuperarlo.',
            icon: 'warning',
            buttons: {
                cancel: 'Cancelar',
                confirm: { text: 'Aceptar', closeModal: false }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                // remueve la fila y reindexa
                fila.remove();
                $('#tabla-detalles tbody tr').each(function(index) {
                    $(this).find('input').each(function() {
                        var name = $(this).attr('name');
                        name = name.replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', name);
                    });
                });
                swal('El detalle se ha eliminado.', { icon: 'success' });
            }
        });
    });

    
});

function validarGlosa() {
    var glosaaut = $('#glosaaut').val();

    if (glosaaut === '1') {
        $('#glosa').prop('readonly', true);
    } else {
        $('#glosa').prop('readonly', false);
    }
}

function validarCosto() {
    var costobloqueado = $('#costobloqueado').val();

    if (costobloqueado === '1') {
        $('#costototal').prop('readonly', true);
    } else {
        $('#costototal').prop('readonly', false);
    }
}

$('.categoriaprod_id').on('change', function () {
    $(".claseprod_id").empty();
    $(".claseprod_id").append("<option value=''>Seleccione...</option>");
    //alert($(this).val());
    var data = {
        categoriaprod_id: $(this).val(),
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/producto/obtClaseProd',
        type: 'POST',
        data: data,
        success: function (claseprod) {
            calcular_precio();
            for (i = 0; i < claseprod.length; i++) {
                $(".claseprod_id").append("<option value='" + claseprod[i].id + "'>" + claseprod[i].cla_nombre + "</option>");
            }
            /*
            $.each(claseprod, function(index,value){
                $(".claseprod_id").append("<option value='" + index + "'>" + value + "</option>")
            });
            */
        }
    });
    $(".grupoprod_id").empty();
    $(".grupoprod_id").append("<option value=''>Seleccione...</option>");
    $.ajax({
        url: '/producto/obtGrupoProd',
        type: 'POST',
        data: data,
        success: function (grupoprod) {
            calcular_precio();
            for (i = 0; i < grupoprod.length; i++) {
                $(".grupoprod_id").append("<option value='" + grupoprod[i].id + "'>" + grupoprod[i].gru_nombre + "</option>");
            }
        }
    });
    if($("#aux_sta").val() == 1){
        $("#dataTables > tbody").empty();
    }else{
        $("#dataTables").find("tr").last().remove();
    }
});
$("#peso").blur(function(){
    calcular_precio();
});

function calcular_precio(){
    aux_precio = $(".categoriaprod_id option:selected").attr('precio');
    aux_precioneto = aux_precio * $("#peso").val();
    $("#precioneto").val(Math.round(aux_precioneto));
}

$('#annomes').on('change', function () {
    var data = {
        annomes: annomes($('#annomes').val()),
        categoriaprod_id: $("#categoriaprod_idH").val(),
        _token: $('input[name=_token]').val()
    };
    $("#categoriaprod_id").empty();
    $("#categoriaprod_id").append("<option value=''>Seleccione...</option>");
    $.ajax({
        url: '/categoriagrupovalmesfilcat',
        type: 'POST',
        data: data,
        success: function (respuesta) {
            for (i = 0; i < respuesta.length; i++) {
                //alert(i);
                $("#categoriaprod_id").append($("<option>", {
                    value: respuesta[i].id,
                    text: respuesta[i].nombre
                  }));
            }
        }
    });
});


function myFunction(i){
    $("#invbodega_id" + i).val($("#invbodega_idtmp" + i + " option:selected").attr('value'));
}

function onBlurProducto_id(producto_id){
	objvlrcodigo = $("#" + producto_id["id"]);
	llenarDatosProdL(objvlrcodigo);
	//console.log(vlrcodigo["id"]);
}


//FUNCTION CON ASYNC, YA QUE ME INTERESA ESPERAR LA RESPUESTA DE LA BUSQUEDA
async function llenarDatosProdL(producto_id){
	let item = producto_id.attr("item");
	if($("#producto_id" + item).val() != $("#producto_id" + item).attr("valor")){
		arrayDP = await buscarDatosProdPesaje(producto_id);
		$("#producto_id" + item).val("");
		if(arrayDP['cont'] > 0){
			$("#producto_id" + item).val(arrayDP["id"]);
			$("#producto_id" + item).attr("valor",arrayDP["id"])
			let aux_producto_nombre = `${arrayDP["nombre"]}`;
			$("#glosadet" + item).val(aux_producto_nombre);
			$("#glosadet" + item).attr("title",aux_producto_nombre);
			$("#precionetodet" + item).val(MASKLA(arrayDP["precioneto"],2));
		}
	}
}

async function buscarDatosProdPesaje(producto_id){
	codigo = producto_id.val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		aux_cliente_id = null;
		if($("#cliente_id").val()){
			aux_cliente_id = $("#cliente_id").val();
		}
		var data = {
			id: codigo,
			cliente_id : aux_cliente_id,
			_token: $('input[name=_token]').val()
		};
		return resul = await $.ajax({
			url: '/producto/buscarUnProducto',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				//console.log(respuesta);
				if(respuesta['cont']>0){
					if(respuesta['estado'] == 0){
						swal({
							title: 'Producto inactivo.',
							text: "Producto existe pero está Inactivo.",
							icon: 'error',
							buttons: {
								confirm: "Aceptar"
							},
						}).then((value) => {
							if (value) {
								$("#producto_idM").focus();
							}
						});
					}
				}else{
					producto_id.val("");
					swal({
						title: `Código producto ${codigo} no existe.`,
						text: "Presione F2 para buscar",
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							producto_id.focus();
						}
					});
				}
				return respuesta;
			}
		});
		//console.log(resul);
	}else{
		return [];
	}
}

function buscarProd(obj,event){
	//console.log(obj)
    //cargardatospantprod();
    $("#itemAct").val($(obj).parent().parent().attr("item")); //Crear input Item actual
    $("#myModalBuscarProd").modal('show');
}

function copiar_codprod(id,codintprod){
	//$("#myModalBuscarProd").modal('hide');
	//$("#myModal").modal('show');
	$('#myModalBuscarProd')
			.modal('hide')
			.on('hidden.bs.modal', function (e) {
				$('#myModal').modal('show');

				$(this).off('hidden.bs.modal'); // Remove the 'on' event binding
			});
	$("#" + aux_nomCamProductoNew).val(id);
	$("#" + aux_nomCamProductoNew).blur();
	//$("#cantM").focus();
}	$("#cantM").focus();


function onBlurInsumo_id(insumo_id){
    //console.log(insumo_id["id"]);
	objvlrcodigo = $("#" + insumo_id["id"]);
	llenarDatosinsumoL(objvlrcodigo);
	//console.log(vlrcodigo["id"]);
}


//FUNCTION CON ASYNC, YA QUE ME INTERESA ESPERAR LA RESPUESTA DE LA BUSQUEDA
async function llenarDatosinsumoL(insumo_id){
	let item = insumo_id.attr("item");
	if($("#insumo_id" + item).val() != $("#insumo_id" + item).attr("valor")){
		arrayDP = await buscarDatosInsumo(insumo_id);
        //console.log(arrayDP);
		$("#insumo_id" + item).val("");
		if(arrayDP.cont > 0){
			$("#insumo_id" + item).val(arrayDP.id);
			$("#insumo_id" + item).attr("valor",arrayDP.id)
			let aux_insumo_nombre = `${arrayDP.nombre}`;
			$("#insumonombredet" + item).val(aux_insumo_nombre);
			$("#insumonombredet" + item).attr("title",aux_insumo_nombre);
			$("#insumocostounitariodet" + item).val(MASKLA(arrayDP.costounitario,2));
		}
	}
}

async function buscarDatosInsumo(insumo_id){
	codigo = insumo_id.val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		aux_cliente_id = null;
		if($("#cliente_id").val()){
			aux_cliente_id = $("#cliente_id").val();
		}
		var data = {
			id: codigo,
			cliente_id : aux_cliente_id,
			_token: $('input[name=_token]').val()
		};
		return resul = await $.ajax({
			url: '/insumo/buscarUnInsumo',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				//console.log('Respuesta PHP:', respuesta);
				
				// Validar que respuesta sea un objeto válido
				if (!respuesta || typeof respuesta !== 'object') {
					swal({
						title: 'Error de validación',
						text: "Respuesta inválida del servidor",
						icon: 'error',
						buttons: { confirm: "Aceptar" }
					});
					return respuesta;
				}
				
				// Validar que cont esté definido
				if (!('cont' in respuesta)) {
					//console.error('Campo cont no encontrado en respuesta');
					insumo_id.val("");
					swal({
						title: 'Error de respuesta',
						text: "La respuesta del servidor no contiene los datos esperados",
						icon: 'error',
						buttons: { confirm: "Aceptar" }
					}).then(() => insumo_id.focus());
					return respuesta;
				}
				
				if(respuesta['cont'] > 0){
					// Insumo encontrado - validar estado
					if(respuesta['estado'] == 0){
						swal({
							title: 'Insumo inactivo.',
							text: "Insumo existe pero está Inactivo.",
							icon: 'warning',
							buttons: { confirm: "Aceptar" }
						}).then((value) => {
							if (value) {
								$("#insumo_idM").focus();
							}
						});
					} else {
						// Insumo encontrado y activo
						//console.log('Insumo válido:', respuesta);
					}
				}else{
					// Insumo no encontrado
					insumo_id.val("");
					swal({
						title: `Código insumo ${codigo} no existe.`,
						text: "Presione F2 para buscar",
						icon: 'error',
						buttons: { confirm: "Aceptar" }
					}).then((value) => {
						if (value) {
                            insumo_id.val("");
                            insumo_id.attr("valor", "");
							insumo_id.focus();
						}
					});
				}
				return respuesta;
			},
			error: function (xhr, status, error) {
				console.error('Error en AJAX:', status, error);
				insumo_id.val("");
				var mensaje = 'Error al buscar insumo';
				
				if (xhr.responseJSON && xhr.responseJSON.mensaje) {
					mensaje = xhr.responseJSON.mensaje;
				}
				
				swal({
					title: 'Error en la búsqueda',
					text: mensaje,
					icon: 'error',
					buttons: { confirm: "Aceptar" }
				}).then(() => insumo_id.focus());
			}
		});
		//console.log(resul);
	}else{
		return [];
	}
}

function buscarInsumo(obj,event){
	//console.log(obj)
    //cargardatospantprod();
    $("#itemAct").val($(obj).parent().parent().attr("item")); //Crear input Item actual
    $("#myModalBuscarInsumo").modal('show');
}

function copiar_codinsumo(id,codintprod,index){
    //console.log("copiar_codinsumo", id, codintprod, index);
	//$("#myModalBuscarInsumo").modal('hide');
	//$("#myModal").modal('show');
	$('#buscarInsumoBDModal')
			.modal('hide')
			.on('hidden.bs.modal', function (e) {
				$('#myModal').modal('show');

				$(this).off('hidden.bs.modal'); // Remove the 'on' event binding
			});
	$("#" + aux_nomCamInsumoNew).val(id);
	$("#" + aux_nomCamInsumoNew).blur();
	//$("#cantM").focus();
}	$("#cantM").focus();

$('#btn-agregar-detalle-insumo').click(function() {
    var index = $('#tabla-detalles-insumos tbody tr').length;
    var newRow = `<tr>
        <td>
            <input type="hidden" name="detalleinsumos[${index}][id]" value="">
            <div class="input-group">
                <input
                    type="text"
                    name="detalleinsumos[${index}][insumo_iddet]"
                    id="insumo_id${index}"
                    item="${index}"
                    class="form-control numericoblanco"
                    required
                    onblur="onBlurInsumo_id(this)"
                    value=""
                    maxlength="4"
                    style="text-align:right;"
                    valor=""
                >
                <span class="input-group-btn">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary btn-buscar-insumo"
                        title="Buscar Insumo"
                        item="${index}"
                        data-toggle="modal"
                        data-target="#buscarInsumoBDModal"
                        onclick="buscarInsumoGenNew(this,event)"
                        nomcampinsumo="insumo_id${index}"
                        >
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
            
        </td>
        <td>
            <input type="text" name="detalleinsumos[${index}][insumocantdet]" id="insumocantdet${index}" class="form-control numerico" required style="text-align:right">
        </td>
        <td>
            <input type="text" name="detalleinsumos[${index}][insumonombredet]" id="insumonombredet${index}" class="form-control" required readonly>
        </td>
        <td>
            <input type="text" name="detalleinsumos[${index}][insumounidadesproductodet]" id="insumounidadesproductodet${index}" class="form-control numerico" required style="text-align:right">
        </td>
        <td>
            <input type="text" name="detalleinsumos[${index}][insumoobsdet]" id="insumoobsdet${index}" class="form-control" required>
        </td>
        <td>
            <input type="text" name="detalleinsumos[${index}][insumocostounitariodet]" id="insumocostounitariodet${index}" class="form-control" required readonly style="text-align:right">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm btn-eliminar-detalle"><i class="fa fa-trash"></i></button>
        </td>
    </tr>`;
    
    $('#tabla-detalles-insumos tbody').append(newRow);
    $(".numerico").numeric();

});
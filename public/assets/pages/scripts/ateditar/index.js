$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('.date-picker').datepicker({
        language: "es",
        format: "MM yyyy",
        viewMode: "years", 
        minViewMode: "months",
        autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");

    //configurarTabla('#tabla-data-producto');
    nombreTabla = "#tabla-data-producto";
    $(nombreTabla).append(encabezadoTabla());
    configurarTabla(nombreTabla,"",false)



    function configurarTabla1(aux_tabla){
        data = datosproducto();
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "ateditarpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            "order": [[ 1, "asc" ]],
            'columns'     : [
                {data: 'producto_id'},
                {data: 'producto_id'},
                {data: 'producto_nombre'},
                {data: 'categoria_nombre'},
                {data: 'cla_nombre'},
                {data: 'diametro'},
                {data: 'long'},
                {data: 'espesor'},
                {data: 'peso'},
                {data: 'tipounion'},
                {data: 'precioneto'},
            ],
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                /*
                if(data.stock <= 0){
                    $(row).hide();                    
                }*/
                $('td', row).eq(1).attr('style','text-align:center');
                if(data.acuerdotecnico_id != null){
                    $('td', row).eq(1).attr('style','text-align:left');
                    //$('td', row).eq(1).html(aux_text);
                    if (data.hasOwnProperty('cliente_id')) {
                        aux_cliente_id = data.cliente_id;
                    } else {
                        aux_cliente_id = 0;
                    }
                    aux_text = 
                    `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm" title="Acuerdo Técnico" onclick='genpdfAcuTec(${data.acuerdotecnico_id},${aux_cliente_id},"")'>
                        ${data.producto_id}
                    </a>`;
                    if(data.at_firmado == null){
                        aux_text += 
                            `<a class="btn-accion-tabla btn-sm" title="Falta AT Firmado">
                                <i class="fa fa-fw fa-question"></i>
                            </a>`;
                    }else{
                        aux_text += 
                            `<a class="btn-accion-tabla btn-sm" title="Ver At Firmado" onclick='verpdf2(\"atfirm/${data.at_firmado}\",2,"","ver-acuerdo-tecnico-firmado")'>
                                <i class="fa fa-fw fa-files-o"></i>
                            </a>`;
                    }
                    aux_text += `<input type="text" name="acuerdotecnico${data.producto_id}" id="acuerdotecnico${data.producto_id}" value="${data.acuerdotecnico_json}" style="display:none;"/>`;
                    aux_text += `<input type="text" name="nombreProdTD${data.producto_id}" id="nombreProdTD${data.producto_id}" categoriaprod_nombre="${data.categoriaprod_nombre}" style="display:none;"/>`;
                    aux_text += `<input type="text" name="producto_idTDT${data.producto_id}" id="producto_idTDT${data.producto_id}" categoriaprod_id="${data.categoriaprod_id}" style="display:none;"/>`;

                    if(data.at_impresofoto != "" && data.at_impresofoto != null){
                        aux_text += 
                            `<a class="btn-accion-tabla btn-sm" title="Ver Imagen arte Impreso" onclick='verpdf2(\"at/${data.at_impresofoto}\",2,"","ver-arte-acuerdo-tecnico")'>
                                <i class="fa fa-fw fa-photo"></i>
                            </a>`;
                    }
                    $('td', row).eq(1).html(aux_text);
                    //$('td', row).eq(1).attr('onClick', 'genpdfAcuTec(' + data.acuerdotecnico_id + ',' + aux_cliente_id +',"");');
                }
                $('td', row).eq(5).attr('style','text-align:center');
                $('td', row).eq(6).attr('style','text-align:center');
                $('td', row).eq(7).attr('data-order',data.espesor);
                $('td', row).eq(7).attr('data-search',data.espesor);
                $('td', row).eq(7).html(MASKLA(data.espesor,3));
                $('td', row).eq(7).attr('style','text-align:center');
                $('td', row).eq(8).attr('data-order',data.peso);
                $('td', row).eq(8).attr('data-search',data.peso);
                $('td', row).eq(8).html(MASKLA(data.peso,3));
                $('td', row).eq(8).attr('style','text-align:right');
                //$('td', row).eq(9).attr('style','text-align:right');
                /* aux_text = 
                        `<div class="tools11">
                                <a class="btn-accion-tabla tooltipsC action-buttons" title="Editar AT" onclick="crearEditarAcuTec(${data.producto_id})">
                                    <i class="fa fa-fw fa-pencil accioness fa-lg"></i>
                                </a>
                        </div>`; */
                aux_text = `
                    <a href='ateditar' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>
                        <i class='fa fa-fw fa-pencil'></i>
                    </a>`;
                //$('td', row).eq(9).attr('style','padding-top: 0px;padding-bottom: 0px;');
                $('td', row).eq(10).html(aux_text);
            }
        });
    }



    arrayBodegas = [];
    $("#invbodega_id option").each(function(){
        //console.log(this);
        //console.log('Opcion: '+$(this).text()+' Valor: '+ $(this).attr('value')+' Sucursal: '+ $(this).attr('sucursal_id'));
        var objeto =   {
            id: $(this).attr('value'),
            nombre: $(this).text(),
            sucursal_id: $(this).attr('sucursal_id')
        };
        arrayBodegas.push(objeto);
    });
    //Lo agregas al array.
    $("#invbodega_id").empty();
    $(".selectpicker").selectpicker('refresh');
    if($("#sucursal_id").val() > 0){
		llenarbodegas($("#sucursal_id").val())
	}

});

$("#btnconsultar").click(function()
{
    /* data = datosproducto();
    $('#tabla-data-producto').DataTable().ajax.url( "reportproductopage/" + data.data2 ).load(); */

    var data = datosproducto();
    var newUrl = "ateditarpage/" + data.data2;
    
    
    configurarTabla("#tabla-data-producto",newUrl,true); // Reinicia DataTable con `serverSide: true`

});

function encabezadoTabla(){
    let html = `
            <thead>
                <tr>
                    <th class="width70 tooltipsC" title="Codigo Producto" style='text-align:center'>Cod</th>
                    <th>Producto</th>
                    <th></th>
                    <th>Categoria</th>
                    <th>Clase<br>Sello</th>
                    <th>Diametro<br>Ancho</th>
                    <th>Largo</th>
                    <th>Espesor</th>
                    <th>Peso</th>
                    <th class="tooltipsC" title="Tipo de Union">TU</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
        `;
    return html;
}

function configurarTabla(nombreTabla,url,serverSide) {
    // Si ya hay una tabla inicializada, la destruimos
    if ($.fn.DataTable.isDataTable(nombreTabla)) {
        $(nombreTabla).DataTable().destroy();
        $(nombreTabla).empty(); // Limpia la tabla para evitar errores de redibujado
    }

    //Asegura que la tabla tiene el encabezado correcto
    if ($(nombreTabla + " thead").length === 0) {
        $(nombreTabla).append(encabezadoTabla());
    }

    // Configura DataTable con `serverSide` dinámico
    data = datosproducto();
    $(nombreTabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : serverSide,
        'ajax'        : url, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
        "order": [[ 1, "asc" ]],
        'columns'     : [
            {data: 'producto_id'},
            {data: 'producto_id'},
            {data: 'producto_nombre'},
            {data: 'categoria_nombre'},
            {data: 'cla_nombre'},
            {data: 'diametro'},
            {data: 'long'},
            {data: 'espesor'},
            {data: 'peso'},
            {data: 'tipounion'},
            {data: 'precioneto'},
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            /*
            if(data.stock <= 0){
                $(row).hide();                    
            }*/
            $('td', row).eq(1).attr('style','text-align:center');
            if(data.acuerdotecnico_id != null){
                $('td', row).eq(1).attr('style','text-align:left');
                //$('td', row).eq(1).html(aux_text);
                if (data.hasOwnProperty('cliente_id')) {
                    aux_cliente_id = data.cliente_id;
                } else {
                    aux_cliente_id = 0;
                }
                aux_text = 
                `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm" title="Acuerdo Técnico" onclick='genpdfAcuTec(${data.acuerdotecnico_id},${aux_cliente_id},"")'>
                    ${data.producto_id}
                </a>`;
                if(data.at_firmado == null){
                    aux_text += 
                        `<a class="btn-accion-tabla btn-sm" title="Falta AT Firmado">
                            <i class="fa fa-fw fa-question"></i>
                        </a>`;
                }else{
                    aux_text += 
                        `<a class="btn-accion-tabla btn-sm" title="Ver At Firmado" onclick='verpdf2(\"atfirm/${data.at_firmado}\",2,"","ver-acuerdo-tecnico-firmado")'>
                            <i class="fa fa-fw fa-files-o"></i>
                        </a>`;
                }
                aux_text += `<input type="text" name="acuerdotecnico${data.producto_id}" id="acuerdotecnico${data.producto_id}" value="${data.acuerdotecnico_json}" style="display:none;"/>`;
                aux_text += `<input type="text" name="nombreProdTD${data.producto_id}" id="nombreProdTD${data.producto_id}" categoriaprod_nombre="${data.categoriaprod_nombre}" style="display:none;"/>`;
                aux_text += `<input type="text" name="producto_idTDT${data.producto_id}" id="producto_idTDT${data.producto_id}" categoriaprod_id="${data.categoriaprod_id}" style="display:none;"/>`;

                if(data.at_impresofoto != "" && data.at_impresofoto != null){
                    aux_text += 
                        `<a class="btn-accion-tabla btn-sm" title="Ver Imagen arte Impreso" onclick='verpdf2(\"at/${data.at_impresofoto}\",2,"","ver-arte-acuerdo-tecnico")'>
                            <i class="fa fa-fw fa-photo"></i>
                        </a>`;
                }
                $('td', row).eq(1).html(aux_text);
                //$('td', row).eq(1).attr('onClick', 'genpdfAcuTec(' + data.acuerdotecnico_id + ',' + aux_cliente_id +',"");');
            }
            $('td', row).eq(5).attr('style','text-align:center');
            $('td', row).eq(6).attr('style','text-align:center');
            $('td', row).eq(7).attr('data-order',data.espesor);
            $('td', row).eq(7).attr('data-search',data.espesor);
            $('td', row).eq(7).html(MASKLA(data.espesor,3));
            $('td', row).eq(7).attr('style','text-align:center');
            $('td', row).eq(8).attr('data-order',data.peso);
            $('td', row).eq(8).attr('data-search',data.peso);
            $('td', row).eq(8).html(MASKLA(data.peso,3));
            $('td', row).eq(8).attr('style','text-align:right');
            //$('td', row).eq(9).attr('style','text-align:right');
            /* aux_text = 
                    `<div class="tools11">
                            <a class="btn-accion-tabla tooltipsC action-buttons" title="Editar AT" onclick="crearEditarAcuTec(${data.producto_id})">
                                <i class="fa fa-fw fa-pencil accioness fa-lg"></i>
                            </a>
                    </div>`; */
            aux_text = `
                <a href='ateditar' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>
                    <i class='fa fa-fw fa-pencil'></i>
                </a>`;
            //$('td', row).eq(9).attr('style','padding-top: 0px;padding-bottom: 0px;');
            $('td', row).eq(10).html(aux_text);
        }
    });
}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-producto tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}

function datosproducto(){
    var data1 = {
        mesanno           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        invbodega_id      : $("#invbodega_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        categoriaprod_id  : $("#categoriaprod_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        at_impreso        : $("#at_impreso").val(),
        at_firmadosta     : $("#at_firmadosta").val(),
        at_impresostaeli  : $("#at_impresostaeli").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?mesanno="+data1.mesanno +
    "&sucursal_id="+data1.sucursal_id +
    "&invbodega_id="+data1.invbodega_id +
    "&producto_id="+data1.producto_id +
    "&categoriaprod_id="+data1.categoriaprod_id +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&rut="+data1.rut +
    "&at_firmadosta="+data1.at_firmadosta +
    "&at_impresostaeli="+data1.at_impresostaeli



    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

$("#btnbuscarproducto").click(function(event){
    $(this).val("");
    $(".input-sm").val('');
    data = datosproducto();
    $('#tabla-data-productos').DataTable().ajax.url( "producto/productobuscarpage/" + data.data2 + "&producto_id=" ).load();
    aux_id = $("#producto_idPxP").val();
    if( aux_id == null || aux_id.length == 0 || /^\s+$/.test(aux_id) ){
        $("#divprodselec").hide();
        $("#productos").html("");
    }else{
        arraynew = aux_id.split(',')
        $("#productos").html("");
        for(var i = 0; i < arraynew.length; i++){
            $("#productos").append("<option value='" + arraynew[i] + "' selected>" + arraynew[i] + "</option>")
        }
        $("#divprodselec").show();
    }
    $('#myModalBuscarProd').modal('show');
});


function copiar_codprod(id,codintprod){
    $("#myModalBuscarProd").modal('hide');
    aux_id = $("#producto_idPxP").val();
    if( aux_id == null || aux_id.length == 0 || /^\s+$/.test(aux_id) ){
        $("#producto_idPxP").val(id);
    }else{
        $("#producto_idPxP").val(aux_id + "," + id);
    }
	//$("#producto_idM").blur();
	$("#producto_idPxP").focus();
}

$("#btnpdf").click(function(event){
    data = datosproducto();
    //alert(cadena);
    $('#contpdf').attr('src', '/reportproducto/exportPdf/'+data.data2);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
});

$("#sucursal_id").change(function(){
	id = $(this).val();
	llenarbodegas(id)
});

function llenarbodegas(sucursal_id){
    $("#invbodega_id").empty();
    for (let i = 0; i < arrayBodegas.length; i++) {
        if(sucursal_id == arrayBodegas[i].sucursal_id){
            $("#invbodega_id").append(`<option value="${arrayBodegas[i].id}" sucursal_id="${arrayBodegas[i].sucursal_id}">${arrayBodegas[i].nombre}</option>`)
        }
    }
    $(".selectpicker").selectpicker('refresh');
}

function exportarExcel() {
    var tabla = $('#tabla-data-producto').DataTable();
    data = datosproducto();
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
      url: 'reportproductopage/' + data.data2, // ajusta la URL de la solicitud al endpoint correcto
      type: 'POST',
      dataType: 'json',
      success: function(data) {
        // Crear una matriz para los datos de Excel
        var datosExcel = [];
        
        // Agregar encabezados de columna al arreglo
        var encabezados = tabla.columns().header().toArray();
        var encabezadosExcel = encabezados.map(function(encabezado) {
          return encabezado.innerHTML;
        });
        datosExcel.push(encabezadosExcel);
        
        // Agregar los datos de la tabla al arreglo
        data.data.forEach(function(registro) {
          var filaExcel = [
            registro.producto_id,
            registro.producto_nombre,
            registro.categoria_nombre,
            registro.diametro,
            registro.cla_nombre,
            registro.long,
            registro.espesor,
            registro.peso,
            registro.tipounion,
            registro.precioneto
          ];
          datosExcel.push(filaExcel);
        });
        
        // Crear el libro de Excel
        var libro = XLSX.utils.book_new();
        var hoja = XLSX.utils.aoa_to_sheet(datosExcel);
        XLSX.utils.book_append_sheet(libro, hoja, 'Datos');
        
        // Generar el archivo Excel y descargarlo
        XLSX.writeFile(libro, 'productos.xlsx');
      },
      error: function(xhr, status, error) {
        console.log(error);
      }
    });
  }

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

function embalajePlastiservi(){
	let aux_val = $("#at_embalajeplastservi").val();
	if(aux_val == "1" || aux_val == ""){
		$(".embalaje").prop("disabled", true);
		$(".embalaje").val("")
	}else{
		$(".embalaje").prop("disabled", false);
	}
}
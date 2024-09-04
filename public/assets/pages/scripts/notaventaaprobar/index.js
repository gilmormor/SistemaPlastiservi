$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('#tabla-data').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "notaventaaprobarpage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'cotizacion_id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'vendedor_nombre'},
            {data: 'contador',className:"ocultar"},
            {data: 'aprobstatus',className:"ocultar"},
            {data: 'aprobobs',className:"ocultar"},
            {data: 'oc_file',className:"ocultar"},
            {data: 'oc_id'},
            {data: 'pdfnv'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
				"<a href='notaventaaprobar' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
					"<i class='fa fa-fw fa-pencil'></i>"+
				"</a>"}
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            colorFila = "";
            aprobstatus = 1;
            aux_data_toggle = "";
            aux_title = "";
            if(data.contador>0){
                colorFila = 'background-color: #87CEEB;';
                aprobstatus = 2;
                aux_data_toggle = "tooltip";
                aux_title = "Precio menor al valor en tabla";
            }
            if(data.aprobstatus==4){
                colorFila = 'background-color: #FFC6C6;';  //" style=background-color: #FFC6C6;  title=Rechazo por: $data->aprobobs data-toggle=tooltip"; //'background-color: #FFC6C6;'; 
                aux_data_toggle = "tooltip";
                aux_title = "Rechazado por: " . data.aprobobs;
            }
            codigo = data.cotizacion_id;
            if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
                aux_text = "";
            }else{
                aux_text = 
                "<a href='#'  class='tooltipsC' title='Cotizacion' " +
				"onclick='genpdfCOT(\"" + data.cotizacion_id + "\",1)'>" + data.cotizacion_id + 
				"</a>";
            }
            $('td', row).eq(1).html(aux_text);

            codigo = data.oc_file;
            if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
                aux_text = "";
            }else{
                aux_text = 
                "<a href='#' class='tooltipsC' title='Orden de Compra' " +
				"onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + 
				"</a>";
            }
            $('td', row).eq(9).html(aux_text);

			aux_text = 
				"<a class='btn-accion-tabla btn-sm btngenpdfNV1 tooltipsC' title='Nota de venta: " + data.id + "'>" +
					"<i class='fa fa-fw fa-file-pdf-o'></i>" +
				"</a>"+
				"<a class='btn-accion-tabla btn-sm btngenpdfNV2 tooltipsC' title='Precio x Kg: " + data.id + "'>" +
					"<i class='fa fa-fw fa-file-pdf-o'></i>" +
				"</a>";
			$('td', row).eq(10).html(aux_text);

            if ( data.contador * 1 > 0 ) {
                //console.log(row);
                ///$('tr').addClass('preciomenor');
                //$('td', row).parent().addClass('preciomenor tooltipsC');
                $('td', row).eq(0).html(
                    "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                    $('td', row).eq(0).html()+
                    "</a>"
                );
                $('td', row).eq(1).html(
                    "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                    $('td', row).eq(1).html()+
                    "</a>"
                );
                $('td', row).eq(2).html(
                    "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                    $('td', row).eq(2).html()+
                    "</a>"
                );
                /*
                $('td', row).eq(4).html(
                    "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                    $('td', row).eq(3).html()+
                    "</a>"
                );*/
                //$('td', row).parent().prop("title","Precio menor al valor en tabla")
            }    
        }
    });


});


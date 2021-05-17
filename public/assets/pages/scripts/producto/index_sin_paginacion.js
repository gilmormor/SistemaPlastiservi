$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
/*
    function generateBarcode(codbar,i){
        var value = codbar; //"7626513231424"; //$("#barcodeValue").val();
        var btype = "ean13" //$("input[name=btype]:checked").val();
        var renderer = "css"; //$("input[name=renderer]:checked").val();

        var settings = {
            output:renderer,
            bgColor: "#FFFFFF", // $("#bgColor").val(),
            color: "#000000", // $("#color").val(),
            barWidth: "1", //$("#barWidth").val(),
            barHeight: "50", //$("#barHeight").val(),
            moduleSize: "3", //$("#moduleSize").val(),
            posX: "5", //$("#posX").val(),
            posY: "5", //$("#posY").val(),
            addQuietZone: "1" //$("#quietZoneSize").val()
        };
        if (renderer == 'canvas'){
            clearCanvas();
            $("#barcodeTarget").hide();
            $("#canvasTarget").show().barcode(value, btype, settings);
        } else {
            $("#canvasTarget").hide();
            $("#barcodeTarget" + i).html("").show().barcode(value, btype, settings);
        }
    }

    $("#btnGuardarM").click(function()
    {
        generateBarcode();
    });

    aux_nfila=parseInt($("#tabla-data-productos >tbody >tr").length);
    for(i=1; i<=aux_nfila; i++){
        codbar = $("#barcodeTarget" + i).html();
        generateBarcode(codbar,i);
    }
    //alert(aux_nfila);
    */

    $('#tabla-data-productos').DataTable( {
        "language": {
			"decimal": ",",
			"emptyTable": "No hay información",
			"info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
			"infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
			"infoFiltered": "(Filtrado de _MAX_ total registros)",
			"infoPostFix": "",
			"thousands": ".",
			"lengthMenu": "Mostrar _MENU_ registros",
			"loadingRecords": "Cargando...",
			"processing": "Procesando...",
			"search": "Buscar:",
			"zeroRecords": "Sin resultados encontrados",
			"paginate": {
				"first": "Primero",
				"last": "Ultimo",
				"next": "Siguiente",
				"previous": "Anterior"
			}

        }
    } );


	$('#tabla-data-productos tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#tabla-data-productos').DataTable();
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change clear', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );

});

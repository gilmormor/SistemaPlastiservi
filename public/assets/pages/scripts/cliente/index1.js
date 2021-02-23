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
        'ajax'        : "clientepage",
        'columns'     : [
            {data: 'id'},
            {data: 'rut'},
            {data: 'razonsocial'},
            {defaultContent : "<a class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'><i class='fa fa-fw fa-pencil'></i></a><a href='' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'><i class='fa fa-fw fa-trash text-danger'></i></a>"}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

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
        /*
        if ($("#rectangular").is(':checked') || $("#rectangular").attr('checked')){
            value = {code:value, rect: true};
        }
        */
        //alert(value);
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

    aux_nfila=parseInt($("#tabla-data >tbody >tr").length);
    for(i=1; i<=aux_nfila; i++){
        codbar = $("#barcodeTarget" + i).html();
        generateBarcode(codbar,i);
    }
    //alert(aux_nfila);

});


$(document).on("click", ".btnEditar", function(){	
    opcion = 2;//editar
    fila = $(this).closest("tr");	        
    id = fila.find('td:eq(0)').text();
    //alert('Id: '+id);
    // *** REDIRECCIONA A UNA RUTA*** 
    var loc = window.location;
    window.location = loc.protocol+"//"+loc.hostname+"/cliente/"+id+"/editar";
    // ****************************** 

    /*
    user_id = parseInt(fila.find('td:eq(0)').text()); //capturo el ID		            
    username = fila.find('td:eq(1)').text();
    first_name = fila.find('td:eq(2)').text();
    last_name = fila.find('td:eq(3)').text();
    gender = fila.find('td:eq(4)').text();
    password = fila.find('td:eq(5)').text();
    status = fila.find('td:eq(6)').text();
    $("#username").val(username);
    $("#first_name").val(first_name);
    $("#last_name").val(last_name);
    $("#gender").val(gender);
    $("#password").val(password);
    $("#status").val(status);
    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white" );
    $(".modal-title").text("Editar Usuario");		
    $('#modalCRUD').modal('show');	*/	   
});

$(document).on("click", ".btnEliminar", function(){	
    fila = $(this).closest("tr");	        
    id = fila.find('td:eq(0)').text();
    //alert('Id: '+id);
    // *** REDIRECCIONA A UNA RUTA*** 
    var loc = window.location;
    window.location = loc.protocol+"//"+loc.hostname+"/cliente/"+id+"/editar";
    // ****************************** 
});
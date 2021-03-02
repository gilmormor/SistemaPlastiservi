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
        'ajax'        : "estadisticaventagipage",
        'columns'     : [
            {data: 'id'},
            {data: 'fechadocumento'},
            {data: 'numerodocumento'},
            {data: 'razonsocial'},
            {data: 'descripcion'},
            {data: 'medidas'},
            {data: 'matprimdesc'},
            {data: 'unidades'},
            {data: 'kilos'},
            {data: 'valorcosto'},
            {data: 'subtotal'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : "<a class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'><i class='fa fa-fw fa-pencil'></i></a><!--<a href='' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'><i class='fa fa-fw fa-trash text-danger'></i></a>-->"}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

});


$(document).on("click", ".btnEditar", function(){	
    opcion = 2;//editar
    fila = $(this).closest("tr");	        
    id = fila.find('td:eq(0)').text();
    //alert('Id: '+id);
    // *** REDIRECCIONA A UNA RUTA*** 
    var loc = window.location;
    window.location = loc.protocol+"//"+loc.hostname+"/estadisticaventagi/"+id+"/editar";
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
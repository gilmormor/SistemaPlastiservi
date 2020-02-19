$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    $('#divfoto').hide();
    $('#impresofoto').fileinput({
        language: 'es',
        allowedFileExtensions: ['jpg', 'jpeg', 'png'],
        maxFileSize: 4000,
        showUpload: false,
        showClose: false,
        initialPreviewAsData: true,
        dropZoneEnabled: false,
        theme: "fa",
    });

    $("#impreso").change(function(){
        if($("#impreso").val()=='0'){
            $('#divfoto').hide();
        }else{
            $('#divfoto').show();
        }
    });

    $("#clientedirec_id").change(function(){
        aux_nomcli = $("#clientedirec_id option:selected").attr('nomcli')
        //alert(aux_nomcli);
        $("#nomcli").val(aux_nomcli);
    });
    $("#nomcli").keypress(function(){
        alert('Prueba');
    });
});

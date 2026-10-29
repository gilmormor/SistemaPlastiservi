$(document).ready(function(){
    /* El tipo de despacho decide si se piden los datos de transporte. El Formato
       DTE 2.5 lo dice en los campos Patente, PatenteCarro y RUTTrans: "Relevante
       si Indicador Tipo de Despacho 2 o 3". Con el tipo 1 (por cuenta del receptor
       del documento) el comprador retira los bienes con su propio vehiculo, y la
       Res. Ex. SII N°144, resolutivo 15°, libera expresamente de informar esos
       datos. El valor vacio existe solo en las facturas de servicio o arriendo,
       que no amparan ningun traslado.
       Si el select no esta en la pagina se devuelve false y todo se valida igual
       que antes: ante la duda se piden los datos. */
    function retiraCliente(){
        var aux_sel = $("#tipodespacho");
        if(aux_sel.length === 0){
            return false;
        }
        return (aux_sel.val() == "1");
    }

    /* Caso distinto del anterior: aqui no hay traslado de ninguna clase, porque la
       factura es de un servicio o un arriendo. Solo existe en las facturas, que son
       las unicas que ofrecen la opcion vacia del tipo de despacho. */
    function sinDespacho(){
        var aux_sel = $("#tipodespacho");
        if(aux_sel.length === 0){
            return false;
        }
        var aux_val = aux_sel.val();
        return (aux_val === null || aux_val === "");
    }

    /* Los campos propios del transportista, que son los unicos que se ocultan
       cuando retira el cliente. Todo lo demas que vive en este mismo recuadro -el
       tipo de traslado, la comuna y el lugar de entrega, y las fechas de salida y
       llegada- sigue siendo obligatorio: su condicion en el Formato DTE 2.5 es
       "Obligatorio para documentos que acompañan traslado de bienes" y no depende
       del tipo de despacho, y ademas comunaentrega_id y lugarentrega son NOT NULL
       en dteguiadesp. */
    /* Ojo con el RUT del chofer: su name es rutchofer1 pero su id es rutchofer, de
       modo que aqui va por el id. */
    var SEL_TRANSPORTISTA = "#tipo_transporte, #ruttrans, #rutchofer, #nombrechofer, #patente, #patentecarro";

    /* Oculta los grupos y les quita el required. Un campo required dentro de un
       contenedor oculto impide enviar el formulario y el navegador no alcanza a
       mostrar el error, de modo que el usuario queda sin saber por que no puede
       guardar. Se marcan con req_trans para devolverlos a su estado original. */
    function liberarGrupos(aux_grupos){
        aux_grupos.find("[required]").each(function(){
            $(this).removeAttr("required").addClass("req_trans");
        });
        aux_grupos.find(".is-invalid").removeClass("is-invalid");
        aux_grupos.hide();
    }

    function reponerGrupos(aux_grupos){
        aux_grupos.show().find(".req_trans").each(function(){
            $(this).attr("required", "required").removeClass("req_trans");
        });
    }

    /* Tres escenarios, segun el tipo de despacho:
         vacio -> factura de servicio o arriendo: no hay traslado, se oculta todo
         1     -> retira el cliente: se ocultan solo los datos del transportista
         2 o 3 -> despacha la empresa: todo visible y obligatorio */
    function aplicarTipoOperacion(){
        var aux_wrap = $("#wrap_datostrans");
        if(aux_wrap.length === 0){
            return;
        }
        var aux_grupos = aux_wrap.find(".form-group");
        if(sinDespacho()){
            liberarGrupos(aux_grupos);
            aux_wrap.hide();
        }else if(retiraCliente()){
            // Se repone todo primero, para no arrastrar el estado del valor anterior
            reponerGrupos(aux_grupos);
            liberarGrupos(aux_wrap.find(SEL_TRANSPORTISTA).closest(".form-group"));
            aux_wrap.show();
        }else{
            reponerGrupos(aux_grupos);
            aux_wrap.show();
        }
    }

    /* Delegado en document porque el select usa select2: select2 dispara el change
       sobre el elemento original, pero lo reemplaza en el DOM al inicializarse. */
    $(document).on("change", "#tipodespacho", function(){
        aplicarTipoOperacion();
    });
    // Se aplica al cargar, por si el formulario vuelve con old() tras un error
    aplicarTipoOperacion();

    // Obtener fecha actual para comparar
    let fechaActual = new Date();
    fechaActual.setHours(0, 0, 0, 0);
    
    // Inicializar datepickers para fechas (solo selección, no escritura manual)
    $('.datepicker').datepicker({
        language: "es",
        autoclose: true,
        format: "dd/mm/yyyy",
        todayHighlight: true,
        todayBtn: true,
        clearBtn: true,
        startDate: fechaActual
    }).on('show', function() {
        $(this).attr('readonly', true);
    }).on('hide', function() {
        // Cuando se cierra el datepicker, disparar la validación
        $(this).trigger('blur');
    }).on('changeDate', function() {
        // Cuando se selecciona una fecha, disparar la validación
        $(this).trigger('blur');
    });
    // Variable para controlar si la hora es válida
    let horaValida = false;

    // Validación de tipo de transporte
    $('#tipo_transporte').change(function() {
        let valor = $(this).val();
        let errorSpan = $('#error_tipo_transporte');
        
        if(valor === '') {
            errorSpan.show();
            $(this).removeClass('is-valid').addClass('is-invalid');
        } else {
            errorSpan.hide();
            $(this).removeClass('is-invalid').addClass('is-valid');
            
            if(valor === 'EXTERNO'){
                $('#div_rut_transportista').fadeIn();
                $('#ruttrans').prop('required', true);
            } else {
                $('#div_rut_transportista').fadeOut();
                $('#ruttrans').prop('required', false);
                $('#ruttrans').val('');
                $('#nombre_transportista').html('');
            }
        }
    }).trigger('change');

    // ============================================
    // VALIDACIONES MEJORADAS PARA RUT
    // ============================================
    
    // Función para limpiar RUT (solo números y K mayúscula)
    function limpiarRUT(rut) {
        // Eliminar puntos y guiones
        let rutLimpio = rut.replace(/[.-]/g, '');
        // Convertir a mayúsculas
        rutLimpio = rutLimpio.toUpperCase();
        // Eliminar cualquier caracter que no sea número o K
        rutLimpio = rutLimpio.replace(/[^0-9K]/g, '');
        return rutLimpio;
    }
    
    // Función para validar formato de RUT (máximo 9 dígitos, K solo al final)
    function validarFormatoRUT(rut) {
        if(!rut) return true;
        
        let rutLimpio = limpiarRUT(rut);
        
        // Verificar que no exceda 9 caracteres
        if(rutLimpio.length > 9) {
            return false;
        }
        
        // Si tiene K, debe ser el último carácter
        if(rutLimpio.indexOf('K') !== -1) {
            if(rutLimpio.indexOf('K') !== rutLimpio.length - 1) {
                return false;
            }
            // Verificar que solo tenga una K
            if(rutLimpio.split('K').length - 1 > 1) {
                return false;
            }
        }
        
        // Verificar que solo tenga números y opcionalmente una K al final
        let patron = /^[0-9]+K?$/;
        return patron.test(rutLimpio);
    }
    
    // Formatear RUT mientras escribe (sin puntos ni guiones)
    function formatearRUTEnTiempoReal(input) {
        let valor = $(input).val();
        let cursorPos = input.selectionStart;
        
        // Limpiar el valor (eliminar puntos, guiones, espacios)
        let valorLimpio = valor.replace(/[.-]/g, '').toUpperCase();
        
        // Eliminar cualquier caracter que no sea número o K
        valorLimpio = valorLimpio.replace(/[^0-9K]/g, '');
        
        // Limitar a 9 caracteres
        if(valorLimpio.length > 9) {
            valorLimpio = valorLimpio.substring(0, 9);
        }
        
        // Si hay una K que no está al final, moverla al final
        if(valorLimpio.indexOf('K') !== -1 && valorLimpio.indexOf('K') !== valorLimpio.length - 1) {
            let sinK = valorLimpio.replace(/K/g, '');
            valorLimpio = sinK + 'K';
        }
        
        // Si hay más de una K, eliminar las extras
        if(valorLimpio.split('K').length - 1 > 1) {
            let primeraK = true;
            valorLimpio = valorLimpio.split('').filter(char => {
                if(char === 'K' && primeraK) {
                    primeraK = false;
                    return true;
                }
                return char !== 'K';
            }).join('');
        }
        
        // Actualizar el valor del campo
        if($(input).val() !== valorLimpio) {
            $(input).val(valorLimpio);
            // Mantener cursor al final
            input.setSelectionRange(valorLimpio.length, valorLimpio.length);
        }
    }
    
    // Validación en tiempo real para RUT Transportista
    $('#ruttrans').on('input', function(e) {
        formatearRUTEnTiempoReal(this);
        
        let rut = $(this).val();
        let errorSpan = $('#nombre_transportista');
        
        if(rut.length > 0) {
            if(validarFormatoRUT(rut)) {
                $(this).removeClass('is-invalid').addClass('is-valid');
                errorSpan.html('<i class="fa fa-spinner fa-spin"></i> Validando RUT...').removeClass('text-red text-green');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> RUT inválido (máximo 9 caracteres, solo números y K al final)').removeClass('text-green').addClass('text-red');
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
            errorSpan.html('');
        }
    });
    
    // Validación en tiempo real para RUT Chofer
    $('#rutchofer').on('input', function(e) {
        formatearRUTEnTiempoReal(this);
        
        let rut = $(this).val();
        let errorSpan = $('#nombre_chofer');
        
        if(rut.length > 0) {
            if(validarFormatoRUT(rut)) {
                $(this).removeClass('is-invalid').addClass('is-valid');
                errorSpan.html('<i class="fa fa-spinner fa-spin"></i> Validando RUT...').removeClass('text-red text-green');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> RUT inválido (máximo 9 caracteres, solo números y K al final)').removeClass('text-green').addClass('text-red');
                //alertify.warning('Formato de RUT inválido (máximo 9 caracteres, solo números y K al final)');
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
            errorSpan.html('');
        }
    });
    
    // Prevenir que se peguen caracteres inválidos en RUT
    $('#ruttrans, #rutchofer').on('paste', function(e) {
        e.preventDefault();
        let pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        let rutLimpio = limpiarRUT(pastedText);
        
        // Limitar a 9 caracteres
        if(rutLimpio.length > 9) {
            rutLimpio = rutLimpio.substring(0, 9);
        }
        
        $(this).val(rutLimpio);
        $(this).trigger('input');
    });
    
    // Función para validar RUT completo (con dígito verificador)
    function validarRUTCompleto(rut) {
        if(!rut) return false;
        
        let rutLimpio = limpiarRUT(rut);
        
        if(rutLimpio.length < 2) return false;
        
        let dv = rutLimpio.substring(rutLimpio.length - 1);
        let rutNum = rutLimpio.substring(0, rutLimpio.length - 1);
        
        if(!/^[0-9]+$/.test(rutNum)) return false;
        
        let suma = 0;
        let multiplicador = 2;
        
        for(let i = rutNum.length - 1; i >= 0; i--) {
            suma += parseInt(rutNum.charAt(i)) * multiplicador;
            multiplicador++;
            if(multiplicador > 7) multiplicador = 2;
        }
        
        let dvEsperado = 11 - (suma % 11);
        if(dvEsperado === 11) dvEsperado = '0';
        if(dvEsperado === 10) dvEsperado = 'K';
        
        return dv === dvEsperado.toString();
    }

    // Función para buscar transportista (AJAX) - ACTUALIZADA
    $('#btn_buscar_transportista').click(function(){
        let rut = $('#ruttrans').val();
        let errorSpan = $('#nombre_transportista');
        
        if(!rut){
            alertify.warning('Ingrese un RUT para buscar.');
            return;
        }
        
        // Validar formato de RUT
        if(!validarFormatoRUT(rut)) {
            alertify.warning('Formato de RUT inválido (máximo 9 caracteres, solo números y K al final)');
            return;
        }
        
        // Validar dígito verificador
        if(!validarRUTCompleto(rut)) {
            alertify.warning('RUT inválido (dígito verificador incorrecto)');
            $('#ruttrans').addClass('is-invalid').removeClass('is-valid');
            errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> RUT inválido (dígito verificador incorrecto)');
            return;
        }
        
        errorSpan.html('<i class="fa fa-spinner fa-spin"></i> Buscando transportista...');
        
        $.ajax({
            url: '{{ route("buscar.transportista") }}',
            type: 'POST',
            data: { rut: rut, _token: '{{ csrf_token() }}' },
            success: function(resp){
                if(resp.existe){
                    errorSpan.html('<i class="fa fa-check-circle text-green"></i> ' + resp.nombre).removeClass('text-red').addClass('text-green');
                    alertify.success('Transportista encontrado.');
                } else {
                    errorSpan.html('<i class="fa fa-exclamation-circle text-blue"></i> Transportista no existe, se creará al guardar.').removeClass('text-green').addClass('text-blue');
                    alertify.message('Transportista no registrado.');
                }
            },
            error: function() {
                errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Error al buscar transportista');
            }
        });
    });

    // Resto de validaciones de fecha, hora, etc. (mantener el código anterior)
    // ... (código de validación de fecha, hora, patente que ya teníamos) ...
    
    // Validación de hora en tiempo real
    $('#hrasalida').on('input', function() {
        let horaValor = $(this).val();
        let errorSpan = $('#error_hrasalida');
        
        if(horaValor.length > 0) {
            if(validarHoraFormato24(horaValor)) {
                errorSpan.hide();
                $(this).removeClass('is-invalid').addClass('is-valid');
                horaValida = true;
            } else {
                errorSpan.show();
                $(this).removeClass('is-valid').addClass('is-invalid');
                horaValida = false;
            }
        } else {
            errorSpan.hide();
            $(this).removeClass('is-valid is-invalid');
            horaValida = true;
        }
    });

    // Validación de fecha de salida (no menor a hoy)
    function validarFechaNoMenorAHoy(fechaStr) {
        if(!fechaStr) return true;
        
        let partes = fechaStr.split('/');
        let fecha = new Date(partes[2], partes[1] - 1, partes[0]);
        fecha.setHours(0, 0, 0, 0);
        
        return fecha >= fechaActual;
    }
    
    function validarFechaSalida() {
        let fechaValor = $('#fchsalida').val();
        let errorSpan = $('#error_fchsalida');
        
        if(fechaValor) {
            let patronFecha = /^([0-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/(19|20)\d{2}$/;
            if(!patronFecha.test(fechaValor)) {
                errorSpan.text('Formato de fecha inválido');
                errorSpan.show();
                $('#fchsalida').removeClass('is-valid').addClass('is-invalid');
                return false;
            }
            
            if(!validarFechaNoMenorAHoy(fechaValor)) {
                errorSpan.text('La fecha no puede ser menor a hoy');
                errorSpan.show();
                $('#fchsalida').removeClass('is-valid').addClass('is-invalid');
                return false;
            }
            
            errorSpan.hide();
            $('#fchsalida').removeClass('is-invalid').addClass('is-valid');
            return true;
        } else {
            errorSpan.hide();
            $('#fchsalida').removeClass('is-valid is-invalid');
            return true;
        }
    }

    function validarFechaLlegada() {
        let fechaSalida = $('#fchsalida').val();
        let fechaLlegada = $('#fchllegada').val();
        let errorSpan = $('#error_fchllegada');
        
        if(fechaLlegada) {
            let patronFecha = /^([0-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/(19|20)\d{2}$/;
            if(!patronFecha.test(fechaLlegada)) {
                errorSpan.text('Formato de fecha inválido');
                errorSpan.show();
                $('#fchllegada').removeClass('is-valid').addClass('is-invalid');
                return false;
            }
            
            if(fechaSalida) {
                let partesSalida = fechaSalida.split('/');
                let fechaSalidaObj = new Date(partesSalida[2], partesSalida[1] - 1, partesSalida[0]);
                
                let partesLlegada = fechaLlegada.split('/');
                let fechaLlegadaObj = new Date(partesLlegada[2], partesLlegada[1] - 1, partesLlegada[0]);
                
                if(fechaLlegadaObj < fechaSalidaObj) {
                    errorSpan.text('La fecha de llegada no puede ser menor a la fecha de salida');
                    errorSpan.show();
                    $('#fchllegada').removeClass('is-valid').addClass('is-invalid');
                    return false;
                }
            }
            
            errorSpan.hide();
            $('#fchllegada').removeClass('is-invalid').addClass('is-valid');
            return true;
        } else {
            errorSpan.hide();
            $('#fchllegada').removeClass('is-valid is-invalid');
            return true;
        }
    }

    $('#fchsalida').on('blur', function() {
        validarFechaSalida();
        if($('#fchllegada').val()) {
            validarFechaLlegada();
        }
    });

    $('#fchllegada').on('blur', function() {
        validarFechaLlegada();
    });

    // Validar que solo se ingresen números y dos puntos
    $('#hrasalida').on('keypress', function(e) {
        let charCode = (e.which) ? e.which : e.keyCode;
        let char = String.fromCharCode(charCode);
        
        if(!/[0-9:]/.test(char) && charCode !== 8 && charCode !== 46 && charCode !== 0) {
            e.preventDefault();
            return false;
        }
        
        let valorActual = $(this).val();
        if(char === ':' && valorActual.split(':').length >= 2) {
            e.preventDefault();
            return false;
        }
    });

    // Formateo automático de hora mientras escribe
    $('#hrasalida').on('keyup', function(e) {
        let key = e.keyCode || e.which;
        if(key === 37 || key === 38 || key === 39 || key === 40 || key === 36 || key === 35) {
            return;
        }
        
        let valor = $(this).val().replace(/[^0-9]/g, '');
        
        if(valor.length >= 2) {
            let horas = valor.substring(0, 2);
            let minutos = valor.substring(2, 4);
            
            if(parseInt(horas) > 23) {
                horas = '23';
            }
            
            if(minutos.length >= 2) {
                if(parseInt(minutos) > 59) {
                    minutos = '59';
                }
                $(this).val(horas + ':' + minutos);
            } else if(valor.length > 2) {
                $(this).val(horas + ':' + minutos);
            } else {
                $(this).val(horas);
            }
        }
        
        $(this).trigger('input');
    });

    function validarHoraFormato24(hora) {
        let patronHora = /^([0-1][0-9]|2[0-3]):[0-5][0-9]$/;
        return patronHora.test(hora);
    }

    // Al seleccionar comuna, actualizar campo oculto cmnadest
    $('#comuna_destino_id').change(function(){
        let comunaNombre = $('#comuna_destino_id option:selected').text();
        $('#cmnadest').val(comunaNombre);
    });
   
    // Validación antes de enviar el formulario (ACTUALIZADA)
    $('#form-general').on('submit', function(e) {
        let isValid = true;

        /* En un servicio o arriendo no hay datos de transporte que validar: se
           sale sin preventDefault para que el formulario siga su curso normal. */
        if(sinDespacho()){
            return;
        }

        /* Cuando retira el cliente no hay transportista que validar, pero las
           fechas de salida y llegada se siguen exigiendo mas abajo. */
        let tipoTransporte = $('#tipo_transporte').val();
        if(!retiraCliente()) {
            // Validar tipo de transporte
            if(!tipoTransporte) {
                $('#error_tipo_transporte').show();
                $('#tipo_transporte').addClass('is-invalid');
                isValid = false;
            } else {
                $('#error_tipo_transporte').hide();
                $('#tipo_transporte').removeClass('is-invalid').addClass('is-valid');
            }
        }
        
        // Validar fecha de salida
        if(!validarFechaSalida()) {
            isValid = false;
        }
        
        // Validar hora de salida
        let horaSalida = $('#hrasalida').val();
        if(horaSalida) {
            if(!validarHoraFormato24(horaSalida)) {
                $('#error_hrasalida').show();
                $('#hrasalida').addClass('is-invalid');
                isValid = false;
            } else {
                $('#error_hrasalida').hide();
                $('#hrasalida').removeClass('is-invalid').addClass('is-valid');
            }
        } else if($('#fchsalida').val()) {
            alertify.warning('Debe ingresar la hora de salida.');
            $('#hrasalida').addClass('is-invalid');
            isValid = false;
        }
        
        // Validar fecha de llegada
        if(!validarFechaLlegada()) {
            isValid = false;
        }
        
        // Validar dirección de destino
        /* if(!$('#dirdest').val()) {
            alertify.warning('Debe ingresar la dirección de destino.');
            $('#dirdest').addClass('is-invalid');
            isValid = false;
        } */
        
        // Validar comuna de destino
        /* if(!$('#comuna_destino_id').val()) {
            alertify.warning('Debe seleccionar la comuna de destino.');
            $('#comuna_destino_id').addClass('is-invalid');
            isValid = false;
        } */
        
        // Validar RUT chofer
        /* let rutChofer = $('#rutchofer').val();
        if(rutChofer) {
            if(!validarFormatoRUT(rutChofer)) {
                alertify.warning('RUT de chofer inválido (máximo 9 caracteres, solo números y K al final)');
                isValid = false;
            } else if(!validarRUTCompleto(rutChofer)) {
                alertify.warning('RUT de chofer inválido (dígito verificador incorrecto)');
                isValid = false;
            }
        } *//*  else {
            alertify.warning('Debe ingresar el RUT del chofer.');
            isValid = false;
        } */
        
        // Validar nombre chofer
        /* if(!$('#nombrechofer').val()) {
            alertify.warning('Debe ingresar el nombre del chofer.');
            $('#nombrechofer').addClass('is-invalid');
            isValid = false;
        } */
        
        // Validar patente
        /* if(!$('#patente').val()) {
            alertify.warning('Debe ingresar la patente del vehículo.');
            $('#patente').addClass('is-invalid');
            isValid = false;
        } */
        
        // Validar RUT transportista si es externo (no aplica si retira el cliente)
        if(!retiraCliente() && tipoTransporte === 'EXTERNO') {
            let rutTrans = $('#ruttrans').val();
            if(!rutTrans) {
                alertify.warning('Debe ingresar el RUT del transportista para transporte externo.');
                isValid = false;
            } else if(!validarFormatoRUT(rutTrans)) {
                alertify.warning('RUT de transportista inválido (máximo 9 caracteres, solo números y K al final)');
                isValid = false;
            } else if(!validarRUTCompleto(rutTrans)) {
                alertify.warning('RUT de transportista inválido (dígito verificador incorrecto)');
                isValid = false;
            }
        }

        // Verificar si existe algún campo con la clase "is-invalid"
        if($('.is-invalid').length > 0) {
            isValid = false;
        }
        
        if(!isValid) {
            e.preventDefault();
            alertify.error('Por favor corrija los errores en el formulario.');
            $("#btnguardargen").prop("disabled", false);
            return false;
        }
    });
    $(".rut").focus(function(){
        eliminarFormatoRut($(this));
    });

    $("#ruttrans").blur(function(){
        let aux_this = this; 
        codigo = $(aux_this).val();
        formato_rut($(aux_this));
        if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
        {
            if(!dgv(codigo.substr(0, codigo.length-1),aux_this)){
                swal({
                    title: 'Dígito verificador no es Válido.',
                    text: "",
                    icon: 'warning',
                    buttons: {
                        confirm: "Aceptar"
                    },
                }).then((value) => {
                    if (value) {
                        $(aux_this).focus();
                    }
                });
                //$(this).val('');
            }else{
                /* var data = {
                    rut   : codigo,
                    modulo_id : 2,
                    sta_consdeuda : 1,
                    _token: $('input[name=_token]').val()
                };
                $.ajax({
                    //url: '/cliente/buscarCliId',
                    url: '/cliente/buscarClixVenRut',
                    type: 'POST',
                    data: data,
                    success: function (respuesta) {
                        if(respuesta.cliente.length>0){
                            $("#razonsocial").val(respuesta.cliente[0].razonsocial);
                        }else{
                            swal({
                                title: 'Cliente no existe.',
                                text: "Presione F2 para buscar",
                                icon: 'warning',
                                buttons: {
                                    confirm: "Aceptar"
                                },
                            }).then((value) => {
                                if (value) {
                                    console.log("enrto");
                                    //ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
                                    $(aux_this).focus();
                                }
                            });			
                        }
                    }
                }); */
                let errorSpan = $('#nombre_transportista');
                
                /* if(!rut){
                    alertify.warning('Ingrese un RUT para buscar.');
                    return;
                } */
                
                // Validar formato de RUT
                /* if(!validarFormatoRUT(rut)) {
                    alertify.warning('Formato de RUT inválido (máximo 9 caracteres, solo números y K al final)');
                    return;
                } */
                
                // Validar dígito verificador
                /* if(!validarRUTCompleto(rut)) {
                    alertify.warning('RUT inválido (dígito verificador incorrecto)');
                    $('#ruttrans').addClass('is-invalid').removeClass('is-valid');
                    errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> RUT inválido (dígito verificador incorrecto)');
                    return;
                } */
                
                errorSpan.html('<i class="fa fa-spinner fa-spin"></i> Buscando transportista...');
                var data = {
                    rut   : codigo,
                    _token: $('input[name=_token]').val()
                };
                $.ajax({
                    //url: '/cliente/buscarCliId',
                    url: '/trans/buscarTransportista',
                    type: 'POST',
                    data: data,
                    success: function(resp){
                        if(resp.existe){
                            errorSpan.html('<i class="fa fa-check-circle text-green"></i> Transportista encontrado.').removeClass('text-red').addClass('text-green');
                            alertify.success('Transportista encontrado.');
                        } else {
                            errorSpan.html('<i class="fa fa-exclamation-circle text-blue"></i> Transportista no existe, se creará al guardar.').removeClass('text-green').addClass('text-blue');
                            alertify.error('Transportista no registrado.');
                        }
                    },
                    error: function() {
                        errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Error al buscar transportista');
                    }
                });

            }
        }
    });
    $("#rutchofer").blur(function(){
        let aux_this = this; 
        codigo = $(aux_this).val();
        $("#nombrechofer").val("");
        formato_rut($(aux_this));
        if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
        {
            //totalizar();
            if(!dgv(codigo.substr(0, codigo.length-1),this)){
                swal({
                    title: 'Dígito verificador no es Válido.',
                    text: "",
                    icon: 'warning',
                    buttons: {
                        confirm: "Aceptar"
                    },
                }).then((value) => {
                    if (value) {
                        $(aux_this).focus();
                    }
                });
                //$(this).val('');
            }else{
                let errorSpan = $('#nombre_chofer');
                
                /* if(!rut){
                    alertify.warning('Ingrese un RUT para buscar.');
                    return;
                } */
                
                // Validar formato de RUT
                /* if(!validarFormatoRUT(rut)) {
                    alertify.warning('Formato de RUT inválido (máximo 9 caracteres, solo números y K al final)');
                    return;
                } */
                
                // Validar dígito verificador
                /* if(!validarRUTCompleto(rut)) {
                    alertify.warning('RUT inválido (dígito verificador incorrecto)');
                    $('#ruttrans').addClass('is-invalid').removeClass('is-valid');
                    errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> RUT inválido (dígito verificador incorrecto)');
                    return;
                } */
                
                errorSpan.html('<i class="fa fa-spinner fa-spin"></i> Buscando chofer...');
                var data = {
                    rut   : codigo,
                    _token: $('input[name=_token]').val()
                };
                $.ajax({
                    //url: '/cliente/buscarCliId',
                    url: '/trans/buscarChofer',
                    type: 'POST',
                    data: data,
                    success: function(resp){
                        if(resp.existe){
                            $("#nombrechofer").val(resp.nombre);
                            errorSpan.html('<i class="fa fa-check-circle text-green"></i> Chofer encontrado').removeClass('text-red').addClass('text-green');
                            alertify.success('Chofer encontrado.');
                        } else {
                            errorSpan.html('<i class="fa fa-info-circle text-blue"></i> Chofer no existe, se creará al guardar.').removeClass('text-green').addClass('text-blue');
                            alertify.error('Chofer no registrado.');
                        }
                    },
                    error: function() {
                        errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Error al buscar transportista');
                    }
                });
            }
        }
    });
    $("#patente1").blur(function(){
        let aux_this = this; 
        codigo = $(aux_this).val();
        //formato_rut($(aux_this));
        if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
        {
            let errorSpan = $('#vehiculo_existe');
            
            errorSpan.html('<i class="fa fa-spinner fa-spin"></i> Buscando Patente...');
            var data = {
                patente : codigo,
                _token  : $('input[name=_token]').val()
            };
            $.ajax({
                //url: '/cliente/buscarCliId',
                url: '/trans/buscarPatente',
                type: 'POST',
                data: data,
                success: function(resp){
                    if(resp.existe){
                        //$("#nombrechofer").val(resp.nombre);
                        errorSpan.html('<i class="fa fa-check-circle text-green"></i> Patente encontrado').removeClass('text-red').addClass('text-green');
                        alertify.success('Patente encontrado.');
                    } else {
                        errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Patente no existe, se creará al guardar.').removeClass('text-green').addClass('text-red');
                        alertify.error('Patente no registrado.');
                    }
                },
                error: function() {
                    errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Error al buscar patente');
                }
            });
        }
    });

// ============================================
// VALIDACIONES MEJORADAS PARA PATENTE
// ============================================

// Función para limpiar patente (solo letras mayúsculas y números)
function limpiarPatente(patente) {
    // Convertir a mayúsculas
    let patenteLimpia = patente.toUpperCase();
    // Eliminar cualquier caracter que no sea letra o número
    patenteLimpia = patenteLimpia.replace(/[^A-Z0-9]/g, '');
    return patenteLimpia;
}

// Función para validar formato de patente chilena
function validarFormatoPatente(patente) {
    if(!patente) return false;
    
    let patenteLimpia = limpiarPatente(patente);
    
    // Patente antigua: 2 letras + 4 números (AA1234)
    let patronAntiguo = /^[A-Z]{2}[0-9]{4}$/;
    // Patente nueva: 4 letras + 2 números (ABCD12)
    let patronNuevo = /^[A-Z]{4}[0-9]{2}$/;
    
    return patronAntiguo.test(patenteLimpia) || patronNuevo.test(patenteLimpia);
}

// Formatear patente mientras escribe
function formatearPatenteEnTiempoReal(input) {
    let valor = $(input).val();
    let cursorPos = input.selectionStart;
    
    // Limpiar el valor (solo letras y números, convertir a mayúsculas)
    let valorLimpio = valor.toUpperCase().replace(/[^A-Z0-9]/g, '');
    
    // Limitar a 6 caracteres (máximo para patentes chilenas)
    if(valorLimpio.length > 6) {
        valorLimpio = valorLimpio.substring(0, 6);
    }
    
    // Actualizar el valor del campo
    if($(input).val() !== valorLimpio) {
        $(input).val(valorLimpio);
        // Mantener cursor al final
        input.setSelectionRange(valorLimpio.length, valorLimpio.length);
    }
}

    // Validación en tiempo real para patente
    $('#patente').on('input', function(e) {
        formatearPatenteEnTiempoReal(this);
        
        let patente = $(this).val();
        let errorSpan = $('#vehiculo_existe');
        
        if(patente.length > 0) {
            // Validar que solo tenga letras y números
            if(/^[A-Z0-9]+$/.test(patente)) {
                // Verificar longitud mínima
                if(patente.length >= 5) {
                    if(validarFormatoPatente(patente)) {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                        errorSpan.html('<i class="fa fa-check-circle text-green"></i> Formato correcto.').removeClass('text-red').addClass('text-green');
                        // Buscar si la patente existe en la base de datos
                        var data = {
                            patente : patente,
                            _token  : $('input[name=_token]').val()
                        };
                        $.ajax({
                            url: '/trans/buscarPatente',
                            type: 'POST',
                            data: data,
                            success: function(resp){
                                if(resp.existe){
                                    errorSpan.html('<i class="fa fa-check-circle text-green"></i> Vehículo registrado.').removeClass('text-red').addClass('text-green');
                                } else {
                                    errorSpan.html('<i class="fa fa-info-circle text-blue"></i> Patente no registrada, se agregará al catálogo al guardar.').removeClass('text-green').addClass('text-red');
                                }
                            }
                        });
                    } else {
                        //$(this).removeClass('is-valid').addClass('is-invalid');
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Formato de patente inválido (AA1234 o ABCD12)').removeClass('text-green').addClass('text-red');
                    }
                } else {
                    //$(this).removeClass('is-valid is-invalid');
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Mínimo 6 caracteres (ej: AA1234 o ABCD12)').removeClass('text-red text-green').addClass('text-blue');
                }
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Solo letras y números, sin espacios ni caracteres especiales').removeClass('text-green').addClass('text-red');
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
            errorSpan.html('');
        }
    });

    // Prevenir que se peguen caracteres inválidos en patente
    $('#patente').on('paste', function(e) {
        e.preventDefault();
        let pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        let patenteLimpia = limpiarPatente(pastedText);
        
        // Limitar a 6 caracteres
        if(patenteLimpia.length > 6) {
            patenteLimpia = patenteLimpia.substring(0, 6);
        }
        
        $(this).val(patenteLimpia);
        $(this).trigger('input');
    });

    // Validar que solo se ingresen letras y números en patente
    $('#patente').on('keypress', function(e) {
        let charCode = (e.which) ? e.which : e.keyCode;
        let char = String.fromCharCode(charCode);
        
        // Permitir letras (mayúsculas y minúsculas) y números
        let patron = /[A-Za-z0-9]/;
        
        // Permitir teclas de control (backspace, delete, etc.)
        if(charCode === 8 || charCode === 46 || charCode === 0) {
            return true;
        }
        
        if(!patron.test(char)) {
            e.preventDefault();
            return false;
        }
        
        // Limitar a 6 caracteres
        let valorActual = $(this).val();
        if(valorActual.length >= 6) {
            e.preventDefault();
            return false;
        }
    });

    // Validación adicional para patente de carro (remolque)
    $('#patentecarro1').on('input', function(e) {
        let valor = $(this).val();
        let valorLimpio = valor.toUpperCase().replace(/[^A-Z0-9]/g, '');
        
        // Limitar a 6 caracteres
        if(valorLimpio.length > 6) {
            valorLimpio = valorLimpio.substring(0, 6);
        }
        
        if($(this).val() !== valorLimpio) {
            $(this).val(valorLimpio);
        }
        
        // Validar formato si tiene contenido
        if(valorLimpio.length > 0) {
            if(valorLimpio.length >= 5) {
                if(validarFormatoPatente(valorLimpio)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    $('#patentecarro').on('input', function(e) {
        formatearPatenteEnTiempoReal(this);
        
        let patente = $(this).val();
        let errorSpan = $('#patentecarro_existe');
        
        if(patente.length > 0) {
            // Validar que solo tenga letras y números
            if(/^[A-Z0-9]+$/.test(patente)) {
                // Verificar longitud mínima
                if(patente.length >= 6) {
                    if(validarFormatoPatente(patente)) {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                        errorSpan.html('<i class="fa fa-check-circle text-green"></i> Formato correcto.').removeClass('text-red').addClass('text-green');
                        // Buscar si la patente existe en la base de datos
                        var data = {
                            patente : patente,
                            _token  : $('input[name=_token]').val()
                        };
                        $.ajax({
                            url: '/trans/buscarPatente',
                            type: 'POST',
                            data: data,
                            success: function(resp){
                                if(resp.existe){
                                    errorSpan.html('<i class="fa fa-check-circle text-green"></i> Vehículo registrado.').removeClass('text-red').addClass('text-green');
                                } else {
                                    errorSpan.html('<i class="fa fa-info-circle text-blue"></i> Patente no registrada, se agregará al catálogo al guardar.').removeClass('text-green').addClass('text-red');
                                }
                            }
                        });
                    } else {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Formato de patente inválido (AA1234 o ABCD12)').removeClass('text-green').addClass('text-red');
                    }
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Mínimo 6 caracteres (ej: AA1234 o ABCD12)').removeClass('text-red text-green').addClass('text-blue');
                }
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
                errorSpan.html('<i class="fa fa-exclamation-circle text-red"></i> Solo letras y números, sin espacios ni caracteres especiales').removeClass('text-green').addClass('text-red');
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
            errorSpan.html('');
        }
    });

    // Prevenir caracteres inválidos en patente de carro
    $('#patentecarro').on('keypress', function(e) {
        let charCode = (e.which) ? e.which : e.keyCode;
        let char = String.fromCharCode(charCode);
        
        let patron = /[A-Za-z0-9]/;
        
        if(charCode === 8 || charCode === 46 || charCode === 0) {
            return true;
        }
        
        if(!patron.test(char)) {
            e.preventDefault();
            return false;
        }
        
        let valorActual = $(this).val();
        if(valorActual.length >= 6) {
            e.preventDefault();
            return false;
        }
    });

    $('#patentecarro').on('paste', function(e) {
        e.preventDefault();
        let pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        let patenteLimpia = limpiarPatente(pastedText);
        
        if(patenteLimpia.length > 6) {
            patenteLimpia = patenteLimpia.substring(0, 6);
        }
        
        $(this).val(patenteLimpia);
        $(this).trigger('input');
    });


});
<script>
    // === PLUGINS ===

    // === CONSTANTES NO TOCAR ===
        var options1 = {
            "closeButton"      : true,
            "debug"            : false,
            "progressBar"      : true,
            "preventDuplicates": false,
            "positionClass"    : "toast-top-right",
            "onclick"          : null,
            "showDuration"     : "400",
            "hideDuration"     : "1000",
            "timeOut"          : "2000",
            "extendedTimeOut"  : "1000",
            "showEasing"       : "swing",
            "hideEasing"       : "linear",
            "showMethod"       : "fadeIn",
            "hideMethod"       : "fadeOut"
        };

    // === VARIABLES GLOBALES ===
        var rol_id            = "{!! $rol_id !!}";
        var base_url          = "{!! url('') !!}";
        var url_controller    = "{!! url('/notificacion') !!}";
        var csrf_token        = "{!! csrf_token() !!}";

    // === FORMULARIOS ===
        var form_1 = "#form_1";

    $(document).ready(function(){
        //=== INICIALIZAR ===
            var valor1 = new Array();
            valor1[0]  = 30;
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 31;
            utilitarios(valor1);

        //=== SELECT2 ===
            $('#caso_id').select2({
                // maximumSelectionLength: 1,
                minimumInputLength    : 2,
                ajax                  : {
                    url     : url_controller + '/send_ajax',
                    type    : 'post',
                    dataType: 'json',
                    data    : function (params) {
                        return {
                            q         : params.term,
                            page_limit: 20,
                            tipo      : 101,
                            _token    : csrf_token
                        };
                    },
                    results: function (data, page) {
                        return {
                            results: data
                        };
                    }
                }
            });

        // === SELECT CHANGE ===
            $("#caso_id").on("change", function(e) {
                switch ($.trim(this.value)){
                    case '':
                        var valor1 = new Array();
                        valor1[0]  = 30;
                        utilitarios(valor1);
                        break;
                    default:
                        var valor1 = new Array();
                        valor1[0]  = 50;
                        valor1[1]  = $.trim(this.value);
                        utilitarios(valor1);
                        break;
                }
            });

        // === CHECKED SELECT ===
            $("#denunciante_all_select").click(function(){
                $(".denunciante_class").prop("checked", this.checked);
            });

            $("#denunciado_all_select").click(function(){
                $(".denunciado_class").prop("checked", this.checked);
            });

            $("#victima_all_select").click(function(){
                $(".victima_class").prop("checked", this.checked);
            });
    });

    function utilitarios(valor){
        switch(valor[0]){
            // === ABRIR MODAL - REGISTRAR NOTIFICACION ===
            case 10:
                if(valor[1] != ''){
                    $("#actividad_id").val(valor[1]);

                    $('#modal_1_title, #modal_2_title').empty();
                    $('#modal_1_title').append('NOTIFICAR');

                    $('#modal_2_title').append(valor[3] + " - " + valor[2]);

                    $('#modal_1').modal();

                    $(form_1)[0].reset();
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                    valor1[2]  = 'No existe ACTIVIDAD para NOTIFICAR.';
                    utilitarios(valor1);
                }
                break;
            // === BORRAR INFORMACION ===
            case 30:
                $('#caso_b, #etapa_caso_b, #origen_caso_b, #estado_caso_b, #f_denuncia_b, #fiscal_asignado_b, #delito_principal_b, #modal_1_title, #actividad_tabla_b, #denunciante_b, #denunciado_b').empty();
                break;
            // === RESETEAR FORMULARIO 1 ===
            case 31:
                $("#denunciante_tabla, #denunciado_tabla, #victima_tabla").slideUp();

                $('#denunciante_tabla_body, #denunciado_tabla_body, #victima_tabla_body').empty();

                $("#actividad_id, #solicitud_asunto").val('');

                $(form_1)[0].reset();
                break;
            // === BUSQUEDA CASO ===
            case 50:
                var valor1 = new Array();
                valor1[0]  = 30;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 31;
                utilitarios(valor1);

                var concatenar_valores = 'tipo=100&_token=' + csrf_token;

                var caso_id = valor[1];

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(caso_id) != ''){
                    concatenar_valores += '&caso_id=' + caso_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += 'Debe de seleccionar un CASO.';
                }

                if(valor_sw){
                    swal({
                        title             : "BUSCANDO CASO",
                        text              : "Espere a que busque el caso.",
                        allowEscapeKey    : false,
                        showConfirmButton : false,
                        type              : "info"
                    });
                    $(".sweet-alert div.sa-info").removeClass("sa-icon sa-info").addClass("fa fa-refresh fa-4x fa-spin");

                    var valor1 = new Array();
                    valor1[0]  = 150;
                    valor1[1]  = url_controller + '/send_ajax';
                    valor1[2]  = 'POST';
                    valor1[3]  = true;
                    valor1[4]  = concatenar_valores;
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                    valor1[2]  = valor_error;
                    utilitarios(valor1);
                }
                break;
            // === PROCESO DE VERIFICACION ===
            case 51:
                var concatenar_valores = '?tipo=2';

                var actividad_id   = $("#actividad_id").val();
                var personas   = $("input[name='persona_select[]']:checked").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(actividad_id) == ''){
                    valor_sw    = false;
                    valor_error = 'La ACTIVIDAD es obligatorio.';
                }

                if(personas == undefined){
                    valor_sw    = false;
                    valor_error = 'Por lo menos a una persona se debe de NOTIFICAR.';
                }

                if(valor_sw){
                    swal({
                        title             : "ENVIANDO INFORMACIÓN",
                        text              : "Espere a que guarde la información.",
                        allowEscapeKey    : false,
                        showConfirmButton : false,
                        type              : "info"
                    });
                    $(".sweet-alert div.sa-info").removeClass("sa-icon sa-info").addClass("fa fa-refresh fa-4x fa-spin");

                    var valor1 = new Array();
                    valor1[0]  = 150;
                    valor1[1]  = url_controller + '/send_ajax';
                    valor1[2]  = 'POST';
                    valor1[3]  = true;
                    valor1[4]  = $(form_1).serialize();
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                    valor1[2]  = valor_error;
                    utilitarios(valor1);
                }
                break;
            // === LLENAR TABLA 1 ===
            case 90:
                var respuesta = "";
                var c = 1;
                $.each(valor[1], function(index, value) {
                    respuesta += '<tr>';
                    respuesta += '<td class="text-right">' + c++ + '</td>';
                    respuesta += '<td class="text-center">' + value.Fecha + '</td>';
                    respuesta += '<td>' + value.TipoActividad + '</td>';

                    var actividad = "";
                    if(value.Actividad != null){
                        actividad = value.Actividad;
                    }
                    respuesta += '<td>' + actividad + '</td>';

                    var sw_tupla   = false;
                    var sw_tupla_1 = true;

                    @if(in_array(['codigo' => '2502'], $permisos) AND $i4_funcionario_id != '')
                        if(value.estado_notificacion == 1){
                            respuesta += '<td class="text-center">';
                            respuesta += '<button type="button" class="btn btn-xs btn-primary" title="Notificar" onclick="utilitarios([10, ' + value.id + ', \'' + value.TipoActividad + '\', \'' + valor[2] + '\']);">';
                            respuesta += '<i class="fa fa-envelope"></i>';
                            respuesta += '</button>';
                            respuesta += '</td>';

                            sw_tupla_1 = false;
                        }

                        sw_tupla = true;
                    @endif

                    @if(in_array(['codigo' => '2503'], $permisos) AND $i4_funcionario_id != '')
                        if(value.Notificaciones == 1){
                            respuesta += '<td class="text-center">';
                            respuesta += '<button type="button" class="btn btn-xs btn-danger" title="Eliminar notificación" onclick="utilitarios([70, ' + value.id + ']);">';
                            respuesta += '<i class="fa fa-trash"></i>';
                            respuesta += '</button>';

                            respuesta += '</td>';

                            sw_tupla_1 = false;
                        }
                        sw_tupla = true;
                    @endif

                    if(sw_tupla){
                        if(sw_tupla_1){
                            respuesta += '<td class="text-center">';
                                    respuesta += '</td>';
                        }
                    }

                    respuesta += '</tr>';
                });
                return respuesta;
                break;
            // === LLENAR FORMULARIO 1 ===
            case 91:
                var valor1 = new Array();
                valor1[0]  = 31;
                utilitarios(valor1);

                var respuesta         = "";
                var denunciado_table  = "";
                var denunciante_table = "";
                var victima_table     = "";
                var denunciado_c      = 0;
                var denunciante_c     = 0;
                var victima_c         = 0;

                // >=2
                var ubicacion_sw = 0;
                var ubicacion    = "";

                // >=4
                var ubicacion_abogado_sw = 0;
                var ubicacion_abogado    = "";

                var c = 1;

                $.each(valor[1], function(index, value) {
                    // === UBICACION PERSONA ===
                        if(value.DirDom == null){
                            ubicacion += '<span class="label label-danger font-sm">SIN DIRECCION</span>';
                        }
                        else{
                            ubicacion += value.DirDom;
                            ubicacion_sw++;
                        }

                        if(value.ZonaDom == null){
                            ubicacion += ' , <span class="label label-danger font-sm">SIN ZONA</span>';
                        }
                        else{
                            ubicacion += " , " + value.ZonaDom;
                            ubicacion_sw++;
                        }

                        if(value.TelDom == null){
                            ubicacion += ' , <span class="label label-warning font-sm">SIN TELEFONO</span>';
                        }
                        else{
                            ubicacion += " , " + value.TelDom;
                        }

                        if(value.CelularDom == null){
                            ubicacion += ' , <span class="label label-warning font-sm">SIN CELULAR</span>';
                        }
                        else{
                            ubicacion += " , " + value.CelularDom;
                        }

                    // === UBICACION ABOGADO ===
                        if(value.abogado_id != null){
                            if(value.abogado == null){
                                ubicacion_abogado += '<span class="label label-warning font-sm">SIN NOMBRE</span>';
                            }
                            else{
                                ubicacion_abogado += value.abogado;
                            }

                            if(value.abogado_DirDom == null){
                                ubicacion_abogado += '<br><span class="label label-danger font-sm">SIN DIRECCION</span>';
                            }
                            else{
                                ubicacion_abogado += '<br>' + value.abogado_DirDom;
                                ubicacion_abogado_sw++;
                            }

                            if(value.abogado_ZonaDom == null){
                                ubicacion_abogado += ' , <span class="label label-danger font-sm">SIN ZONA</span>';
                            }
                            else{
                                ubicacion_abogado += " , " + value.abogado_ZonaDom;
                                ubicacion_abogado_sw++;
                            }

                            if(value.abogado_TelDom == null){
                                ubicacion_abogado += ' , <span class="label label-warning font-sm">SIN TELEFONO</span>';
                            }
                            else{
                                ubicacion_abogado += " , " + value.abogado_TelDom;
                            }

                            if(value.abogado_CelularDom == null){
                                ubicacion_abogado += ' , <span class="label label-danger font-sm">SIN CELULAR</span>';
                            }
                            else{
                                ubicacion_abogado += " , " + value.abogado_CelularDom;
                                ubicacion_abogado_sw++;
                            }

                            if(value.abogado_EMailPrivado == null){
                                ubicacion_abogado += ' , <span class="label label-danger font-sm">SIN CORREO ELECTRONICO</span>';
                            }
                            else{
                                ubicacion_abogado += " , " + value.abogado_EMailPrivado;
                                ubicacion_abogado_sw++;
                            }
                        }
                        else{
                            ubicacion_abogado += '<span class="label label-danger font-sm">SIN ABOGADO</span>';
                        }

                    if(value.EsDenunciado == 1){
                        denunciado_table += '<tr>';

                        denunciado_table += '<td class="text-center">';
                        if(ubicacion_sw == 2 || ubicacion_abogado_sw == 4){
                            denunciado_table += '<input type="checkbox" class="denunciado_class" name="persona_select[]" value="' + value.id + '">';
                        }
                        denunciado_table += '</td>';

                        denunciado_table += '<td class="text-center">' + value.Persona + '</td>';

                        denunciado_table += '<td class="text-center">' + ubicacion + '</td>';

                        denunciado_table += '<td class="text-center">' + ubicacion_abogado + '</td>';

                        denunciado_table += '</tr>';

                        denunciado_c++;
                    }
                    else if(value.EsDenunciante == 1){
                        denunciante_table += '<tr>';

                                denunciante_table += '<td class="text-center">';
                        if(ubicacion_sw == 2 || ubicacion_abogado_sw == 4){
                            denunciante_table += '<input type="checkbox" class="denunciante_class" name="persona_select[]" value="' + value.id + '">';
                        }
                        denunciante_table += '</td>';

                        denunciante_table += '<td class="text-center">' + value.Persona + '</td>';

                        denunciante_table += '<td class="text-center">' + ubicacion + '</td>';

                        denunciante_table += '<td class="text-center">' + ubicacion_abogado + '</td>';

                        denunciante_table += '</tr>';

                        denunciante_c++;
                    }
                    else if(value.EsVictima == 1){
                        victima_table += '<tr>';

                        victima_table += '<td class="text-center">';
                        if(ubicacion_sw == 2 || ubicacion_abogado_sw == 4){
                            victima_table += '<input type="checkbox" class="victima_class" name="persona_select[]" value="' + value.id + '">';
                        }
                        victima_table += '</td>';

                        victima_table += '<td class="text-center">' + value.Persona + '</td>';

                        victima_table += '<td class="text-center">' + ubicacion + '</td>';

                        victima_table += '<td class="text-center">' + ubicacion_abogado + '</td>';

                        victima_table += '</tr>';

                        victima_c++;
                    }

                    ubicacion_sw = 0;
                    ubicacion    = "";

                    ubicacion_abogado_sw = 0;
                    ubicacion_abogado    = "";
                });

                if(denunciado_c > 0){
                    $('#denunciado_tabla_body').append(denunciado_table);
                    $("#denunciado_tabla").slideDown();
                }

                if(denunciante_c > 0){
                    $('#denunciante_tabla_body').append(denunciante_table);
                    $("#denunciante_tabla").slideDown();
                }

                if(victima_c > 0){
                    $('#victima_tabla_body').append(victima_table);
                    $("#victima_tabla").slideDown();
                }

                return respuesta;
                break;
            // === MENSAJE ERROR ===
            case 100:
                toastr.success(valor[2], valor[1], options1);
                break;
            // === MENSAJE ERROR ===
            case 101:
                toastr.error(valor[2], valor[1], options1);
                break;
            // === AJAX ===
            case 150:
                var respuesta_ajax = false;
                $.ajax({
                    url     : valor[1],
                    type    : valor[2],
                    async   : valor[3],
                    data    : valor[4],
                    dataType: valor[5],
                    success : function(data){
                        switch(data.tipo){
                            // === INSERT UPDATE ===
                            case '1':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $('#modal_1').modal('hide');
                                }
                                else if(data.sw === 0){
                                    if(data.error_sw === 1){
                                        var valor1 = new Array();
                                        valor1[0]  = 101;
                                        valor1[1]  = data.titulo;
                                        valor1[2]  = data.respuesta;
                                        utilitarios(valor1);
                                    }
                                    else if(data.error_sw === 2){
                                        var respuesta_server = '';
                                        $.each(data.error.response.original, function(index, value) {
                                            respuesta_server += value + '<br>';
                                        });
                                        var valor1 = new Array();
                                        valor1[0]  = 101;
                                        valor1[1]  = data.titulo;
                                        valor1[2]  = respuesta_server;
                                        utilitarios(valor1);
                                    }

                                    $('#modal_1').modal('hide');
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === BUSCANDO CASO ===
                            case '100':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $('#caso_b').append(data.cosulta1.Caso);
                                    $('#etapa_caso_b').append(data.cosulta1.etapa_caso);
                                    $('#origen_caso_b').append(data.cosulta1.origen_caso);
                                    $('#estado_caso_b').append(data.cosulta1.estado_caso);
                                    $('#f_denuncia_b').append(data.cosulta1.FechaDenuncia);
                                    $('#delito_principal_b').append(data.cosulta1.delito_principal);

                                    if(data.sw_1 === 1){
                                        $('#fiscal_asignado_b').append(data.cosulta2.funcionario);
                                    }

                                    if(data.sw_2 === 1){
                                        var actividad_tabla = ""

                                        var valor1 = new Array();
                                        valor1[0]  = 90;
                                        valor1[1]  = data.cosulta3;
                                        valor1[2]  = data.cosulta1.Caso;
                                        actividad_tabla = utilitarios(valor1);

                                        $('#actividad_tabla_b').append(actividad_tabla);
                                    }

                                    if(data.sw_3 === 1){
                                        $('#denunciante_b').append(data.cosulta4.denunciante);
                                    }

                                    if(data.sw_4 === 1){
                                        $('#denunciado_b').append(data.cosulta5.denunciado);
                                    }

                                    if(data.sw_6 === 1){
                                        var valor1 = new Array();
                                        valor1[0]  = 91;
                                        valor1[1]  = data.cosulta6;
                                        utilitarios(valor1);
                                    }

                                    $("#caso_id_1").val(data.cosulta1.id);
                                }
                                else if(data.sw === 0){
                                    var valor1 = new Array();
                                    valor1[0]  = 101;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            default:
                                break;
                        }
                    },
                    error: function(result) {
                        alert(result.responseText);
                        window.location.reload();
                        //console.error("Este callback maneja los errores", result);
                    }
                });
                return respuesta_ajax;
                break;
            default:
                break;
        }
    }
</script>
<script>
    // === PLUGINS ===
        // $.fn.steps.reset = function () {
        //     var wizard = this,
        //     options    = getOptions(this),
        //     state      = getState(this);
        //     goToStep(wizard, options, state, 0);

        //     for (i = 1; i < state.stepCount; i++) {
        //         var stepAnchor = getStepAnchor(wizard, i);
        //         stepAnchor.parent().removeClass("done")._enableAria(false);
        //     }
        // };

        // $.fn.steps.reset=function(){var wizard=this,options=getOptions(this),state=getState(this);goToStep(wizard,options,state,0);for(i=1;i<state.stepCount;i++){var stepAnchor=getStepAnchor(wizard, i);stepAnchor.parent().removeClass("done")._enableAria(false);}};

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
        var base_url       = "{!! url('') !!}";
        var url_controller = "{!! url('/solicitud_dpvt') !!}";
        var csrf_token     = "{!! csrf_token() !!}";
        var public_dir     = "{!! asset($public_dir) !!}";
        var uso_step       = true;

    // === FORMULARIOS ===
        var form_1 = "#form_1";

    // === JQGRID ===
        var jqgrid1  = "#jqgrid1";
        var pjqgrid1 = "#pjqgrid1";

        var jqgrid2  = "#jqgrid2";
        var pjqgrid2 = "#pjqgrid2";

        var jqgrid3  = "#jqgrid3";
        var pjqgrid3 = "#pjqgrid3";

        var jqgrid4  = "#jqgrid4";
        var pjqgrid4 = "#pjqgrid4";

    // === ESTADO ===
        var estado_json   = $.parseJSON('{!! json_encode($estado_array) !!}');
        var estado_select = '';
        var estado_jqgrid = ':Todos';

        $.each(estado_json, function(index, value) {
            estado_select += '<option value="' + index + '">' + value + '</option>';
            estado_jqgrid += ';' + index + ':' + value;
        });

    // === CERRADO ABIERTO ===
        var cerrado_abierto_json   = $.parseJSON('{!! json_encode($cerrado_abierto_array) !!}');
        var cerrado_abierto_select = '';
        var cerrado_abierto_jqgrid = ':Todos';

        $.each(cerrado_abierto_json, function(index, value) {
            cerrado_abierto_select += '<option value="' + index + '">' + value + '</option>';
            cerrado_abierto_jqgrid += ';' + index + ':' + value;
        });

    // === SOLICITANTE ===
        var solicitante_json   = $.parseJSON('{!! json_encode($solicitante_array) !!}');
        var solicitante_select = '';
        var solicitante_jqgrid = ':Todos';

        $.each(solicitante_json, function(index, value) {
            solicitante_select += '<option value="' + index + '">' + value + '</option>';
            solicitante_jqgrid += ';' + index + ':' + value;
        });

    // === ETAPA PROCESO ===
        var etapa_proceso_json   = $.parseJSON('{!! json_encode($etapa_proceso_array) !!}');
        var etapa_proceso_select = '';
        var etapa_proceso_jqgrid = ':Todos';

        $.each(etapa_proceso_json, function(index, value) {
            etapa_proceso_select += '<option value="' + index + '">' + value + '</option>';
            etapa_proceso_jqgrid += ';' + index + ':' + value;
        });

    // === ESTADO PDF ===
        var estado_pdf_json   = $.parseJSON('{!! json_encode($estado_pdf_array) !!}');
        var estado_pdf_select = '';
        var estado_pdf_jqgrid = ':Todos';

        $.each(estado_pdf_json, function(index, value) {
            estado_pdf_select += '<option value="' + index + '">' + value + '</option>';
            estado_pdf_jqgrid += ';' + index + ':' + value;
        });

    // === USUARIO TIPO ===
        var usuario_tipo_json   = $.parseJSON('{!! json_encode($usuario_tipo_array) !!}');
        var usuario_tipo_select = '';
        var usuario_tipo_jqgrid = ':Todos';

        $.each(usuario_tipo_json, function(index, value) {
            usuario_tipo_select += '<option value="' + index + '">' + value + '</option>';
            usuario_tipo_jqgrid += ';' + index + ':' + value;
        });

    // === SEXO ===
        var sexo_json   = $.parseJSON('{!! json_encode($sexo_array) !!}');
        var sexo_select = '';
        var sexo_jqgrid = ':Todos';

        $.each(sexo_json, function(index, value) {
            sexo_select += '<option value="' + index + '">' + value + '</option>';
            sexo_jqgrid += ';' + index + ':' + value;
        });

    // === EDAD ===
        var edad_json   = $.parseJSON('{!! json_encode($edad_array) !!}');
        var edad_select = '';
        var edad_jqgrid = ':Todos';

        $.each(edad_json, function(index, value) {
            edad_select += '<option value="' + index + '">' + value + '</option>';
            edad_jqgrid += ';' + index + ':' + value;
        });

    // === DIRIGIDO A ===
        var dirigido_a_json   = $.parseJSON('{!! json_encode($dirigido_a_array) !!}');
        var dirigido_a_select = '';
        var dirigido_a_jqgrid = ':Todos';

        $.each(dirigido_a_json, function(index, value) {
            dirigido_a_select += '<option value="' + index + '">' + value + '</option>';
            dirigido_a_jqgrid += ';' + index + ':' + value;
        });

    // === DIRIGIDO PSICOLOGIA ===
        var dirigido_psicologia_json   = $.parseJSON('{!! json_encode($dirigido_psicologia_array) !!}');
        var dirigido_psicologia_select = '';
        var dirigido_psicologia_jqgrid = ':Todos';

        $.each(dirigido_psicologia_json, function(index, value) {
            dirigido_psicologia_select += '<option value="' + index + '">' + value + '</option>';
            dirigido_psicologia_jqgrid += ';' + index + ':' + value;
        });

    // === DIRIGIDO TRABAJO SOCIAL ===
        var dirigido_trabajo_social_json   = $.parseJSON('{!! json_encode($dirigido_trabajo_social_array) !!}');
        var dirigido_trabajo_social_select = '';
        var dirigido_trabajo_social_jqgrid = ':Todos';

        $.each(dirigido_trabajo_social_json, function(index, value) {
            dirigido_trabajo_social_select += '<option value="' + index + '">' + value + '</option>';
            dirigido_trabajo_social_jqgrid += ';' + index + ':' + value;
        });

    // === RESOLUCION TIPO DISPOSICION ===
        var resolucion_tipo_disposicion_json   = $.parseJSON('{!! json_encode($resolucion_tipo_disposicion_array) !!}');
        var resolucion_tipo_disposicion_select = '';
        var resolucion_tipo_disposicion_jqgrid = ':Todos';

        $.each(resolucion_tipo_disposicion_json, function(index, value) {
            resolucion_tipo_disposicion_select += '<option value="' + index + '">' + value + '</option>';
            resolucion_tipo_disposicion_jqgrid += ';' + index + ':' + value;
        });

    // === MEDIDA DE PROTECCION DISPUESTA ===
        var resolucion_mpd_json   = $.parseJSON('{!! json_encode($resolucion_mpd_array) !!}');
        var resolucion_mpd_select = '';
        var resolucion_mpd_jqgrid = ':Todos';

        $.each(resolucion_mpd_json, function(index, value) {
            resolucion_mpd_select += '<option value="' + index + '">' + value + '</option>';
            resolucion_mpd_jqgrid += ';' + index + ':' + value;
        });

    // === CONTADOR DE GESTIONES ===
        var anio_filter = '';
        var anio_filter_jqgrid = ':Todos';
        var gestion_i = {!! $gestion_i !!};
        var gestion_f = {!! $gestion_f !!};
        for (var i = gestion_i; i <= gestion_f; i++){
            anio_filter        += '<option value="' + i + '">' + i + '</option>';
            anio_filter_jqgrid += ';' + i + ':' + i;
        }

    // === DROPZONE ===
        Dropzone.autoDiscover = false;

    $(document).ready(function(){
        //=== INICIALIZAR ===
            $('#anio_filter, #gestion').append(anio_filter);
            $("#anio_filter option[value=" + gestion_f +"]").attr("selected","selected");

            $('#solicitante').append(solicitante_select);

            $('#etapa_proceso').append(etapa_proceso_select);

            $('#usuario_tipo').append(usuario_tipo_select);

            $('#dirigido_a_psicologia, #dirigido_a_trabajo_social, #dirigido_a_otro_trabajo, #complementario_dirigido_a').append(dirigido_a_select);

            $('#estado').append(estado_select);

            $('#dirigido_psicologia').append(dirigido_psicologia_select);

            $('#dirigido_trabajo_social').append(dirigido_trabajo_social_select);

            $('#resolucion_tipo_disposicion').append(resolucion_tipo_disposicion_select);

            $('#resolucion_medidas_proteccion').append(resolucion_mpd_select);

        //=== WIZARDS ===
            $(form_1).steps({
                bodyTag: "fieldset",
                labels: {
                    current   : "Paso actual",
                    pagination: "Paginación",
                    finish    : "Finalizar",
                    next      : "Siguiente",
                    previous  : "Anterior",
                    loading   : "Cargando ..."
                },
                onStepChanging: function (event, currentIndex, newIndex)
                {
                    switch(currentIndex){
                        // === PASO 1 ===
                        case 0:
                            if(uso_step){
                                var concatenar_valores = '';

                                concatenar_valores += "tipo=1&_token=" + csrf_token;

                                var id                     = $("#solicitud_id").val();
                                var gestion                = $("#gestion").val();
                                var solicitante            = $("#solicitante").val();
                                var persona_id_solicitante = $("#persona_id_solicitante").val();
                                var municipio_id           = $("#municipio_id").val();
                                var f_solicitud            = $("#f_solicitud").val();
                                var n_caso                 = $("#n_caso").val();
                                var etapa_proceso          = $("#etapa_proceso").val();
                                var denunciante            = $("#denunciante").val();
                                var denunciado             = $("#denunciado").val();
                                var victima                = $("#victima").val();
                                var persona_protegida      = $("#persona_protegida").val();

                                var valor_sw    = true;
                                var valor_error = '';

                                if($.trim(gestion) != ''){
                                    concatenar_valores += '&gestion=' + gestion;
                                }
                                else{
                                    valor_sw    = false;
                                    valor_error += '<br>El campo GESTION es obligatorio.';
                                }

                                concatenar_valores += '&id=' + id;
                                concatenar_valores += '&solicitante=' + solicitante;
                                concatenar_valores += '&persona_id_solicitante=' + persona_id_solicitante;
                                concatenar_valores += '&municipio_id=' + municipio_id;
                                concatenar_valores += '&f_solicitud=' + f_solicitud;
                                concatenar_valores += '&n_caso=' + n_caso;
                                concatenar_valores += '&etapa_proceso=' + etapa_proceso;
                                concatenar_valores += '&denunciante=' + denunciante;
                                concatenar_valores += '&denunciado=' + denunciado;
                                concatenar_valores += '&victima=' + victima;
                                concatenar_valores += '&persona_protegida=' + persona_protegida;

                                if(valor_sw){
                                    swal({
                                        title             : "ENVIANDO INFORMACIÓN",
                                        text              : "Espere a que guarde la información.",
                                        allowEscapeKey    : false,
                                        showConfirmButton : false,
                                        type              : "info"
                                    });
                                    $(".sweet-alert div.sa-info").removeClass("sa-icon sa-info").addClass("fa fa-refresh fa-4x fa-spin");

                                    var valor1    = new Array();
                                    valor1[0]     = 150;
                                    valor1[1]     = url_controller + '/send_ajax';
                                    valor1[2]     = 'POST';
                                    valor1[3]     = false;
                                    valor1[4]     = concatenar_valores;
                                    valor1[5]     = 'json';
                                    var respuesta = utilitarios(valor1);

                                    return respuesta;
                                }
                                else{
                                    var valor1 = new Array();
                                    valor1[0]  = 101;
                                    valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                                    valor1[2]  = valor_error;
                                    utilitarios(valor1);

                                    return false;
                                }
                            }
                            else{
                                uso_step = true;
                                return true;
                            }
                            break;
                        // === PASO 2 ===
                        case 1:
                            var concatenar_valores = '';

                            concatenar_valores += '?tipo=2';

                            var id                       = $("#solicitud_id").val();
                            var usuario_tipo             = $("#usuario_tipo").val();
                            var usuario_tipo_descripcion = $("#usuario_tipo_descripcion").val();
                            var usuario_nombre           = $("#usuario_nombre").val();
                            var usuario_sexo             = $(".usuario_sexo_class:checked").val();
                            var usuario_celular          = $("#usuario_celular").val();
                            var usuario_domicilio        = $("#usuario_domicilio").val();
                            var usuario_otra_referencia  = $("#usuario_otra_referencia").val();
                            var usuario_edad             = $(".usuario_edad:checked").val();

                            var valor_sw    = true;
                            var valor_error = '';

                            concatenar_valores += '&id=' + id;
                            concatenar_valores += '&usuario_tipo=' + usuario_tipo;
                            concatenar_valores += '&usuario_tipo_descripcion=' + usuario_tipo_descripcion;
                            concatenar_valores += '&usuario_nombre=' + usuario_nombre;
                            concatenar_valores += '&usuario_sexo=' + usuario_sexo;
                            concatenar_valores += '&usuario_celular=' + usuario_celular;
                            concatenar_valores += '&usuario_domicilio=' + usuario_domicilio;
                            concatenar_valores += '&usuario_otra_referencia=' + usuario_otra_referencia;
                            concatenar_valores += '&usuario_edad=' + usuario_edad;

                            if(valor_sw){
                                return true;
                            }
                            else{
                                var valor1 = new Array();
                                valor1[0]  = 101;
                                valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                                valor1[2]  = valor_error;
                                utilitarios(valor1);

                                return false;
                            }
                            break;
                        // === PASO 3 ===
                        case 2:
                            var concatenar_valores = '';

                            concatenar_valores += '?tipo=3';

                            var id                        = $("#solicitud_id").val();
                            var dirigido_a_psicologia     = $("#dirigido_a_psicologia").val();
                            var dirigido_psicologia       = $("#dirigido_psicologia").val();
                            var dirigido_a_trabajo_social = $("#dirigido_a_trabajo_social").val();
                            var dirigido_trabajo_social   = $("#dirigido_trabajo_social").val();
                            var dirigido_a_otro_trabajo   = $("#dirigido_a_otro_trabajo").val();
                            var dirigido_otro_trabajo     = $("#dirigido_otro_trabajo").val();

                            var valor_sw    = true;
                            var valor_error = '';

                            concatenar_valores += '&id=' + id;
                            concatenar_valores += '&dirigido_a_psicologia=' + dirigido_a_psicologia;
                            concatenar_valores += '&dirigido_psicologia=' + dirigido_psicologia;
                            concatenar_valores += '&dirigido_a_trabajo_social=' + dirigido_a_trabajo_social;
                            concatenar_valores += '&dirigido_trabajo_social=' + dirigido_trabajo_social;
                            concatenar_valores += '&dirigido_a_otro_trabajo=' + dirigido_a_otro_trabajo;
                            concatenar_valores += '&dirigido_otro_trabajo=' + dirigido_otro_trabajo;

                            if(valor_sw){
                                return true;
                            }
                            else{
                                var valor1 = new Array();
                                valor1[0]  = 101;
                                valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                                valor1[2]  = valor_error;
                                utilitarios(valor1);

                                return false;
                            }
                            break;
                        // === PASO 4 ===
                        case 3:
                            var concatenar_valores = '';

                            concatenar_valores += '?tipo=4';

                            var id                                = $("#solicitud_id").val();
                            var estado                            = $("#estado").val();
                            var complementario_dirigido_a         = $("#complementario_dirigido_a").val();
                            var complementario_trabajo_solicitado = $("#complementario_trabajo_solicitado").val();

                            var valor_sw    = true;
                            var valor_error = '';

                            concatenar_valores += '&id=' + id;
                            concatenar_valores += '&estado=' + estado;
                            concatenar_valores += '&complementario_dirigido_a=' + complementario_dirigido_a;
                            concatenar_valores += '&complementario_trabajo_solicitado=' + complementario_trabajo_solicitado;

                            if(valor_sw){
                                return true;
                            }
                            else{
                                var valor1 = new Array();
                                valor1[0]  = 101;
                                valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                                valor1[2]  = valor_error;
                                utilitarios(valor1);

                                return false;
                            }
                            break;
                        // === PASO 5 ===
                        case 4:
                            var concatenar_valores = '';

                            concatenar_valores += '?tipo=5';

                            var id                                      = $("#solicitud_id").val();
                            var plazo_fecha_solicitud                   = $("#plazo_fecha_solicitud").val();
                            var plazo_fecha_recepcion                   = $("#plazo_fecha_recepcion").val();
                            var plazo_psicologico_fecha_entrega_digital = $("#plazo_psicologico_fecha_entrega_digital").val();
                            var plazo_psicologico_fecha_entrega_fisico  = $("#plazo_psicologico_fecha_entrega_fisico").val();
                            var plazo_social_fecha_entrega_digital      = $("#plazo_social_fecha_entrega_digital").val();
                            var plazo_social_fecha_entrega_fisico       = $("#plazo_social_fecha_entrega_fisico").val();
                            var plazo_complementario_fecha              = $("#plazo_complementario_fecha").val();

                            var valor_sw    = true;
                            var valor_error = '';

                            concatenar_valores += '&id=' + id;
                            concatenar_valores += '&plazo_fecha_solicitud=' + plazo_fecha_solicitud;
                            concatenar_valores += '&plazo_fecha_recepcion=' + plazo_fecha_recepcion;
                            concatenar_valores += '&plazo_psicologico_fecha_entrega_digital=' + plazo_psicologico_fecha_entrega_digital;
                            concatenar_valores += '&plazo_psicologico_fecha_entrega_fisico=' + plazo_psicologico_fecha_entrega_fisico;
                            concatenar_valores += '&plazo_social_fecha_entrega_digital=' + plazo_social_fecha_entrega_digital;
                            concatenar_valores += '&plazo_social_fecha_entrega_fisico=' + plazo_social_fecha_entrega_fisico;
                            concatenar_valores += '&plazo_complementario_fecha=' + plazo_complementario_fecha;

                            if(valor_sw){
                                return true;
                            }
                            else{
                                var valor1 = new Array();
                                valor1[0]  = 101;
                                valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                                valor1[2]  = valor_error;
                                utilitarios(valor1);

                                return false;
                            }
                            break;
                        default:
                            return true;
                            break;
                    }

                    // // Always allow going backward even if the current step contains invalid fields!
                    // if (currentIndex > newIndex)
                    // {
                    //     return true;
                    // }

                    // // Forbid suppressing "Warning" step if the user is to young
                    // if (newIndex === 3 && Number($("#age").val()) < 18)
                    // {
                    //     return false;
                    // }

                    // var form = $(this);

                    // // Clean up if user went backward before
                    // if (currentIndex < newIndex)
                    // {
                    //     // To remove error styles
                    //     $(".body:eq(" + newIndex + ") label.error", form).remove();
                    //     $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
                    // }

                    // // Disable validation on fields that are disabled or hidden.
                    // form.validate().settings.ignore = ":disabled,:hidden";

                    // // Start validation; Prevent going forward if false
                    // return form.valid();
                },
                onStepChanged: function (event, currentIndex, priorIndex)
                {
                    // return true;
                    // // Suppress (skip) "Warning" step if the user is old enough.
                    // if (currentIndex === 2 && Number($("#age").val()) >= 18)
                    // {
                    //     $(this).steps("next");
                    // }

                    // // Suppress (skip) "Warning" step if the user is old enough and wants to the previous step.
                    // if (currentIndex === 2 && priorIndex === 3)
                    // {
                    //     $(this).steps("previous");
                    // }
                },
                onFinishing: function (event, currentIndex)
                {
                    $('#modal_1').modal('hide');
                    // return true;
                    // var form = $(this);

                    // // Disable validation on fields that are disabled.
                    // // At this point it's recommended to do an overall check (mean ignoring only disabled fields)
                    // form.validate().settings.ignore = ":disabled";

                    // // Start validation; Prevent form submission if false
                    // return form.valid();
                },
                onFinished: function (event, currentIndex)
                {
                    // var form = $(this);

                    // // Submit form input
                    // form.submit();
                }
            });
            // $(form_1).steps(configuraciones);


        //=== SELECT2 ===
            $("#gestion, #solicitante, #etapa_proceso, #usuario_tipo, #estado").select2({
                maximumSelectionLength: 1
            });
            $("#gestion").appendTo("#gestion_div");
            $("#solicitante").appendTo("#solicitante_div");
            $("#etapa_proceso").appendTo("#etapa_proceso_div");
            $("#usuario_tipo").appendTo("#usuario_tipo_div");
            $("#estado").appendTo("#estado_div");

            $("#dirigido_a_psicologia, #dirigido_psicologia, #dirigido_a_trabajo_social, #dirigido_trabajo_social, #dirigido_a_otro_trabajo, #complementario_dirigido_a, #resolucion_tipo_disposicion, #resolucion_medidas_proteccion").select2();
            $("#dirigido_a_psicologia").appendTo("#dirigido_a_psicologia_div");
            $("#dirigido_a_trabajo_social").appendTo("#dirigido_a_trabajo_social_div");
            $("#dirigido_a_otro_trabajo").appendTo("#dirigido_a_otro_trabajo_div");
            $("#dirigido_psicologia").appendTo("#dirigido_psicologia_div");
            $("#dirigido_trabajo_social").appendTo("#dirigido_trabajo_social_div");
            $("#complementario_dirigido_a").appendTo("#complementario_dirigido_a_div");
            $("#resolucion_tipo_disposicion").appendTo("#resolucion_tipo_disposicion_div");
            $("#resolucion_medidas_proteccion").appendTo("#resolucion_medidas_proteccion_div");

            $('#persona_id_solicitante').select2({
                maximumSelectionLength: 1,
                minimumInputLength    : 2,
                ajax                  : {
                    url     : url_controller + '/send_ajax',
                    type    : 'post',
                    dataType: 'json',
                    data    : function (params) {
                        return {
                            q         : params.term,
                            page_limit: 10,
                            estado    : 1,
                            tipo      : 100,
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
            $("#persona_id_solicitante").appendTo("#persona_id_solicitante_div");

            $('#municipio_id').select2({
                maximumSelectionLength: 1,
                minimumInputLength    : 2,
                ajax                  : {
                    url     : url_controller + '/send_ajax',
                    type    : 'post',
                    dataType: 'json',
                    data    : function (params) {
                        return {
                            q         : params.term,
                            page_limit: 10,
                            estado    : 1,
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
            $("#municipio_id").appendTo("#municipio_id_div");

            $('#delito_id, #delito_id_r').select2({
                maximumSelectionLength: 1,
                minimumInputLength    : 2,
                ajax                  : {
                    url     : url_controller + '/send_ajax',
                    type    : 'post',
                    dataType: 'json',
                    data    : function (params) {
                        return {
                            q         : params.term,
                            page_limit: 10,
                            estado    : 1,
                            tipo      : 102,
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
            $("#delito_id").appendTo("#delito_id_div");

        //=== datepicker3 ===
            $('#f_solicitud, #plazo_fecha_solicitud, #plazo_fecha_recepcion, #plazo_fecha_entrega_digital, #plazo_fecha_entrega_fisico, #plazo_psicologico_fecha, #plazo_social_fecha, #plazo_complementario_fecha, #fecha_inicio, #fecha_entrega_digital, #fecha_entrega_fisico, #informe_seguimiento_fecha, #complementario_fecha, #resolucion_fecha_emision').datepicker({
                startView            : 2,
                // todayBtn          : "linked",
                // keyboardNavigation: false,
                // forceParse        : false,
                autoclose            : true,
                format               : "yyyy-mm-dd",
                startDate            : '-100y',
                endDate              : '+0d',
                language             : "es"
            });

        // === DROPZONE ===
            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_1";
            valor1[2]  = "file1";
            valor1[3]  = 11;
            valor1[4]  = 1;
            valor1[5]  = "solicitud_documento_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_2";
            valor1[2]  = "file2";
            valor1[3]  = 11;
            valor1[4]  = 2;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_3";
            valor1[2]  = "file3";
            valor1[3]  = 11;
            valor1[4]  = 3;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_4";
            valor1[2]  = "file4";
            valor1[3]  = 11;
            valor1[4]  = 4;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_5";
            valor1[2]  = "file5";
            valor1[3]  = 11;
            valor1[4]  = 5;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_6";
            valor1[2]  = "file6";
            valor1[3]  = 11;
            valor1[4]  = 6;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_7";
            valor1[2]  = "file7";
            valor1[3]  = 11;
            valor1[4]  = 7;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_8";
            valor1[2]  = "file8";
            valor1[3]  = 11;
            valor1[4]  = 8;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_9";
            valor1[2]  = "file9";
            valor1[3]  = 11;
            valor1[4]  = 9;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_10";
            valor1[2]  = "file10";
            valor1[3]  = 11;
            valor1[4]  = 10;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_11";
            valor1[2]  = "file11";
            valor1[3]  = 11;
            valor1[4]  = 11;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_12";
            valor1[2]  = "file12";
            valor1[3]  = 11;
            valor1[4]  = 12;
            valor1[5]  = "solicitud_estado_pdf";
            utilitarios(valor1);

        // === JQGRID ===
            var valor1 = new Array();
            valor1[0]  = 40;
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 41;
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 42;
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 43;
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 44;
            utilitarios(valor1);

        // === VALIDATE 1 ===
            // var valor1 = new Array();
            // valor1[0]  = 20;
            // utilitarios(valor1);

        // Add responsive to jqGrid
            $(window).bind('resize', function () {
                var width = $('.tab-content').width() - 35;
                $(jqgrid1).setGridWidth(width);
            });

            setTimeout(function(){
                $('.wrapper-content').removeClass('animated fadeInRight');
                var valor1 = new Array();
                valor1[0]  = 0;
                utilitarios(valor1);
            },300);

            $("#navbar-minimalize-button" ).on( "click", function() {
                setTimeout(function(){
                    $('.wrapper-content').removeClass('animated fadeInRight');
                    var valor1 = new Array();
                    valor1[0]  = 0;
                    utilitarios(valor1);
                },500);
            });

        // $('#modal_1').modal();

        // setTimeout(function(){
        //     $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
        //     $(jqgrid3).jqGrid('setGridWidth', $("#div_jqgrid3").width());
        //     $(jqgrid4).jqGrid('setGridWidth', $("#div_jqgrid2").width());
        // }, 300);
    });

    $(window).on('resize.jqGrid', function() {
        var valor1 = new Array();
        valor1[0]  = 0;
        utilitarios(valor1);
    });

    function utilitarios(valor){
        switch(valor[0]){
            // === JQGRID REDIMENCIONAR ===
            case 0:
                $(jqgrid1).jqGrid('setGridWidth', $(".jqGrid_wrapper").width());
                $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
                $(jqgrid3).jqGrid('setGridWidth', $("#div_jqgrid3").width());
                $(jqgrid4).jqGrid('setGridWidth', $("#div_jqgrid4").width());
                break;
            // === MODAL MEDIDAS DE PROTECCION ===
            case 10:
                $('#modal_1').modal();

                setTimeout(function(){
                    $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
                    $(jqgrid3).jqGrid('setGridWidth', $("#div_jqgrid3").width());
                }, 300);
                break;

            // === EDICION MODAL ===
            case 20:
                var valor1 = new Array();
                valor1[0]  = 30;
                utilitarios(valor1);

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $('#modal_1_title').append(' - ' + ret.codigo);
                $("#solicitud_id").val(valor[1]);

                // === SOLICITUD ===

                    $("#gestion").select2("val", ret.gestion);
                    $("#gestion").select2("enable", false);
                    $("#solicitante").select2("val", val_json.solicitante);
                    if(ret.n_documento != ""){
                        var persona = ret.n_documento + ' - ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno) + ' ' + ret.nombre_persona;

                        $('#persona_id_solicitante').append('<option value="' + val_json.persona_id_solicitante + '">' + persona + '</option>');
                        $("#persona_id_solicitante").select2("val", val_json.persona_id_solicitante);
                    }
                    if(ret.municipio != ""){
                        var dpm = ret.departamento + ', ' + ret.provincia + ', ' + ret.municipio;
                        $('#municipio_id').append('<option value="' + val_json.municipio_id + '">' + dpm + '</option>');
                        $("#municipio_id").select2("val", val_json.municipio_id);
                    }
                    $("#f_solicitud").val(ret.f_solicitud);

                    $("#n_caso").val(ret.n_caso);
                    $("#etapa_proceso").select2("val", val_json.etapa_proceso);
                    $("#denunciante").val(ret.denunciante);
                    $("#denunciado").val(ret.denunciado);
                    $("#victima").val(ret.victima);
                    $("#persona_protegida").val(ret.persona_protegida);

                // $('#modal_1_title').empty();

                // if(val_json.funcionario_id == null){
                //     $('#modal_1_title').append('Agregar información del funcionario con ' + ret.tipo_cargo);
                // }
                // else{
                //     $('#modal_1_title').append('Modificar información del funcionario con ' + ret.tipo_cargo);
                // }

                // $("#id_funcionario").val(val_json.funcionario_id);
                // $("#cargo_id").val(valor[1]);
                // $("#tipo_cargo_id").val(val_json.tipo_cargo_id);

                // if(val_json.situacion != ''){
                //     $(".situacion_class[value=" + val_json.situacion + "]").prop('checked', true);
                // }

                // if(ret.n_documento != ""){
                //     var persona = ret.n_documento + ' - ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno) + ' ' + ret.nombre_persona;

                //     $('#persona_id').append('<option value="' + val_json.persona_id + '">' + persona + '</option>');
                //     $("#persona_id").select2("val", val_json.persona_id);
                // }

                // $("#f_ingreso").val(ret.f_ingreso);
                // $("#f_salida").val(ret.f_salida);
                // $("#sueldo").val(ret.sueldo);
                // $("#observaciones").val(ret.observaciones);

                // if(ret.lugar_dependencia_funcionario != ""){
                //     $("#lugar_dependencia_id_funcionario").select2("val", val_json.lugar_dependencia_id_funcionario);

                //     $("#unidad_desconcentrada_id").select2("val", val_json.unidad_desconcentrada_id);

                //     $("#horario_id_1").select2("val", val_json.horario_id_1);
                //     $("#horario_id_2").select2("val", val_json.horario_id_2);
                // }

                // if(ret.cargo != ""){
                //     $("#lugar_dependencia_id_cargo").select2("val", val_json.lugar_dependencia_id_cargo);

                //     $('#auo_id').append('<option value="' + val_json.auo_id + '">' + ret.auo_cargo + '</option>');
                //     $("#auo_id").select2("val", val_json.auo_id);

                //     $('#cargo_id_d').append('<option value="' + valor[1] + '">' + ret.cargo + '</option>');
                //     $("#cargo_id_d").select2("val", valor[1]);
                // }

                // $("#lugar_dependencia_id_cargo").select2("enable", false);
                // $("#auo_id").select2("enable", false);
                // $("#cargo_id_d").select2("enable", false);

                var valor1 = new Array();
                valor1[0]  = 10;
                utilitarios(valor1);
                break;

            // === RESETEAR FORMULARIO ===
            case 30:
                $('#modal_1_title').empty();

                $("#solicitud_id").val('');

                // === SOLICITUD ===
                    $('#gestion').select2("val", "");
                    $("#gestion").select2("enable", true);
                    $('#solicitante').select2("val", "");
                    $('#persona_id_solicitante').select2("val", "");
                    $('#persona_id_solicitante option').remove();
                    $('#municipio_id').select2("val", "");
                    $('#municipio_id option').remove();

                    $('#etapa_proceso').select2("val", "");

                // $("#solicitud_id").val('');

                // $("#cargo_id").val('');
                // $("#tipo_cargo_id").val('');

                // $('#persona_id').select2("val", "");
                // $('#persona_id option').remove();

                // $('#lugar_dependencia_id_funcionario').select2("val", "");

                // $('#unidad_desconcentrada_id').select2("val", "");
                // $('#unidad_desconcentrada_id option').remove();

                // $('#lugar_dependencia_id_cargo').select2("val", "");

                // $('#auo_id').select2("val", "");
                // $('#auo_id option').remove();

                // $('#cargo_id_d').select2("val", "");
                // $('#cargo_id_d option').remove();

                $(form_1)[0].reset();

                uso_step = false;
                $(form_1).steps('reset');
                break;

            // === JQGRID 1 ===
            case 40:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1903'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid1).jqGrid({
                    // caption     : title_table,
                    url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
                    datatype    : 'json',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid1,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'pvt_solicitudes.codigo',
                    sortorder   : "desc",
                    viewrecords : true,
                    shrinkToFit : false,
                    hidegrid    : false,
                    multiboxonly: true,
                    altRows     : true,
                    rownumbers  : true,
                    // subGrid     : subgrid_sw,
                    // multiselect  : true,
                    //autowidth     : true,
                    //gridview      :true,
                    //forceFit      : true,
                    //toolbarfilter : true,
                    colNames : [
                        "",

                        "ESTADO",
                        "ABIERTO/CERRADO",
                        "GESTION",
                        "CODIGO",

                        "SOLICITANTE",
                        "C.I.",
                        "NOMBRE(S)",
                        "AP. PATERNO",
                        "AP. MATERNO",
                        "MUNICIPIO",
                        "PROVINCIA",
                        "DEPARTAMENTO",
                        "SOLICITUD",
                        "¿CON PDF?",

                        "N° DE CASO",
                        "ETAPA DEL PROCESO",
                        "DENUNCIANTE",
                        "DENUNCIADO",
                        "VICTIMA",
                        "PERSONA PROTEGIDA",

                        "DELITO",
                        "RECALIFICACION DEL DELITO",

                        ""
                    ],
                    colModel : [
                        {
                            name    : "act",
                            index   : "",
                            width   : ancho1,
                            align   : "center",
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : edit1
                        },

                        {
                            name       : "estado",
                            index      : "pvt_solicitudes.estado",
                            width      : 200,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:estado_jqgrid}
                        },
                        {
                            name       : "cerrado_abierto",
                            index      : "pvt_solicitudes.cerrado_abierto",
                            width      : 135,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:cerrado_abierto_jqgrid}
                        },
                        {
                            name       : "gestion",
                            index      : "pvt_solicitudes.gestion",
                            width      : 90,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:anio_filter_jqgrid}
                        },
                        {
                            name : "codigo",
                            index: "pvt_solicitudes.codigo",
                            width: 80,
                            align: "center"
                        },

                        {
                            name       : "solicitante",
                            index      : "pvt_solicitudes.solicitante",
                            width      : 300,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:solicitante_jqgrid}
                        },
                        {
                            name : "n_documento",
                            index: "a2.n_documento",
                            width: 80,
                            align: "right"
                        },
                        {
                            name : "nombre_persona",
                            index: "a2.nombre",
                            width: 180,
                            align: "center"
                        },
                        {
                            name : "ap_paterno",
                            index: "a2.ap_paterno",
                            width: 150,
                            align: "center"
                        },
                        {
                            name : "ap_materno",
                            index: "a2.ap_materno",
                            width: 150,
                            align: "center"
                        },
                        {
                            name : "municipio",
                            index: "a3.nombre",
                            width: 150,
                            align: "center"
                        },
                        {
                            name : "provincia",
                            index: "a4.nombre",
                            width: 150,
                            align: "center"
                        },
                        {
                            name : "departamento",
                            index: "a5.nombre",
                            width: 150,
                            align: "center"
                        },
                        {
                            name : "f_solicitud",
                            index: "pvt_solicitudes.f_solicitud::text",
                            width: 100,
                            align: "center"
                        },
                        {
                            name       : "solicitud_estado_pdf",
                            index      : "pvt_solicitudes.solicitud_estado_pdf",
                            width      : 80,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:estado_pdf_jqgrid}
                        },


                        {
                            name : "n_caso",
                            index: "rrhh_salidas.n_caso",
                            width: 150,
                            align: "center"
                        },
                        {
                            name       : "etapa_proceso",
                            index      : "pvt_solicitudes.etapa_proceso",
                            width      : 150,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:etapa_proceso_jqgrid}
                        },
                        {
                            name : "denunciante",
                            index: "pvt_solicitudes.denunciante",
                            width: 300,
                            align: "center"
                        },
                        {
                            name : "denunciado",
                            index: "pvt_solicitudes.denunciado",
                            width: 300,
                            align: "center"
                        },
                        {
                            name : "victima",
                            index: "pvt_solicitudes.victima",
                            width: 300,
                            align: "center"
                        },
                        {
                            name : "persona_protegida",
                            index: "pvt_solicitudes.persona_protegida",
                            width: 300,
                            align: "center"
                        },

                        {
                            name : "delitos",
                            index: "pvt_solicitudes.delitos",
                            width: 500,
                            align: "center"
                        },
                        {
                            name : "recalificacion_delitos",
                            index: "pvt_solicitudes.recalificacion_delitos",
                            width: 500,
                            align: "center"
                        },

                        // === OCULTO ===
                            {
                                name  : "val_json",
                                index : "",
                                width : 10,
                                align : "center",
                                search: false,
                                hidden: true
                            }
                    ],
                    loadComplete : function(){
                        $("tr.jqgrow:odd").addClass('myAltRowClass');
                    },
                    gridComplete : function() {
                        var ids = $(jqgrid1).jqGrid('getDataIDs');
                        for(var i = 0; i < ids.length; i++){
                            var cl       = ids[i];
                            var ret      = $(jqgrid1).jqGrid('getRowData', cl);
                            var val_json = $.parseJSON(ret.val_json);

                            var ed = "";
                            @if(in_array(['codigo' => '1903'], $permisos))
                                ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed)
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'n_documento',
                            numberOfColumns: 4,
                            titleText      : 'PERSONA SOLICITANTE'
                        },
                        {
                            startColumnName: 'municipio',
                            numberOfColumns: 3,
                            titleText      : 'UBICACION'
                        }
                    ]
                });

                $(jqgrid1).jqGrid('filterToolbar',{
                    searchOnEnter : true,
                    stringResult  : true,
                    defaultSearch : 'cn'
                });

                $(jqgrid1).jqGrid('navGrid', pjqgrid1, {
                    edit  : false,
                    add   : false,
                    del   : false,
                    search: false
                })
                @if(in_array(['codigo' => '1902'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "add1",
                        caption       : "",
                        title         : 'Agregar nueva fila',
                        buttonicon    : "ui-icon ui-icon-plusthick",
                        onClickButton : function(){
                            var valor1 = new Array();
                            valor1[0]  = 30;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 10;
                            utilitarios(valor1);
                        }
                    })
                @endif
                ;
                break;
            // === JQGRID 2 ===
            case 41:
                $(jqgrid2).jqGrid({
                    caption     : '',
                    datatype    : 'local',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid2,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'pvt_delitos.created_at',
                    sortorder   : "desc",
                    viewrecords : true,
                    shrinkToFit : false,
                    hidegrid    : false,
                    multiboxonly: true,
                    altRows     : true,
                    rownumbers  : true,
                    // subGrid     : subgrid_sw,
                    // multiselect  : true,
                    //autowidth     : true,
                    //gridview      :true,
                    //forceFit      : true,
                    //toolbarfilter : true,
                    colNames :[
                        "",
                        "DELITO",
                        "TENTATIVA",
                        ""
                    ],
                    colModel : [
                        {
                            name    : "act",
                            index   : "",
                            width   : 34,
                            align   : "center",
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : false
                        },
                        {
                            name  : "nombre",
                            index : "pvt_delitos.nombre",
                            width : 700,
                            align : "center"
                        },
                        {
                            name       : "tentativa",
                            index      : "a2.tentativa",
                            width      : 90,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:estado_pdf_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name: 'val_json',
                                index: '',
                                width: 10,
                                search: false,
                                hidden: true
                            }
                    ],
                    loadComplete: function(){
                        $("tr.jqgrow:odd").addClass('myAltRowClass');
                    }
                });

                // $(jqgrid2).jqGrid('filterToolbar',{
                //     searchOnEnter : true,
                //     stringResult  : true,
                //     defaultSearch : 'cn'
                // });

                $(jqgrid2).jqGrid('navGrid', pjqgrid2, {
                    edit  : false,
                    add   : false,
                    del   : false,
                    search: false
                })
                .navSeparatorAdd(pjqgrid2,{
                    sepclass : "ui-separator"
                })
                ;
                break;
            // === JQGRID 3 ===
            case 42:
                $(jqgrid3).jqGrid({
                    caption     : '',
                    datatype    : 'local',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid3,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'pvt_delitos.created_at',
                    sortorder   : "desc",
                    viewrecords : true,
                    shrinkToFit : false,
                    hidegrid    : false,
                    multiboxonly: true,
                    altRows     : true,
                    rownumbers  : true,
                    // subGrid     : subgrid_sw,
                    // multiselect  : true,
                    //autowidth     : true,
                    //gridview      :true,
                    //forceFit      : true,
                    //toolbarfilter : true,
                    colNames :[
                        "",
                        "DELITO",
                        "TENTATIVA",
                        ""
                    ],
                    colModel : [
                        {
                            name    : "act",
                            index   : "",
                            width   : 34,
                            align   : "center",
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : false
                        },
                        {
                            name  : "nombre",
                            index : "pvt_delitos.nombre",
                            width : 700,
                            align : "center"
                        },
                        {
                            name       : "tentativa",
                            index      : "a2.tentativa",
                            width      : 90,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:estado_pdf_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name: 'val_json',
                                index: '',
                                width: 10,
                                search: false,
                                hidden: true
                            }
                    ],
                    loadComplete: function(){
                        $("tr.jqgrow:odd").addClass('myAltRowClass');
                    }
                });

                $(jqgrid3).jqGrid('navGrid', pjqgrid3, {
                    edit  : false,
                    add   : false,
                    del   : false,
                    search: false
                })
                .navSeparatorAdd(pjqgrid3,{
                    sepclass : "ui-separator"
                })
                ;
                break;
            // === JQGRID 4 ===
            case 44:
                $(jqgrid4).jqGrid({
                    caption     : '',
                    datatype    : 'local',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid4,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'pvt_resoluciones.created_at',
                    sortorder   : "desc",
                    viewrecords : true,
                    shrinkToFit : false,
                    hidegrid    : false,
                    multiboxonly: true,
                    altRows     : true,
                    rownumbers  : true,
                    // subGrid     : subgrid_sw,
                    // multiselect  : true,
                    //autowidth     : true,
                    //gridview      :true,
                    //forceFit      : true,
                    //toolbarfilter : true,
                    colNames :[
                        "",
                        "RESOLUCION",
                        "EMISION",
                        "¿PDF?",
                        "TIPO DE DISPOSICION",
                        "MEDIDA DE PROTECCION DISPUESTA",
                        "OTRA MEDIDA",
                        "INSTITUCION COADYUVANTE",
                        "¿PDF?",

                        "FECHA DE INICIO",
                        "ENTREGA DIGITAL",
                        "ENTREGA FISICO",
                        "INFORME SEGUIMIENTO",
                        "¿PDF?",
                        "INFORME COMPLEMENTARIO",
                        "¿PDF?",

                        ""
                    ],
                    colModel : [
                        {
                            name    : "act",
                            index   : "",
                            width   : 34,
                            align   : "center",
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : false
                        },
                        {
                            name  : "resolucion_descripcion",
                            index : "pvt_resoluciones.resolucion_descripcion",
                            width : 300,
                            align : "center"
                        },
                        {
                            name       : "resolucion_fecha_emision",
                            index      : "pvt_resoluciones.resolucion_fecha_emision",
                            width      : 100,
                            align      : "center"
                        },
                        {
                            name       : "resolucion_estado_pdf",
                            index      : "pvt_resoluciones.resolucion_estado_pdf",
                            width      : 90,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:estado_pdf_jqgrid}
                        },
                        {
                            name  : "resolucion_tipo_disposicion",
                            index : "pvt_resoluciones.resolucion_tipo_disposicion_1",
                            width : 200,
                            align : "left"
                        },
                        {
                            name  : "resolucion_medidas_proteccion",
                            index : "pvt_resoluciones.resolucion_medidas_proteccion_1",
                            width : 300,
                            align : "left"
                        },
                        {
                            name  : "resolucion_otra_medidas_proteccion",
                            index : "pvt_resoluciones.resolucion_otra_medidas_proteccion",
                            width : 300,
                            align : "left"
                        },
                        {
                            name  : "resolucion_instituciones_coadyuvantes",
                            index : "pvt_resoluciones.resolucion_instituciones_coadyuvantes",
                            width : 300,
                            align : "left"
                        },
                        {
                            name       : "resolucion_estado_pdf_2",
                            index      : "pvt_resoluciones.resolucion_estado_pdf_2",
                            width      : 90,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:estado_pdf_jqgrid}
                        },

                        {
                            name       : "fecha_inicio",
                            index      : "pvt_resoluciones.fecha_inicio",
                            width      : 100,
                            align      : "center"
                        },
                        {
                            name       : "fecha_entrega_digital",
                            index      : "pvt_resoluciones.fecha_entrega_digital",
                            width      : 100,
                            align      : "center"
                        },
                        {
                            name       : "fecha_entrega_fisico",
                            index      : "pvt_resoluciones.fecha_entrega_fisico",
                            width      : 100,
                            align      : "center"
                        },
                        {
                            name       : "informe_seguimiento_fecha",
                            index      : "pvt_resoluciones.informe_seguimiento_fecha",
                            width      : 100,
                            align      : "center"
                        },
                        {
                            name       : "informe_seguimiento_estado_pdf",
                            index      : "pvt_resoluciones.informe_seguimiento_estado_pdf",
                            width      : 90,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:estado_pdf_jqgrid}
                        },
                        {
                            name       : "complementario_fecha",
                            index      : "pvt_resoluciones.complementario_fecha",
                            width      : "100",
                            align      : "center"
                        },
                        {
                            name       : "complementario_estado_pdf",
                            index      : "pvt_resoluciones.complementario_estado_pdf",
                            width      : 90,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:estado_pdf_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : 'val_json',
                                index : '',
                                width : 10,
                                search: false,
                                hidden: true
                            }
                    ],
                    loadComplete: function(){
                        $("tr.jqgrow:odd").addClass('myAltRowClass');
                    }
                });

                $(jqgrid4).jqGrid('navGrid', pjqgrid4, {
                    edit  : false,
                    add   : false,
                    del   : false,
                    search: false
                })
                .navSeparatorAdd(pjqgrid4,{
                    sepclass : "ui-separator"
                })
                ;
                break;

            // === DROPZONE 1 ===
            case 51:
                $(valor[1]).dropzone({
                    url              : url_controller + "/send_ajax",
                    method           :'post',
                    addRemoveLinks   : true,
                    maxFilesize      : 5, // MB
                    dictResponseError: "Ha ocurrido un error en el server.",
                    acceptedFiles    :'application/pdf',
                    paramName        : valor[2], // The name that will be used to transfer the file
                    maxFiles         :1,
                    clickable        :true,
                    parallelUploads  :1,
                    params           : {
                        tipo     : valor[3],
                        tipo_file: valor[4],
                        col_name : valor[5],
                        file_name: valor[2],
                        _token   : csrf_token
                    },
                    // forceFallback:true,
                    createImageThumbnails: true,
                    maxThumbnailFilesize : 1,
                    autoProcessQueue     :true,

                    dictRemoveFile:'Eliminar',
                    dictCancelUpload:'Cancelar',
                    dictCancelUploadConfirmation:'¿Confirme la cancelación?',
                    dictDefaultMessage: "<strong>Arrastra el documento PDF aquí o haz clic para subir.</strong>",
                    dictFallbackMessage:'Su navegador no soporta arrastrar y soltar la carga de archivos.',
                    dictFallbackText:'Utilice el formulario de reserva de abajo para subir tus archivos, como en los viejos tiempos.',
                    dictInvalidFileType:'El archivo no coincide con los tipos de archivo permitidos.',
                    dictFileTooBig:'El archivo es demasiado grande.',
                    dictMaxFilesExceeded:'Número máximo de archivos superado.',
                    init: function(){
                        this.on("sending", function(file, xhr, formData){
                            formData.append("solicitud_id", $("#solicitud_id").val());
                        });
                    },
                    success: function(file, response){
                        var data = $.parseJSON(response);
                        if(data.sw === 1){
                            var valor1 = new Array();
                            valor1[0]  = 100;
                            valor1[1]  = data.titulo;
                            valor1[2]  = data.respuesta;
                            utilitarios(valor1);

                            $(jqgrid1).trigger("reloadGrid");
                            // if(data.iu === 1){
                            //     var valor1 = new Array();
                            //     valor1[0]  = 14;
                            //     utilitarios(valor1);
                            // }
                            // else if(data.iu === 2){
                            //     $('#modal_1').modal('hide');
                            // }
                        }
                        else if(data.sw === 0){
                            if(data.error_sw === 1){
                                var valor1 = new Array();
                                valor1[0]  = 101;
                                valor1[1]  = data.titulo;
                                valor1[2]  = data.respuesta;
                                utilitarios(valor1);
                            }
                            else
                            {
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
                        }
                        else if(data.sw === 2){
                            window.location.reload();
                        }
                        this.removeAllFiles(true);
                    }
                });
                break;

            // === GUARDAR DELITO ===
            case 70:
                alert("70");
                break;
            // === GUARDAR RECALIFICACION DEL DELITO ===
            case 71:
                alert("71");
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

                                    $(jqgrid1).trigger("reloadGrid");

                                    if(data.iu === 1){
                                        $("#solicitud_id").val(data.id);
                                        $("#gestion").select2("enable", false);

                                        $('#modal_1_title').append(' - ' + data.codigo);
                                    }
                                    // else if(data.iu === 2){
                                    //     $('#modal_1').modal('hide');
                                    // }

                                    // if(data.iu === 1){
                                    //     var valor1 = new Array();
                                    //     valor1[0]  = 14;
                                    //     utilitarios(valor1);
                                    // }
                                    // else if(data.iu === 2){
                                    //     $('#modal_1').modal('hide');
                                    // }

                                    respuesta_ajax = true;
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
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === VALIDAR PERSONA POR EL SEGIP ===
                            case '2':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");
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
                            // === CERTIFICACION SEGIP ===
                            case '3':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $('#div_pdf').empty();
                                    $('#div_pdf').append('<object id="object_pdf" data="data:application/pdf;base64,' + data.pdf + '" type="application/pdf" style="min-height:500px;width:100%"></object>');

                                    $('#modal_2').modal();
                                    setTimeout(function(){
                                        $("#object_pdf").css("height", $( window ).height()-150 + 'px');
                                    }, 300);
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
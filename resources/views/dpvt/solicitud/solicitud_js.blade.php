<script>
    // === PLUGINS ===

    // === CONSTANTES NO TOCAR ===
        var options1 = {
            "closeButton"      : true,
            "debug"            : false,
            "progressBar"      : true,
            "preventDuplicates": false,
            "positionClass"    : "toast-top-left",
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
        var public_url     = "{!! asset($public_url) !!}";
        var uso_step       = true;

    // === FORMULARIOS ===
        var form_1 = "#form_1";
        var form_2 = "#form_2";

    // === JQGRID ===
        var jqgrid1  = "#jqgrid1";
        var pjqgrid1 = "#pjqgrid1";

        var jqgrid2  = "#jqgrid2";
        var pjqgrid2 = "#pjqgrid2";

        var jqgrid3  = "#jqgrid3";
        var pjqgrid3 = "#pjqgrid3";

        var jqgrid4  = "#jqgrid4";
        var pjqgrid4 = "#pjqgrid4";

        var jqgrid5  = "#jqgrid5";
        var pjqgrid5 = "#pjqgrid5";

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
            $('#anio_filter, #gestion, #gestion_2').append(anio_filter);
            $("#anio_filter option[value=" + gestion_f +"]").attr("selected","selected");

            $('#solicitante').append(solicitante_select);

            $('#etapa_proceso').append(etapa_proceso_select);

            $('#usuario_tipo').append(usuario_tipo_select);

            $('#dirigido_a_psicologia, #dirigido_a_trabajo_social, #dirigido_a_otro_trabajo').append(dirigido_a_select);

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
                onStepChanging: function (event, currentIndex, newIndex){
                    switch(currentIndex){
                        // === PASO 1 ===
                        case 0:
                            if(uso_step){
                                var concatenar_valores = '';

                                concatenar_valores += "tipo=1&_token=" + csrf_token;

                                var id                 = $("#solicitud_id").val();
                                var gestion            = $("#gestion").val();
                                var solicitante        = $("#solicitante").val();
                                var nombre_solicitante = $("#nombre_solicitante").val();
                                var municipio_id       = $("#municipio_id").val();
                                var f_solicitud        = $("#f_solicitud").val();
                                var n_caso             = $("#n_caso").val();
                                var etapa_proceso      = $("#etapa_proceso").val();
                                var denunciante        = $("#denunciante").val();
                                var denunciado         = $("#denunciado").val();
                                var victima            = $("#victima").val();
                                var persona_protegida  = $("#persona_protegida").val();

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
                                // concatenar_valores += '&persona_id_solicitante=' + persona_id_solicitante;
                                concatenar_valores += '&nombre_solicitante=' + nombre_solicitante;
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

                            concatenar_valores += "tipo=2&_token=" + csrf_token;

                            var id                       = $("#solicitud_id").val();
                            var usuario_tipo             = $("#usuario_tipo").val();
                            var usuario_tipo_descripcion = $("#usuario_tipo_descripcion").val();
                            var usuario_nombre           = $("#usuario_nombre").val();
                            var usuario_sexo             = $(".usuario_sexo_class:checked").val();
                            var usuario_celular          = $("#usuario_celular").val();
                            var usuario_domicilio        = $("#usuario_domicilio").val();
                            var usuario_otra_referencia  = $("#usuario_otra_referencia").val();
                            var usuario_edad             = $(".usuario_edad_class:checked").val();

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
                            break;
                        // === PASO 3 ===
                        case 2:
                            var concatenar_valores = '';

                            concatenar_valores += "tipo=3&_token=" + csrf_token;

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

                                setTimeout(function(){
                                    $(jqgrid5).jqGrid('setGridWidth', $("#div_jqgrid5").width());
                                }, 300);
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
                            break;
                        // === PASO 4 ===
                        case 3:
                            var concatenar_valores = '';

                            concatenar_valores += "tipo=4&_token=" + csrf_token;

                            var id     = $("#solicitud_id").val();
                            var estado = $("#estado").val();

                            var valor_sw    = true;
                            var valor_error = '';

                            concatenar_valores += '&id=' + id;
                            concatenar_valores += '&estado=' + estado;

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
                            break;
                        // === PASO 5 ===
                        case 4:
                            var concatenar_valores = '';

                            concatenar_valores += "tipo=5&_token=" + csrf_token;

                            var id                                      = $("#solicitud_id").val();
                            var plazo_fecha_solicitud                   = $("#plazo_fecha_solicitud").val();
                            var plazo_psicologico_fecha_entrega_digital = $("#plazo_psicologico_fecha_entrega_digital").val();
                            var plazo_social_fecha_entrega_digital      = $("#plazo_social_fecha_entrega_digital").val();
                            var plazo_complementario_fecha              = $("#plazo_complementario_fecha").val();

                            var valor_sw    = true;
                            var valor_error = '';

                            concatenar_valores += '&id=' + id;
                            concatenar_valores += '&plazo_fecha_solicitud=' + plazo_fecha_solicitud;
                            concatenar_valores += '&plazo_psicologico_fecha_entrega_digital=' + plazo_psicologico_fecha_entrega_digital;
                            concatenar_valores += '&plazo_social_fecha_entrega_digital=' + plazo_social_fecha_entrega_digital;
                            concatenar_valores += '&plazo_complementario_fecha=' + plazo_complementario_fecha;

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

                                setTimeout(function(){
                                    $(jqgrid4).jqGrid('setGridWidth', $("#div_jqgrid4").width());
                                }, 300);
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
                            break;
                        default:
                            return true;
                            break;
                    }
                },
                onStepChanged: function (event, currentIndex, priorIndex){
                },
                onFinishing: function (event, currentIndex){
                    $('#modal_1').modal('hide');
                    utilitarios([1]);
                    return true;
                },
                onFinished: function (event, currentIndex){
                }
            });
            // $(form_1).steps(configuraciones);

        //=== SELECT2 ===
            $("#gestion, #solicitante, #etapa_proceso, #estado, #gestion_2").select2({
                maximumSelectionLength: 1
            });
            $("#gestion").appendTo("#gestion_div");
            $("#solicitante").appendTo("#solicitante_div");
            $("#etapa_proceso").appendTo("#etapa_proceso_div");
            $("#estado").appendTo("#estado_div");
            $("#gestion_2").appendTo("#gestion_2_div");

            $("#usuario_tipo, #dirigido_a_psicologia, #dirigido_psicologia, #dirigido_a_trabajo_social, #dirigido_trabajo_social, #dirigido_a_otro_trabajo, #resolucion_tipo_disposicion, #resolucion_medidas_proteccion").select2();
            $("#usuario_tipo").appendTo("#usuario_tipo_div");
            $("#dirigido_a_psicologia").appendTo("#dirigido_a_psicologia_div");
            $("#dirigido_a_trabajo_social").appendTo("#dirigido_a_trabajo_social_div");
            $("#dirigido_a_otro_trabajo").appendTo("#dirigido_a_otro_trabajo_div");
            $("#dirigido_psicologia").appendTo("#dirigido_psicologia_div");
            $("#dirigido_trabajo_social").appendTo("#dirigido_trabajo_social_div");
            $("#resolucion_tipo_disposicion").appendTo("#resolucion_tipo_disposicion_div");
            $("#resolucion_medidas_proteccion").appendTo("#resolucion_medidas_proteccion_div");

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
            $("#delito_id_r").appendTo("#delito_id_r_div");

        //=== datepicker3 ===
            $('#f_solicitud, #plazo_fecha_solicitud, #plazo_psicologico_fecha_entrega_digital, #plazo_psicologico_fecha_entrega_fisico, #plazo_psicologico_fecha, #plazo_social_fecha_entrega_digital, #plazo_social_fecha_entrega_fisico, #plazo_complementario_fecha, #fecha_inicio, #fecha_entrega_digital, #fecha_entrega_fisico, #informe_seguimiento_fecha, #complementario_fecha, #resolucion_fecha_emision, #f_solicitud_2_del, #f_solicitud_2_al').datepicker({
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
            valor1[5]  = "dirigido_psicologia_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_3";
            valor1[2]  = "file3";
            valor1[3]  = 11;
            valor1[4]  = 3;
            valor1[5]  = "dirigido_trabajo_social_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_4";
            valor1[2]  = "file4";
            valor1[3]  = 11;
            valor1[4]  = 4;
            valor1[5]  = "dirigido_otro_trabajo_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 52;
            valor1[1]  = "#dropzone_5";
            valor1[2]  = "file5";
            valor1[3]  = 13;
            valor1[4]  = 1;
            valor1[5]  = "complementario_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_6";
            valor1[2]  = "file6";
            valor1[3]  = 11;
            valor1[4]  = 6;
            valor1[5]  = "plazo_psicologico_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_7";
            valor1[2]  = "file7";
            valor1[3]  = 11;
            valor1[4]  = 7;
            valor1[5]  = "plazo_social_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 51;
            valor1[1]  = "#dropzone_8";
            valor1[2]  = "file8";
            valor1[3]  = 11;
            valor1[4]  = 8;
            valor1[5]  = "plazo_complementario_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 53;
            valor1[1]  = "#dropzone_9";
            valor1[2]  = "file9";
            valor1[3]  = 15;
            valor1[4]  = 1;
            valor1[5]  = "resolucion_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 53;
            valor1[1]  = "#dropzone_10";
            valor1[2]  = "file10";
            valor1[3]  = 15;
            valor1[4]  = 2;
            valor1[5]  = "resolucion_archivo_pdf_2";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 53;
            valor1[1]  = "#dropzone_11";
            valor1[2]  = "file11";
            valor1[3]  = 15;
            valor1[4]  = 3;
            valor1[5]  = "informe_seguimiento_archivo_pdf";
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 53;
            valor1[1]  = "#dropzone_12";
            valor1[2]  = "file12";
            valor1[3]  = 15;
            valor1[4]  = 4;
            valor1[5]  = "complementario_archivo_pdf";
            utilitarios(valor1);

        // === CHANGE SELECT GESTION ===
            $("#anio_filter").on("change", function(){
                if(this.value != ''){
                    $(jqgrid1).jqGrid('setGridParam',{
                        url: url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1&anio_filter=' + this.value,
                        datatype: 'json'
                    }).trigger('reloadGrid');
                }
                else{
                    $(jqgrid1).jqGrid('setGridParam',{
                        url: url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
                        datatype: 'json'
                    }).trigger('reloadGrid');
                }
            });

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

            var valor1 = new Array();
            valor1[0]  = 45;
            utilitarios(valor1);

        // === VALIDATE 1 ===

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
            case 1:
                $(form_1).steps('reset');
                break;
            // === MODAL MEDIDAS DE PROTECCION ===
            case 10:
                $('#modal_1').modal();

                setTimeout(function(){
                    $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
                    $(jqgrid3).jqGrid('setGridWidth', $("#div_jqgrid3").width());
                }, 300);
                break;
            // === MODAL MEDIDAS DE PROTECCION ===
            case 11:
                $('#modal_2').modal();
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
                    $("#solicitante").select2("val", val_json.solicitante);
                    $("#nombre_solicitante").val(ret.nombre_solicitante);
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

                // === USUARIO ===
                    if(val_json.usuario_tipo != null){
                        var usuario_tipo       = val_json.usuario_tipo;
                        var usuario_tipo_array = usuario_tipo.split(",");
                        $("#usuario_tipo").select2().val(usuario_tipo_array).trigger("change");
                    }
                    $("#usuario_tipo_descripcion").val(val_json.usuario_tipo_descripcion);
                    $("#usuario_nombre").val(val_json.usuario_nombre);
                    if(val_json.usuario_sexo != "null"){
                        $(".usuario_sexo_class[value=" + val_json.usuario_sexo + "]").prop('checked', true);
                    }

                    $("#usuario_celular").val(val_json.usuario_celular);
                    $("#usuario_domicilio").val(val_json.usuario_domicilio);
                    $("#usuario_otra_referencia").val(val_json.usuario_otra_referencia);
                    if(val_json.usuario_edad != "null"){
                        $(".usuario_edad_class[value=" + val_json.usuario_edad + "]").prop('checked', true);
                    }

                // === SOLICITUD DE TRABAJO ===
                    if(val_json.dirigido_a_psicologia != null){
                        var dirigido_a_psicologia       = val_json.dirigido_a_psicologia;
                        var dirigido_a_psicologia_array = dirigido_a_psicologia.split(",");
                        $("#dirigido_a_psicologia").select2().val(dirigido_a_psicologia_array).trigger("change");
                    }
                    if(val_json.dirigido_psicologia != null){
                        var dirigido_psicologia       = val_json.dirigido_psicologia;
                        var dirigido_psicologia_array = dirigido_psicologia.split(",");
                        $("#dirigido_psicologia").select2().val(dirigido_psicologia_array).trigger("change");
                    }

                    if(val_json.dirigido_a_trabajo_social != null){
                        var dirigido_a_trabajo_social       = val_json.dirigido_a_trabajo_social;
                        var dirigido_a_trabajo_social_array = dirigido_a_trabajo_social.split(",");
                        $("#dirigido_a_trabajo_social").select2().val(dirigido_a_trabajo_social_array).trigger("change");
                    }
                    if(val_json.dirigido_trabajo_social != null){
                        var dirigido_trabajo_social       = val_json.dirigido_trabajo_social;
                        var dirigido_trabajo_social_array = dirigido_trabajo_social.split(",");
                        $("#dirigido_trabajo_social").select2().val(dirigido_trabajo_social_array).trigger("change");
                    }

                    if(val_json.dirigido_a_otro_trabajo != null){
                        var dirigido_a_otro_trabajo       = val_json.dirigido_a_otro_trabajo;
                        var dirigido_a_otro_trabajo_array = dirigido_a_otro_trabajo.split(",");
                        $("#dirigido_a_otro_trabajo").select2().val(dirigido_a_otro_trabajo_array).trigger("change");
                    }
                    $("#dirigido_otro_trabajo").val(val_json.dirigido_otro_trabajo);

                // === SOLICITUD TRABAJO COMPLEMENTARIO ===
                    $("#estado").select2("val", val_json.estado);

                // === PRESENTACION DE INFORMES ===
                    $("#plazo_fecha_solicitud").val(val_json.plazo_fecha_solicitud);
                    $("#plazo_psicologico_fecha_entrega_digital").val(val_json.plazo_psicologico_fecha_entrega_digital);
                    $("#plazo_social_fecha_entrega_digital").val(val_json.plazo_social_fecha_entrega_digital);
                    $("#plazo_complementario_fecha").val(val_json.plazo_complementario_fecha);

                var valor1 = new Array();
                valor1[0]  = 31;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 32;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 33;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 34;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 10;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 411;
                valor1[1]  = valor[1];
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 421;
                valor1[1]  = valor[1];
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 441;
                valor1[1]  = valor[1];
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 451;
                valor1[1]  = valor[1];
                utilitarios(valor1);
                break;
            // === EDICION - SOLICITUD TRABAJO COMPLEMENTARIO ===
            case 21:
                var valor1 = new Array();
                valor1[0]  = 34;
                utilitarios(valor1);

                var ret      = $(jqgrid5).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $("#solicitud_complementaria_id").val(valor[1]);
                $("#complementario_dirigido_a").val(ret.complementario_dirigido_a);
                $("#complementario_trabajo_solicitado").val(ret.complementario_trabajo_solicitado);
                break;
            // === EDICION - RESOLUCIONES DEL MP Y SEGUIMIENTO ===
            case 22:
                var valor1 = new Array();
                valor1[0]  = 33;
                utilitarios(valor1);

                var ret      = $(jqgrid4).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $("#resolucion_id").val(valor[1]);
                $("#resolucion_descripcion").val(ret.resolucion_descripcion);
                $("#resolucion_fecha_emision").val(ret.resolucion_fecha_emision);
                if(val_json.resolucion_tipo_disposicion != null){
                    var resolucion_tipo_disposicion       = val_json.resolucion_tipo_disposicion;
                    var resolucion_tipo_disposicion_array = resolucion_tipo_disposicion.split(",");
                    $("#resolucion_tipo_disposicion").select2().val(resolucion_tipo_disposicion_array).trigger("change");
                }
                if(val_json.resolucion_medidas_proteccion != null){
                    var resolucion_medidas_proteccion       = val_json.resolucion_medidas_proteccion;
                    var resolucion_medidas_proteccion_array = resolucion_medidas_proteccion.split(",");
                    $("#resolucion_medidas_proteccion").select2().val(resolucion_medidas_proteccion_array).trigger("change");
                }
                $("#resolucion_otra_medidas_proteccion").val(ret.resolucion_otra_medidas_proteccion);
                $("#resolucion_instituciones_coadyuvantes").val(ret.resolucion_instituciones_coadyuvantes);


                $("#fecha_inicio").val(ret.fecha_inicio);
                $("#fecha_entrega_digital").val(ret.fecha_entrega_digital);
                $("#informe_seguimiento_fecha").val(ret.informe_seguimiento_fecha);
                $("#complementario_fecha").val(ret.complementario_fecha);
                break;

            // === RESETEAR - FORMULARIO ===
            case 30:
                $('#modal_1_title').empty();

                $("#solicitud_id").val('');

                // === SOLICITUD ===
                    $('#gestion').select2("val", "");
                    $("#gestion").select2("enable", true);
                    $('#solicitante').select2("val", "");
                    $('#municipio_id').select2("val", "");
                    $('#municipio_id option').remove();

                    $('#etapa_proceso').select2("val", "");

                // === USUARIO ===
                    $('#usuario_tipo').select2("val", "");

                // === SOLICITUD DE TRABAJO ===
                    $('#dirigido_a_psicologia').select2("val", "");
                    $('#dirigido_psicologia').select2("val", "");

                    $('#dirigido_a_trabajo_social').select2("val", "");
                    $('#dirigido_trabajo_social').select2("val", "");

                    $('#dirigido_a_otro_trabajo').select2("val", "");

                // === SOLICITUD TRABAJO COMPLEMENTARIO ===
                    $('#estado').select2("val", "");

                $(form_1)[0].reset();

                var valor1 = new Array();
                valor1[0]  = 412;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 422;
                utilitarios(valor1);

                uso_step = false;
                $(form_1).steps('reset');
                break;
            // === RESETEAR - DELITO ===
            case 31:
                $('#delito_id').select2("val", "");
                $('#delito_id option').remove();
                $('#tentativa').prop('checked', false);
                break;
            // === RESETEAR - RECALIFICACION DEL DELITO ===
            case 32:
                $('#delito_id_r').select2("val", "");
                $('#delito_id_r option').remove();
                $('#tentativa_r').prop('checked', false);
                break;
            // === RESETEAR - RESOLUCIONES DEL MP Y SEGUIMIENTO ===
            case 33:
                $("#resolucion_id").val('');
                $("#resolucion_descripcion").val('');
                $("#resolucion_fecha_emision").val('');
                $('#resolucion_tipo_disposicion').select2("val", "");
                $('#resolucion_medidas_proteccion').select2("val", "");
                $("#resolucion_otra_medidas_proteccion").val('');
                $("#resolucion_instituciones_coadyuvantes").val('');

                $("#fecha_inicio").val('');
                $("#fecha_entrega_digital").val('');
                $("#informe_seguimiento_fecha").val('');
                $("#complementario_fecha").val('');
                break;
            // === RESETEAR - SOLICITUD TRABAJO COMPLEMENTARIO ===
            case 34:
                $("#solicitud_complementaria_id").val('');
                $("#complementario_dirigido_a").val('');
                $("#complementario_trabajo_solicitado").val('');
                break;
            // === RESETEAR - REPORTE DE MEDIDAS DE PROTECCION ===
            case 35:
                $('#modal_2_title').append('REPORTE DE MEDIDAS DE PROTECCION');
                $('#gestion_2').select2("val", "");
                $(form_2)[0].reset();
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
                @if(in_array(['codigo' => '1905'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid1).jqGrid({
                    // caption     : title_table,
                    url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1&anio_filter=' + $("#anio_filter").val(),
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
                        "NOMBRE DEL SOLICITANTE",
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
                        "USUARIO",

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
                            width      : 250,
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
                            name : "nombre_solicitante",
                            index: "pvt_solicitudes.nombre_solicitante",
                            width: 300,
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
                                if(val_json.cerrado_abierto == 1){
                                    ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                                }
                            @endif

                            var cer1 = "";
                            @if(in_array(['codigo' => '1905'], $permisos))
                                if(val_json.cerrado_abierto == 1){
                                    cer1 = " <button type='button' class='btn btn-xs btn-warning' title='Editar fila' onclick=\"utilitarios([80, " + cl + "]);\"><i class='fa fa-lock'></i></button>";
                                }
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed + cer1)
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
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
                            valor1[0]  = 31;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 32;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 33;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 34;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 10;
                            utilitarios(valor1);
                        }
                    })
                @endif
                @if(in_array(['codigo' => '1904'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "print1",
                        caption       : "",
                        title         : 'Reportes',
                        buttonicon    : "ui-icon ui-icon-print",
                        onClickButton : function(){
                            $('#modal_2_title').empty();

                            var valor1 = new Array();
                            valor1[0]  = 35;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 11;
                            utilitarios(valor1);
                        }
                    })
                @endif
                ;
                break;
            // === JQGRID 2 ===
            case 41:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1903'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid2).jqGrid({
                    caption     : '',
                    datatype    : 'local',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid2,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'pvt_solicitudes_delitos.created_at',
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
                            width   : ancho1,
                            align   : "center",
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : edit1
                        },
                        {
                            name  : "nombre",
                            index : "a2.nombre",
                            width : 1000,
                            align : "center"
                        },
                        {
                            name       : "tentativa",
                            index      : "pvt_solicitudes_delitos.tentativa",
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
                    },
                    gridComplete : function() {
                        var ids = $(jqgrid2).jqGrid('getDataIDs');
                        for(var i = 0; i < ids.length; i++){
                            var cl       = ids[i];

                            var del1 = "";
                            @if(in_array(['codigo' => '1903'], $permisos))
                                del1 = "<button type='button' class='btn btn-xs btn-danger' title='Eliminar fila' onclick=\"utilitarios([701, " + cl + "]);\"><i class='fa fa-trash'></i></button>";
                            @endif

                            $(jqgrid2).jqGrid('setRowData', ids[i], {
                                act : $.trim(del1)
                            });
                        }
                    }
                });

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
            // === JQGRID 2 - RELOAD ===
            case 411:
                $(jqgrid2).jqGrid('setGridParam',{
                    url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=2&solicitud_id=' + valor[1],
                    datatype: 'json'
                }).trigger('reloadGrid');
                break;
            // === JQGRID 2 - RELOAD SIN VALOR ===
            case 412:
                $(jqgrid2).jqGrid('setGridParam',{
                    datatype: 'local'
                }).trigger('reloadGrid');
                break;
            // === JQGRID 3 ===
            case 42:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1903'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid3).jqGrid({
                    caption     : '',
                    datatype    : 'local',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid3,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'pvt_solicitudes_delitos.created_at',
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
                            width   : ancho1,
                            align   : "center",
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : edit1
                        },
                        {
                            name  : "nombre",
                            index : "a2.nombre",
                            width : 1000,
                            align : "center"
                        },
                        {
                            name       : "tentativa",
                            index      : "pvt_solicitudes_delitos.tentativa",
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
                    },
                    gridComplete : function() {
                        var ids = $(jqgrid3).jqGrid('getDataIDs');
                        for(var i = 0; i < ids.length; i++){
                            var cl       = ids[i];

                            var del1 = "";
                            @if(in_array(['codigo' => '1903'], $permisos))
                                del1 = "<button type='button' class='btn btn-xs btn-danger' title='Eliminar fila' onclick=\"utilitarios([711, " + cl + "]);\"><i class='fa fa-trash'></i></button>";
                            @endif

                            $(jqgrid3).jqGrid('setRowData', ids[i], {
                                act : $.trim(del1)
                            });
                        }
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
            // === JQGRID 3 - RELOAD ===
            case 421:
                $(jqgrid3).jqGrid('setGridParam',{
                    url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=3&solicitud_id=' + valor[1],
                    datatype: 'json'
                }).trigger('reloadGrid');
                break;
            // === JQGRID 3 - RELOAD SIN VALOR ===
            case 422:
                $(jqgrid3).jqGrid('setGridParam',{
                    datatype: 'local'
                }).trigger('reloadGrid');
                break;
            // === JQGRID 4 ===
            case 44:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1903'], $permisos))
                    edit1  = false;
                    ancho1 += 2 * ancho_d;
                @endif

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
                        "FECHA DE ENTREGA",
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
                            width   : ancho1,
                            align   : "center",
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : edit1
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
                            width : 300,
                            align : "left"
                        },
                        {
                            name  : "resolucion_medidas_proteccion",
                            index : "pvt_resoluciones.resolucion_medidas_proteccion_1",
                            width : 500,
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
                            name       : "informe_seguimiento_fecha",
                            index      : "pvt_resoluciones.informe_seguimiento_fecha",
                            width      : 150,
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
                            width      : 150,
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
                    },
                    gridComplete : function() {
                        var ids = $(jqgrid4).jqGrid('getDataIDs');
                        for(var i = 0; i < ids.length; i++){
                            var cl       = ids[i];

                            var edi1 = "";
                            var del1 = "";
                            @if(in_array(['codigo' => '1903'], $permisos))
                                edi1 = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([22, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            @endif

                            @if(in_array(['codigo' => '1903'], $permisos))
                                del1 = " <button type='button' class='btn btn-xs btn-danger' title='Eliminar fila' onclick=\"utilitarios([731, " + cl + "]);\"><i class='fa fa-trash'></i></button>";
                            @endif

                            $(jqgrid4).jqGrid('setRowData', ids[i], {
                                act : $.trim(edi1 + del1)
                            });
                        }
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
            // === JQGRID 4 - RELOAD ===
            case 441:
                $(jqgrid4).jqGrid('setGridParam',{
                    url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=4&solicitud_id=' + valor[1],
                    datatype: 'json'
                }).trigger('reloadGrid');
                break;
            // === JQGRID 4 - RELOAD SIN VALOR ===
            case 442:
                $(jqgrid4).jqGrid('setGridParam',{
                    datatype: 'local'
                }).trigger('reloadGrid');
                break;
            // === JQGRID 5 ===
            case 45:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1903'], $permisos))
                    edit1  = false;
                    ancho1 += 2 * ancho_d;
                @endif

                $(jqgrid5).jqGrid({
                    caption     : '',
                    datatype    : 'local',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid5,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'pvt_solicitudes_complementarias.created_at',
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
                        "¿CON PDF?",
                        "DIRIGIDO A",
                        "TRABAJO SOLICITADO",
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
                            name       : "complementario_estado_pdf",
                            index      : "pvt_solicitudes_complementarias.complementario_estado_pdf",
                            width      : 90,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:estado_pdf_jqgrid}
                        },
                        {
                            name  : "complementario_dirigido_a",
                            index : "pvt_solicitudes_complementarias.complementario_dirigido_a",
                            width : 500,
                            align : "center"
                        },
                        {
                            name  : "complementario_trabajo_solicitado",
                            index : "pvt_solicitudes_complementarias.complementario_trabajo_solicitado",
                            width : 500,
                            align : "center"
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
                    },
                    gridComplete : function() {
                        var ids = $(jqgrid5).jqGrid('getDataIDs');
                        for(var i = 0; i < ids.length; i++){
                            var cl       = ids[i];

                            var edi1 = "";
                            var del1 = "";
                            @if(in_array(['codigo' => '1903'], $permisos))
                                edi1 = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([21, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            @endif

                            @if(in_array(['codigo' => '1903'], $permisos))
                                del1 = " <button type='button' class='btn btn-xs btn-danger' title='Eliminar fila' onclick=\"utilitarios([721, " + cl + "]);\"><i class='fa fa-trash'></i></button>";
                            @endif

                            $(jqgrid5).jqGrid('setRowData', ids[i], {
                                act : $.trim(edi1 + del1)
                            });
                        }
                    }
                });

                $(jqgrid5).jqGrid('navGrid', pjqgrid5, {
                    edit  : false,
                    add   : false,
                    del   : false,
                    search: false
                })
                .navSeparatorAdd(pjqgrid5,{
                    sepclass : "ui-separator"
                })
                ;
                break;
            // === JQGRID 5 - RELOAD ===
            case 451:
                $(jqgrid5).jqGrid('setGridParam',{
                    url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=5&solicitud_id=' + valor[1],
                    datatype: 'json'
                }).trigger('reloadGrid');
                break;
            // === JQGRID 5 - RELOAD SIN VALOR ===
            case 452:
                $(jqgrid5).jqGrid('setGridParam',{
                    datatype: 'local'
                }).trigger('reloadGrid');
                break;

            // === DROPZONE 1 ===
            case 51:
                $(valor[1]).dropzone({
                    url              : url_controller + "/send_ajax",
                    method           :'post',
                    addRemoveLinks   : true,
                    maxFilesize      : 20, // MB
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
            // === DROPZONE 2 ===
            case 52:
                $(valor[1]).dropzone({
                    url              : url_controller + "/send_ajax",
                    method           :'post',
                    addRemoveLinks   : true,
                    maxFilesize      : 20, // MB
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

                    dictRemoveFile              :'Eliminar',
                    dictCancelUpload            :'Cancelar',
                    dictCancelUploadConfirmation:'¿Confirme la cancelación?',
                    dictDefaultMessage          : "<strong>Arrastra el documento PDF aquí o haz clic para subir.</strong>",
                    dictFallbackMessage         :'Su navegador no soporta arrastrar y soltar la carga de archivos.',
                    dictFallbackText            :'Utilice el formulario de reserva de abajo para subir tus archivos, como en los viejos tiempos.',
                    dictInvalidFileType         :'El archivo no coincide con los tipos de archivo permitidos.',
                    dictFileTooBig              :'El archivo es demasiado grande.',
                    dictMaxFilesExceeded        :'Número máximo de archivos superado.',
                    init                        : function(){
                        this.on("sending", function(file, xhr, formData){
                            formData.append("solicitud_id", $("#solicitud_id").val());
                            formData.append("solicitud_complementaria_id", $("#solicitud_complementaria_id").val());
                            formData.append("complementario_dirigido_a", $("#complementario_dirigido_a").val());
                            formData.append("complementario_trabajo_solicitado", $("#complementario_trabajo_solicitado").val());
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

                            $("#solicitud_complementaria_id").val(data.id);

                            $(jqgrid5).trigger("reloadGrid");
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
            // === DROPZONE 3 ===
            case 53:
                $(valor[1]).dropzone({
                    url              : url_controller + "/send_ajax",
                    method           :'post',
                    addRemoveLinks   : true,
                    maxFilesize      : 20, // MB
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

                    dictRemoveFile              :'Eliminar',
                    dictCancelUpload            :'Cancelar',
                    dictCancelUploadConfirmation:'¿Confirme la cancelación?',
                    dictDefaultMessage          : "<strong>Arrastra el documento PDF aquí o haz clic para subir.</strong>",
                    dictFallbackMessage         :'Su navegador no soporta arrastrar y soltar la carga de archivos.',
                    dictFallbackText            :'Utilice el formulario de reserva de abajo para subir tus archivos, como en los viejos tiempos.',
                    dictInvalidFileType         :'El archivo no coincide con los tipos de archivo permitidos.',
                    dictFileTooBig              :'El archivo es demasiado grande.',
                    dictMaxFilesExceeded        :'Número máximo de archivos superado.',
                    init                        : function(){
                        this.on("sending", function(file, xhr, formData){
                            formData.append("solicitud_id", $("#solicitud_id").val());
                            formData.append("resolucion_id", $("#resolucion_id").val());
                            formData.append("resolucion_descripcion", $("#resolucion_descripcion").val());
                            formData.append("resolucion_fecha_emision", $("#resolucion_fecha_emision").val());
                            formData.append("resolucion_tipo_disposicion", $("#resolucion_tipo_disposicion").val());
                            formData.append("resolucion_medidas_proteccion", $("#resolucion_medidas_proteccion").val());
                            formData.append("resolucion_otra_medidas_proteccion", $("#resolucion_otra_medidas_proteccion").val());
                            formData.append("resolucion_instituciones_coadyuvantes", $("#resolucion_instituciones_coadyuvantes").val());
                            formData.append("fecha_inicio", $("#fecha_inicio").val());
                            formData.append("fecha_entrega_digital", $("#fecha_entrega_digital").val());
                            formData.append("informe_seguimiento_fecha", $("#informe_seguimiento_fecha").val());
                            formData.append("complementario_fecha", $("#complementario_fecha").val());
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

                            $(jqgrid4).trigger("reloadGrid");
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

            // === VER PDF - 1 ===
            case 60:
                var id = $("#solicitud_id").val();
                if(id != ''){
                    var ret            = $(jqgrid1).jqGrid('getRowData', id);
                    var val_json       = $.parseJSON(ret.val_json);
                    var respado_pdf_sw = true;
                    switch(valor[1]){
                        case 1:
                            if(val_json.solicitud_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.solicitud_documento_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 2:
                            if(val_json.dirigido_psicologia_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.dirigido_psicologia_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 3:
                            if(val_json.dirigido_trabajo_social_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.dirigido_trabajo_social_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 4:
                            if(val_json.dirigido_otro_trabajo_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.dirigido_otro_trabajo_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 5:
                            if(val_json.plazo_psicologico_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.plazo_psicologico_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 6:
                            if(val_json.plazo_social_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.plazo_social_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 7:
                            if(val_json.plazo_complementario_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.plazo_complementario_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                    }
                    if(respado_pdf_sw){
                        var valor1 = new Array();
                        valor1[0]  = 101;
                        valor1[1]  = '<div class="text-center"><strong>SIN RESPALDO PDF</strong></div>';
                        valor1[2]  = "No se subio ningun PDF.";
                        utilitarios(valor1);
                    }
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>SIN RESPALDO PDF</strong></div>';
                    valor1[2]  = "No existe CODIGO en la MEDIDA DE PROTECCION.";
                    utilitarios(valor1);
                }
                break;
            // === ELIMINAR PDF - 1 ===
            case 61:
                var id = $("#solicitud_id").val();
                if(id != ''){
                    var concatenar_valores = '';
                    concatenar_valores += "tipo=12&_token=" + csrf_token;
                    concatenar_valores += '&id=' + id;
                    switch(valor[1]){
                        case 1:
                            concatenar_valores += '&tipo_del=1'
                            break;
                        case 2:
                            concatenar_valores += '&tipo_del=2'
                            break;
                        case 3:
                            concatenar_valores += '&tipo_del=3'
                            break;
                        case 4:
                            concatenar_valores += '&tipo_del=4'
                            break;
                        case 5:
                            concatenar_valores += '&tipo_del=5'
                            break;
                        case 6:
                            concatenar_valores += '&tipo_del=6'
                            break;
                        case 7:
                            concatenar_valores += '&tipo_del=7'
                            break;
                    }

                    swal({
                        title             : "ENVIANDO INFORMACIÓN",
                        text              : "Eliminando PDF.",
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
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>SIN CODIGO</strong></div>';
                    valor1[2]  = "No existe CODIGO en la MEDIDA DE PROTECCION.";
                    utilitarios(valor1);
                }
                break;
            // === VER PDF - 2 ===
            case 62:
                if(valor[2] === 0){
                    var id = $("#solicitud_complementaria_id").val();
                }
                else{
                    var id = valor[2];
                }
                if(id != ''){
                    var ret            = $(jqgrid5).jqGrid('getRowData', id);
                    var val_json       = $.parseJSON(ret.val_json);
                    var respado_pdf_sw = true;
                    switch(valor[1]){
                        case 1:
                            if(val_json.complementario_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.complementario_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                    }
                    if(respado_pdf_sw){
                        var valor1 = new Array();
                        valor1[0]  = 101;
                        valor1[1]  = '<div class="text-center"><strong>SIN RESPALDO PDF</strong></div>';
                        valor1[2]  = "No se subio ningun PDF.";
                        utilitarios(valor1);
                    }
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>SIN RESPALDO PDF</strong></div>';
                    valor1[2]  = "Edite una SOLICITUD DE TRABAJO COMPLEMENTARIO.";
                    utilitarios(valor1);
                }
                break;
            // === ELIMINAR PDF - 2 ===
            case 63:
                var id = $("#solicitud_complementaria_id").val();
                if(id != ''){
                    var concatenar_valores = '';
                    concatenar_valores += "tipo=14&_token=" + csrf_token;
                    concatenar_valores += '&id=' + id;
                    switch(valor[1]){
                        case 1:
                            concatenar_valores += '&tipo_del=1'
                            break;
                    }

                    swal({
                        title             : "ENVIANDO INFORMACIÓN",
                        text              : "Eliminando PDF.",
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
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>NO SE EDITO/strong></div>';
                    valor1[2]  = "Edite una SOLICITUD DE TRABAJO COMPLEMENTARIO.";
                    utilitarios(valor1);
                }
                break;
            // === VER PDF - 3 ===
            case 64:
                if(valor[2] === 0){
                    var id = $("#resolucion_id").val();
                }
                else{
                    var id = valor[2];
                }
                if(id != ''){
                    var ret            = $(jqgrid4).jqGrid('getRowData', id);
                    var val_json       = $.parseJSON(ret.val_json);
                    var respado_pdf_sw = true;
                    switch(valor[1]){
                        case 1:
                            if(val_json.resolucion_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.resolucion_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 2:
                            if(val_json.resolucion_estado_pdf_2 == '2'){
                                var win = window.open(public_url + '/' + val_json.resolucion_archivo_pdf_2,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 3:
                            if(val_json.informe_seguimiento_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.informe_seguimiento_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                        case 4:
                            if(val_json.complementario_estado_pdf == '2'){
                                var win = window.open(public_url + '/' + val_json.complementario_archivo_pdf,  '_blank');
                                win.focus();
                                respado_pdf_sw = false;
                            }
                            break;
                    }
                    if(respado_pdf_sw){
                        var valor1 = new Array();
                        valor1[0]  = 101;
                        valor1[1]  = '<div class="text-center"><strong>SIN RESPALDO PDF</strong></div>';
                        valor1[2]  = "No se subio ningun PDF.";
                        utilitarios(valor1);
                    }
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>SIN RESPALDO PDF</strong></div>';
                    valor1[2]  = "Edite una RESOLUCION DEL MP Y SEGUIMIENTO.";
                    utilitarios(valor1);
                }
                break;
            // === ELIMINAR PDF - 3 ===
            case 65:
                var id = $("#resolucion_id").val();
                if(id != ''){
                    var concatenar_valores = '';
                    concatenar_valores += "tipo=16&_token=" + csrf_token;
                    concatenar_valores += '&id=' + id;
                    switch(valor[1]){
                        case 1:
                            concatenar_valores += '&tipo_del=1'
                            break;
                        case 2:
                            concatenar_valores += '&tipo_del=2'
                            break;
                        case 3:
                            concatenar_valores += '&tipo_del=3'
                            break;
                        case 4:
                            concatenar_valores += '&tipo_del=4'
                            break;
                    }

                    swal({
                        title             : "ENVIANDO INFORMACIÓN",
                        text              : "Eliminando PDF.",
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
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>NO SE EDITO/strong></div>';
                    valor1[2]  = "Edite una SOLICITUD DE TRABAJO COMPLEMENTARIO.";
                    utilitarios(valor1);
                }
                break;

            // === GUARDAR - DELITO ===
            case 70:
                var concatenar_valores = '';

                concatenar_valores += "tipo=21&_token=" + csrf_token;

                var solicitud_id = $("#solicitud_id").val();
                var delito_id    = $("#delito_id").val();
                var tentativa    = $("#tentativa:checked").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(solicitud_id) != ''){
                    concatenar_valores += '&solicitud_id=' + solicitud_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>Debe de existir el codigo de la MEDIDAS DE PROTECCION para guardar el DELITO.';
                }

                if($.trim(delito_id) != ''){
                    concatenar_valores += '&delito_id=' + delito_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo DELITO es obligatorio.';
                }

                if($.trim(tentativa) != ''){
                    concatenar_valores += '&tentativa=' + tentativa;
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
                break;
            // === ELIMINAR - DELITO ===
            case 701:
                var concatenar_valores = '';

                var ret      = $(jqgrid2).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                concatenar_valores += "tipo=211&_token=" + csrf_token + "&solicitud_delito_id=" + valor[1] + "&solicitud_id=" + val_json.solicitud_id;

                swal({
                    title             : "ENVIANDO INFORMACIÓN",
                    text              : "Espere a que elimine la información.",
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
                break;
            // === GUARDAR - RECALIFICACION DEL DELITO ===
            case 71:
                var concatenar_valores = '';

                concatenar_valores += "tipo=22&_token=" + csrf_token;

                var solicitud_id = $("#solicitud_id").val();
                var delito_id    = $("#delito_id_r").val();
                var tentativa    = $("#tentativa_r:checked").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(solicitud_id) != ''){
                    concatenar_valores += '&solicitud_id=' + solicitud_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>Debe de existir el codigo de la MEDIDAS DE PROTECCION para guardar el DELITO.';
                }

                if($.trim(delito_id) != ''){
                    concatenar_valores += '&delito_id=' + delito_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo DELITO es obligatorio.';
                }

                if($.trim(tentativa) != ''){
                    concatenar_valores += '&tentativa=' + tentativa;
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
                break;
            // === ELIMINAR - RECALIFICACION DEL DELITO ===
            case 711:
                var concatenar_valores = '';

                var ret      = $(jqgrid3).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                concatenar_valores += "tipo=221&_token=" + csrf_token + "&solicitud_delito_id=" + valor[1] + "&solicitud_id=" + val_json.solicitud_id;

                swal({
                    title             : "ENVIANDO INFORMACIÓN",
                    text              : "Espere a que elimine la información.",
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
                break;
            // === GUARDAR - SOLICITUD TRABAJO COMPLEMENTARIO ===
            case 72:
                var concatenar_valores = '';

                concatenar_valores += "tipo=23&_token=" + csrf_token;

                var solicitud_id                      = $("#solicitud_id").val();
                var solicitud_complementaria_id       = $("#solicitud_complementaria_id").val();
                var complementario_dirigido_a         = $("#complementario_dirigido_a").val();
                var complementario_trabajo_solicitado = $("#complementario_trabajo_solicitado").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(solicitud_id) != ''){
                    concatenar_valores += '&solicitud_id=' + solicitud_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>Debe de existir el codigo de la MEDIDAS DE PROTECCION para guardar el TRABAJO COMPLEMENTARIO.';
                }

                if($.trim(complementario_dirigido_a) != ''){
                    concatenar_valores += '&complementario_dirigido_a=' + complementario_dirigido_a;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo DIRIGIDO A es obligatorio.';
                }

                if($.trim(complementario_trabajo_solicitado) != ''){
                    concatenar_valores += '&complementario_trabajo_solicitado=' + complementario_trabajo_solicitado;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo TRABAJO SOLICITADO es obligatorio.';
                }

                concatenar_valores += '&id=' + solicitud_complementaria_id;

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
                break;
            // === ELIMINAR - SOLICITUD TRABAJO COMPLEMENTARIO ===
            case 721:
                var concatenar_valores = '';

                var ret      = $(jqgrid5).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                concatenar_valores += "tipo=231&_token=" + csrf_token + "&solicitud_complementaria_id=" + valor[1] + "&complementario_archivo_pdf=" + val_json.complementario_archivo_pdf;

                swal({
                    title             : "ENVIANDO INFORMACIÓN",
                    text              : "Espere a que elimine la información.",
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
                break;
            // === GUARDAR - RESOLUCIONES DEL MP Y SEGUIMIENTO ===
            case 73:
                var concatenar_valores = '';

                concatenar_valores += "tipo=24&_token=" + csrf_token;

                var solicitud_id                          = $("#solicitud_id").val();
                var resolucion_id                         = $("#resolucion_id").val();
                var resolucion_descripcion                = $("#resolucion_descripcion").val();
                var resolucion_fecha_emision              = $("#resolucion_fecha_emision").val();
                var resolucion_tipo_disposicion           = $("#resolucion_tipo_disposicion").val();
                var resolucion_medidas_proteccion         = $("#resolucion_medidas_proteccion").val();
                var resolucion_otra_medidas_proteccion    = $("#resolucion_otra_medidas_proteccion").val();
                var resolucion_instituciones_coadyuvantes = $("#resolucion_instituciones_coadyuvantes").val();

                var fecha_inicio              = $("#fecha_inicio").val();
                var fecha_entrega_digital     = $("#fecha_entrega_digital").val();
                var informe_seguimiento_fecha = $("#informe_seguimiento_fecha").val();
                var complementario_fecha      = $("#complementario_fecha").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(solicitud_id) != ''){
                    concatenar_valores += '&solicitud_id=' + solicitud_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>Debe de existir el codigo de la MEDIDAS DE PROTECCION para guardar las RESOLUCIONES DEL MP Y SEGUIMIENTO.';
                }

                if($.trim(resolucion_descripcion) != ''){
                    concatenar_valores += '&resolucion_descripcion=' + resolucion_descripcion;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo DIRIGIDO A es obligatorio.';
                }

                concatenar_valores += '&id=' + resolucion_id;
                concatenar_valores += '&resolucion_fecha_emision=' + resolucion_fecha_emision;
                concatenar_valores += '&resolucion_tipo_disposicion=' + resolucion_tipo_disposicion;
                concatenar_valores += '&resolucion_medidas_proteccion=' + resolucion_medidas_proteccion;
                concatenar_valores += '&resolucion_otra_medidas_proteccion=' + resolucion_otra_medidas_proteccion;
                concatenar_valores += '&resolucion_instituciones_coadyuvantes=' + resolucion_instituciones_coadyuvantes;

                concatenar_valores += '&fecha_inicio=' + fecha_inicio;
                concatenar_valores += '&fecha_entrega_digital=' + fecha_entrega_digital;
                concatenar_valores += '&informe_seguimiento_fecha=' + informe_seguimiento_fecha;
                concatenar_valores += '&complementario_fecha=' + complementario_fecha;

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
                break;
            // === ELIMINAR - RESOLUCIONES DEL MP Y SEGUIMIENTO ===
            case 731:
                var concatenar_valores = '';

                var ret      = $(jqgrid4).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                concatenar_valores += "tipo=241&_token=" + csrf_token + "&resolucion_id=" + valor[1] + "&resolucion_archivo_pdf=" + val_json.resolucion_archivo_pdf + "&resolucion_archivo_pdf_2=" + val_json.resolucion_archivo_pdf_2 + "&informe_seguimiento_archivo_pdf=" + val_json.informe_seguimiento_archivo_pdf + "&complementario_archivo_pdf=" + val_json.complementario_archivo_pdf;

                swal({
                    title             : "ENVIANDO INFORMACIÓN",
                    text              : "Espere a que elimine la información.",
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
                break;

            // === SOLICITUD - CERRAR ===
            case 80:
                swal({
                    title             : "CERRAR MEDIDA DE PROTECCION",
                    text              : "¿Esta seguro de cerrar la MEDIDA DE PROTECCION?",
                    type              : "warning",
                    showCancelButton  : true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText : "Cerrar",
                    cancelButtonText  : "Cancelar",
                    closeOnConfirm    : false,
                    closeOnCancel     : false
                },
                function(isConfirm){
                    if (isConfirm){
                        // swal.close();

                        var concatenar_valores = '';

                        concatenar_valores += "tipo=30&_token=" + csrf_token + "&id=" + valor[1];

                        swal({
                            title             : "CERRANDO MEDIDA DE PROTECCION",
                            text              : "Espere a que se cierre la MEDIDA DE PROTECCION.",
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
                    }
                    else{
                        swal.close();
                    }
                });
                return respuesta;
                break;

            // === REPORTES EXCEL ===
            case 90:
                var concatenar_valores = '?tipo=10';

                var gestion = $("#gestion_2").val();

                var f_solicitud_del = $("#f_solicitud_2_del").val();
                var f_solicitud_al  = $("#f_solicitud_2_al").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(gestion) != ''){
                    concatenar_valores += '&gestion=' + gestion;
                }

                if($.trim(f_solicitud_del) != ''){
                    concatenar_valores += '&f_solicitud_del=' + f_solicitud_del;
                }

                if($.trim(f_solicitud_al) != ''){
                    concatenar_valores += '&f_solicitud_al=' + f_solicitud_al;
                }

                if(valor_sw){
                    var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                    win.focus();
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                    valor1[2]  = valor_error;
                    utilitarios(valor1);
                }
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
                            // === PASO 1 - INSERT UPDATE ===
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

                                        var valor1 = new Array();
                                        valor1[0]  = 411;
                                        valor1[1]  = data.id;
                                        utilitarios(valor1);

                                        var valor1 = new Array();
                                        valor1[0]  = 421;
                                        valor1[1]  = data.id;
                                        utilitarios(valor1);

                                        var valor1 = new Array();
                                        valor1[0]  = 441;
                                        valor1[1]  = data.id;
                                        utilitarios(valor1);

                                        var valor1 = new Array();
                                        valor1[0]  = 451;
                                        valor1[1]  = data.id;
                                        utilitarios(valor1);
                                    }

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
                            // === PASO 2 - INSERT UPDATE ===
                            case '2':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");

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
                            // === PASO 3 - INSERT UPDATE ===
                            case '3':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");

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
                            // === PASO 4 - INSERT UPDATE ===
                            case '4':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");

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
                            // === PASO 5 - INSERT UPDATE ===
                            case '5':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");

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

                            // === ELIMINAR PDF ===
                            case '12':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");

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
                            // === ELIMINAR PDF ===
                            case '14':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid5).trigger("reloadGrid");

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
                            // === ELIMINAR PDF ===
                            case '16':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid4).trigger("reloadGrid");

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

                            // === DELITO - INSERT UPDATE ===
                            case '21':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");
                                    $(jqgrid2).trigger("reloadGrid");

                                    var valor1 = new Array();
                                    valor1[0]  = 31;
                                    utilitarios(valor1);

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
                            // === DELITO - ELIMINAR ===
                            case '211':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");
                                    $(jqgrid2).trigger("reloadGrid");

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

                            // === RECALIFICACION DEL DELITO - INSERT UPDATE ===
                            case '22':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");
                                    $(jqgrid3).trigger("reloadGrid");

                                    var valor1 = new Array();
                                    valor1[0]  = 32;
                                    utilitarios(valor1);

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
                            // === RECALIFICACION DEL DELITO - ELIMINAR ===
                            case '221':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");
                                    $(jqgrid3).trigger("reloadGrid");

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

                            // === SOLICITUD TRABAJO COMPLEMENTARIO - INSERT UPDATE ===
                            case '23':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $("#solicitud_complementaria_id").val(data.id);

                                    $(jqgrid5).trigger("reloadGrid");

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
                            // === SOLICITUD TRABAJO COMPLEMENTARIO - ELIMINAR ===
                            case '231':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid5).trigger("reloadGrid");

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

                            // === SOLICITUD TRABAJO COMPLEMENTARIO - INSERT UPDATE ===
                            case '24':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $("#resolucion_id").val(data.id);

                                    $(jqgrid4).trigger("reloadGrid");

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
                            // === SOLICITUD TRABAJO COMPLEMENTARIO - ELIMINAR ===
                            case '241':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid4).trigger("reloadGrid");

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

                            // === CERRAR MEDIDA DE PROTECCION ===
                            case '30':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");

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
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
        var grupo_id          = "{!! $grupo_id !!}";
        var i4_funcionario_id = "{!! $i4_funcionario_id !!}";
        var base_url          = "{!! url('') !!}";
        var url_controller    = "{!! url('/detencion_preventiva') !!}";
        var csrf_token        = "{!! csrf_token() !!}";

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

    // === NO SI ===
        var no_si_json   = $.parseJSON('{!! json_encode($no_si_array) !!}');
        var no_si_select = '';
        var no_si_jqgrid = ':Todos';

        $.each(no_si_json, function(index, value) {
            no_si_select += '<option value="' + index + '">' + value + '</option>';
            no_si_jqgrid += ';' + index + ':' + value;
        });

    // === NUMERO DE DETENIDOS ===
        var no_si_json   = $.parseJSON('{!! json_encode($no_si_array) !!}');
        var no_si_select = '';
        var no_si_jqgrid = ':Todos';

        $.each(no_si_json, function(index, value) {
            no_si_select += '<option value="' + index + '">' + value + '</option>';
            no_si_jqgrid += ';' + index + ':' + value;
        });

        var n_detenidos_select = '';
        var n_detenidos_jqgrid = ': Todos';
        var n_inicial    = 1;
        var n_final      = 30;
        for (var i = n_inicial; i <= n_final; i++){
            n_detenidos_select += '<option value="' + i + '">' + i + '</option>';
            n_detenidos_jqgrid += ';' + i + ':' + i;
        }

    // === DP ESTADO ===
        var dp_estado_json   = $.parseJSON('{!! json_encode($dp_estado_array) !!}');
        var dp_estado_select = '';
        var dp_estado_jqgrid = ':Todos';

        $.each(dp_estado_json, function(index, value) {
            dp_estado_select += '<option value="' + index + '">' + value + '</option>';
            dp_estado_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO RECINTO ===
        var tipo_recinto_json   = $.parseJSON('{!! json_encode($tipo_recinto_array) !!}');
        var tipo_recinto_select = '';
        var tipo_recinto_jqgrid = ':Todos';

        $.each(tipo_recinto_json, function(index, value) {
            tipo_recinto_select += '<option value="' + index + '">' + value + '</option>';
            tipo_recinto_jqgrid += ';' + index + ':' + value;
        });

    // === DP SEMAFORO ===
        var dp_semaforo_json   = $.parseJSON('{!! json_encode($dp_semaforo_array) !!}');
        var dp_semaforo_select = '';
        var dp_semaforo_jqgrid = ':Todos';

        $.each(dp_semaforo_json, function(index, value) {
            dp_semaforo_select += '<option value="' + index + '">' + value + '</option>';
            dp_semaforo_jqgrid += ';' + index + ':' + value;
        });

    // === SEXO ===
        var sexo_json   = $.parseJSON('{!! json_encode($sexo_array) !!}');
        var sexo_select = '';
        var sexo_jqgrid = ':Todos';

        $.each(sexo_json, function(index, value) {
            sexo_select += '<option value="' + index + '">' + value + '</option>';
            sexo_jqgrid += ';' + index + ':' + value;
        });

    // === PELIGRO PROCESAL ===
        var peligro_procesal_json   = $.parseJSON('{!! json_encode($peligro_procesal_array) !!}');
        var peligro_procesal_select = '';
        var peligro_procesal_jqgrid = ':Todos';

        $.each(peligro_procesal_json, function(index, value) {
            peligro_procesal_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            peligro_procesal_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    // === ETAPA CASO ===
        var etapa_caso_json   = $.parseJSON('{!! json_encode($etapa_caso_array) !!}');
        var etapa_caso_select = '';
        var etapa_caso_jqgrid = ':Todos';

        $.each(etapa_caso_json, function(index, value) {
            etapa_caso_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            etapa_caso_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });
    // === DEPARTAMENTO ===
        var departamento_json   = $.parseJSON('{!! json_encode($departamento_array) !!}');
        var departamento_select = '';
        var departamento_jqgrid = ':Todos';

        $.each(departamento_json, function(index, value) {
            departamento_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            departamento_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    // === DROPZONE ===
        Dropzone.autoDiscover = false;

    $(document).ready(function(){
        //=== INICIALIZAR ===
            $('#peligro_procesal_id').append(peligro_procesal_select);

        //=== SELECT2 ===
            $("#peligro_procesal_id").select2();
            $("#peligro_procesal_id").appendTo("#peligro_procesal_id_div");

            $('#recinto_carcelario_id').select2({
                maximumSelectionLength: 1,
                minimumInputLength    : 2,
                ajax                  : {
                    url     : url_controller + '/send_ajax',
                    type    : 'post',
                    dataType: 'json',
                    data    : function (params) {
                        return {
                            q         : params.term,
                            page_limit: 20,
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
            $("#recinto_carcelario_id").appendTo("#recinto_carcelario_id_div");

            // $("#gestion, #solicitante, #etapa_proceso, #estado, #gestion_2").select2({
            //     maximumSelectionLength: 1
            // });
            // $("#gestion").appendTo("#gestion_div");
            // $("#solicitante").appendTo("#solicitante_div");
            // $("#etapa_proceso").appendTo("#etapa_proceso_div");
            // $("#estado").appendTo("#estado_div");
            // $("#gestion_2").appendTo("#gestion_2_div");

            // $("#usuario_tipo, #dirigido_a_psicologia, #dirigido_psicologia, #dirigido_a_trabajo_social, #dirigido_trabajo_social, #dirigido_a_otro_trabajo, #resolucion_tipo_disposicion, #resolucion_medidas_proteccion").select2();
            // $("#usuario_tipo").appendTo("#usuario_tipo_div");
            // $("#dirigido_a_psicologia").appendTo("#dirigido_a_psicologia_div");
            // $("#dirigido_a_trabajo_social").appendTo("#dirigido_a_trabajo_social_div");
            // $("#dirigido_a_otro_trabajo").appendTo("#dirigido_a_otro_trabajo_div");
            // $("#dirigido_psicologia").appendTo("#dirigido_psicologia_div");
            // $("#dirigido_trabajo_social").appendTo("#dirigido_trabajo_social_div");
            // $("#resolucion_tipo_disposicion").appendTo("#resolucion_tipo_disposicion_div");
            // $("#resolucion_medidas_proteccion").appendTo("#resolucion_medidas_proteccion_div");

            // $('#municipio_id').select2({
            //     maximumSelectionLength: 1,
            //     minimumInputLength    : 2,
            //     ajax                  : {
            //         url     : url_controller + '/send_ajax',
            //         type    : 'post',
            //         dataType: 'json',
            //         data    : function (params) {
            //             return {
            //                 q         : params.term,
            //                 page_limit: 10,
            //                 estado    : 1,
            //                 tipo      : 101,
            //                 _token    : csrf_token
            //             };
            //         },
            //         results: function (data, page) {
            //             return {
            //                 results: data
            //             };
            //         }
            //     }
            // });
            // $("#municipio_id").appendTo("#municipio_id_div");

            // $('#delito_id, #delito_id_r').select2({
            //     maximumSelectionLength: 1,
            //     minimumInputLength    : 2,
            //     ajax                  : {
            //         url     : url_controller + '/send_ajax',
            //         type    : 'post',
            //         dataType: 'json',
            //         data    : function (params) {
            //             return {
            //                 q         : params.term,
            //                 page_limit: 10,
            //                 estado    : 1,
            //                 tipo      : 102,
            //                 _token    : csrf_token
            //             };
            //         },
            //         results: function (data, page) {
            //             return {
            //                 results: data
            //             };
            //         }
            //     }
            // });
            // $("#delito_id").appendTo("#delito_id_div");
            // $("#delito_id_r").appendTo("#delito_id_r_div");

        //=== DATEPICKER 3 ===
            $('#dp_fecha_detencion_preventiva, #dp_fecha_conclusion_detencion, #dp_madre_lactante_1_fecha_nacimiento_menor, #dp_custodia_menor_6_fecha_nacimiento_menor').datepicker({
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

        //=== TOUCHSPIN ===
            $("#dp_etapa_gestacion_semana").TouchSpin({
                buttondown_class: 'btn btn-white',
                buttonup_class: 'btn btn-white'
            });

        //=== FLIPSWITCH ===
            $("#dp_etapa_gestacion_estado").change(function(){
                if(this.checked){
                    $("#dp_etapa_gestacion_semana").prop('disabled', false);
                    $("#div_dp_etapa_gestacion_semana").slideDown("slow");
                }
                else{
                    $("#dp_etapa_gestacion_semana").prop('disabled', true);
                    $("#div_dp_etapa_gestacion_semana").slideUp("slow");
                }
            });

            $("#dp_enfermo_terminal_estado").change(function(){
                if(this.checked){
                    $("#dp_enfermo_terminal_tipo").prop('disabled', false);
                    $("#div_dp_enfermo_terminal_tipo").slideDown("slow");
                }
                else{
                    $("#dp_enfermo_terminal_tipo").prop('disabled', true);
                    $("#div_dp_enfermo_terminal_tipo").slideUp("slow");
                }
            });

            $("#dp_madre_lactante_1").change(function(){
                if(this.checked){
                    $("#dp_madre_lactante_1_fecha_nacimiento_menor").prop('disabled', false);
                    $("#div_dp_madre_lactante_1_fecha_nacimiento_menor").slideDown("slow");
                }
                else{
                    $("#dp_madre_lactante_1_fecha_nacimiento_menor").prop('disabled', true);
                    $("#div_dp_madre_lactante_1_fecha_nacimiento_menor").slideUp("slow");
                }
            });

            $("#dp_custodia_menor_6").change(function(){
                if(this.checked){
                    $("#dp_custodia_menor_6_fecha_nacimiento_menor").prop('disabled', false);
                    $("#div_dp_custodia_menor_6_fecha_nacimiento_menor").slideDown("slow");
                }
                else{
                    $("#dp_custodia_menor_6_fecha_nacimiento_menor").prop('disabled', true);
                    $("#div_dp_custodia_menor_6_fecha_nacimiento_menor").slideUp("slow");
                }
            });

        // === JQGRID ===
            var valor1 = new Array();
            valor1[0]  = 40;
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
                break;

            // === EDICION MODAL ===
            case 20:
                var valor1 = new Array();
                valor1[0]  = 30;
                utilitarios(valor1);

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $('#modal_1_title, #modal_2_title').empty();
                $('#modal_1_title').append('MODIFICAR CARACTERISTICAS DEL DETENIDO');
                $('#modal_2_title').append(ret.Caso + ' - ' + $.trim(ret.ApPat + ' ' + ret.ApMat) + ' ' + ret.Nombres);

                $("#persona_id").val(valor[1]);
                $("#caso_id").val(val_json.caso_id);

                // === IDENTIFICACION DEL CASO ===
                    $("#CodCasoJuz").val(ret.CodCasoJuz);

                // === DATOS DEL PROCESO ===
                    if(ret.peligro_procesal != ""){
                        var peligro_procesal      = val_json.peligro_procesal_id
                        var peligro_procesal_id_array = peligro_procesal.split('::');
                        $("#peligro_procesal_id").select2().val(peligro_procesal_id_array).trigger("change");
                    }
                    $("#dp_fecha_detencion_preventiva").val(ret.dp_fecha_detencion_preventiva);
                    $("#dp_fecha_conclusion_detencion").val(ret.dp_fecha_conclusion_detencion);
                    if(val_json.recinto_carcelario_id != null){
                        $('#recinto_carcelario_id').append('<option value="' + val_json.recinto_carcelario_id + '">' + ret.recinto_carcelario + '</option>');
                        $("#recinto_carcelario_id").select2("val", val_json.recinto_carcelario_id);
                    }

                // === SOLICITUD ===
                    // $("#gestion").select2("val", ret.gestion);
                    // $("#gestion").select2("enable", false);
                    // $("#solicitante").select2("val", val_json.solicitante);
                    // $("#solicitante").select2("val", val_json.solicitante);
                    // $("#nombre_solicitante").val(ret.nombre_solicitante);
                    // if(ret.municipio != ""){
                    //     var dpm = ret.departamento + ', ' + ret.provincia + ', ' + ret.municipio;
                    //     $('#municipio_id').append('<option value="' + val_json.municipio_id + '">' + dpm + '</option>');
                    //     $("#municipio_id").select2("val", val_json.municipio_id);
                    // }
                    // $("#f_solicitud").val(ret.f_solicitud);

                    // $("#n_caso").val(ret.n_caso);
                    // $("#etapa_proceso").select2("val", val_json.etapa_proceso);
                    // $("#denunciante").val(ret.denunciante);
                    // $("#denunciado").val(ret.denunciado);
                    // $("#victima").val(ret.victima);
                    // $("#persona_protegida").val(ret.persona_protegida);

                // === USUARIO ===
                    // if(val_json.usuario_tipo != null){
                    //     var usuario_tipo       = val_json.usuario_tipo;
                    //     var usuario_tipo_array = usuario_tipo.split(",");
                    //     $("#usuario_tipo").select2().val(usuario_tipo_array).trigger("change");
                    // }
                    // $("#usuario_tipo_descripcion").val(val_json.usuario_tipo_descripcion);
                    // $("#usuario_nombre").val(val_json.usuario_nombre);
                    // if(val_json.usuario_sexo != "null"){
                    //     $(".usuario_sexo_class[value=" + val_json.usuario_sexo + "]").prop('checked', true);
                    // }

                    // $("#usuario_celular").val(val_json.usuario_celular);
                    // $("#usuario_domicilio").val(val_json.usuario_domicilio);
                    // $("#usuario_otra_referencia").val(val_json.usuario_otra_referencia);
                    // if(val_json.usuario_edad != "null"){
                    //     $(".usuario_edad_class[value=" + val_json.usuario_edad + "]").prop('checked', true);
                    // }

                // === SOLICITUD DE TRABAJO ===
                    // if(val_json.dirigido_a_psicologia != null){
                    //     var dirigido_a_psicologia       = val_json.dirigido_a_psicologia;
                    //     var dirigido_a_psicologia_array = dirigido_a_psicologia.split(",");
                    //     $("#dirigido_a_psicologia").select2().val(dirigido_a_psicologia_array).trigger("change");
                    // }
                    // if(val_json.dirigido_psicologia != null){
                    //     var dirigido_psicologia       = val_json.dirigido_psicologia;
                    //     var dirigido_psicologia_array = dirigido_psicologia.split(",");
                    //     $("#dirigido_psicologia").select2().val(dirigido_psicologia_array).trigger("change");
                    // }

                    // if(val_json.dirigido_a_trabajo_social != null){
                    //     var dirigido_a_trabajo_social       = val_json.dirigido_a_trabajo_social;
                    //     var dirigido_a_trabajo_social_array = dirigido_a_trabajo_social.split(",");
                    //     $("#dirigido_a_trabajo_social").select2().val(dirigido_a_trabajo_social_array).trigger("change");
                    // }
                    // if(val_json.dirigido_trabajo_social != null){
                    //     var dirigido_trabajo_social       = val_json.dirigido_trabajo_social;
                    //     var dirigido_trabajo_social_array = dirigido_trabajo_social.split(",");
                    //     $("#dirigido_trabajo_social").select2().val(dirigido_trabajo_social_array).trigger("change");
                    // }

                    // if(val_json.dirigido_a_otro_trabajo != null){
                    //     var dirigido_a_otro_trabajo       = val_json.dirigido_a_otro_trabajo;
                    //     var dirigido_a_otro_trabajo_array = dirigido_a_otro_trabajo.split(",");
                    //     $("#dirigido_a_otro_trabajo").select2().val(dirigido_a_otro_trabajo_array).trigger("change");
                    // }
                    // $("#dirigido_otro_trabajo").val(val_json.dirigido_otro_trabajo);

                // === SOLICITUD TRABAJO COMPLEMENTARIO ===
                    // $("#estado").select2("val", val_json.estado);

                $('#modal_1').modal();
                break;
            // === RESETEAR - FORMULARIO ===
            case 30:
                $("#persona_id").val('');
                $("#caso_id").val('');

                // === CARACTERISTICAS DEL DETENIDO ===
                    $("#dp_etapa_gestacion_semana").prop('disabled', true);
                    $("#dp_enfermo_terminal_tipo").prop('disabled', true);
                    $("#dp_madre_lactante_1_fecha_nacimiento_menor").prop('disabled', true);
                    $("#dp_custodia_menor_6_fecha_nacimiento_menor").prop('disabled', true);

                    $("#div_dp_etapa_gestacion_semana").slideUp("slow");
                    $("#div_dp_enfermo_terminal_tipo").slideUp("slow");
                    $("#div_dp_madre_lactante_1_fecha_nacimiento_menor").slideUp("slow");
                    $("#div_dp_custodia_menor_6_fecha_nacimiento_menor").slideUp("slow");

                // // === SOLICITUD ===
                //     $('#gestion').select2("val", "");
                //     $("#gestion").select2("enable", true);
                //     $('#solicitante').select2("val", "");
                //     $('#municipio_id').select2("val", "");
                //     $('#municipio_id option').remove();

                //     $('#etapa_proceso').select2("val", "");

                // // === USUARIO ===
                //     $('#usuario_tipo').select2("val", "");

                // // === SOLICITUD DE TRABAJO ===
                //     $('#dirigido_a_psicologia').select2("val", "");
                //     $('#dirigido_psicologia').select2("val", "");

                //     $('#dirigido_a_trabajo_social').select2("val", "");
                //     $('#dirigido_trabajo_social').select2("val", "");

                //     $('#dirigido_a_otro_trabajo').select2("val", "");

                // // === SOLICITUD TRABAJO COMPLEMENTARIO ===
                //     $('#estado').select2("val", "");

                $(form_1)[0].reset();
                break;
            // === JQGRID 1 ===
            case 40:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '2003'], $permisos))
                    if(grupo_id == 2 && i4_funcionario_id != ''){
                        edit1  = false;
                        ancho1 += ancho_d;
                    }
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
                    sortname    : 'Caso.FechaDenuncia',
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

                        "SEMAFORO",
                        "SEMAFORO DELITO",
                        "DETENIDOS",
                        "ESTADO DETENIDO",
                        "NUMERO DE CASO",
                        "IANUS / NUREJ",
                        "DEPARTAMENTO",

                        "DOCUMENTO DE IDENTIDAD",
                        "AP. PATERNO",
                        "AP. MATERNO",
                        "AP. ESPOSO",
                        "NOMBRE(S)",
                        "FECHA DE NACIMIENTO",
                        "SEXO",

                        "FECHA DENUNCIA",
                        "DELITO PRINCIPAL",
                        "DELITOS",

                        "DEL",
                        "AL",
                        "ETAPA",
                        "PELIGRO PROCESAL",

                        "RECINTO CARCELARIO",

                        "FISCAL RESPONSABLE",

                        "MUNICIPIO",
                        "OFICINA",
                        "DIVISION",

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
                            name       : "dp_semaforo",
                            index      : "a2.dp_semaforo",
                            width      : 100,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:dp_semaforo_jqgrid}
                        },
                        {
                            name       : "dp_semaforo_delito",
                            index      : "a2.dp_semaforo_delito",
                            width      : 130,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:dp_semaforo_jqgrid}
                        },
                        {
                            name       : "n_detenidos",
                            index      : "Caso.n_detenidos",
                            width      : 80,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value: n_detenidos_jqgrid}
                        },
                        {
                            name       : "dp_estado",
                            index      : "a2.dp_estado",
                            width      : 190,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:dp_estado_jqgrid}
                        },
                        {
                            name : "Caso",
                            index: "Caso.Caso",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "CodCasoJuz",
                            index: "Caso.CodCasoJuz",
                            width: 150,
                            align: "left"
                        },
                        {
                            name       : "departamento",
                            index      : "a15.Dep",
                            width      : 150,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:departamento_jqgrid}
                        },

                        {
                            name : "NumDocId",
                            index: "a2.NumDocId",
                            width: 190,
                            align: "left"
                        },
                        {
                            name : "ApPat",
                            index: "a2.ApPat",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "ApMat",
                            index: "a2.ApMat",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "ApEsp",
                            index: "a2.ApEsp",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "Nombres",
                            index: "a2.Nombres",
                            width: 200,
                            align: "left"
                        },
                        {
                            name : "FechaNac",
                            index: "a2.FechaNac",
                            width: 160,
                            align: "center"
                        },
                        {
                            name       : "Sexo",
                            index      : "a2.Sexo",
                            width      : 100,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:sexo_jqgrid}
                        },

                        {
                            name : "FechaDenuncia",
                            index: "Caso.FechaDenuncia",
                            width: 150,
                            align: "center"
                        },
                        {
                            name : "DelitoPrincipal",
                            index: "a3.Delito",
                            width: 500,
                            align: "left"
                        },
                        {
                            name : "delitos",
                            index: "a11.Delito",
                            width: 500,
                            align: "left"
                        },

                        {
                            name : "dp_fecha_detencion_preventiva",
                            index: "a2.dp_fecha_detencion_preventiva",
                            width: 100,
                            align: "center"
                        },
                        {
                            name : "dp_fecha_conclusion_detencion",
                            index: "a2.dp_fecha_conclusion_detencion",
                            width: 100,
                            align: "center"
                        },
                        {
                            name       : "EtapaCaso",
                            index      : "a4.EtapaCaso",
                            width      : 150,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value: etapa_caso_jqgrid}
                        },
                        {
                            name : "peligro_procesal",
                            index: "a9.nombre",
                            width: 300,
                            align: "left"
                        },

                        {
                            name : "recinto_carcelario",
                            index: "a5.nombre",
                            width: 300,
                            align: "center"
                        },

                        {
                            name : "fiscal_responsable",
                            index: "a6.Funcionario",
                            width: 300,
                            align: "left"
                        },

                        {
                            name       : "municipio",
                            index      : "a14.Muni",
                            width      : 300,
                            align      : "left"
                        },
                        {
                            name       : "oficina",
                            index      : "a15.Oficina",
                            width      : 300,
                            align      : "left"
                        },
                        {
                            name       : "division",
                            index      : "a16.Division",
                            width      : 500,
                            align      : "left"
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
                            @if(in_array(['codigo' => '2003'], $permisos))
                                if(grupo_id == 2 && i4_funcionario_id != ''){
                                    ed = "<button type='button' class='btn btn-xs btn-success' title='Modificar detención' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                                }
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
                            startColumnName: 'NumDocId',
                            numberOfColumns: 7,
                            titleText      : 'PERSONA DETENIDA',
                        },
                        {
                            startColumnName: 'dp_fecha_detencion_preventiva',
                            numberOfColumns: 2,
                            titleText      : 'FECHA DETENCION',
                        },
                        {
                            startColumnName: 'municipio',
                            numberOfColumns: 3,
                            titleText      : 'UBICACION DEL CASO',
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
                @if(in_array(['codigo' => '2002'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "add1",
                        caption       : "",
                        title         : 'Agregar nueva fila',
                        buttonicon    : "ui-icon ui-icon-plusthick",
                        onClickButton : function(){
                        }
                    })
                @endif
                @if(in_array(['codigo' => '2004'], $permisos))
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
                        }
                    })
                @endif
                ;
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
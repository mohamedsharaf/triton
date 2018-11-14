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
        var base_url       = "{!! url('') !!}";
        var url_controller = "{!! url('/detencion_preventiva') !!}";
        var csrf_token     = "{!! csrf_token() !!}";

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

    // === DROPZONE ===
        Dropzone.autoDiscover = false;

    $(document).ready(function(){
        //=== INICIALIZAR ===

        //=== SELECT2 ===
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

        //=== datepicker3 ===
            // $('#f_solicitud, #plazo_fecha_solicitud, #plazo_psicologico_fecha_entrega_digital, #plazo_psicologico_fecha_entrega_fisico, #plazo_psicologico_fecha, #plazo_social_fecha_entrega_digital, #plazo_social_fecha_entrega_fisico, #plazo_complementario_fecha, #fecha_inicio, #fecha_entrega_digital, #fecha_entrega_fisico, #informe_seguimiento_fecha, #complementario_fecha, #resolucion_fecha_emision, #f_solicitud_2_del, #f_solicitud_2_al').datepicker({
            //     startView            : 2,
            //     // todayBtn          : "linked",
            //     // keyboardNavigation: false,
            //     // forceParse        : false,
            //     autoclose            : true,
            //     format               : "yyyy-mm-dd",
            //     startDate            : '-100y',
            //     endDate              : '+0d',
            //     language             : "es"
            // });

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
            case 1:
                $(form_1).steps('reset');
                break;
            // === MODAL MEDIDAS DE PROTECCION ===
            case 10:
                $('#modal_1').modal();
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
            // === JQGRID 1 ===
            case 40:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '2003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '2004'], $permisos))
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
                        "DETENIDOS",
                        "ESTADO DETENIDO",
                        "NUMERO DE CASO",
                        "IANUS / NUREJ",

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
                        "ACTIVIDAD",

                        "RECINTO CARCELARIO",

                        "FISCAL RESPONSABLE",

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
                            name : "n_detenidos",
                            index: "Caso.n_detenidos::text",
                            width: 80,
                            align: "center"
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
                            index: "",
                            width: 300,
                            align: "center"
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
                            name : "actividad",
                            index: "",
                            width: 300,
                            align: "center"
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
                            startColumnName: 'NumDocId',
                            numberOfColumns: 7,
                            titleText      : 'PERSONA DETENIDA',
                        },
                        {
                            startColumnName: 'dp_fecha_detencion_preventiva',
                            numberOfColumns: 2,
                            titleText      : 'FECHA DETENCION',
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
                @if(in_array(['codigo' => '2001'], $permisos))
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
                @if(in_array(['codigo' => '2001'], $permisos))
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
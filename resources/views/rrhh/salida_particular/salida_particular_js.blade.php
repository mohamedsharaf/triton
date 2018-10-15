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
        var url_controller = "{!! url('/salida_particular') !!}";
        var csrf_token     = "{!! csrf_token() !!}";
        var public_url     = "{!! asset($public_url) !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";

    // === FORMULARIOS ===
        var form_1 = "#form_1";
        var form_2 = "#form_2";
        var form_3 = "#form_3";
        var form_4 = "#form_4";
        var form_5 = "#form_5";
        var form_6 = "#form_6";
        var form_7 = "#form_7";
        var form_8 = "#form_8";
        var form_9 = "#form_9";

    // === ESTADO ===
        var estado_json   = $.parseJSON('{!! json_encode($estado_array) !!}');
        var estado_select = '';
        var estado_jqgrid = ':Todos';

        $.each(estado_json, function(index, value) {
            estado_select += '<option value="' + index + '">' + value + '</option>';
            estado_jqgrid += ';' + index + ':' + value;
        });

    // === SALIDO Y RETORNO ESTADO ===
        var sp_estado_json   = $.parseJSON('{!! json_encode($sp_estado_array) !!}');
        var sp_estado_select = '';
        var sp_estado_jqgrid = ':Todos';

        $.each(sp_estado_json, function(index, value) {
            sp_estado_select += '<option value="' + index + '">' + value + '</option>';
            sp_estado_jqgrid += ';' + index + ':' + value;
        });

    // === CON SIN RETORNO ===
        var con_sin_retorno_json   = $.parseJSON('{!! json_encode($con_sin_retorno_array) !!}');
        var con_sin_retorno_select = '';
        var con_sin_retorno_jqgrid = ':Todos';

        $.each(con_sin_retorno_json, function(index, value) {
            con_sin_retorno_select += '<option value="' + index + '">' + value + '</option>';
            con_sin_retorno_jqgrid += ';' + index + ':' + value;
        });

    // === SI NO ===
        var no_si_json   = $.parseJSON('{!! json_encode($no_si_array) !!}');
        var no_si_select = '';
        var no_si_jqgrid = ':Todos';

        $.each(no_si_json, function(index, value) {
            no_si_select += '<option value="' + index + '">' + value + '</option>';
            no_si_jqgrid += ';' + index + ':' + value;
        });

    // === LUGAR DE DEPENDENCIA ===
        var lugar_dependencia_json   = $.parseJSON('{!! json_encode($lugar_dependencia_array) !!}');
        var lugar_dependencia_select = '';
        var lugar_dependencia_jqgrid = ':Todos';

        $.each(lugar_dependencia_json, function(index, value) {
            lugar_dependencia_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            lugar_dependencia_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    // === DROPZONE ===
        Dropzone.autoDiscover = false;

    $(document).ready(function(){
        //=== INICIALIZAR ===
            $('#fecha_del_1, #fecha_al_1').datepicker({
                // startView            : 0,
                // todayBtn          : "linked",
                // keyboardNavigation: false,
                // forceParse        : false,
                autoclose            : true,
                format               : "yyyy-mm-dd",
                startDate            : '-20y',
                endDate              : '0d',
                language             : "es"
            });

            $('#persona_id_1').select2({
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
            $("#persona_id_1").appendTo("#persona_id_div_1");

            $('#lugar_dependencia_id_funcionario_1').append(lugar_dependencia_select);
            $("#lugar_dependencia_id_funcionario_1").select2({
                maximumSelectionLength: 1
            });
            $("#lugar_dependencia_id_funcionario_1").appendTo("#lugar_dependencia_id_funcionario_div_1");

        // === JQGRID 1 ===
            var valor1 = new Array();
            valor1[0]  = 10;
            utilitarios(valor1);

        // Add responsive to jqGrid
            $(window).bind('resize', function () {
                var width = $('.tab-content').width() - 35;
                $(jqgrid1).setGridWidth(width);
                $(jqgrid2).setGridWidth(width);
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
            // === JQGRID 1 ===
            case 10:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1603'], $permisos))
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
                    sortname    : 'rrhh_salidas.f_salida',
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

                        "CODIGO",

                        "C.I.",
                        "NOMBRE(S)",
                        "AP. PATERNO",
                        "AP. MATERNO",

                        "FECHA DE SALIDA",
                        "HORA SALIDA",
                        "HORA RETORNO",
                        "RETORNO",

                        "HORA SALIDA",
                        "HORA RETORNO",
                        "RETRASO",

                        "UNIDAD DESCONCENTRADA",
                        "LUGAR DE DEPENDENCIA",

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
                            index      : "rrhh_salidas.estado",
                            width      : 90,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:estado_jqgrid}
                        },

                        {
                            name : "codigo",
                            index: "rrhh_salidas.codigo",
                            width: 100,
                            align: "center"
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
                            name : "f_salida",
                            index: "rrhh_salidas.f_salida::text",
                            width: 125,
                            align: "center"
                        },
                        {
                            name : "h_salida",
                            index: "rrhh_salidas.h_salida::text",
                            width: 110,
                            align: "center"
                        },
                        {
                            name : "h_retorno",
                            index: "rrhh_salidas.h_retorno::text",
                            width: 110,
                            align: "center"
                        },
                        {
                            name : "con_sin_retorno",
                            index: "rrhh_salidas.con_sin_retorno",
                            width: 100,
                            align: "center"
                        },

                        {
                            name : "salida_s",
                            index: "rrhh_salidas.salida_s",
                            width: 250,
                            align: "center"
                        },
                        {
                            name : "salida_r",
                            index: "rrhh_salidas.salida_r",
                            width: 250,
                            align: "center"
                        },
                        {
                            name : "min_retrasos",
                            index: "rrhh_salidas.min_retrasos::text",
                            width: 65,
                            align: "center"
                        },

                        {
                            name : "ud_funcionario",
                            index: "a3.nombre",
                            width: 400,
                            align: "center"
                        },
                        {
                            name       : "lugar_dependencia_funcionario",
                            index      : "a4.nombre",
                            width      : 400,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:lugar_dependencia_jqgrid}
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

                            var pdf1 = "";
                            @if(in_array(['codigo' => '1003'], $permisos))
                                pdf1 = " <button type='button' class='btn btn-xs btn-primary' title='Generar PAPELETA DE SALIDA' onclick=\"utilitarios([11, " + cl + "]);\"><i class='fa fa-file-pdf-o'></i></button>";
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(pdf1)
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'f_salida',
                            numberOfColumns: 4,
                            titleText      : 'PAPELETA'
                        },
                        {
                            startColumnName: 'salida_s',
                            numberOfColumns: 3,
                            titleText      : 'BIOMETRICO'
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
                @if(in_array(['codigo' => '1602'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "add2",
                        caption       : "",
                        title         : 'Sincronizar salida particular',
                        buttonicon    : "ui-icon ui-icon-arrowrefresh-1-w",
                        onClickButton : function(){
                            var valor1 = new Array();
                            valor1[0]  = 13;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 12;
                            utilitarios(valor1);
                        }
                    })
                @endif
                ;
                break;

            // === REPORTE PAPELETA DE SALIDA ===
            case 11:
                var concatenar_valores = '';
                concatenar_valores     += '?tipo=1&salida_id=' + valor[1];

                var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                win.focus();
                break;
            // === SINCRONIZAR SALIDA PARTICULAR ===
            case 12:
                $('#modal_1').modal();
                break;
            // === RESETEAR FORMULARIO DE SINCRONIZAR SALIDA PARTICULAR ===
            case 13:
                $('#modal_1_title').empty();
                $('#modal_1_title').append('Sincronizar salida particular');

                $('#persona_id_1').select2("val", "");
                $('#persona_id_1 option').remove();

                $('#lugar_dependencia_id_funcionario_1').select2("val", "");

                $('#fecha_del_1, #fecha_al_1').val("").datepicker("update");

                $(form_1)[0].reset();
                break;
            // === SINCRONIZAR SALIDA PARTICULAR ===
            case 14:
                var concatenar_valores = '';
                concatenar_valores     += 'tipo=1&_token=' + csrf_token;

                var fecha_del = $("#fecha_del_1").val();
                var fecha_al  = $("#fecha_al_1").val();

                var persona_id = $("#persona_id_1").val();

                var lugar_dependencia_id_funcionario = $("#lugar_dependencia_id_funcionario_1").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(fecha_del) != ''){
                    concatenar_valores += '&fecha_del=' + fecha_del;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo FECHA DEL es obligatorio.';
                }

                if($.trim(fecha_al) != ''){
                    concatenar_valores += '&fecha_al=' + fecha_al;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo FECHA AL es obligatorio.';
                }

                if($.trim(persona_id) != '' || $.trim(lugar_dependencia_id_funcionario) != ''){
                    if($.trim(persona_id) != ''){
                        concatenar_valores += '&persona_id=' + persona_id;
                    }

                    if($.trim(lugar_dependencia_id_funcionario) != ''){
                        concatenar_valores += '&lugar_dependencia_id_funcionario=' + lugar_dependencia_id_funcionario;
                    }
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo FUNCIONARIO o LUGAR DE DEPENDENCIA es obligatorio.';
                }

                if(valor_sw){
                    swal({
                        title             : "SINCRONIZANDO SALIDAS PARTICULARES",
                        text              : "Espere a que se sincronicen las asistencias.",
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

            // === MENSAJE ERROR ===
            case 100:
                toastr.success(valor[2], valor[1], options1);
                break;
            // === MENSAJE ERROR ===
            case 101:
                toastr.error(valor[2], valor[1], options1);
                break;
            // === MENSAJE ALERTA ===
            case 102:
                toastr.warning(valor[2], valor[1], options1);
                break;

            // === AJAX ===
            case 150:
                $.ajax({
                    url: valor[1],
                    type: valor[2],
                    async: valor[3],
                    data: valor[4],
                    dataType: valor[5],
                    success: function(data){
                        switch(data.tipo){
                            // === SINCRONIZAR SALIDA PARTICULAR ===
                            case '1':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");
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
                break;

            default:
                break;
        }
    }

</script>
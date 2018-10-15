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
        var url_controller = "{!! url('/marcacion_biometrico') !!}";
        var csrf_token     = "{!! csrf_token() !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",

            "ESTADO",
            "TIPO DE MARCACION",

            "MARCACION",

            "C.I.",

            "CODIGO AF",
            "IP",
            "UNIDAD DESCONCENTRADA",
            "LUGAR DE DEPENDENCIA",

            ""
        );
        var col_m_name_1  = new Array(
            "act",

            "estado",
            "tipo_marcacion",

            "f_marcacion",

            "n_documento_biometrico",

            "codigo_af",
            "ip",
            "ud_funcionario",
            "lugar_dependencia_funcionario",

            "val_json"
        );
        var col_m_index_1 = new Array(
            "",

            "rrhh_log_marcaciones_backup.estado",
            "rrhh_log_marcaciones_backup.tipo_marcacion",

            "rrhh_log_marcaciones_backup.f_marcacion::text",

            "rrhh_log_marcaciones_backup.n_documento_biometrico::text",

            "a2.codigo_af",
            "a2.ip",
            "a3.nombre",
            "a4.nombre",

            ""
        );
        var col_m_width_1 = new Array(
            33,

            90,
            180,

            140,

            70,

            100,
            110,
            400,
            400,

            10
        );
        var col_m_align_1 = new Array(
            "center",

            "center",
            "center",

            "center",

            "center",

            "center",
            "center",
            "center",
            "center",

            "center"
        );

    // === FORMULARIOS ===
        var form_1 = "#form_1";
        var form_2 = "#form_2";

    // === ESTADO ===
        var estado_json   = $.parseJSON('{!! json_encode($estado_array) !!}');
        var estado_select = '';
        var estado_jqgrid = ':Todos';

        $.each(estado_json, function(index, value) {
            estado_select += '<option value="' + index + '">' + value + '</option>';
            estado_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO DE MARCACION ===
        var tipo_marcacion_json   = $.parseJSON('{!! json_encode($tipo_marcacion_array) !!}');
        var tipo_marcacion_select = '';
        var tipo_marcacion_jqgrid = ':Todos';

        $.each(tipo_marcacion_json, function(index, value) {
            tipo_marcacion_select += '<option value="' + index + '">' + value + '</option>';
            tipo_marcacion_jqgrid += ';' + index + ':' + value;
        });

    // === LUGAR DE DEPENDENCIA ===
        var lugar_dependencia_json   = $.parseJSON('{!! json_encode($lugar_dependencia_array) !!}');
        var lugar_dependencia_select = '';
        var lugar_dependencia_jqgrid = ':Todos';

        $.each(lugar_dependencia_json, function(index, value) {
            lugar_dependencia_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            lugar_dependencia_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    $(document).ready(function(){
        //=== INICIALIZAR ===
            $('#fecha_jqgrid, #fecha_del_1, #fecha_al_1, #fecha_del_2, #fecha_al_2').datepicker({
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

            $('#persona_id_1, #persona_id_2').select2({
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
            $("#persona_id_2").appendTo("#persona_id_div_2");

        // === SELECT CHANGE ===

        // === SELECT CHANGE JQGRID 1 ===
            $('#fecha_jqgrid').datepicker().on('changeDate', function(e){
                switch(e.format("yyyy-mm-dd")){
                    case '':
                        $(jqgrid1).jqGrid('setGridParam',{
                            url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
                            datatype: 'json'
                        }).trigger('reloadGrid');
                        break;
                    default:
                        $(jqgrid1).jqGrid('setGridParam',{
                            url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1&fecha=' + e.format("yyyy-mm-dd"),
                            datatype: 'json'
                        }).trigger('reloadGrid');
                        break;
                }
            });

        // === JQGRID 1 ===
            var valor1 = new Array();
            valor1[0]  = 10;
            utilitarios(valor1);

        // === VALIDATE 1 ===

        // Add responsive to jqGrid
            $(window).bind('resize', function () {
            var width = $('.jqGrid_wrapper').width();
                $(jqgrid1).setGridWidth(width);
            });

            setTimeout(function(){
                $('.wrapper-content').removeClass('animated fadeInRight');
                var valor1 = new Array();
                valor1[0]  = 0;
                utilitarios(valor1);
            },0);

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
                var ancho_d = 29;

                var edit1  = true;
                var ancho1 = 5;

                @if(in_array(['codigo' => '1802'], $permisos))
                    // edit1  = false;
                    // ancho1 += ancho_d;
                @endif

                $(jqgrid1).jqGrid({
                    // caption     : title_table,
                    url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1&fecha=' + $("#fecha_jqgrid").val(),
                    datatype    : 'json',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid1,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'rrhh_log_marcaciones_backup.f_marcacion',
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
                        col_name_1[0],

                        col_name_1[1],
                        col_name_1[2],

                        col_name_1[3],

                        col_name_1[4],

                        col_name_1[5],
                        col_name_1[6],
                        col_name_1[7],
                        col_name_1[8],

                        col_name_1[9]
                    ],
                    colModel : [
                        {
                            name    : col_m_name_1[0],
                            index   : col_m_index_1[0],
                            width   : ancho1,
                            align   : col_m_align_1[0],
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : edit1
                        },

                        {
                            name       : col_m_name_1[1],
                            index      : col_m_index_1[1],
                            width      : col_m_width_1[1],
                            align      : col_m_align_1[1],
                            stype      :'select',
                            editoptions: {value:estado_jqgrid}
                        },
                        {
                            name       : col_m_name_1[2],
                            index      : col_m_index_1[2],
                            width      : col_m_width_1[2],
                            align      : col_m_align_1[2],
                            stype      :'select',
                            editoptions: {value:tipo_marcacion_jqgrid}
                        },

                        {
                            name : col_m_name_1[3],
                            index: col_m_index_1[3],
                            width: col_m_width_1[3],
                            align: col_m_align_1[3]
                        },

                        {
                            name : col_m_name_1[4],
                            index: col_m_index_1[4],
                            width: col_m_width_1[4],
                            align: col_m_align_1[4]
                        },

                        {
                            name : col_m_name_1[5],
                            index: col_m_index_1[5],
                            width: col_m_width_1[5],
                            align: col_m_align_1[5]
                        },
                        {
                            name : col_m_name_1[6],
                            index: col_m_index_1[6],
                            width: col_m_width_1[6],
                            align: col_m_align_1[6]
                        },
                        {
                            name  : col_m_name_1[7],
                            index : col_m_index_1[7],
                            width : col_m_width_1[7],
                            align : col_m_align_1[7]
                        },
                        {
                            name       : col_m_name_1[8],
                            index      : col_m_index_1[8],
                            width      : col_m_width_1[8],
                            align      : col_m_align_1[8],
                            stype      :'select',
                            editoptions: {value:lugar_dependencia_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_1[9],
                                index : col_m_index_1[9],
                                width : col_m_width_1[9],
                                align : col_m_align_1[9],
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

                            var ora_1 = '';

                            @if(in_array(['codigo' => '1306'], $permisos))
                                ora_1 = " <button type='button' class='btn btn-xs btn-success' title='Obtener registro de asistencia' onclick=\"utilitarios([25, " + cl + "']);\"><i class='fa fa-long-arrow-right'></i></button>";
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act: $.trim(ora_1)
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'codigo_af',
                            numberOfColumns: 4,
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
                .navSeparatorAdd(pjqgrid1,{
                    sepclass : "ui-separator"
                })
                @if(in_array(['codigo' => '1802'], $permisos))
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "ora1",
                        caption       : "",
                        title         : 'Obtener registro de asistencia',
                        buttonicon    : "ui-icon ui-icon-arrowthick-1-e",
                        onClickButton : function(){
                            var valor1 = new Array();
                            valor1[0]  = 13;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 11;
                            utilitarios(valor1);
                        }
                    })
                @endif
                @if(in_array(['codigo' => '1803'], $permisos))
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "imm1",
                        caption       : "",
                        title         : 'Imprimir marcaciones',
                        buttonicon    : "ui-icon ui-icon-print",
                        onClickButton : function(){
                            var valor1 = new Array();
                            valor1[0]  = 16;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 14;
                            utilitarios(valor1);
                        }
                    })
                @endif
                ;
                break;
            // === OBTENER ASISTENCIA MODAL ===
            case 11:
                $('#modal_1').modal();
                break;
            // === OBTENER ASISTENCIA ===
            case 12:
                var concatenar_valores = '';
                concatenar_valores     += 'tipo=1&_token=' + csrf_token;

                var fecha_del = $("#fecha_del_1").val();
                var fecha_al  = $("#fecha_al_1").val();

                var persona_id = $("#persona_id_1").val();

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

                if(fecha_del <= fecha_al){
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>La FECHA DEL debe de ser menor o igual a FECHA AL.';
                }

                if($.trim(persona_id) != ''){
                    concatenar_valores += '&persona_id=' + persona_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>Seleccione una persona.';
                }

                if(valor_sw){
                    swal({
                        title             : "OBTENIENDO ASISTENCIAS",
                        text              : "Espere a que se obtenga las asistencias.",
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
            // === LIMPIAR FORMULARIO OBTENER ASISTENCIA ===
            case 13:
                $('#modal_1_title').empty();
                $('#modal_1_title').append('Obtener asistencia');

                $('#persona_id_1').select2("val", "");

                $('#fecha_del_1, #fecha_al_1').val("").datepicker("update");

                $(form_1)[0].reset();
                break;
            // === IMPRIMIR MARCACIONES ===
            case 14:
                $('#modal_2').modal();
                break;
            // === IMPRIMIR MARCACIONES ENVIAR ===
            case 15:
                var concatenar_valores = '';
                concatenar_valores     += '?tipo=10';

                var fecha_del = $("#fecha_del_2").val();
                var fecha_al  = $("#fecha_al_2").val();

                var persona_id = $("#persona_id_2").val();

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

                if(fecha_del <= fecha_al){
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>La FECHA DEL debe de ser menor o igual a FECHA AL.';
                }

                if($.trim(persona_id) != ''){
                    concatenar_valores += '&persona_id=' + persona_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>Seleccione una persona.';
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
            // === IMPRIMIR MARCACIONES LIMPIAR ===
            case 16:
                $('#modal_2_title').empty();
                $('#modal_2_title').append('Imprimir marcaciones');

                $('#persona_id_2').select2("val", "");

                $('#fecha_del_2, #fecha_al_2').val("").datepicker("update");

                $(form_2)[0].reset();
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
                            // === OBTENER ASISTENCIA ===
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
                break;
            default:
                break;
        }
    }
</script>
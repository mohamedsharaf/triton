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
        var url_controller = "{!! url('/asistencia') !!}";
        var csrf_token     = "{!! csrf_token() !!}";
        var public_url     = "{!! asset($public_url) !!}";
        var f_corte        = "{!! $f_corte !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",

            "ESTADO",

            "FECHA",

            "C.I.",
            "NOMBRE(S)",
            "AP. PATERNO",
            "AP. MATERNO",

            "INGRESO",
            "SALIDA",
            "RETRASO",

            "INGRESO",
            "SALIDA",
            "RETRASO",

            "UNIDAD DESCONCENTRADA",
            "LUGAR DE DEPENDENCIA",

            "",

            "",
            "",
            "",
            ""
        );
        var col_m_name_1  = new Array(
            "act",

            "estado",

            "fecha",

            "n_documento",
            "nombre_persona",
            "ap_paterno",
            "ap_materno",

            "horario_1_i",
            "horario_1_s",
            "h1_min_retrasos",

            "horario_2_i",
            "horario_2_s",
            "h2_min_retrasos",

            "ud_funcionario",
            "lugar_dependencia_funcionario",

            "val_json",

            "h_1_e",
            "h_1_s",
            "h_2_e",
            "h_2_s"
        );
        var col_m_index_1 = new Array(
            "",

            "rrhh_asistencias.estado",

            "rrhh_asistencias.fecha::text",

            "a2.n_documento",
            "a2.nombre",
            "a2.ap_paterno",
            "a2.ap_materno",

            "rrhh_asistencias.horario_1_i",
            "rrhh_asistencias.horario_1_s",
            "rrhh_asistencias.h1_min_retrasos::text",

            "rrhh_asistencias.horario_2_i",
            "rrhh_asistencias.horario_2_s",
            "rrhh_asistencias.h2_min_retrasos::text",

            "a3.nombre",
            "a4.nombre",

            "",

            "",
            "",
            "",
            ""
        );
        var col_m_width_1 = new Array(
            33,

            90,

            80,

            80,
            150,
            120,
            120,

            250,
            250,
            65,

            250,
            250,
            65,

            400,
            400,

            10,

            10,
            10,
            10,
            10
        );
        var col_m_align_1 = new Array(
            "center",

            "center",

            "center",

            "right",
            "center",
            "center",
            "center",

            "center",
            "center",
            "center",

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

    // === JQGRID2 ===
        var jqgrid2   = "#jqgrid2";
        var pjqgrid2 = "#pjqgrid2";

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

    // === OMISION ===
        var omision_json   = $.parseJSON('{!! json_encode($omision_array) !!}');
        var omision_select = '';
        var omision_jqgrid = ':Todos';

        $.each(omision_json, function(index, value) {
            omision_select += '<option value="' + index + '">' + value + '</option>';
            omision_jqgrid += ';' + index + ':' + value;
        });

    // === FALTA ===
        var falta_json   = $.parseJSON('{!! json_encode($falta_array) !!}');
        var falta_select = '';
        var falta_jqgrid = ':Todos';

        $.each(falta_json, function(index, value) {
            falta_select += '<option value="' + index + '">' + value + '</option>';
            falta_jqgrid += ';' + index + ':' + value;
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
            $('#fecha_jqgrid, #fecha_del, #fecha_al, #fecha_del_2, #fecha_al_2, #fecha_del_7, #fecha_al_7, #fecha_del_8, #fecha_al_8').datepicker({
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

            $('#persona_id, #persona_id_2, #persona_id_7, #persona_id_8').select2({
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
            $("#persona_id").appendTo("#persona_id_div");
            $("#persona_id_2").appendTo("#persona_id_div_2");
            $("#persona_id_7").appendTo("#persona_id_div_7");
            $("#persona_id_8").appendTo("#persona_id_div_8");

            $('#lugar_dependencia_id_funcionario, #lugar_dependencia_id_cargo, #lugar_dependencia_id_funcionario_2, #lugar_dependencia_id_funcionario_7, #lugar_dependencia_id_funcionario_8').append(lugar_dependencia_select);
            $("#lugar_dependencia_id_funcionario, #lugar_dependencia_id_cargo, #unidad_desconcentrada_id, #auo_id, #cargo_id, #horario_id_1, #horario_id_2, #lugar_dependencia_id_funcionario_2, #lugar_dependencia_id_funcionario_7, #lugar_dependencia_id_funcionario_8").select2({
                maximumSelectionLength: 1
            });
            $("#lugar_dependencia_id_funcionario").appendTo("#lugar_dependencia_id_funcionario_div");
            $("#lugar_dependencia_id_cargo").appendTo("#lugar_dependencia_id_cargo_div");
            $("#unidad_desconcentrada_id").appendTo("#unidad_desconcentrada_id_div");
            $("#auo_id").appendTo("#auo_id_div");
            $("#cargo_id").appendTo("#cargo_id_div");

            $("#lugar_dependencia_id_funcionario_2").appendTo("#lugar_dependencia_id_funcionario_div_2");

            $("#lugar_dependencia_id_funcionario_7").appendTo("#lugar_dependencia_id_funcionario_div_7");

            $("#lugar_dependencia_id_funcionario_8").appendTo("#lugar_dependencia_id_funcionario_div_8");

        // === DROPZONE ===
            // var valor1 = new Array();
            // valor1[0]  = 20;
            // utilitarios(valor1);

        // === SELECT CHANGE ===
        	$("#lugar_dependencia_id_funcionario").on("change", function(e) {
                $('#unidad_desconcentrada_id').select2('val','');
                $('#unidad_desconcentrada_id option').remove();

                $('#horario_id_1').select2('val','');
                $('#horario_id_1 option').remove();

                $('#horario_id_2').select2('val','');
                $('#horario_id_2 option').remove();
                switch ($.trim(this.value)){
                    case '':
                        break;
                    default:
                        var valor1 = new Array();
                        valor1[0]  = 150;
                        valor1[1]  = url_controller + '/send_ajax';
                        valor1[2]  = 'POST';
                        valor1[3]  = false;
                        valor1[4]  = "tipo=103&lugar_dependencia_id=" + this.value + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                }
            });

            $("#lugar_dependencia_id_cargo").on("change", function(e) {
                $('#auo_id').select2('val','');
                $('#auo_id option').remove();

                $('#cargo_id').select2('val','');
                $('#cargo_id option').remove();
                switch ($.trim(this.value)){
                    case '':
                        break;
                    default:
                        var valor1 = new Array();
                        valor1[0]  = 150;
                        valor1[1]  = url_controller + '/send_ajax';
                        valor1[2]  = 'POST';
                        valor1[3]  = false;
                        valor1[4]  = "tipo=101&lugar_dependencia_id=" + this.value + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                }
            });

            $("#auo_id").on("change", function(e) {
                $('#cargo_id').select2('val','');
                $('#cargo_id option').remove();
                switch ($.trim(this.value)){
                    case '':
                        break;
                    default:
                        var valor1 = new Array();
                        valor1[0]  = 150;
                        valor1[1]  = url_controller + '/send_ajax';
                        valor1[2]  = 'POST';
                        valor1[3]  = false;
                        valor1[4]  = "tipo=102&auo_id=" + this.value + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                }
            });

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

        // === JQGRID 2 ===
            var valor1 = new Array();
            valor1[0]  = 23;
            utilitarios(valor1);

        // === VALIDATE 1 ===
            // var valor1 = new Array();
            // valor1[0]  = 16;
            // utilitarios(valor1);

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
                $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
                break;
            // === JQGRID 1 ===
            case 10:
                var ancho_d = 29;

                var edit1  = true;
                var ancho1 = 5;

                @if(in_array(['codigo' => '1306'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                var edit2  = true;
                var ancho2 = 5;

                @if(in_array(['codigo' => '1304'], $permisos))
                    edit2  = false;
                    ancho2 += ancho_d;
                @endif
                @if(in_array(['codigo' => '1305'], $permisos))
                    edit2  = false;
                    ancho2 += ancho_d;
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
                    sortname    : 'rrhh_asistencias.fecha',
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
                        col_name_1[16],
                        col_name_1[8],
                        col_name_1[17],
                        col_name_1[9],

                        col_name_1[10],
                        col_name_1[18],
                        col_name_1[11],
                        col_name_1[19],
                        col_name_1[12],

                        col_name_1[13],
                        col_name_1[14],

                        col_name_1[15]
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
                            name : col_m_name_1[2],
                            index: col_m_index_1[2],
                            width: col_m_width_1[2],
                            align: col_m_align_1[2]
                        },

                        {
                            name  : col_m_name_1[3],
                            index : col_m_index_1[3],
                            width : col_m_width_1[3],
                            align : col_m_align_1[3]
                        },
                        {
                            name  : col_m_name_1[4],
                            index : col_m_index_1[4],
                            width : col_m_width_1[4],
                            align : col_m_align_1[4]
                        },
                        {
                            name : col_m_name_1[5],
                            index: col_m_index_1[5],
                            width: col_m_width_1[5],
                            align: col_m_align_1[5]
                        },
                        {
                            name  : col_m_name_1[6],
                            index : col_m_index_1[6],
                            width : col_m_width_1[6],
                            align : col_m_align_1[6]
                        },

                        //=== HORARIO 1 ===
                            {
                                name : col_m_name_1[7],
                                index: col_m_index_1[7],
                                width: col_m_width_1[7],
                                align: col_m_align_1[7]
                            },
                            {
                                name    : col_m_name_1[16],
                                index   : col_m_index_1[16],
                                width   : ancho2,
                                align   : col_m_align_1[16],
                                fixed   : true,
                                sortable: false,
                                resize  : false,
                                search  : false,
                                hidden  : edit2
                            },
                            {
                                name : col_m_name_1[8],
                                index: col_m_index_1[8],
                                width: col_m_width_1[8],
                                align: col_m_align_1[8]
                            },
                            {
                                name    : col_m_name_1[17],
                                index   : col_m_index_1[17],
                                width   : ancho2,
                                align   : col_m_align_1[17],
                                fixed   : true,
                                sortable: false,
                                resize  : false,
                                search  : false,
                                hidden  : edit2
                            },
                            {
                                name : col_m_name_1[9],
                                index: col_m_index_1[9],
                                width: col_m_width_1[9],
                                align: col_m_align_1[9]
                            },

                        //=== HORARIO 2 ===
                            {
                                name : col_m_name_1[10],
                                index: col_m_index_1[10],
                                width: col_m_width_1[10],
                                align: col_m_align_1[10]
                            },
                            {
                                name    : col_m_name_1[18],
                                index   : col_m_index_1[18],
                                width   : ancho2,
                                align   : col_m_align_1[18],
                                fixed   : true,
                                sortable: false,
                                resize  : false,
                                search  : false,
                                hidden  : edit2
                            },
                            {
                                name : col_m_name_1[11],
                                index: col_m_index_1[11],
                                width: col_m_width_1[11],
                                align: col_m_align_1[11]
                            },
                            {
                                name    : col_m_name_1[19],
                                index   : col_m_index_1[19],
                                width   : ancho2,
                                align   : col_m_align_1[19],
                                fixed   : true,
                                sortable: false,
                                resize  : false,
                                search  : false,
                                hidden  : edit2
                            },
                            {
                                name : col_m_name_1[12],
                                index: col_m_index_1[12],
                                width: col_m_width_1[12],
                                align: col_m_align_1[12]
                            },

                        {
                            name : col_m_name_1[13],
                            index: col_m_index_1[13],
                            width: col_m_width_1[13],
                            align: col_m_align_1[13]
                        },
                        {
                            name       : col_m_name_1[14],
                            index      : col_m_index_1[14],
                            width      : col_m_width_1[14],
                            align      : col_m_align_1[14],
                            stype      :'select',
                            editoptions: {value:lugar_dependencia_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_1[15],
                                index : col_m_index_1[15],
                                width : col_m_width_1[15],
                                align : col_m_align_1[15],
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

                            var ci_nombre = ret.nombre_persona + ' ' + $.trim(ret.ap_paterno + ' ' + ret.ap_materno);

                            var del_1 = '';

                            @if(in_array(['codigo' => '1306'], $permisos))
                                if(val_json.estado != '3'){
                                    del_1 = " <button type='button' class='btn btn-xs btn-danger' title='Eliminar ASISTENCIA' onclick=\"utilitarios([25, " + cl + ", '" + ci_nombre + "', '" + ret.fecha + "']);\"><i class='fa fa-trash'></i></button>";
                                }
                            @endif

                            var vac_h1_e = '';
                            var vac_h1_s = '';
                            var vac_h2_e = '';
                            var vac_h2_s = '';

                            var mig_h1_e = '';
                            var mig_h1_s = '';
                            var mig_h2_e = '';
                            var mig_h2_s = '';

                            @if(in_array(['codigo' => '1304'], $permisos))
                                if(val_json.estado != '3'){
                                    if(val_json.fthc_id_h1 == null){
                                        if((val_json.h1_falta == '1') || ((val_json.h1_i_omitir == '2') && (val_json.h1_s_omitir == '2'))){
                                            vac_h1_e = "<button type='button' class='btn btn-xs btn-info' title='Licencia por VACACIONES' onclick=\"utilitarios([20, " + cl + ", 1, '" + ci_nombre +"']);\"><i class='fa fa-suitcase'></i></button>";
                                            vac_h1_s = "<button type='button' class='btn btn-xs btn-info' title='Licencia por VACACIONES' onclick=\"utilitarios([20, " + cl + ", 1, '" + ci_nombre +"']);\"><i class='fa fa-suitcase'></i></button>";
                                        }
                                    }

                                    if(val_json.fthc_id_h2 == null){
                                        if((val_json.h2_falta == '1') || ((val_json.h2_i_omitir == '2') && (val_json.h2_s_omitir == '2'))){
                                            vac_h2_e = "<button type='button' class='btn btn-xs btn-info' title='Licencia por VACACIONES' onclick=\"utilitarios([20, " + cl + ", 2, '" + ci_nombre +"']);\"><i class='fa fa-suitcase'></i></button>";
                                            vac_h2_s = "<button type='button' class='btn btn-xs btn-info' title='Licencia por VACACIONES' onclick=\"utilitarios([20, " + cl + ", 2, '" + ci_nombre +"']);\"><i class='fa fa-suitcase'></i></button>";
                                        }
                                    }
                                }
                            @endif

                            @if(in_array(['codigo' => '1305'], $permisos))
                                if(val_json.estado != '3'){
                                    if(ret.fecha <= f_corte){
                                        if((val_json.h1_i_omitir != '2')){
                                            if((val_json.h1_falta == '1') || (val_json.h1_i_omision_registro == '1') || (val_json.h1_i_omitir == '3')){
                                                mig_h1_e = " <button type='button' class='btn btn-xs btn-danger' title='Licencia por MIGRACION' onclick=\"utilitarios([21, " + cl + ", 1, 1, '" + ci_nombre +"']);\"><i class='fa fa-history'></i></button>";
                                            }
                                        }

                                        if((val_json.h1_s_omitir != '2')){
                                            if((val_json.h1_falta == '1') || (val_json.h1_s_omision_registro == '1') || (val_json.h1_s_omitir == '3')){
                                                mig_h1_s = " <button type='button' class='btn btn-xs btn-danger' title='Licencia por MIGRACION' onclick=\"utilitarios([21, " + cl + ", 1, 2, '" + ci_nombre +"']);\"><i class='fa fa-history'></i></button>";
                                            }
                                        }

                                        if((val_json.h2_i_omitir != '2')){
                                            if((val_json.h2_falta == '1') || (val_json.h2_i_omision_registro == '1') || (val_json.h2_i_omitir == '3')){
                                                mig_h2_e = " <button type='button' class='btn btn-xs btn-danger' title='Licencia por MIGRACION' onclick=\"utilitarios([21, " + cl + ", 2, 1, '" + ci_nombre +"']);\"><i class='fa fa-history'></i></button>";
                                            }
                                        }

                                        if((val_json.h2_s_omitir != '2')){
                                            if((val_json.h2_falta == '1') || (val_json.h2_s_omision_registro == '1') || (val_json.h2_s_omitir == '3')){
                                                mig_h2_s = " <button type='button' class='btn btn-xs btn-danger' title='Licencia por MIGRACION' onclick=\"utilitarios([21, " + cl + ", 2, 2, '" + ci_nombre +"']);\"><i class='fa fa-history'></i></button>";
                                            }
                                        }
                                    }
                                }
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act  : $.trim(del_1),
                                h_1_e: $.trim(vac_h1_e + mig_h1_e),
                                h_1_s: $.trim(vac_h1_s + mig_h1_s),
                                h_2_e: $.trim(vac_h2_e + mig_h2_e),
                                h_2_s: $.trim(vac_h2_s + mig_h2_s)
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
                            titleText      : 'FUNCIONARIO'
                        },
                        {
                            startColumnName: 'horario_1_i',
                            numberOfColumns: 5,
                            titleText      : 'HORARIO 1'
                        },
                        {
                            startColumnName: 'horario_2_i',
                            numberOfColumns: 5,
                            titleText      : 'HORARIO 2'
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
                @if(in_array(['codigo' => '1302'], $permisos))
                    .navButtonAdd(pjqgrid1,{
                    "id"          : "add1",
                    caption       : "",
                    title         : 'Agregar fechas',
                    buttonicon    : "ui-icon ui-icon-plusthick",
                    onClickButton : function(){
                        var valor1 = new Array();
                        valor1[0]  = 14;
                        utilitarios(valor1);

                        var valor1 = new Array();
                        valor1[0]  = 11;
                        utilitarios(valor1);
                    }
                })
                @endif
                @if(in_array(['codigo' => '1303'], $permisos))
                    .navButtonAdd(pjqgrid1,{
                    "id"          : "add2",
                    caption       : "",
                    title         : 'Sincronizar asistencias',
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
                @if(in_array(['codigo' => '1306'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                      sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "del1",
                        caption       : "",
                        title         : 'Eliminar asistencias',
                        buttonicon    : "ui-icon ui-icon-trash",
                        onClickButton : function(){
                            var valor1 = new Array();
                            valor1[0]  = 28;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 26;
                            utilitarios(valor1);
                        }
                    })
                @endif
                @if(in_array(['codigo' => '1307'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                      sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "rep1",
                        caption       : "",
                        title         : 'Generar reporte de asistencias',
                        buttonicon    : "ui-icon ui-icon-print",
                        onClickButton : function(){
                            var valor1 = new Array();
                            valor1[0]  = 32;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 29;
                            utilitarios(valor1);
                        }
                    })
                @endif
                ;
                break;
            // === AGREGAR FECHA MODAL ===
            case 11:
                $('#modal_1').modal();
                break;
            // === SINCRONIZAR ASISTENCIAS MODAL ===
            case 12:
                $('#modal_2').modal();
                break;
            // === RESETEAR FORMULARIO DE SINCRONIZAR ASISTENCIAS ===
            case 13:
                $('#modal_2_title').empty();
                $('#modal_2_title').append('Sincronizar asistencia');

                $('#persona_id_2').select2("val", "");
                $('#persona_id_2 option').remove();

                $('#lugar_dependencia_id_funcionario_2').select2("val", "");

                $('#fecha_del, #fecha_al, #fecha_del_2, #fecha_al_2').val("").datepicker("update");

                $(form_2)[0].reset();
                break;
            // === RESETEAR FORMULARIO DE AGREGAR FECHA ===
            case 14:
            	$('#modal_1_title').empty();
                $('#modal_1_title').append('Agregar fechas para el controlar asistencia');

                $('#persona_id').select2("val", "");
                $('#persona_id option').remove();

                $('#lugar_dependencia_id_funcionario').select2("val", "");

                $('#unidad_desconcentrada_id').select2("val", "");
                $('#unidad_desconcentrada_id option').remove();

                $('#lugar_dependencia_id_cargo').select2("val", "");

                $('#auo_id').select2("val", "");
                $('#auo_id option').remove();

                $('#cargo_id').select2("val", "");
                $('#cargo_id option').remove();

                $('#fecha_del, #fecha_al, #fecha_del_2, #fecha_al_2').val("").datepicker("update");

                $(form_1)[0].reset();
                break;
            // === GUARDAR FECHA DE ASISTENCIAS ===
            case 15:
            	var concatenar_valores = '';
                concatenar_valores     += 'tipo=1&_token=' + csrf_token;

				var fecha_del = $("#fecha_del").val();
				var fecha_al  = $("#fecha_al").val();

				var persona_id = $("#persona_id").val();

				var lugar_dependencia_id_funcionario = $("#lugar_dependencia_id_funcionario").val();
				var unidad_desconcentrada_id         = $("#unidad_desconcentrada_id").val();

                var horario_id_1 = $("#horario_id_1").val();
                var horario_id_2 = $("#horario_id_2").val();

				var lugar_dependencia_id_cargo = $("#lugar_dependencia_id_cargo").val();
				var auo_id                     = $("#auo_id").val();
				var cargo_id                   = $("#cargo_id").val();

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

                if($.trim(lugar_dependencia_id_funcionario) != ''){
                    concatenar_valores += '&lugar_dependencia_id_funcionario=' + lugar_dependencia_id_funcionario;
                }
                else{
					valor_sw    = false;
					valor_error += '<br>El campo LUGAR DE DEPENDENCIA DEL FUNCIONARIO es obligatorio.';
                }

				if($.trim(persona_id) != ''){
                    if($.trim(persona_id) != ''){
                        concatenar_valores += '&persona_id=' + persona_id;
                    }
                    else{
						valor_sw    = false;
						valor_error += '<br>El campo FUNCIONARIO es obligatorio.';
                    }

                    if($.trim(unidad_desconcentrada_id) != ''){
                        concatenar_valores += '&unidad_desconcentrada_id=' + unidad_desconcentrada_id;
                    }
                    else{
						valor_sw    = false;
						valor_error += '<br>El campo UNIDAD DESCONCENTRADA es obligatorio.';
                    }

                    if($.trim(lugar_dependencia_id_cargo) != ''){
                        concatenar_valores += '&lugar_dependencia_id_cargo=' + lugar_dependencia_id_cargo;
                    }
                    else{
						valor_sw    = false;
						valor_error += '<br>El campo LUGAR DE DEPENDENCIA DEL CARGO es obligatorio.';
                    }

                    if($.trim(auo_id) != ''){
                        concatenar_valores += '&auo_id=' + auo_id;
                    }
                    else{
						valor_sw    = false;
						valor_error += '<br>El campo AREA O UNIDAD ORGANIZACIONAL es obligatorio.';
                    }

                    if($.trim(cargo_id) != ''){
                        concatenar_valores += '&cargo_id=' + cargo_id;
                    }
                    else{
						valor_sw    = false;
						valor_error += '<br>El campo CARGO es obligatorio.';
                    }

                    if($.trim(horario_id_1) != ''){
                        concatenar_valores += '&horario_id_1=' + horario_id_1;
                    }
                    else{
                        valor_sw    = false;
                        valor_error += '<br>El campo HORARIO 1 es obligatorio.';
                    }

                    if($.trim(horario_id_2) != ''){
                        concatenar_valores += '&horario_id_2=' + horario_id_2;
                    }
                    else{
                        valor_sw    = false;
                        valor_error += '<br>El campo HORARIO 2 es obligatorio.';
                    }
                }

                if(valor_sw){
                	swal({
                        title             : "CREANDO FECHAS",
                        text              : "Espere a que se creen las fechas para el control de las asistencias.",
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
            // === SINCRONIZAR ASISTENCIAS ===
            case 16:
                var concatenar_valores = '';
                concatenar_valores     += 'tipo=2&_token=' + csrf_token;

                var fecha_del = $("#fecha_del_2").val();
                var fecha_al  = $("#fecha_al_2").val();

                var persona_id = $("#persona_id_2").val();

                var lugar_dependencia_id_funcionario = $("#lugar_dependencia_id_funcionario_2").val();

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
                        title             : "SINCRONIZANDO ASISTENCIAS",
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
            // === REPORTE SALIDA ===
            case 17:
                var concatenar_valores = '';
                concatenar_valores     += '?tipo=1&salida_id=' + valor[1];

                var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                win.focus();
                break;
            // === DONDE ASISTIO ===
            case 18:
                $('#modal_3_title, #modal_3_subtitle, #td_ud, #td_ld').empty();
                $('#modal_3_title').append('DONDE ASISTIO');

                var ret = $(jqgrid1).jqGrid('getRowData', valor[2]);

                var persona = ret.n_documento + ' - ' + ret.nombre_persona + ' ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno);

                $('#modal_3_subtitle').append(persona);

                var valor1 = new Array();
                valor1[0]  = 150;
                valor1[1]  = url_controller + '/send_ajax';
                valor1[2]  = 'POST';
                valor1[3]  = false;
                valor1[4]  = "tipo=50&id=" + valor[1] + "&_token=" + csrf_token;
                valor1[5]  = 'json';
                utilitarios(valor1);

                $('#modal_3').modal();
                break;
            // === FERIADO, TOLERANCIA, HORARIO CONTINUO ===
            case 19:
                $('#modal_4_title, #modal_4_subtitle, #th_nombre_4, #td_nombre_4, #td_ud_4, #td_ld_4').empty();
                $('#modal_4_title').append(valor[3]);

                var ret = $(jqgrid1).jqGrid('getRowData', valor[2]);

                var persona = ret.n_documento + ' - ' + ret.nombre_persona + ' ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno);

                $('#modal_4_subtitle').append(persona);
                $('#th_nombre_4').append(valor[3]);

                var valor1 = new Array();
                valor1[0]  = 150;
                valor1[1]  = url_controller + '/send_ajax';
                valor1[2]  = 'POST';
                valor1[3]  = false;
                valor1[4]  = "tipo=51&id=" + valor[1] + "&_token=" + csrf_token;
                valor1[5]  = 'json';
                utilitarios(valor1);

                $('#modal_4').modal();
                break;
            // === LICENCIA POR VACACIONES ===
            case 20:
                swal({
                    title             : "LICENCIA POR VACACIONES",
                    text              : "¿" + valor[3] + " está de VACIONES?",
                    type              : "warning",
                    showCancelButton  : true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText : "Vacaciones",
                    cancelButtonText  : "Cancelar",
                    closeOnConfirm    : false,
                    closeOnCancel     : false
                },
                function(isConfirm){
                    if (isConfirm){
                        swal.close();

                        swal({
                            title            : "Modificando VACACIONES",
                            text             : "Espere a que se modifique las VACACIONES.",
                            allowEscapeKey   : false,
                            showConfirmButton: false,
                            type             : "info"
                        });
                        $(".sweet-alert div.sa-info").removeClass("sa-icon sa-info").addClass("fa fa-refresh fa-4x fa-spin");

                        var valor1 = new Array();
                        valor1[0]  = 150;
                        valor1[1]  = url_controller + '/send_ajax';
                        valor1[2]  = 'POST';
                        valor1[3]  = true;
                        valor1[4]  = "tipo=3&id=" + valor[1] + "&horario=" + valor[2] + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                    }
                    else{
                        swal.close();
                    }
                });
                break;
            // === LICENCIA POR MIGRACION ===
            case 21:
                swal({
                    title             : "LICENCIA POR MIGRACION",
                    text              : "¿Se registrará/quitará LICENCIA POR MIGRACION de " + valor[4] + "?",
                    type              : "warning",
                    showCancelButton  : true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText : "Migración",
                    cancelButtonText  : "Cancelar",
                    closeOnConfirm    : false,
                    closeOnCancel     : false
                },
                function(isConfirm){
                    if (isConfirm){
                        swal.close();

                        swal({
                            title            : "REGISTRANDO/QUITANDO MIGRACION",
                            text             : "Espere a que se registre/quite la MIGRACION.",
                            allowEscapeKey   : false,
                            showConfirmButton: false,
                            type             : "info"
                        });
                        $(".sweet-alert div.sa-info").removeClass("sa-icon sa-info").addClass("fa fa-refresh fa-4x fa-spin");

                        var valor1 = new Array();
                        valor1[0]  = 150;
                        valor1[1]  = url_controller + '/send_ajax';
                        valor1[2]  = 'POST';
                        valor1[3]  = true;
                        valor1[4]  = "tipo=4&id=" + valor[1] + "&horario=" + valor[2] + "&salida_entrada=" + valor[3] + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                    }
                    else{
                        swal.close();
                    }
                });
                break;
            // === MODAL ABRIR MARCACIONES ===
            case 22:
                $(jqgrid2).jqGrid('setGridParam',{
                    url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=2&persona_id=' + valor[3] + '&f_marcacion=' + valor[2],
                    datatype: 'json'
                }).trigger('reloadGrid');

                var ret = $(jqgrid1).jqGrid('getRowData', valor[1]);

                var persona = ret.n_documento + ' - ' + ret.nombre_persona + ' ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno);

                $(jqgrid2).jqGrid('setCaption', "<span class='text-success'>" + persona + "</span>");

                $('#modal_5').modal();

                setTimeout(function(){
                    $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
                }, 300);
                break;
            // === JQGRID 2 ===
            case 23:
                $(jqgrid2).jqGrid({
                    caption     : '',
                    datatype    : 'local',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid2,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'rrhh_log_marcaciones.f_marcacion',
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
                        "FECHA Y HORA",
                        "BIOMETRICO",
                        "UNIDAD DESCONCENTRADA",
                        "LUGAR DE DEPENDENCIA",
                        ""
                    ],
                    colModel : [
                        {
                            name  : "f_marcacion",
                            index : "rrhh_log_marcaciones.f_marcacion::text",
                            width : "150",
                            align : "center"
                        },
                        {
                            name  : "codigo_af",
                            index : "a2.codigo_af",
                            width : "100",
                            align : "center"
                        },
                        {
                            name  : "unidad_desconcentrada",
                            index : "a3.nombre",
                            width : "700",
                            align : "center"
                        },
                        {
                            name       : "lugar_dependencia",
                            index      : "a4.nombre",
                            width      : "400",
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:lugar_dependencia_jqgrid}
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
                    loadComplete : function(){
                        $("tr.jqgrow:odd").addClass('myAltRowClass');
                    }
                });

                $(jqgrid2).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'codigo_af',
                            numberOfColumns: 3,
                            titleText      : 'UBICACION DEL BIOMETRICO'
                        }
                    ]
                });

                $(jqgrid2).jqGrid('filterToolbar',{
                    searchOnEnter : true,
                    stringResult  : true,
                    defaultSearch : 'cn'
                });

                $(jqgrid2).jqGrid('navGrid', pjqgrid2, {
                    edit  : false,
                    add   : false,
                    del   : false,
                    search: false
                })
                .navSeparatorAdd(pjqgrid1,{
                    sepclass : "ui-separator"
                })
                ;
                break;
            // === MOSTRAR USUARIO QUE MODIFICO LA ASISTENCIA ===
            case 24:
                if(valor[2] != undefined){
                    $('#modal_6_title, #modal_6_subtitle, #td_persona').empty();
                    // $('#modal_6_title').append('USUARIO QUE MODIFICO LA ASISTENCIA');

                    var ret = $(jqgrid1).jqGrid('getRowData', valor[1]);

                    var persona = ret.n_documento + ' - ' + ret.nombre_persona + ' ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno);

                    $('#modal_6_title').append(persona);

                    // $('#modal_6_subtitle').append(persona);

                    var valor1 = new Array();
                    valor1[0]  = 150;
                    valor1[1]  = url_controller + '/send_ajax';
                    valor1[2]  = 'POST';
                    valor1[3]  = false;
                    valor1[4]  = "tipo=52&id=" + valor[2] + "&_token=" + csrf_token;
                    valor1[5]  = 'json';
                    utilitarios(valor1);

                    $('#modal_6').modal();
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                    valor1[2]  = "No existe usuario que modifico la asistencia.";
                    utilitarios(valor1);
                }
                break;
            // === ELIMINAR ASISTENCIA POR FILA ===
            case 25:
                swal({
                    title             : "ELIMINAR ASISTENCIA",
                    text              : "¿Está seguro de eliminar la ASISTENCIA de " + valor[2] + " en fecha " + valor[3] + "?",
                    type              : "warning",
                    showCancelButton  : true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText : "Eliminar",
                    cancelButtonText  : "Cancelar",
                    closeOnConfirm    : false,
                    closeOnCancel     : false
                },
                function(isConfirm){
                    if (isConfirm){
                        swal.close();

                        swal({
                            title            : "ELIMINANDO ASISTENCIA",
                            text             : "Espere que se elimine la ASISTENCIA.",
                            allowEscapeKey   : false,
                            showConfirmButton: false,
                            type             : "info"
                        });
                        $(".sweet-alert div.sa-info").removeClass("sa-icon sa-info").addClass("fa fa-refresh fa-4x fa-spin");

                        var valor1 = new Array();
                        valor1[0]  = 150;
                        valor1[1]  = url_controller + '/send_ajax';
                        valor1[2]  = 'POST';
                        valor1[3]  = true;
                        valor1[4]  = "tipo=5&id=" + valor[1] + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                    }
                    else{
                        swal.close();
                    }
                });
                break;
            // === MODAL ELIMINAR ASISTENCIA ===
            case 26:
                $('#modal_7').modal();
                break;
            // === ELIMINAR ASISTENCIA ===
            case 27:
                var concatenar_valores = '';
                concatenar_valores     += 'tipo=5&_token=' + csrf_token;

                var fecha_del = $("#fecha_del_7").val();
                var fecha_al  = $("#fecha_al_7").val();

                var persona_id = $("#persona_id_7").val();

                var lugar_dependencia_id_funcionario = $("#lugar_dependencia_id_funcionario_7").val();

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
                        title             : "ELIMINANDO ASISTENCIAS",
                        text              : "Espere que se eliminen las ASISTENCIAS.",
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
            // === RESETEAR ELIMINAR ASISTENCIA ===
            case 28:
                $('#modal_7_title').empty();
                $('#modal_7_title').append('Eliminar asistencia');

                $('#persona_id_7').select2("val", "");
                $('#persona_id_7 option').remove();

                $('#lugar_dependencia_id_funcionario_7').select2("val", "");

                $('#fecha_del_7, #fecha_al_7').val("").datepicker("update");

                $(form_7)[0].reset();
                break;
            // === MODAL REPORTES ===
            case 29:
                $('#modal_8').modal();
                break;
            // === REPORTES EXCEL ===
            case 31:
                var concatenar_valores = '';
                concatenar_valores     += '?tipo=10';

                var fecha_del = $("#fecha_del_8").val();
                var fecha_al  = $("#fecha_al_8").val();

                var persona_id = $("#persona_id_8").val();

                var lugar_dependencia_id_funcionario = $("#lugar_dependencia_id_funcionario_8").val();

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
            // === RESETEAR REPORTES ===
            case 32:
                $('#modal_8_title').empty();
                $('#modal_8_title').append('Generar reportes');

                $('#persona_id_8').select2("val", "");
                $('#persona_id_8 option').remove();

                $('#lugar_dependencia_id_funcionario_8').select2("val", "");

                $('#fecha_del_8, #fecha_al_8').val("").datepicker("update");

                $(form_8)[0].reset();
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
                            // === INSERT FECHAS PARA LAS ASISTENCIAS ===
                            case '1':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");
                                    $('#modal_1').modal('hide');
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

                            // === SINCRONIZAR ASISTENCIAS ===
                            case '2':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid1).trigger("reloadGrid");
                                    $('#modal_2').modal('hide');
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

                            // === LICENCIA POR VACACIONES ===
                            case '3':
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

                                    $(jqgrid1).trigger("reloadGrid");
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;

                            // === LICENCIA POR MIGRACION ===
                            case '4':
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

                                    $(jqgrid1).trigger("reloadGrid");
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;

                            // === ELIMINAR LICENCIA ===
                            case '5':
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

                                    $(jqgrid1).trigger("reloadGrid");
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;

                            // === DONDE ASISTIO ===
                            case '50':
                                if(data.sw === 2){
                                    $('#td_ud').append(data.consulta.unidad_desconcentrada);
                                    $('#td_ld').append(data.consulta.lugar_dependencia);
                                }
                                break;

                            // === FERIADO, TOLERANCIA, HORARIO CONTINUO ===
                            case '51':
                                if(data.sw === 2){
                                    $('#td_nombre_4').append(data.consulta.nombre);
                                    $('#td_ld_4').append(data.consulta.lugar_dependencia);
                                    $('#td_ud_4').append(data.consulta.unidad_desconcentrada);
                                }
                                break;

                            // === MOSTRAR USUARIO QUE MODIFICO LA ASISTENCIA ===
                            case '52':
                                if(data.sw === 2){
                                    $('#td_persona').append(data.consulta.text);
                                }
                                break;

                            // === SELECT2 AUO POR LUGAR DE DEPENDENCIA ===
                            case '101':
                                if(data.sw === 2){
                                    var auo_select = '';
                                    $.each(data.consulta, function(index, value) {
                                        auo_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                                    });
                                    $('#auo_id').append(auo_select);
                                }
                                break;
                            // === SELECT2 CARGOS POR AUO ===
                            case '102':
                                if(data.sw === 2){
                                    var cargo_select = '';
                                    $.each(data.consulta, function(index, value) {
                                        cargo_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                                    });
                                    $('#cargo_id').append(cargo_select);
                                }
                                break;
                            // === SELECT2 UNIDAD DESCONCENTRADA POR LUGAR DE DEPENDENCIA ===
                            case '103':
                                if(data.sw === 2){
                                    var unidad_desconcentrada_select = '';
                                    $.each(data.consulta, function(index, value) {
                                        unidad_desconcentrada_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                                    });
                                    $('#unidad_desconcentrada_id').append(unidad_desconcentrada_select);

                                    if(data.sw_horario_1 === 2){
                                        var horario_1_select = '';
                                        var horario_1_defecto_id = '';
                                        $.each(data.horario_1, function(index, value) {
                                            horario_1_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                                            if(value.defecto == '2'){
                                                horario_1_defecto_id = value.id;
                                            }
                                        });
                                        $('#horario_id_1').append(horario_1_select);
                                        if(horario_1_defecto_id != ''){
                                            $("#horario_id_1").select2("val", horario_1_defecto_id);
                                        }
                                    }

                                    if(data.sw_horario_2 === 2){
                                        var horario_2_select = '';
                                        var horario_2_defecto_id = '';
                                        $.each(data.horario_2, function(index, value) {
                                            horario_2_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                                            if(value.defecto == '2'){
                                                horario_2_defecto_id = value.id;
                                            }
                                        });
                                        $('#horario_id_2').append(horario_2_select);
                                        if(horario_2_defecto_id != ''){
                                            $("#horario_id_2").select2("val", horario_2_defecto_id);
                                        }
                                    }
                                }
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
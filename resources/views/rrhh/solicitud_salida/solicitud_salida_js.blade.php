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
        var url_controller = "{!! url('/solicitud_salida') !!}";
        var csrf_token     = "{!! csrf_token() !!}";
        var public_url     = "{!! asset($public_url) !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",
            "ESTADO",
            "INMEDIATO SUPERIOR?",
            "RRHH?",
            "¿CON PDF?",

            "TIPO DE PAPELETA",
            "TIPO DE SALIDA",
            "CODIGO",

            "C.I.",
            "NOMBRE(S)",
            "AP. PATERNO",
            "AP. MATERNO",

            "DESTINO",
            "MOTIVO",

            "FECHA DE SALIDA",
            "HORA SALIDA",
            "HORA RETORNO",
            "RETORNO",

            ""
        );
        var col_m_name_1  = new Array(
            "act",
            "estado",
            "validar_superior",
            "validar_rrhh",
            "pdf",

            "papeleta_salida",
            "tipo_salida",
            "codigo",

            "n_documento",
            "nombre_persona",
            "ap_paterno",
            "ap_materno",

            "destino",
            "motivo",

            "f_salida",
            "h_salida",
            "h_retorno",
            "con_sin_retorno",

            "val_json"
        );
        var col_m_index_1 = new Array(
            "",
            "rrhh_salidas.estado",
            "rrhh_salidas.validar_superior",
            "rrhh_salidas.validar_rrhh",
            "rrhh_salidas.pdf",

            "a2.nombre",
            "a2.tipo_salida",
            "rrhh_salidas.codigo",

            "a3.n_documento",
            "a3.nombre",
            "a3.ap_paterno",
            "a3.ap_materno",

            "rrhh_salidas.destino",
            "rrhh_salidas.motivo",

            "rrhh_salidas.f_salida::text",
            "rrhh_salidas.h_salida::text",
            "rrhh_salidas.h_retorno::text",
            "rrhh_salidas.con_sin_retorno",

            ""
        );
        var col_m_width_1 = new Array(
            33,
            90,
            160,
            70,
            80,

            400,
            120,
            100,

            100,
            100,
            100,
            100,

            400,
            400,

            125,
            110,
            110,
            100,

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
        var title_table_2 = "{!! $title_table_1 !!}";
        var jqgrid2       = "#jqgrid2";
        var pjqgrid2      = "#pjqgrid2";
        var col_name_2    = new Array(
            "",
            "ESTADO",
            "INMEDIATO SUPERIOR?",
            "RRHH?",
            "¿CON PDF?",

            "TIPO DE PAPELETA",
            "TIPO DE SALIDA",
            "CODIGO",
            "N° DIAS",

            "C.I.",
            "NOMBRE(S)",
            "AP. PATERNO",
            "AP. MATERNO",

            "DESTINO",
            "MOTIVO",

            "FECHA",
            "PERIODO",

            "FECHA",
            "PERIODO",

            ""
        );
        var col_m_name_2 = new Array(
            "act",
            "estado",
            "validar_superior",
            "validar_rrhh",
            "pdf",

            "papeleta_salida",
            "tipo_salida",
            "codigo",
            "n_dias",

            "n_documento",
            "nombre_persona",
            "ap_paterno",
            "ap_materno",

            "destino",
            "motivo",

            "f_salida",
            "periodo_salida",

            "f_retorno",
            "periodo_retorno",

            "val_json"
        );
        var col_m_index_2 = new Array(
            "",
            "rrhh_salidas.estado",
            "rrhh_salidas.validar_superior",
            "rrhh_salidas.validar_rrhh",
            "rrhh_salidas.pdf",

            "a2.nombre",
            "a2.tipo_salida",
            "rrhh_salidas.codigo",
            "rrhh_salidas.n_dias::text",

            "a3.n_documento",
            "a3.nombre",
            "a3.ap_paterno",
            "a3.ap_materno",

            "rrhh_salidas.destino",
            "rrhh_salidas.motivo",

            "rrhh_salidas.f_salida::text",
            "rrhh_salidas.periodo_salida",

            "rrhh_salidas.f_retorno::text",
            "rrhh_salidas.periodo_retorno",

            ""
        );
        var col_m_width_2 = new Array(
            33,
            90,
            160,
            70,
            80,

            400,
            220,
            100,
            100,

            100,
            100,
            100,
            100,

            400,
            400,

            100,
            100,

            100,
            100,

            10
        );
        var col_m_align_2 = new Array(
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

            "center",
            "center",

            "center",
            "center",

            "center"
        );

    // === FORMULARIO 1 ===
        var form_1 = "#form_1";

    // === FORMULARIO 2 ===
        var form_2 = "#form_2";

    // === ESTADO ===
        var estado_json   = $.parseJSON('{!! json_encode($estado_array) !!}');
        var estado_select = '';
        var estado_jqgrid = ':Todos';

        $.each(estado_json, function(index, value) {
            estado_select += '<option value="' + index + '">' + value + '</option>';
            estado_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO DE SALIDA ===
        var tipo_salida_json   = $.parseJSON('{!! json_encode($tipo_salida_array) !!}');
        var tipo_salida_select = '';
        var tipo_salida_jqgrid = ':Todos';

        $.each(tipo_salida_json, function(index, value) {
            tipo_salida_select += '<option value="' + index + '">' + value + '</option>';
            tipo_salida_jqgrid += ';' + index + ':' + value;
        });

    // === CON SIN RETORNO ===
        var con_sin_retorno_json   = $.parseJSON('{!! json_encode($con_sin_retorno_array) !!}');
        var con_sin_retorno_select = '';
        var con_sin_retorno_jqgrid = ':Todos';

        $.each(con_sin_retorno_json, function(index, value) {
            con_sin_retorno_select += '<option value="' + index + '">' + value + '</option>';
            con_sin_retorno_jqgrid += ';' + index + ':' + value;
        });

    // === PERIODO ===
        var periodo_json   = $.parseJSON('{!! json_encode($periodo_array) !!}');
        var periodo_select = '';
        var periodo_jqgrid = ':Todos';

        $.each(periodo_json, function(index, value) {
            periodo_select += '<option value="' + index + '">' + value + '</option>';
            periodo_jqgrid += ';' + index + ':' + value;
        });

    // === SI NO ===
        var no_si_json   = $.parseJSON('{!! json_encode($no_si_array) !!}');
        var no_si_select = '';
        var no_si_jqgrid = ':Todos';

        $.each(no_si_json, function(index, value) {
            no_si_select += '<option value="' + index + '">' + value + '</option>';
            no_si_jqgrid += ';' + index + ':' + value;
        });

    // === FUNCIONARIO ===
        var funcionario_json   = $.parseJSON('{!! json_encode($funcionario_array) !!}');

    // === TIPO DE SALIDA POR HORAS ===
        var tipo_salida_por_horas_json        = $.parseJSON('{!! json_encode($tipo_salida_por_horas_array) !!}');
        var tipo_salida_por_horas_select      = '';
        var tipo_salida_por_horas_jqgrid      = ':Todos';
        var tipo_salida_por_horas_tipo_salida = new Array();


        $.each(tipo_salida_por_horas_json, function(index, value) {
            tipo_salida_por_horas_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            tipo_salida_por_horas_jqgrid += ';' + value.nombre + ':' + value.nombre;

            tipo_salida_por_horas_tipo_salida[value.id] = value.tipo_salida;
        });

    // === TIPO DE SALIDA POR DIAS ===
        var tipo_salida_por_dias_json        = $.parseJSON('{!! json_encode($tipo_salida_por_dias_array) !!}');
        var tipo_salida_por_dias_select      = '';
        var tipo_salida_por_dias_jqgrid      = ':Todos';
        var tipo_salida_por_dias_tipo_salida = new Array();

        $.each(tipo_salida_por_dias_json, function(index, value) {
            tipo_salida_por_dias_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            tipo_salida_por_dias_jqgrid += ';' + value.nombre + ':' + value.nombre;

            tipo_salida_por_dias_tipo_salida[value.id] = value.tipo_salida;
        });

    // === DROPZONE ===
        Dropzone.autoDiscover = false;

    $(document).ready(function(){
        //=== INICIALIZAR ===
            $('#tipo_salida_id').append(tipo_salida_por_horas_select);
            $('#tipo_salida_id_2').append(tipo_salida_por_dias_select);
            $("#tipo_salida_id, #tipo_salida_id_2").select2({
                maximumSelectionLength: 1
            });
            $("#tipo_salida_id").appendTo("#tipo_salida_id_div");
            $("#tipo_salida_id_2").appendTo("#tipo_salida_id_2_div");

            $('#tipo_salida, #tipo_salida_2').append(tipo_salida_select);
            $("#tipo_salida, #tipo_salida_2").select2({
                maximumSelectionLength: 1
            });
            $("#tipo_salida").appendTo("#tipo_salida_div");
            $("#tipo_salida_2").appendTo("#tipo_salida_2_div");

            $('#persona_id_superior, #persona_id_superior_2').select2({
                maximumSelectionLength: 1,
                minimumInputLength    : 2,
                ajax                  : {
                    url     : url_controller + '/send_ajax',
                    type    : 'post',
                    dataType: 'json',
                    data    : function (params) {
                        return {
                            q                               : params.term,
                            page_limit                      : 10,
                            estado                          : 1,
                            tipo                            : 100,
                            lugar_dependencia_id_funcionario: funcionario_json.lugar_dependencia_id_funcionario,
                            persona_id                      : funcionario_json.persona_id,
                            _token                          : csrf_token
                        };
                    },
                    results: function (data, page) {
                        return {
                            results: data
                        };
                    }
                }
            });
            $("#persona_id_superior").appendTo("#persona_id_superior_div");
            $("#persona_id_superior_2").appendTo("#persona_id_superior_2_div");

            $('#f_salida').datepicker({
                // startView            : 2,
                // todayBtn          : "linked",
                // keyboardNavigation: false,
                // forceParse        : false,
                autoclose: true,
                format   : "yyyy-mm-dd",
                startDate: '-2d',
                endDate  : '+1y',
                language : "es"
            });

            $('#h_salida, #h_retorno').clockpicker({
                autoclose: true,
                placement: 'top',
                align    : 'left',
                donetext : 'Hecho'
            });

            $('#f_salida_2, #f_retorno_2').datepicker({
                // startView            : 2,
                // todayBtn          : "linked",
                // keyboardNavigation: false,
                // forceParse        : false,
                autoclose: true,
                format   : "yyyy-mm-dd",
                startDate: '-2d',
                endDate  : '+1y',
                language : "es"
            });

        // === SELECT CHANGE ===
            $("#tipo_salida_id").on("change", function(e) {
                $('#tipo_salida').select2('val','');
                $('#destino').prop('disabled', false);
                $('#motivo').prop('disabled', false);
                switch ($.trim(this.value)){
                    case '':
                        break;
                    default:
                        $('#tipo_salida').select2("val", tipo_salida_por_horas_tipo_salida[$.trim(this.value)]);

                        if(tipo_salida_por_horas_tipo_salida[$.trim(this.value)] == '2'){
                            $('#destino').prop('disabled', true);
                            $('#motivo').prop('disabled', true);
                        }
                        break;
                }
            });

            $("#tipo_salida_id_2").on("change", function(e) {
                $('#tipo_salida_2').select2('val','');
                $('#destino_2').prop('disabled', false);
                $('#motivo_2').prop('disabled', false);
                $('#f_salida_2').prop('disabled', false);
                $('#f_retorno_2').prop('disabled', false);
                $('#n_dias_2').prop('disabled', false);
                $("#periodo_retorno_1_id").prop('checked', false);
                $("#periodo_salida_2_id").prop('checked', false);
                $('#periodo_retorno_1_id').prop('disabled', false);
                $('#periodo_salida_2_id').prop('disabled', false);
                $("#n_dias_2").val('');
                switch ($.trim(this.value)){
                    case '':
                        break;
                    default:
                        $('#tipo_salida_2').select2("val", tipo_salida_por_dias_tipo_salida[$.trim(this.value)]);

                        if(tipo_salida_por_dias_tipo_salida[$.trim(this.value)] == '4'){
                            if(funcionario_json.f_nacimiento != ''){
                                $('#destino_2').prop('disabled', true);
                                $('#motivo_2').prop('disabled', true);
                                $('#f_salida_2').prop('disabled', true);
                                $('#f_retorno_2').prop('disabled', true);
                                $('#n_dias_2').prop('disabled', true);

                                $("#periodo_salida_2_id").prop('checked', true);

                                $('#periodo_retorno_1_id').prop('disabled', true);
                                $('#periodo_salida_2_id').prop('disabled', true);

                                var f_nacimiento = funcionario_json.f_nacimiento;
                                var f_nacimiento_array = f_nacimiento.split("-");
                                var anio_actual = "{!! date("Y") !!}";

                                var f_cumple = anio_actual + "-" + f_nacimiento_array[1] + "-" + f_nacimiento_array[2];

                                $("#f_salida_2").val(f_cumple);
                                $("#f_retorno_2").val(f_cumple);
                                $("#n_dias_2").val('0.5');
                            }
                            else{
                                var valor1 = new Array();
                                valor1[0]  = 101;
                                valor1[1]  = "FECHA DE NACIMIENTO";
                                valor1[2]  = "No registro su fecha de nacimiento.";
                                utilitarios(valor1);

                                var valor1 = new Array();
                                valor1[0]  = 54;
                                utilitarios(valor1);
                            }
                        }

                        if(tipo_salida_por_dias_tipo_salida[$.trim(this.value)] == '3' || tipo_salida_por_dias_tipo_salida[$.trim(this.value)] == '5'){
                            $('#destino_2').prop('disabled', true);
                            $('#motivo_2').prop('disabled', true);
                        }
                        break;
                }
            });

        // === DROPZONE ===
            var valor1 = new Array();
            valor1[0]  = 20;
            utilitarios(valor1);

        // === JQGRID 1 ===
            var valor1 = new Array();
            valor1[0]  = 10;
            utilitarios(valor1);

        // === JQGRID 2 ===
            var valor1 = new Array();
            valor1[0]  = 50;
            utilitarios(valor1);

        // === VALIDATE 1 ===
            var valor1 = new Array();
            valor1[0]  = 16;
            utilitarios(valor1);

        // === VALIDATE 2 ===
            var valor1 = new Array();
            valor1[0]  = 56;
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
                $(jqgrid1).jqGrid('setGridWidth', $(".tab-content").width() - 30);
                $(jqgrid2).jqGrid('setGridWidth', $(".tab-content").width() - 30);
                break;
            // === JQGRID 1 ===
            case 10:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid1).jqGrid({
                    caption     : title_table,
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
                        col_name_1[0],
                        col_name_1[1],
                        col_name_1[2],
                        col_name_1[3],
                        col_name_1[4],

                        col_name_1[5],
                        col_name_1[6],
                        col_name_1[7],

                        col_name_1[8],
                        col_name_1[9],
                        col_name_1[10],
                        col_name_1[11],

                        col_name_1[12],
                        col_name_1[13],

                        col_name_1[14],
                        col_name_1[15],
                        col_name_1[16],
                        col_name_1[17],

                        col_name_1[18]
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
                            editoptions: {value:no_si_jqgrid}
                        },
                        {
                            name       : col_m_name_1[3],
                            index      : col_m_index_1[3],
                            width      : col_m_width_1[3],
                            align      : col_m_align_1[3],
                            stype      :'select',
                            editoptions: {value:no_si_jqgrid}
                        },
                        {
                            name       : col_m_name_1[4],
                            index      : col_m_index_1[4],
                            width      : col_m_width_1[4],
                            align      : col_m_align_1[4],
                            stype      :'select',
                            editoptions: {value:no_si_jqgrid}
                        },

                        {
                            name : col_m_name_1[5],
                            index: col_m_index_1[5],
                            width: col_m_width_1[5],
                            align: col_m_align_1[5]
                        },
                        {
                            name       : col_m_name_1[6],
                            index      : col_m_index_1[6],
                            width      : col_m_width_1[6],
                            align      : col_m_align_1[6],
                            stype      :'select',
                            editoptions: {value:tipo_salida_jqgrid}
                        },
                        {
                            name : col_m_name_1[7],
                            index: col_m_index_1[7],
                            width: col_m_width_1[7],
                            align: col_m_align_1[7]
                        },

                        {
                            name : col_m_name_1[8],
                            index: col_m_index_1[8],
                            width: col_m_width_1[8],
                            align: col_m_align_1[8]
                        },
                        {
                            name : col_m_name_1[9],
                            index: col_m_index_1[9],
                            width: col_m_width_1[9],
                            align: col_m_align_1[9]
                        },
                        {
                            name : col_m_name_1[10],
                            index: col_m_index_1[10],
                            width: col_m_width_1[10],
                            align: col_m_align_1[10]
                        },
                        {
                            name : col_m_name_1[11],
                            index: col_m_index_1[11],
                            width: col_m_width_1[11],
                            align: col_m_align_1[11]
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
                            name : col_m_name_1[14],
                            index: col_m_index_1[14],
                            width: col_m_width_1[14],
                            align: col_m_align_1[14]
                        },
                        {
                            name : col_m_name_1[15],
                            index: col_m_index_1[15],
                            width: col_m_width_1[15],
                            align: col_m_align_1[15]
                        },
                        {
                            name : col_m_name_1[16],
                            index: col_m_index_1[16],
                            width: col_m_width_1[16],
                            align: col_m_align_1[16]
                        },
                        {
                            name       : col_m_name_1[17],
                            index      : col_m_index_1[17],
                            width      : col_m_width_1[17],
                            align      : col_m_align_1[17],
                            stype      :'select',
                            editoptions: {value:con_sin_retorno_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_1[18],
                                index : col_m_index_1[18],
                                width : col_m_width_1[18],
                                align : col_m_align_1[18],
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

                            if(val_json.estado == '1' || val_json.estado == '3'){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    pdf1 = " <button type='button' class='btn btn-xs btn-primary' title='Generar PAPELETA DE SALIDA' onclick=\"utilitarios([13, " + cl + "]);\"><i class='fa fa-file-pdf-o'></i></button>";
                                @else
                                    pdf1 = '';
                                @endif
                            }
                            else{
                                pdf1 = '';
                            }

                            if((val_json.validar_superior == '1') && (val_json.validar_rrhh == '1') && (val_json.estado == '1')){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                                @else
                                    ed = '';
                                @endif

                                @if(in_array(['codigo' => '1003'], $permisos))
                                    up1 = " <button type='button' class='btn btn-xs btn-info' title='Subir documentación' onclick=\"utilitarios([19, " + cl + ", '" + ret.codigo + "', 1]);\"><i class='fa fa-cloud-upload'></i></button>";
                                @else
                                    up1 = '';
                                @endif

                                @if(in_array(['codigo' => '1003'], $permisos))
                                    del1 = " <button type='button' class='btn btn-xs btn-danger' title='Anular PAPELETA DE SALIDA' onclick=\"utilitarios([17, " + cl + ", 2, 1]);\"><i class='fa fa-trash'></i></button>";
                                @else
                                    del1 = '';
                                @endif
                            }
                            else{
                                ed   = '';
                                del1 = '';
                                up1 = '';
                            }

                            if((val_json.validar_superior == '1') && (val_json.validar_rrhh == '1') && (val_json.estado == '2')){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    // del2 = " <button type='button' class='btn btn-xs btn-warning' title='Habilitar PAPELETA DE SALIDA' onclick=\"utilitarios([18, " + cl + ", 1, 1]);\"><i class='fa fa-check'></i></button>";
                                    del2 = '';
                                @else
                                    del2 = '';
                                @endif
                            }
                            else{
                                del2 = '';
                            }

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed + up1 + pdf1 + del1 + del2)
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'validar_superior',
                            numberOfColumns: 2,
                            titleText      : '¿VALIDADO'
                        },
                        {
                            startColumnName: 'n_documento',
                            numberOfColumns: 4,
                            titleText      : 'INMEDIATO SUPERIOR'
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
                @if(in_array(['codigo' => '1002'], $permisos))
                    .navButtonAdd(pjqgrid1,{
                    "id"          : "add1",
                    caption       : "",
                    title         : 'Agregar nueva fila',
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
                @if(in_array(['codigo' => '1003'], $permisos))
                    // .navButtonAdd(pjqgrid1,{
                    //     "id"          : "edit1",
                    //     caption       : "",
                    //     title         : 'Editar fila',
                    //     buttonicon    : "ui-icon ui-icon-pencil",
                    //     onClickButton : function(){
                    //         var id = $(jqgrid1).jqGrid('getGridParam','selrow');
                    //         if(id == null)
                    //         {
                    //             var valor1 = new Array();
                    //             valor1[0]  = 101;
                    //             valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                    //             valor1[2]  = "¡Favor seleccione una fila!";
                    //             utilitarios(valor1);
                    //         }
                    //         else
                    //         {
                    //             utilitarios([12, id]);
                    //         }
                    //     }
                    // })
                @endif
                @if(in_array(['codigo' => '1004'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "print1",
                        caption       : "",
                        title         : 'Reportes',
                        buttonicon    : "ui-icon ui-icon-print",
                        onClickButton : function(){
                            var id = $(jqgrid1).jqGrid('getGridParam','selrow');
                            if(id == null)
                            {
                                var valor1 = new Array();
                                valor1[0]  = 101;
                                valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                                valor1[2]  = "¡Favor seleccione una fila!";
                                utilitarios(valor1);
                            }
                            else
                            {
                                utilitarios([13, id]);
                            }
                        }
                    })
                @endif
                ;
                break;
            // === ABRIR MODAL ===
            case 11:
                $('#modal_1').modal();
                break;
            // === EDICION MODAL ===
            case 12:
                var valor1 = new Array();
                valor1[0]  = 14;
                utilitarios(valor1);

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $('#modal_1_title').empty();
                $('#modal_1_title').append('Modificar solicitud de salida por horas');

                $("#id_salida").val(valor[1]);

                $("#tipo_salida_id").select2("val", val_json.tipo_salida_id);
                $("#codigo").val(ret.codigo);

                if(ret.n_documento != ""){
                    var persona = ret.n_documento + ' - ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno) + ' ' + ret.nombre_persona;

                    $('#persona_id_superior').append('<option value="' + val_json.persona_id_superior + '">' + persona + '</option>');
                    $("#persona_id_superior").select2("val", val_json.persona_id_superior);
                }

                $("#destino").val(ret.destino);
                $("#motivo").val(ret.motivo);

                $("#f_salida").val(ret.f_salida);
                $("#h_salida").val(ret.h_salida);
                $("#h_retorno").val(ret.h_retorno);

                $(".con_sin_retorno_class[value=" + val_json.con_sin_retorno + "]").prop('checked', true);

                $('#modal_1').modal();
                break;
            // === REPORTES MODAL ===
            case 13:
                var concatenar_valores = '';
                concatenar_valores     += '?tipo=1&salida_id=' + valor[1];

                var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                win.focus();
                break;
            // === RESETEAR FORMULARIO ===
            case 14:
                $('#modal_1_title').empty();
                $('#modal_1_title').append('Nueva solicitud de salida por horas');

                $("#id_salida").val('');

                $('#tipo_salida_id').select2("val", "");
                $('#tipo_salida').select2("val", "");
                $('#persona_id_superior').select2("val", "");
                $('#persona_id_superior option').remove();

                $("#tipo_salida").select2("enable", false);

                $('#destino').prop('disabled', false);
                $('#motivo').prop('disabled', false);

                $(form_1)[0].reset();
                break;
            // === GUARDAR REGISTRO ===
            case 15:
                if($(form_1).valid()){
                    var h_salida  = $("#h_salida").val();
                    var h_retorno = $("#h_retorno").val();

                    if(h_retorno == ''){
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
                        if(h_salida < h_retorno){
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
                            valor1[2]  = "La HORA DE RETORNO debe de ser, mayor o igual a la HORA DE SALIDA";
                            utilitarios(valor1);
                        }
                    }
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                    valor1[2]  = "¡Favor complete o corrija los datos solicitados!";
                    utilitarios(valor1);
                }
                break;
            // === VALIDACION ===
            case 16:
                $(form_1).validate({
                    rules: {
                        tipo_salida_id:{
                            required: true
                        },
                        persona_id_superior:{
                            required: true
                        },
                        destino:{
                            required : true,
                            maxlength: 500
                        },
                        motivo:{
                            required : true,
                            maxlength: 500
                        },
                        f_salida:{
                            required: true
                        },
                        h_salida:{
                            required: true
                        }//,
                        // h_retorno:{
                        //     required: true
                        // }
                    }
                });
                break;
            // === ANULAR PAPELETA DE SALIDA ===
            case 17:
                if(valor[3] == 1){
                    var ret = $(jqgrid1).jqGrid('getRowData', valor[1]);
                }
                else{
                    var ret = $(jqgrid2).jqGrid('getRowData', valor[1]);
                }

                swal({
                    title             : "ANULAR PAPELETA DE SALIDA",
                    text              : "¿Esta seguro de ANULAR la PAPELETA DE SALIDA con el código " + ret.codigo + "?",
                    type              : "warning",
                    showCancelButton  : true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText : "Anular",
                    cancelButtonText  : "Cancelar",
                    closeOnConfirm    : false,
                    closeOnCancel     : false
                },
                function(isConfirm){
                    if (isConfirm){
                        // swal.close();

                        swal({
                            title            : "ANULANDO PAPELETA DE SALIDA",
                            text             : "Espere a que se anule la papeleta de salida.",
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
                        valor1[4]  = "tipo=3&id=" + valor[1] + "&estado=" + valor[2] + "&dia_hora=" + valor[3] + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                    }
                    else{
                        swal.close();
                    }
                });
                break;
            // === HABILITAR PAPELETA DE SALIDA ===
            case 18:
                if(valor[3] == 1){
                    var ret = $(jqgrid1).jqGrid('getRowData', valor[1]);
                }
                else{
                    var ret = $(jqgrid2).jqGrid('getRowData', valor[1]);
                }

                swal({
                    title             : "HABILITAR PAPELETA DE SALIDA",
                    text              : "¿Esta seguro de HABILITAR la PAPELETA DE SALIDA con el código " + ret.codigo + "?",
                    type              : "warning",
                    showCancelButton  : true,
                    confirmButtonColor: "#1A7bb9",
                    confirmButtonText : "Habilitar",
                    cancelButtonText  : "Cancelar",
                    closeOnConfirm    : false,
                    closeOnCancel     : false
                },
                function(isConfirm){
                    if (isConfirm){
                        // swal.close();

                        swal({
                            title            : "HABILITANDO PAPELETA DE SALIDA",
                            text             : "Espere a que se habilite la papeleta de salida.",
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
                        valor1[4]  = "tipo=3&id=" + valor[1] + "&estado=" + valor[2] + "&dia_hora=" + valor[3] + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                    }
                    else{
                        swal.close();
                    }
                });
                break;
            // === MODAL SUBIR DOCUMENTO ===
            case 19:
                $('#modal_3_subtitle').empty();
                $('#modal_3_subtitle').append('Código de la PAPELETA DE SALIDA: <b>' + valor[2] + '</b>');
                $("#id_salida_3").val(valor[1]);
                $("#dia_hora_3").val(valor[3]);
                $('#modal_3').modal();
                break;
            // === DROPZONE 1 ===
            case 20:
                $("#dropzoneForm_1").dropzone({
                    url              : url_controller + "/send_ajax",
                    method           :'post',
                    addRemoveLinks   : true,
                    maxFilesize      : 5, // MB
                    dictResponseError: "Ha ocurrido un error en el server.",
                    acceptedFiles    : 'application/pdf',
                    paramName        : "file", // The name that will be used to transfer the file
                    maxFiles         : 1,
                    clickable        : true,
                    parallelUploads  : 1,
                    params           : {
                        tipo  : 4,
                        _token: csrf_token
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
                        // this.on("sending", function(file, xhr, formData){
                        //     formData.append("usuario_id", $("#usuario_id").val());
                        //     formData.append("estado", $(".estado_class:checked").val());
                        //     formData.append("persona_id", $("#persona_id").val());
                        //     formData.append("email", $("#email").val());
                        //     formData.append("password", $("#password").val());
                        //     formData.append("rol_id", $("#rol_id").val());
                        //     formData.append("lugar_dependencia", $("#lugar_dependencia").val());
                        //     formData.append("enviar_mail", $("#enviar_mail:checked").val());
                        // });
                    },
                    success: function(file, response){
                        var data = $.parseJSON(response);
                        if(data.sw === 1){
                            var valor1 = new Array();
                            valor1[0]  = 100;
                            valor1[1]  = data.titulo;
                            valor1[2]  = data.respuesta;
                            utilitarios(valor1);

                            if(data.dia_hora == "1"){
                                $(jqgrid1).trigger("reloadGrid");
                            }
                            else{
                                $(jqgrid2).trigger("reloadGrid");
                            }
                            $('#modal_3').modal('hide');
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
            // === MOSTRAR DOCUMENTO ===
            case 21:
                if(valor[2] == 1){
                    var ret = $(jqgrid1).jqGrid('getRowData', valor[1]);
                }
                else{
                    var ret = $(jqgrid2).jqGrid('getRowData', valor[1]);
                }
                var val_json = $.parseJSON(ret.val_json);

                if(val_json.pdf == '2'){
                    var win = window.open(public_url + '/' + val_json.papeleta_pdf,  '_blank');
                    win.focus();
                }
                break;
            case 22:
                var concatenar_valores = '';
                concatenar_valores     += '?tipo=1';

                var persona_id               = $("#persona_id_3").val();
                var f_marcacion_del          = $("#f_marcacion_del_3").val();
                var f_marcacion_al           = $("#f_marcacion_al_3").val();
                var lugar_dependencia_id     = $("#lugar_dependencia_id_3").val();
                var unidad_desconcentrada_id = $("#unidad_desconcentrada_id_3").val();

                if($.trim(persona_id) != ''){
                    concatenar_valores += '&persona_id=' + persona_id;

                    if($.trim(f_marcacion_del) != ''){
                        concatenar_valores += '&f_marcacion_del=' + f_marcacion_del;
                    }

                    if($.trim(f_marcacion_al) != ''){
                        concatenar_valores += '&f_marcacion_al=' + f_marcacion_al;
                    }

                    if($.trim(lugar_dependencia_id) != ''){
                        concatenar_valores += '&lugar_dependencia_id=' + lugar_dependencia_id;
                    }

                    if($.trim(unidad_desconcentrada_id) != ''){
                        concatenar_valores += '&unidad_desconcentrada_id=' + unidad_desconcentrada_id;
                    }

                    var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                    win.focus();
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                    valor1[2]  = "¡Seleccione un FUNCIONARIO!";
                    utilitarios(valor1);
                }
                break;

            // === JQGRID 2 ===
            case 50:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 28;
                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid2).jqGrid({
                    caption     : title_table_2,
                    url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=2',
                    datatype    : 'json',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid2,
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
                        col_name_2[0],
                        col_name_2[1],
                        col_name_2[2],
                        col_name_2[3],
                        col_name_2[4],

                        col_name_2[5],
                        col_name_2[6],
                        col_name_2[7],
                        col_name_2[8],

                        col_name_2[9],
                        col_name_2[10],
                        col_name_2[11],
                        col_name_2[12],

                        col_name_2[13],
                        col_name_2[14],

                        col_name_2[15],
                        col_name_2[16],

                        col_name_2[17],
                        col_name_2[18],

                        col_name_2[19]
                    ],
                    colModel : [
                        {
                            name    : col_m_name_2[0],
                            index   : col_m_index_2[0],
                            width   : ancho1,
                            align   : col_m_align_2[0],
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : edit1
                        },
                        {
                            name       : col_m_name_2[1],
                            index      : col_m_index_2[1],
                            width      : col_m_width_2[1],
                            align      : col_m_align_2[1],
                            stype      :'select',
                            editoptions: {value:estado_jqgrid}
                        },
                        {
                            name       : col_m_name_2[2],
                            index      : col_m_index_2[2],
                            width      : col_m_width_2[2],
                            align      : col_m_align_2[2],
                            stype      :'select',
                            editoptions: {value:no_si_jqgrid}
                        },
                        {
                            name       : col_m_name_2[3],
                            index      : col_m_index_2[3],
                            width      : col_m_width_2[3],
                            align      : col_m_align_2[3],
                            stype      :'select',
                            editoptions: {value:no_si_jqgrid}
                        },
                        {
                            name       : col_m_name_2[4],
                            index      : col_m_index_2[4],
                            width      : col_m_width_2[4],
                            align      : col_m_align_2[4],
                            stype      :'select',
                            editoptions: {value:no_si_jqgrid}
                        },

                        {
                            name : col_m_name_2[5],
                            index: col_m_index_2[5],
                            width: col_m_width_2[5],
                            align: col_m_align_2[5]
                        },
                        {
                            name       : col_m_name_2[6],
                            index      : col_m_index_2[6],
                            width      : col_m_width_2[6],
                            align      : col_m_align_2[6],
                            stype      :'select',
                            editoptions: {value:tipo_salida_jqgrid}
                        },
                        {
                            name : col_m_name_2[7],
                            index: col_m_index_2[7],
                            width: col_m_width_2[7],
                            align: col_m_align_2[7]
                        },
                        {
                            name : col_m_name_2[8],
                            index: col_m_index_2[8],
                            width: col_m_width_2[8],
                            align: col_m_align_2[8]
                        },

                        {
                            name : col_m_name_2[9],
                            index: col_m_index_2[9],
                            width: col_m_width_2[9],
                            align: col_m_align_2[9]
                        },
                        {
                            name : col_m_name_2[10],
                            index: col_m_index_2[10],
                            width: col_m_width_2[10],
                            align: col_m_align_2[10]
                        },
                        {
                            name : col_m_name_2[11],
                            index: col_m_index_2[11],
                            width: col_m_width_2[11],
                            align: col_m_align_2[11]
                        },
                        {
                            name : col_m_name_2[12],
                            index: col_m_index_2[12],
                            width: col_m_width_2[12],
                            align: col_m_align_2[12]
                        },

                        {
                            name : col_m_name_2[13],
                            index: col_m_index_2[13],
                            width: col_m_width_2[13],
                            align: col_m_align_2[13]
                        },
                        {
                            name : col_m_name_2[14],
                            index: col_m_index_2[14],
                            width: col_m_width_2[14],
                            align: col_m_align_2[14]
                        },

                        {
                            name : col_m_name_2[15],
                            index: col_m_index_2[15],
                            width: col_m_width_2[15],
                            align: col_m_align_2[15]
                        },
                        {
                            name       : col_m_name_2[16],
                            index      : col_m_index_2[16],
                            width      : col_m_width_2[16],
                            align      : col_m_align_2[16],
                            stype      :'select',
                            editoptions: {value:periodo_jqgrid}
                        },


                        {
                            name : col_m_name_2[17],
                            index: col_m_index_2[17],
                            width: col_m_width_2[17],
                            align: col_m_align_2[17]
                        },
                        {
                            name       : col_m_name_2[18],
                            index      : col_m_index_2[18],
                            width      : col_m_width_2[18],
                            align      : col_m_align_2[18],
                            stype      :'select',
                            editoptions: {value:periodo_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_2[19],
                                index : col_m_index_2[19],
                                width : col_m_width_2[19],
                                align : col_m_align_2[19],
                                search: false,
                                hidden: true
                            }
                    ],
                    loadComplete : function(){
                        $("tr.jqgrow:odd").addClass('myAltRowClass');
                    },
                    gridComplete : function() {
                        var ids = $(jqgrid2).jqGrid('getDataIDs');
                        for(var i = 0; i < ids.length; i++){
                            var cl       = ids[i];
                            var ret      = $(jqgrid2).jqGrid('getRowData', cl);
                            var val_json = $.parseJSON(ret.val_json);

                            if(val_json.estado == '1' || val_json.estado == '3'){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    pdf1 = " <button type='button' class='btn btn-xs btn-primary' title='Generar PAPELETA DE SALIDA' onclick=\"utilitarios([13, " + cl + "]);\"><i class='fa fa-file-pdf-o'></i></button>";
                                @else
                                    pdf1 = '';
                                @endif
                            }
                            else{
                                pdf1 = '';
                            }

                            if((val_json.validar_superior == '1') && (val_json.validar_rrhh == '1') && (val_json.estado == '1')){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([52, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                                @else
                                    ed = '';
                                @endif

                                @if(in_array(['codigo' => '1003'], $permisos))
                                    up1 = " <button type='button' class='btn btn-xs btn-info' title='Subir documentación' onclick=\"utilitarios([19, " + cl + ", '" + ret.codigo + "', 2]);\"><i class='fa fa-cloud-upload'></i></button>";
                                @else
                                    up1 = '';
                                @endif

                                @if(in_array(['codigo' => '1003'], $permisos))
                                    del1 = " <button type='button' class='btn btn-xs btn-danger' title='Anular PAPELETA DE SALIDA' onclick=\"utilitarios([17, " + cl + ", 2, 2]);\"><i class='fa fa-trash'></i></button>";
                                @else
                                    del1 = '';
                                @endif
                            }
                            else{
                                ed   = '';
                                del1 = '';
                                up1 = '';
                            }

                            if((val_json.validar_superior == '1') && (val_json.validar_rrhh == '1') && (val_json.estado == '2')){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    del2 = " <button type='button' class='btn btn-xs btn-warning' title='Habilitar PAPELETA DE SALIDA' onclick=\"utilitarios([18, " + cl + ", 1, 2]);\"><i class='fa fa-check'></i></button>";
                                @else
                                    del2 = '';
                                @endif
                            }
                            else{
                                del2 = '';
                            }

                            $(jqgrid2).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed + up1 + pdf1 + del1 + del2)
                            });
                        }
                    }
                });

                $(jqgrid2).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'validar_superior',
                            numberOfColumns: 2,
                            titleText      : '¿VALIDADO'
                        },
                        {
                            startColumnName: 'n_documento',
                            numberOfColumns: 4,
                            titleText      : 'INMEDIATO SUPERIOR'
                        },
                        {
                            startColumnName: 'f_salida',
                            numberOfColumns: 2,
                            titleText      : 'SALIDA'
                        },
                        {
                            startColumnName: 'f_retorno',
                            numberOfColumns: 2,
                            titleText      : 'RETORNO'
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
                .navSeparatorAdd(pjqgrid2,{
                    sepclass : "ui-separator"
                })
                @if(in_array(['codigo' => '1002'], $permisos))
                    .navButtonAdd(pjqgrid2,{
                    "id"          : "add2",
                    caption       : "",
                    title         : 'Agregar nueva fila',
                    buttonicon    : "ui-icon ui-icon-plusthick",
                    onClickButton : function(){
                        var valor1 = new Array();
                        valor1[0]  = 54;
                        utilitarios(valor1);

                        var valor1 = new Array();
                        valor1[0]  = 51;
                        utilitarios(valor1);
                    }
                })
                @endif
                @if(in_array(['codigo' => '1003'], $permisos))
                    // .navButtonAdd(pjqgrid2,{
                    //     "id"          : "edit2",
                    //     caption       : "",
                    //     title         : 'Editar fila',
                    //     buttonicon    : "ui-icon ui-icon-pencil",
                    //     onClickButton : function(){
                    //         var id = $(jqgrid1).jqGrid('getGridParam','selrow');
                    //         if(id == null)
                    //         {
                    //             var valor1 = new Array();
                    //             valor1[0]  = 101;
                    //             valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                    //             valor1[2]  = "¡Favor seleccione una fila!";
                    //             utilitarios(valor1);
                    //         }
                    //         else
                    //         {
                    //             utilitarios([52, id]);
                    //         }
                    //     }
                    // })
                @endif
                @if(in_array(['codigo' => '1004'], $permisos))
                    .navSeparatorAdd(pjqgrid2,{
                      sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid2,{
                      "id"          : "print2",
                      caption       : "",
                      title         : 'Reportes',
                      buttonicon    : "ui-icon ui-icon-print",
                      onClickButton : function(){
                        var id = $(jqgrid2).jqGrid('getGridParam','selrow');
                        if(id == null)
                        {
                            var valor1 = new Array();
                            valor1[0]  = 101;
                            valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                            valor1[2]  = "¡Favor seleccione una fila!";
                            utilitarios(valor1);
                        }
                        else
                        {
                            utilitarios([13, id]);
                        }
                      }
                    })
                @endif
                ;
                break;
            // === ABRIR MODAL ===
            case 51:
                $('#modal_2').modal();
                break;
            // === EDICION MODAL ===
            case 52:
                var valor1 = new Array();
                valor1[0]  = 54;
                utilitarios(valor1);

                var ret      = $(jqgrid2).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $('#modal_1_title').empty();
                $('#modal_1_title').append('Modificar solicitud de salida por días');

                $("#id_salida_2").val(valor[1]);

                $("#tipo_salida_id_2").select2("val", val_json.tipo_salida_id);
                $("#n_dias_2").val(ret.n_dias);
                $("#codigo_2").val(ret.codigo);

                if(ret.n_documento != ""){
                    var persona = ret.n_documento + ' - ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno) + ' ' + ret.nombre_persona;

                    $('#persona_id_superior_2').append('<option value="' + val_json.persona_id_superior + '">' + persona + '</option>');
                    $("#persona_id_superior_2").select2("val", val_json.persona_id_superior);
                }

                $("#destino_2").val(ret.destino);
                $("#motivo_2").val(ret.motivo);

                $("#f_salida_2").val(ret.f_salida);
                if(val_json.periodo_salida != null){
                    $("#periodo_salida_2_id").prop('checked', true);
                }

                $("#f_retorno_2").val(ret.f_retorno);
                if(val_json.periodo_retorno != null){
                    $("#periodo_retorno_1_id").prop('checked', true);
                }

                $('#modal_2').modal();
                break;

            // === RESETEAR FORMULARIO ===
            case 54:
                $('#modal_2_title').empty();
                $('#modal_2_title').append('Nueva solicitud de salida por días');

                $("#id_salida_2").val('');

                $('#tipo_salida_id_2').select2("val", "");
                $('#tipo_salida_2').select2("val", "");
                $('#persona_id_superior_2').select2("val", "");
                $('#persona_id_superior_2 option').remove();

                $("#tipo_salida_2").select2("enable", false);

                $('#destino_2').prop('disabled', false);
                $('#motivo_2').prop('disabled', false);
                $('#f_salida_2').prop('disabled', false);
                $('#f_retorno_2').prop('disabled', false);
                $('#n_dias_2').prop('disabled', false);

                $('#periodo_retorno_1_id').prop('disabled', false);
                $('#periodo_salida_2_id').prop('disabled', false);

                $(form_2)[0].reset();
                break;
            // === GUARDAR REGISTRO ===
            case 55:
                if($(form_2).valid()){
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
                    valor1[4]  = $(form_2).serialize();
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                    valor1[2]  = "¡Favor complete o corrija los datos solicitados!";
                    utilitarios(valor1);
                }
                break;
            // === VALIDACION ===
            case 56:
                $(form_2).validate({
                    rules: {
                        tipo_salida_id:{
                            required: true
                        },
                        persona_id_superior:{
                            required: true
                        },
                        destino:{
                            required : true,
                            maxlength: 500
                        },
                        motivo:{
                            required : true,
                            maxlength: 500
                        },
                        f_salida:{
                            required: true
                        },
                        f_retorno:{
                            required: true
                        }
                    }
                });
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
                                        var valor1 = new Array();
                                        valor1[0]  = 14;
                                        utilitarios(valor1);
                                    }
                                    else if(data.iu === 2){
                                        $('#modal_1').modal('hide');
                                    }
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
                            // === INSERT UPDATE ===
                            case '2':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid2).trigger("reloadGrid");
                                    if(data.iu === 1){
                                        var valor1 = new Array();
                                        valor1[0]  = 54;
                                        utilitarios(valor1);
                                    }
                                    else if(data.iu === 2){
                                        $('#modal_2').modal('hide');
                                    }
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
                            // === ANULAR / HABILITAR PAPELETA DE SALIDA ===
                            case '3':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    if(data.dia_hora == "1"){
                                        $(jqgrid1).trigger("reloadGrid");
                                    }
                                    else{
                                        $(jqgrid2).trigger("reloadGrid");
                                    }
                                }
                                else if(data.sw === 0){
                                    var valor1 = new Array();
                                    valor1[0]  = 101;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    if(data.dia_hora == "1"){
                                        $(jqgrid1).trigger("reloadGrid");
                                    }
                                    else{
                                        $(jqgrid2).trigger("reloadGrid");
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
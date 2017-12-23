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

            "FECHA DE SALIDA ",
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

            "rrhh_salidas.f_salida",
            "rrhh_salidas.h_salida",
            "rrhh_salidas.h_retorno",
            "rrhh_salidas.con_sin_retorno",

            ""
        );
        var col_m_width_1 = new Array(
            33,
            100,
            100,
            100,
            100,

            300,
            100,
            100,

            100,
            100,
            100,
            100,

            100,
            100,
            100,
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

    // === FORMULARIO 1 ===
        var form_1 = "#form_1";

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
        var tipo_salida_por_dias_json   = $.parseJSON('{!! json_encode($tipo_salida_por_dias_array) !!}');
        var tipo_salida_por_dias_select = '';
        var tipo_salida_por_dias_jqgrid = ':Todos';

        $.each(tipo_salida_por_dias_json, function(index, value) {
            tipo_salida_por_dias_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            tipo_salida_por_dias_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    $(document).ready(function(){
        //=== INICIALIZAR ===
            $('#tipo_salida_id').append(tipo_salida_por_horas_select);
            $("#tipo_salida_id").select2({
                maximumSelectionLength: 1
            });
            $("#tipo_salida_id").appendTo("#tipo_salida_id_div");

            $('#tipo_salida').append(tipo_salida_select);
            $("#tipo_salida").select2({
                maximumSelectionLength: 1
            });
            $("#tipo_salida").appendTo("#tipo_salida_div");

            $('#persona_id_superior').select2({
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

        // === SELECT CHANGE ===
            $("#tipo_salida_id").on("change", function(e) {
                $('#tipo_salida').select2('val','');
                switch ($.trim(this.value)){
                    case '':
                        break;
                    default:
                        $('#tipo_salida').select2("val", tipo_salida_por_horas_tipo_salida[$.trim(this.value)]);
                        break;
                }
            });

        // === JQGRID 1 ===
            var valor1 = new Array();
            valor1[0]  = 10;
            utilitarios(valor1);

        // === VALIDATE 1 ===
            var valor1 = new Array();
            valor1[0]  = 16;
            utilitarios(valor1);

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
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 28;
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
                    sortname    : 'rrhh_salidas.id',
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
                            editoptions: {value:no_si_json}
                        },
                        {
                            name       : col_m_name_1[3],
                            index      : col_m_index_1[3],
                            width      : col_m_width_1[3],
                            align      : col_m_align_1[3],
                            stype      :'select',
                            editoptions: {value:no_si_json}
                        },
                        {
                            name       : col_m_name_1[4],
                            index      : col_m_index_1[4],
                            width      : col_m_width_1[4],
                            align      : col_m_align_1[4],
                            stype      :'select',
                            editoptions: {value:no_si_json}
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
                            editoptions: {value:no_si_jqgrid}
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

                            @if(in_array(['codigo' => '1403'], $permisos))
                                ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            @else
                                ed = '';
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
                    .navButtonAdd(pjqgrid1,{
                    "id"          : "edit1",
                    caption       : "",
                    title         : 'Editar fila',
                    buttonicon    : "ui-icon ui-icon-pencil",
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
                            utilitarios([12, id]);
                        }
                    }
                })
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
                          var valor1 = new Array();
                          valor1[0]  = 13;
                          utilitarios(valor1);
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
                 $('#modal_1_title').append('Modificar horario');

                $("#id_horario").val(valor[1]);

                $(".estado_class[value=" + val_json.estado + "]").prop('checked', true);
                $(".defecto_class[value=" + val_json.defecto + "]").prop('checked', true);
                $(".tipo_horario_class[value=" + val_json.tipo_horario + "]").prop('checked', true);
                $("#lugar_dependencia_id").select2("val", val_json.lugar_dependencia_id);
                $("#nombre").val(ret.nombre);

                $("#h_ingreso").val(ret.h_ingreso);
                $("#h_salida").val(ret.h_salida);
                $("#tolerancia").val(ret.tolerancia);

                $("#marcacion_ingreso_del").val(ret.marcacion_ingreso_del);
                $("#marcacion_ingreso_al").val(ret.marcacion_ingreso_al);
                $("#marcacion_salida_del").val(ret.marcacion_salida_del);
                $("#marcacion_salida_al").val(ret.marcacion_salida_al);

                if(val_json.lunes == '2'){
                     $("#lunes").prop('checked', true);
                }
                if(val_json.martes == '2'){
                     $("#martes").prop('checked', true);
                }
                if(val_json.miercoles == '2'){
                     $("#miercoles").prop('checked', true);
                }
                if(val_json.jueves == '2'){
                     $("#jueves").prop('checked', true);
                }
                if(val_json.viernes == '2'){
                     $("#viernes").prop('checked', true);
                }
                if(val_json.sabado == '2'){
                     $("#sabado").prop('checked', true);
                }
                if(val_json.domingo == '2'){
                     $("#domingo").prop('checked', true);
                }

                $('#modal_1').modal();
                break;
            // === REPORTES MODAL ===
            case 13:
                $('#modal_2').modal();
                break;
            // === RESETEAR FORMULARIO ===
            case 14:
                $('#modal_1_title').empty();
                $('#modal_1_title').append('Nueva solicitud de salida');

                $("#id_salida").val('');

                $('#tipo_salida_id').select2("val", "");
                $('#tipo_salida').select2("val", "");
                $('#persona_id_superior').select2("val", "");
                $('#persona_id_superior option').remove();

                $("#tipo_salida").select2("enable", false);

                $(form_1)[0].reset();
                break;
            // === GUARDAR REGISTRO ===
            case 15:
                if($(form_1).valid()){
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
                            maxlength: 500
                        },
                        motivo:{
                            maxlength: 500
                        },
                        f_salida:{
                            required: true
                        },
                        h_salida:{
                            required: true
                        }
                    }
                });
                break;
            // === EXCEL CARGOS ===
            case 17:
                var concatenar_valores = '';
                concatenar_valores     += '?tipo=1';

                var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                win.focus();
                break;
            // === EXCEL MARCACIONES ===
            case 18:
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
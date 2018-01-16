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

            "horario_1_e",
            "horario_1_s",
            "h1_min_retrasos",

            "horario_2_e",
            "horario_2_s",
            "h2_min_retrasos",

            "ud_funcionario",
            "lugar_dependencia_funcionario",

            "horario_1",
            "horario_2",

            "cargo",
            "auo_cargo",
            "lugar_dependencia_cargo",

            "observaciones",

            "val_json"
        );
        var col_m_index_1 = new Array(
            "",

            "rrhh_asistencias.estado",

            "rrhh_asistencias.fecha::text",

            "a2.n_documento",
            "a2.nombre",
            "a2.ap_paterno",
            "a2.ap_materno",

            "rrhh_asistencias.horario_1_e",
            "rrhh_asistencias.horario_1_s",
            "rrhh_asistencias.h1_min_retrasos::text",

            "rrhh_asistencias.horario_2_e",
            "rrhh_asistencias.horario_2_s",
            "rrhh_asistencias.h2_min_retrasos::text",

            "a3.nombre",
            "a4.lugar_dependencia_funcionario",

            ""
        );
        var col_m_width_1 = new Array(
            33,

            80,

            80,

            80,
            100,
            100,
            100,

            150,
            150,
            65,

            150,
            150,
            65,

            400,
            400,

            10
        );
        var col_m_align_1 = new Array(
            "center",

            "center",

            "center",

            "right",
            "left",
            "left",
            "left",

            "center",
            "center",
            "right",

            "center",
            "center",
            "right",

            "center",
            "center",

            "center"
        );

    // === JQGRID2 ===
        var jqgrid2   = "#jqgrid2";
        var pjqgrid2 = "#pjqgrid2";

    // === FORMULARIO 1 ===
        var form_1 = "#form_1";
        var form_3 = "#form_3";

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
            $('#fecha_jqgrid').datepicker({
                // startView            : 0,
                // todayBtn          : "linked",
                // keyboardNavigation: false,
                // forceParse        : false,
                autoclose            : true,
                format               : "yyyy-mm-dd",
                startDate            : '-100y',
                endDate              : '-1d',
                language             : "es"
            });


        // === DROPZONE ===
            // var valor1 = new Array();
            // valor1[0]  = 20;
            // utilitarios(valor1);

        // === SELECT CHANGE ===
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
            // var valor1 = new Array();
            // valor1[0]  = 23;
            // utilitarios(valor1);

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
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1303'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '1304'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
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
                        col_name_1[8],
                        col_name_1[9],
                        col_name_1[10],
                        col_name_1[11],
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

                            @if(in_array(['codigo' => '1303'], $permisos))
                                ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            @else
                                ed = '';
                            @endif

                            @if(in_array(['codigo' => '1304'], $permisos))
                                if(ret.n_documento != ''){
                                    var ci_nombre = ret.n_documento + ' - ' + $.trim(ret.ap_paterno + ' ' + ret.ap_materno) + ' ' + ret.nombre_persona;
                                    vmb = " <button type='button' class='btn btn-xs btn-primary' title='Ver marcaciones del biométrico' onclick=\"utilitarios([22, " + val_json.persona_id + ", '" + ci_nombre +"']);\"><i class='fa fa-eye'></i></button>";
                                }
                                else{
                                    vmb = '';
                                }
                            @else
                                vmb = '';
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed + vmb)
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
                            startColumnName: 'horario_1_e',
                            numberOfColumns: 3,
                            titleText      : 'HORARIO 1'
                        },
                        {
                            startColumnName: 'horario_2_e',
                            numberOfColumns: 3,
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
                @if(in_array(['codigo' => '1303'], $permisos))
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
                @if(in_array(['codigo' => '1304'], $permisos))
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

                if(val_json.funcionario_id == null){
                    $('#modal_1_title').append('Agregar información del funcionario con ' + ret.tipo_cargo);
                }
                else{
                    $('#modal_1_title').append('Modificar información del funcionario con ' + ret.tipo_cargo);
                }

                $("#id_funcionario").val(val_json.funcionario_id);
                $("#cargo_id").val(valor[1]);
                $("#tipo_cargo_id").val(val_json.tipo_cargo_id);

                if(val_json.situacion != ''){
                    $(".situacion_class[value=" + val_json.situacion + "]").prop('checked', true);
                }

                if(ret.n_documento != ""){
                    var persona = ret.n_documento + ' - ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno) + ' ' + ret.nombre_persona;

                    $('#persona_id').append('<option value="' + val_json.persona_id + '">' + persona + '</option>');
                    $("#persona_id").select2("val", val_json.persona_id);
                }

                $("#f_ingreso").val(ret.f_ingreso);
                $("#f_salida").val(ret.f_salida);
                $("#sueldo").val(ret.sueldo);
                $("#observaciones").val(ret.observaciones);

                if(ret.lugar_dependencia_funcionario != ""){
                    $("#lugar_dependencia_id_funcionario").select2("val", val_json.lugar_dependencia_id_funcionario);

                    $("#unidad_desconcentrada_id").select2("val", val_json.unidad_desconcentrada_id);
                }

                if(ret.cargo != ""){
                    $("#lugar_dependencia_id_cargo").select2("val", val_json.lugar_dependencia_id_cargo);

                    $('#auo_id').append('<option value="' + val_json.auo_id + '">' + ret.auo_cargo + '</option>');
                    $("#auo_id").select2("val", val_json.auo_id);

                    $('#cargo_id_d').append('<option value="' + valor[1] + '">' + ret.cargo + '</option>');
                    $("#cargo_id_d").select2("val", valor[1]);
                }

                $("#lugar_dependencia_id_cargo").select2("enable", false);
                $("#auo_id").select2("enable", false);
                $("#cargo_id_d").select2("enable", false);

                $('#modal_1').modal();
                break;
            // === REPORTES MODAL ===
            case 13:
                $('#modal_2').modal();
                break;
            // === RESETEAR FORMULARIO ===
            case 14:
                $("#id_funcionario").val('');
                $("#cargo_id").val('');
                $("#tipo_cargo_id").val('');

                $('#persona_id').select2("val", "");
                $('#persona_id option').remove();

                $('#lugar_dependencia_id_funcionario').select2("val", "");

                $('#unidad_desconcentrada_id').select2("val", "");
                $('#unidad_desconcentrada_id option').remove();

                $('#lugar_dependencia_id_cargo').select2("val", "");

                $('#auo_id').select2("val", "");
                $('#auo_id option').remove();

                $('#cargo_id_d').select2("val", "");
                $('#cargo_id_d option').remove();

                $(form_1)[0].reset();
                break;
            // === GUARDAR REGISTRO ===
            case 15:
                if($(form_1).valid()){
                    var tipo_cargo_id = $.trim($("#tipo_cargo_id").val());

                    if(tipo_cargo_id == '1'){
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
                        var f_salida = $.trim($("#f_salida").val());
                        if(f_salida != ''){
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
                            valor1[2]  = "¡El campo FECHA DE SALIDA es obligatorio!";
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
                        persona_id:{
                            required: true
                        },
                        f_ingreso:{
                            required: true,
                            date    :true
                        },
                        f_salida:{
                            date:true
                        },
                        sueldo:{
                            required: true,
                            number  : true,
                            min     : 0
                        },
                        observaciones:{
                            maxlength: 500
                        },

                        lugar_dependencia_id_funcionario:{
                            required: true
                        },
                        unidad_desconcentrada_id:{
                            required: true
                        },

                        horario_id_1:{
                            required: true
                        }
                    }
                });
                break;
            // === SELECT2 ORGANIGRAMA AREA O UNIDAD DESCONCENTRADA ===
            case 17:
                $('#chart-container-1').empty();

                var auo_id=$.trim($("#auo_id_r").val());

                if(auo_id != ''){
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
                    valor1[4]  = "tipo=101&auo_id=" + $("#auo_id_r").val() + "&_token=" + csrf_token;
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = "ALERTA";
                    valor1[2]  = "¡Favor seleccione un ÁREA O UNIDAD ORGANIZACIONAL!";
                    utilitarios(valor1);
                }
                break;
            // === EXCEL CARGOS ===
            case 18:
                var concatenar_valores = '';
                concatenar_valores     += '?tipo=1';

                var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                win.focus();
                break;
            // === MODAL SUBIR DOCUMENTO ===
            case 19:
                $('#modal_2_subtitle').empty();
                $('#modal_2_subtitle').append(valor[2]);
                $("#id_funcionario_2").val(valor[1]);
                $('#modal_2').modal();
                break;
            // === DROPZONE 1 ===
            case 20:
                $("#dropzoneForm_1").dropzone({
                    url: url_controller + "/send_ajax",
                    method:'post',
                    addRemoveLinks: true,
                    maxFilesize: 5, // MB
                    dictResponseError: "Ha ocurrido un error en el server.",
                    acceptedFiles:'application/pdf',
                    paramName: "file", // The name that will be used to transfer the file
                    maxFiles:1,
                    clickable:true,
                    parallelUploads:1,
                    params: {
                        tipo: 2,
                        _token: csrf_token
                    },
                    // forceFallback:true,
                    createImageThumbnails: true,
                    maxThumbnailFilesize: 1,
                    autoProcessQueue:true,

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
            // === ELIMINAR FUNCIONARIO DEL CARGO ===
            case 21:
                swal({
                    title             : "ELIMINAR FUNCIONARIO DEL CARGO",
                    text              : "¿Esta seguro de eliminar al FUNCIONARIO del cargo?",
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
                            title            : "ELIMINANDO FUNCIONARIO DEL CARGO",
                            text             : "Espere a que se elimine al FUNCIONARIO del cargo.",
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
                        valor1[4]  = "tipo=3&id=" + valor[1] + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                    }
                    else{
                        swal.close();
                    }
                });
                break;
            // === MODAL SUBIR DOCUMENTO ===
            case 22:
                var valor1 = new Array();
                valor1[0]  = 25;
                utilitarios(valor1);

                $(jqgrid2).jqGrid('setGridParam',{
                    url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=2&persona_id=' + valor[1],
                    datatype: 'json'
                }).trigger('reloadGrid');

                $(jqgrid2).jqGrid('setCaption', "<span class='text-success'>" + valor[2] + "</span>");

                $("#persona_id_3").val(valor[1]);

                $('#modal_3').modal();

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
            // === EXCEL MARCACIONES ===
            case 24:
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
            // === RESETEAR FORMULARIO 3 ===
            case 25:
                $("#persona_id_3").val('');

                $('#lugar_dependencia_id_3').select2("val", "");

                $('#unidad_desconcentrada_id_3').select2("val", "");
                $('#unidad_desconcentrada_id_3 option').remove();

                $(form_3)[0].reset();
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
                            // === ELIMINAR FUNCIONARIO DEL CARGO ===
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
                            // === SELECT2 ORGANIGRAMA CARGOS POR AREA O UNIDAD DESCONCENTRADA ===
                            case '101':
                                if(data.sw === 1){
                                    $('#chart-container-1').orgchart({
                                        'data'               : data.respuesta,
                                        // 'depth'           : 2,
                                        'nodeContent'        : 'title',
                                        'exportButton'       : true,
                                        'exportFilename'     : 'organigrama_por_auo'
                                    });
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
                            // === SELECT2 CARGOS POR UNIDAD DESCONCENTRADA ===
                            case '102':
                                if(data.sw === 2){
                                    var cargo_select = '';
                                    $.each(data.consulta, function(index, value) {
                                        cargo_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                                    });
                                    $('#cargo_id').append(cargo_select);
                                }
                                break;
                            // === SELECT2 UNIDAD DESCONCENTRADA ===
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
                            // === SELECT2 UNIDAD DESCONCENTRADA ===
                            case '104':
                                if(data.sw === 2){
                                    var unidad_desconcentrada_select = '';
                                    $.each(data.consulta, function(index, value) {
                                        unidad_desconcentrada_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                                    });
                                    $('#unidad_desconcentrada_id_3').append(unidad_desconcentrada_select);
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
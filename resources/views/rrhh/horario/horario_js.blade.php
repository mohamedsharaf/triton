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
        var url_controller = "{!! url('/horario') !!}";
        var csrf_token     = "{!! csrf_token() !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",
            "ESTADO",
            "¿POR DEFECTO?",
            "TIPO HORARIO",
            "NOMBRE DEL HORARIO",

            "INGRESO",
            "SALIDA",
            "TOLERANCIA",

            "DEL ",
            "AL",
            "DEL",
            "AL",

            "LUNES",
            "MARTES",
            "MIERCOLES",
            "JUEVES",
            "VIERNES",
            "SABADO",
            "DOMINGO",

            "LUGAR DE DEPENDENCIA",

            ""
        );
        var col_m_name_1  = new Array(
            "act",
            "estado",
            "defecto",
            "tipo_horario",
            "nombre",

            "h_ingreso",
            "h_salida",
            "tolerancia",

            "marcacion_ingreso_del",
            "marcacion_ingreso_al",
            "marcacion_salida_del",
            "marcacion_salida_al",

            "lunes",
            "martes",
            "miercoles",
            "jueves",
            "viernes",
            "sabado",
            "domingo",

            "lugar_dependencia",

            "val_json"
        );
        var col_m_index_1 = new Array(
            "",
            "rrhh_horarios.estado",
            "rrhh_horarios.defecto",
            "rrhh_horarios.tipo_horario",
            "rrhh_horarios.nombre",

            "rrhh_horarios.h_ingreso::text",
            "rrhh_horarios.h_salida::text",
            "rrhh_horarios.tolerancia::text",

            "rrhh_horarios.marcacion_ingreso_del::text",
            "rrhh_horarios.marcacion_ingreso_al::text",
            "rrhh_horarios.marcacion_salida_del::text",
            "rrhh_horarios.marcacion_salida_al::text",

            "rrhh_horarios.lunes",
            "rrhh_horarios.martes",
            "rrhh_horarios.miercoles",
            "rrhh_horarios.jueves",
            "rrhh_horarios.viernes",
            "rrhh_horarios.sabado",
            "rrhh_horarios.domingo",

            "a2.nombre",

            ""
        );
        var col_m_width_1 = new Array(
            33,
            100,
            110,
            100,
            300,

            80,
            80,
            90,

            85,
            85,
            85,
            85,

            100,
            100,
            100,
            100,
            100,
            100,
            100,

            300,

            10
        );
        var col_m_align_1 = new Array(
            "center",
            "center",
            "center",
            "center",
            "left",

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

    // === DEFECTO ===
        var defecto_json   = $.parseJSON('{!! json_encode($defecto_array) !!}');
        var defecto_select = '';
        var defecto_jqgrid = ':Todos';

        $.each(defecto_json, function(index, value) {
            defecto_select += '<option value="' + index + '">' + value + '</option>';
            defecto_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO DE HORARIO ===
        var tipo_horario_json   = $.parseJSON('{!! json_encode($tipo_horario_array) !!}');
        var tipo_horario_select = '';
        var tipo_horario_jqgrid = ':Todos';

        $.each(tipo_horario_json, function(index, value) {
            tipo_horario_select += '<option value="' + index + '">' + value + '</option>';
            tipo_horario_jqgrid += ';' + index + ':' + value;
        });

    // === DIAS ===
        var dias_json   = $.parseJSON('{!! json_encode($dias_array) !!}');
        var dias_select = '';
        var dias_jqgrid = ':Todos';

        $.each(dias_json, function(index, value) {
            dias_select += '<option value="' + index + '">' + value + '</option>';
            dias_jqgrid += ';' + index + ':' + value;
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
            $('#lugar_dependencia_id').append(lugar_dependencia_select);
            $("#lugar_dependencia_id").select2({
                maximumSelectionLength: 1
            });
            $("#lugar_dependencia_id").appendTo("#lugar_dependencia_id_div");

            $('#h_ingreso, #h_salida, #marcacion_ingreso_del, #marcacion_ingreso_al, #marcacion_salida_del, #marcacion_salida_al').clockpicker({
                autoclose: true,
                donetext : 'Hecho'
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
                @if(in_array(['codigo' => '1403'], $permisos))
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
                    sortname    : 'rrhh_horarios.id',
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
                        col_name_1[18],

                        col_name_1[19],

                        col_name_1[20]
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
                            editoptions: {value:defecto_jqgrid}
                        },
                        {
                            name       : col_m_name_1[3],
                            index      : col_m_index_1[3],
                            width      : col_m_width_1[3],
                            align      : col_m_align_1[3],
                            stype      :'select',
                            editoptions: {value:tipo_horario_jqgrid}
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
                            name       : col_m_name_1[12],
                            index      : col_m_index_1[12],
                            width      : col_m_width_1[12],
                            align      : col_m_align_1[12],
                            stype      :'select',
                            editoptions: {value:dias_jqgrid}
                        },
                        {
                            name       : col_m_name_1[13],
                            index      : col_m_index_1[13],
                            width      : col_m_width_1[13],
                            align      : col_m_align_1[13],
                            stype      :'select',
                            editoptions: {value:dias_jqgrid}
                        },
                        {
                            name       : col_m_name_1[14],
                            index      : col_m_index_1[14],
                            width      : col_m_width_1[14],
                            align      : col_m_align_1[14],
                            stype      :'select',
                            editoptions: {value:dias_jqgrid}
                        },
                        {
                            name       : col_m_name_1[15],
                            index      : col_m_index_1[15],
                            width      : col_m_width_1[15],
                            align      : col_m_align_1[15],
                            stype      :'select',
                            editoptions: {value:dias_jqgrid}
                        },
                        {
                            name       : col_m_name_1[16],
                            index      : col_m_index_1[16],
                            width      : col_m_width_1[16],
                            align      : col_m_align_1[16],
                            stype      :'select',
                            editoptions: {value:dias_jqgrid}
                        },
                        {
                            name       : col_m_name_1[17],
                            index      : col_m_index_1[17],
                            width      : col_m_width_1[17],
                            align      : col_m_align_1[17],
                            stype      :'select',
                            editoptions: {value:dias_jqgrid}
                        },
                        {
                            name       : col_m_name_1[18],
                            index      : col_m_index_1[18],
                            width      : col_m_width_1[18],
                            align      : col_m_align_1[18],
                            stype      :'select',
                            editoptions: {value:dias_jqgrid}
                        },

                        {
                            name       : col_m_name_1[19],
                            index      : col_m_index_1[19],
                            width      : col_m_width_1[19],
                            align      : col_m_align_1[19],
                            stype      :'select',
                            editoptions: {value:lugar_dependencia_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_1[20],
                                index : col_m_index_1[20],
                                width : col_m_width_1[20],
                                align : col_m_align_1[20],
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
                            startColumnName: 'h_ingreso',
                            numberOfColumns: 2,
                            titleText      : 'HORA DE'
                        },
                        {
                            startColumnName: 'marcacion_ingreso_del',
                            numberOfColumns: 2,
                            titleText      : 'MARCACION DE INGRESO'
                        },
                        {
                            startColumnName: 'marcacion_salida_del',
                            numberOfColumns: 2,
                            titleText      : 'MARCACION DE SALIDA'
                        },
                        {
                            startColumnName: 'lunes',
                            numberOfColumns: 7,
                            titleText      : 'DIAS DE LA SEMANA'
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
                @if(in_array(['codigo' => '1402'], $permisos))
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
                @if(in_array(['codigo' => '1403'], $permisos))
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
                @if(in_array(['codigo' => '1404'], $permisos))
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
                $('#modal_1_title').append('Agregar nuevo horario');

                $("#id_horario").val('');

                $('#lugar_dependencia_id').select2("val", "");

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
                        lugar_dependencia_id:{
                            required: true
                        },
                        nombre:{
                            required : true,
                            maxlength: 500
                        },
                        h_ingreso:{
                            required: true
                        },
                        h_salida:{
                            required: true
                        },
                        tolerancia:{
                            required: true,
                            number  : true,
                            min     : 0
                        },
                        marcacion_ingreso_del:{
                            required: true
                        },
                        marcacion_ingreso_al:{
                            required: true
                        },
                        marcacion_salida_del:{
                            required: true
                        },
                        marcacion_salida_al:{
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
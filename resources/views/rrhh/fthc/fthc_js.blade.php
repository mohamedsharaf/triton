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
        var url_controller = "{!! url('/fthc') !!}";
        var csrf_token     = "{!! csrf_token() !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",
            "ESTADO",
            "FECHA",
            "NOMBRE",

            "TIPO",
            "LUGAR DE DEPENDENCIA",
            "UNIDAD DESCONCENTRADA",

            "HORARIO ",

            "JORNADA",
            "SEXO",

            ""
        );
        var col_m_name_1  = new Array(
            "act",
            "estado",
            "fecha",
            "nombre",

            "tipo_fthc",
            "lugar_dependencia",
            "unidad_desconcentrada",

            "horario",

            "tipo_horario",
            "sexo",

            "val_json"
        );
        var col_m_index_1 = new Array(
            "",
            "rrhh_fthc.estado",
            "rrhh_fthc.fecha::text",
            "rrhh_fthc.nombre",

            "rrhh_fthc.tipo_fthc",
            "a2.nombre",
            "a3.nombre",

            "a4.nombre",

            "rrhh_fthc.tipo_horario",
            "rrhh_fthc.sexo",

            ""
        );
        var col_m_width_1 = new Array(
            33,
            100,
            90,
            300,

            135,
            300,
            300,

            300,

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

    // === FERIADO, TOLERANCIA Y HORARIO CONTINUO ===
        var fthc_json   = $.parseJSON('{!! json_encode($fthc_array) !!}');
        var fthc_select = '';
        var fthc_jqgrid = ':Todos';

        $.each(fthc_json, function(index, value) {
            fthc_select += '<option value="' + index + '">' + value + '</option>';
            fthc_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO DE HORARIO ===
        var tipo_horario_json   = $.parseJSON('{!! json_encode($tipo_horario_array) !!}');
        var tipo_horario_select = '';
        var tipo_horario_jqgrid = ':Todos';

        $.each(tipo_horario_json, function(index, value) {
            tipo_horario_select += '<option value="' + index + '">' + value + '</option>';
            tipo_horario_jqgrid += ';' + index + ':' + value;
        });

    // === SEXO ===
        var sexo_json   = $.parseJSON('{!! json_encode($sexo_array) !!}');
        var sexo_select = '';
        var sexo_jqgrid = ':Todos';

        $.each(sexo_json, function(index, value) {
            sexo_select += '<option value="' + index + '">' + value + '</option>';
            sexo_jqgrid += ';' + index + ':' + value;
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
            $("#lugar_dependencia_id, #unidad_desconcentrada_id, #horario_id").select2({
                maximumSelectionLength: 1
            });
            $("#lugar_dependencia_id").appendTo("#lugar_dependencia_id_div");
            $("#unidad_desconcentrada_id").appendTo("#unidad_desconcentrada_id_div");
            $("#horario_id").appendTo("#horario_id_div");

            $('#fecha').datepicker({
                // startView            : 2,
                // todayBtn          : "linked",
                // keyboardNavigation: false,
                // forceParse        : false,
                autoclose            : true,
                format               : "yyyy-mm-dd",
                startDate            : '-0y',
                endDate              : '+5y',
                language             : "es"
            });

        // === SELECT CHANGE ===
            $("#lugar_dependencia_id").on("change", function(e) {
                $('#unidad_desconcentrada_id').select2('val','');
                $('#unidad_desconcentrada_id option').remove();
                $('#horario_id').select2('val','');
                $('#horario_id option').remove();
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

            $(".fthc_class").on("change", function(e){
                $(".horario_continuo_div, .tolerancia_div").slideUp('');
                switch($(".fthc_class:checked").val()){
                    case '2':
                        $(".tolerancia_div").slideDown('');
                        break;
                    case '3':
                        $(".horario_continuo_div").slideDown('');
                        break;
                    default:
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
                @if(in_array(['codigo' => '1503'], $permisos))
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
                    sortname    : 'rrhh_fthc.fecha',
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

                        col_name_1[10]
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
                            align: col_m_align_1[2],
                        },
                        {
                            name : col_m_name_1[3],
                            index: col_m_index_1[3],
                            width: col_m_width_1[3],
                            align: col_m_align_1[3]
                        },

                        {
                            name       : col_m_name_1[4],
                            index      : col_m_index_1[4],
                            width      : col_m_width_1[4],
                            align      : col_m_align_1[4],
                            stype      :'select',
                            editoptions: {value:fthc_jqgrid}
                        },
                        {
                            name       : col_m_name_1[5],
                            index      : col_m_index_1[5],
                            width      : col_m_width_1[5],
                            align      : col_m_align_1[5],
                            stype      :'select',
                            editoptions: {value:lugar_dependencia_jqgrid}
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
                            name       : col_m_name_1[8],
                            index      : col_m_index_1[8],
                            width      : col_m_width_1[8],
                            align      : col_m_align_1[8],
                            stype      :'select',
                            editoptions: {value:tipo_horario_jqgrid}
                        },
                        {
                            name       : col_m_name_1[9],
                            index      : col_m_index_1[9],
                            width      : col_m_width_1[9],
                            align      : col_m_align_1[9],
                            stype      :'select',
                            editoptions: {value:sexo_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_1[10],
                                index : col_m_index_1[10],
                                width : col_m_width_1[10],
                                align : col_m_align_1[10],
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

                            @if(in_array(['codigo' => '1503'], $permisos))
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
                            startColumnName: 'horario',
                            numberOfColumns: 1,
                            titleText      : 'HORARIO CONTINUO'
                        },
                        {
                            startColumnName: 'tipo_horario',
                            numberOfColumns: 2,
                            titleText      : 'TOLERANCIA'
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
                @if(in_array(['codigo' => '1502'], $permisos))
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
                @if(in_array(['codigo' => '1503'], $permisos))
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
                @if(in_array(['codigo' => '1504'], $permisos))
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
                 $('#modal_1_title').append('Modificar feriado o tolerancia o horario continuo');

                $("#id_fthc").val(valor[1]);

                $(".estado_class[value=" + val_json.estado + "]").prop('checked', true);
                $("#fecha").val(ret.fecha);
                $("#nombre").val(ret.nombre);

                $(".fthc_class[value=" + val_json.tipo_fthc + "]").prop('checked', true);

                $("#lugar_dependencia_id").select2("val", val_json.lugar_dependencia_id);

                if(ret.unidad_desconcentrada != ''){
                    $("#unidad_desconcentrada_id").select2("val", val_json.unidad_desconcentrada_id);
                }

                switch(val_json.tipo_fthc + ''){
                    case '2':
                        $(".tolerancia_div").slideDown('');

                        if(ret.tipo_horario != ''){
                            $(".tipo_horario_class[value=" + val_json.tipo_horario + "]").prop('checked', true);
                        }

                        if(ret.sexo != ''){
                            $(".sexo_class[value=" + val_json.sexo + "]").prop('checked', true);
                        }
                        break;
                    case '3':
                        $(".horario_continuo_div").slideDown('');

                        if(ret.horario != ''){
                            $("#horario_id").select2("val", val_json.horario_id);
                        }
                        break;
                    default:
                        break;
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
                $('#modal_1_title').append('Agregar nuevo feriado o tolerancia o horario continuo');

                $("#id_fthc").val('');

                $('#lugar_dependencia_id').select2("val", "");

                $(".horario_continuo_div, .tolerancia_div").slideUp('');

                $(form_1)[0].reset();
                break;
            // === GUARDAR REGISTRO ===
            case 15:
                if($(form_1).valid()){
                    var tipo_fthc = $(".fthc_class:checked").val();
                    if(tipo_fthc == '3'){
                        var horario_id = $("#horario_id").val();
                        if(horario_id != ''){
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
                            valor1[2]  = "El campo HORARIO es obligatorio.";
                            utilitarios(valor1);
                        }
                    }
                    else{
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
                        fecha:{
                            required: true
                        },
                        nombre:{
                            required : true,
                            maxlength: 500
                        },

                        lugar_dependencia_id:{
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
                                        $.each(data.horario_1, function(index, value) {
                                            horario_1_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                                        });
                                        $('#horario_id').append(horario_1_select);
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
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
        var url_controller = "{!! url('/home') !!}";
        var csrf_token     = "{!! csrf_token() !!}";
        var public_url     = "{!! asset($public_url) !!}";

    // === INFORMACION PERSONAL ===
        var usuario_json = $.parseJSON('{!! json_encode($usuario_array) !!}');
        var persona_json = $.parseJSON('{!! json_encode($persona_array) !!}');

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",

            "ESTADO",

            "FECHA",

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

            "horario_1_i",
            "horario_1_s",
            "h1_min_retrasos",

            "horario_2_i",
            "horario_2_s",
            "h2_min_retrasos",

            "ud_funcionario",
            "lugar_dependencia_funcionario",

            "val_json"
        );
        var col_m_index_1 = new Array(
            "",

            "rrhh_asistencias.estado",

            "rrhh_asistencias.fecha::text",

            "rrhh_asistencias.horario_1_i",
            "rrhh_asistencias.horario_1_s",
            "rrhh_asistencias.h1_min_retrasos::text",

            "rrhh_asistencias.horario_2_i",
            "rrhh_asistencias.horario_2_s",
            "rrhh_asistencias.h2_min_retrasos::text",

            "a3.nombre",
            "a4.nombre",

            ""
        );
        var col_m_width_1 = new Array(
            33,

            90,

            80,

            250,
            250,
            65,

            250,
            250,
            65,

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

            "center",
            "center",

            "center"
        );

    // === FORMULARIO 1 ===
        var form_1 = "#form_1";
        var form_2 = "#form_2";

    // === ESTADO CIVIL ===
        var estado_civil_json   = $.parseJSON('{!! json_encode($estado_civil_array) !!}');
        var estado_civil_select = '';
        var estado_civil_jqgrid = ':Todos';

        $.each(estado_civil_json, function(index, value) {
            estado_civil_select += '<option value="' + index + '">' + value + '</option>';
            estado_civil_jqgrid += ';' + index + ':' + value;
        });

    // === SEXO ===
        var sexo_json   = $.parseJSON('{!! json_encode($sexo_array) !!}');
        var sexo_select = '';
        var sexo_jqgrid = ':Todos';

        $.each(sexo_json, function(index, value) {
            sexo_select += '<option value="' + index + '">' + value + '</option>';
            sexo_jqgrid += ';' + index + ':' + value;
        });

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
            $('#estado_civil').append(estado_civil_select);
            $("#estado_civil").select2({
                maximumSelectionLength: 1
            });
            $("#estado_civil").appendTo("#estado_civil_div");

            $('#municipio_id_nacimiento, #municipio_id_residencia').select2({
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
            $("#municipio_id_nacimiento").appendTo("#municipio_id_nacimiento_div");
            $("#municipio_id_residencia").appendTo("#municipio_id_residencia_div");

            $('#f_nacimiento').datepicker({
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

            @if($sw_asistencia)
                $('.nav-tabs a[href="#tab-3"]').tab('show');
            @endif

        // === INFORMACIÓN PERSONAL ===
            var valor1 = new Array();
            valor1[0]  = 1;
            utilitarios(valor1);

        // === DROPZONE ===
            var valor1 = new Array();
            valor1[0]  = 18;
            utilitarios(valor1);

        // === JQGRID 1 ===
            @if($sw_asistencia)
                var valor1 = new Array();
                valor1[0]  = 10;
                utilitarios(valor1);
            @endif

        // === JQGRID 2 ===
            @if($sw_asistencia)
                var valor1 = new Array();
                valor1[0]  = 25;
                utilitarios(valor1);
            @endif

        // === VALIDATE 1 ===
            var valor1 = new Array();
            valor1[0]  = 16;
            utilitarios(valor1);

        // === VALIDATE 2 ===
            var valor1 = new Array();
            valor1[0]  = 19;
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
            // === INFORMACIÓN PERSONAL ===
            case 1:
                @if($persona_array_sw)
                    var n_documento       = persona_json.n_documento;
                    var n_documento_array = n_documento.split('-');
                    $("#n_documento").val(n_documento_array[0]);
                    $("#n_documento_1").val(n_documento_array[1]);
                    $("#nombre").val(persona_json.nombre);
                    $("#ap_paterno").val(persona_json.ap_paterno);
                    $("#ap_materno").val(persona_json.ap_materno);
                    $("#ap_esposo").val(persona_json.ap_esposo);
                    $("#f_nacimiento").val(persona_json.f_nacimiento);
                    $("#estado_civil").select2("val", persona_json.estado_civil);

                    $(".sexo_class[value=" + persona_json.sexo + "]").prop('checked', true);
                    $("#domicilio").val(persona_json.domicilio);
                    $("#telefono").val(persona_json.telefono);
                    $("#celular").val(persona_json.celular);

                    if(persona_json.municipio_nacimiento != null){
                        var dpm = persona_json.departamento_nacimiento + ', ' + persona_json.provincia_nacimiento + ', ' + persona_json.municipio_nacimiento;
                        $('#municipio_id_nacimiento').append('<option value="' + persona_json.municipio_id_nacimiento + '">' + dpm + '</option>');
                        $("#municipio_id_nacimiento").select2("val", persona_json.municipio_id_nacimiento);
                    }

                    if(persona_json.municipio_residencia != ""){
                        var dpm = persona_json.departamento_residencia + ', ' + persona_json.provincia_residencia + ', ' + persona_json.municipio_residencia;
                        $('#municipio_id_residencia').append('<option value="' + persona_json.municipio_id_residencia + '">' + dpm + '</option>');
                        $("#municipio_id_residencia").select2("val", persona_json.municipio_id_residencia);
                    }
                @endif

                $("#rol").val(usuario_json.rol);
                $("#email").val(usuario_json.email);

                if(usuario_json.imagen != null){
                    $('#image_user').removeAttr('scr');
                    $('#image_user').attr('src', public_url + '/' + usuario_json.imagen + '?' + Math.random());
                }
                break;

            // === JQGRID 1 ===
            @if($sw_asistencia)
                case 10:
                    var edit1      = true;
                    var ancho1     = 5;
                    var ancho_d    = 29;

                    $(jqgrid1).jqGrid({
                        caption     : title_table,
                        url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
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
                            col_name_1[11]
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
                                name       : col_m_name_1[10],
                                index      : col_m_index_1[10],
                                width      : col_m_width_1[10],
                                align      : col_m_align_1[10],
                                stype      :'select',
                                editoptions: {value:lugar_dependencia_jqgrid}
                            },

                            // === OCULTO ===
                                {
                                    name  : col_m_name_1[11],
                                    index : col_m_index_1[11],
                                    width : col_m_width_1[11],
                                    align : col_m_align_1[11],
                                    search: false,
                                    hidden: true
                                }
                        ],
                        loadComplete : function(){
                            $("tr.jqgrow:odd").addClass('myAltRowClass');
                        }
                    });

                    $(jqgrid1).jqGrid('setGroupHeaders', {
                        useColSpanStyle: true,
                        groupHeaders   :[
                            {
                                startColumnName: 'horario_1_i',
                                numberOfColumns: 3,
                                titleText      : 'HORARIO 1'
                            },
                            {
                                startColumnName: 'horario_2_i',
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
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "print1",
                        caption       : "",
                        title         : 'Reportes',
                        buttonicon    : "ui-icon ui-icon-print",
                        onClickButton : function(){
                            // var valor1 = new Array();
                            // valor1[0]  = 13;
                            // utilitarios(valor1);
                        }
                    })
                    ;
                    break;
            @endif
            // === ABRIR MODAL ===
            case 11:
                $('#modal_1').modal();
                break;
            // === EDICION MODAL ===
            case 12:
                break;
            // === REPORTES MODAL ===
            case 13:
                alert("REPORTE");
                break;
            // === RESETEAR FORMULARIO ===
            case 14:
                break;
            // === GUARDAR REGISTRO ===
            case 15:
                if($(form_1).valid()){
                    var ap_paterno = $.trim($("#ap_paterno").val());
                    var ap_materno = $.trim($("#ap_materno").val());
                    if(ap_paterno != '' || ap_materno != ''){
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
                        valor1[2]  = "¡APELLIDO PATERNO o MATERNO debe de existir!";
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
                        f_nacimiento:{
                            required: true,
                            date    :true
                        },
                        nombre:{
                            required : true,
                            maxlength: 50
                        },
                        ap_paterno:{
                            maxlength: 50
                        },
                        ap_materno:{
                            maxlength: 50
                        },
                        ap_esposo:{
                            maxlength: 50
                        },
                        estado_civil:{
                            required : true
                        },
                        municipio_id_nacimiento:{
                            required : true
                        },
                        domicilio:{
                            required : true,
                            maxlength: 500
                        },
                        telefono:{
                            maxlength: 50,
                            digits   : true
                        },
                        celular:{
                            required : true,
                            maxlength: 50,
                            digits   : true
                        },
                        municipio_id_residencia:{
                            required : true
                        }
                    },
                    // errorElement: 'p'
                });
                break;
            // === BOTON SUBIR FOTOGRAFIA ===
            case 17:
                if($("#image_user_p").is(":hidden")){
                    $("#dropzoneForm_1").hide();
                    $("#image_user_p").show();
                }
                else{
                    $("#image_user_p").hide();
                    $("#dropzoneForm_1").show();
                }
                break;
            // === DROPZONE 1 ===
            case 18:
                $("#dropzoneForm_1").dropzone({
                    url: url_controller + "/send_ajax",
                    method:'post',
                    addRemoveLinks: true,
                    maxFilesize: 5, // MB
                    dictResponseError: "Ha ocurrido un error en el server.",
                    acceptedFiles:'image/*',
                    paramName: "file", // The name that will be used to transfer the file
                    maxFiles:1,
                    clickable:true,
                    parallelUploads:1,
                    params: {
                        tipo      : 2,
                        _token    : csrf_token
                    },
                    // forceFallback:true,
                    createImageThumbnails: true,
                    maxThumbnailFilesize: 1,
                    autoProcessQueue:true,

                    dictRemoveFile:'Eliminar',
                    dictCancelUpload:'Cancelar',
                    dictCancelUploadConfirmation:'¿Confirme la cancelación?',
                    dictDefaultMessage: "<strong>Arrastra la imagen aquí o haz clic para subir.</strong>",
                    dictFallbackMessage:'Su navegador no soporta arrastrar y soltar la carga de archivos.',
                    dictFallbackText:'Utilice el formulario de reserva de abajo para subir tus archivos, como en los viejos tiempos.',
                    dictInvalidFileType:'El archivo no coincide con los tipos de archivo permitidos.',
                    dictFileTooBig:'El archivo es demasiado grande.',
                    dictMaxFilesExceeded:'Número máximo de archivos superado.',
                    init: function(){
                    },
                    success: function(file, response){
                        var data = $.parseJSON(response);
                        if(data.sw === 1){
                            var valor1 = new Array();
                            valor1[0]  = 100;
                            valor1[1]  = data.titulo;
                            valor1[2]  = data.respuesta;
                            utilitarios(valor1);

                            $('#image_user').removeAttr('scr');
                            $('#image_user').attr('src', public_url + '/' + data.nombre_archivo + '?' + Math.random());

                            $('#img_imagen').removeAttr('scr');
                            $('#img_imagen').attr('src', public_url + '/' + data.nombre_archivo + '?' + Math.random());

                            $("#dropzoneForm_1").hide();
                            $("#image_user_p").show();
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
            // === VALIDACION ===
            case 19:
                $(form_2).validate({
                    rules: {
                        a_contrasenia:{
                            required : true,
                            minlength: 6,
                            maxlength: 16
                        },
                        contrasenia:{
                            required : true,
                            minlength: 6,
                            maxlength: 16
                        },
                        c_contrasenia:{
                            equalTo: "#contrasenia"
                        }
                    },
                    // errorElement: 'p'
                });
                break;
            // === GUARDAR CONTRASEÑA USUARIO ===
            case 20:
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

            @if($sw_asistencia)
                // === REPORTE SALIDA ===
                case 21:
                    var concatenar_valores = '';
                    concatenar_valores     += '?tipo=1&salida_id=' + valor[1];

                    var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                    win.focus();
                    break;
                // === DONDE ASISTIO ===
                case 22:
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
                case 23:
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

                // === MODAL ABRIR MARCACIONES ===
                case 24:
                    $(jqgrid2).jqGrid('setGridParam',{
                        url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=2&persona_id=' + valor[3] + '&f_marcacion=' + valor[2],
                        datatype: 'json'
                    }).trigger('reloadGrid');

                    $(jqgrid2).jqGrid('setCaption', "<span class='text-success'>Mis marcaciones</span>");

                    $('#modal_5').modal();

                    setTimeout(function(){
                        $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
                    }, 300);
                    break;
                // === JQGRID 2 ===
                case 25:
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
            @endif

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
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === CAMBIAR CONTRASEÑA ===
                            case '3':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $("#a_contrasenia, #contrasenia, #c_contrasenia").val();
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
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;

                            @if($sw_asistencia)
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
                            @endif
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
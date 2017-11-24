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
        var title_table   = "NOMBRE TABLA";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",
            "ESTADO",
            "C.I.",
            "NOMBRE(S)",
            "AP. PATERNO",
            "AP. MATERNO",
            "AP. ESPOSO",
            "SEXO",
            "FECHA NACIMIENTO",
            "ESTADO CIVIL",
            "DOMICILIO",
            "TELEFONO",
            "CELULAR",

            "MUNICIPIO",
            "PROVINCIA",
            "DEPARTAMENTO",

            "MUNICIPIO",
            "PROVINCIA",
            "DEPARTAMENTO",

            ""
        );
        var col_m_name_1  = new Array(
            "act",
            "estado",
            "n_documento",
            "nombre",
            "ap_paterno",
            "ap_materno",
            "ap_esposo",
            "sexo",
            "f_nacimiento",
            "estado_civil",
            "domicilio",
            "telefono",
            "celular",

            "municipio_nacimiento",
            "provincia_nacimiento",
            "departamento_nacimiento",

            "municipio_residencia",
            "provincia_residencia",
            "departamento_residencia",

            "val_json"
        );
        var col_m_index_1 = new Array(
            "",
            "rrhh_personas.estado",
            "rrhh_personas.n_documento",
            "rrhh_personas.nombre",
            "rrhh_personas.ap_paterno",
            "rrhh_personas.ap_materno",
            "rrhh_personas.ap_esposo",
            "rrhh_personas.sexo",
            "rrhh_personas.f_nacimiento::text",
            "rrhh_personas.estado_civil",
            "rrhh_personas.domicilio",
            "rrhh_personas.telefono",
            "rrhh_personas.celular",

            "a2.nombre",
            "a3.nombre",
            "a4.nombre",

            "a5.nombre",
            "a6.nombre",
            "a7.nombre",

            ""
        );
        var col_m_width_1 = new Array(
            33,
            100,
            100,
            100,
            100,
            100,
            100,
            100,
            140,
            100,
            400,
            100,
            100,

            150,
            150,
            150,

            150,
            150,
            150,

            10
        );
        var col_m_align_1 = new Array(
            "center",
            "center",
            "right",
            "left",
            "left",
            "left",
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

            "center"
        );

    // === FORMULARIO 1 ===
        var form_1 = "#form_1";

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

        // === INFORMACIÓN PERSONAL ===
            var valor1 = new Array();
            valor1[0]  = 1;
            utilitarios(valor1);

        // === DROPZONE ===
            var valor1 = new Array();
            valor1[0]  = 18;
            utilitarios(valor1);

        // === JQGRID 1 ===
            // var valor1 = new Array();
            // valor1[0]  = 10;
            // utilitarios(valor1);

        // === VALIDATE 1 ===
            var valor1 = new Array();
            valor1[0]  = 16;
            utilitarios(valor1);

        // Add responsive to jqGrid
            // $(window).bind('resize', function () {
            // var width = $('.jqGrid_wrapper').width();
            //     $(jqgrid1).setGridWidth(width);
            // });

            // setTimeout(function(){
            //     $('.wrapper-content').removeClass('animated fadeInRight');
            //     var valor1 = new Array();
            //     valor1[0]  = 0;
            //     utilitarios(valor1);
            // },0);

            // $( "#navbar-minimalize-button" ).on( "click", function() {
            //     setTimeout(function(){
            //         $('.wrapper-content').removeClass('animated fadeInRight');
            //         var valor1 = new Array();
            //         valor1[0]  = 0;
            //         utilitarios(valor1);
            //     },500);
            // });
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

                if(usuario_json.imagen != null){
                    $('#image_user').removeAttr('scr');
                    $('#image_user').attr('src', public_url + '/' + usuario_json.imagen + '?' + Math.random());
                }
                break;

            // === JQGRID 1 ===
            case 10:
                var edit1 = true;
                @if(in_array(['codigo' => '0503'], $permisos))
                    edit1 = false;
                @endif
                $(jqgrid1).jqGrid({
                    caption      : title_table,
                    url          : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
                    datatype     : 'json',
                    mtype        : 'post',
                    height       : 'auto',
                    pager        : pjqgrid1,
                    rowNum       : 10,
                    rowList      : [10, 20, 30],
                    sortname     : 'rrhh_personas.id',
                    sortorder    : "desc",
                    viewrecords  : true,
                    shrinkToFit  : false,
                    hidegrid     : false,
                    multiboxonly : true,
                    altRows      : true,
                    rownumbers   : true,
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
                        col_name_1[19]
                    ],
                    colModel : [
                        {
                            name    : col_m_name_1[0],
                            index   : col_m_index_1[0],
                            width   : col_m_width_1[0],
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
                            name  : col_m_name_1[2],
                            index : col_m_index_1[2],
                            width : col_m_width_1[2],
                            align : col_m_align_1[2]
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
                            name  : col_m_name_1[5],
                            index : col_m_index_1[5],
                            width : col_m_width_1[5],
                            align : col_m_align_1[5]
                        },
                        {
                            name  : col_m_name_1[6],
                            index : col_m_index_1[6],
                            width : col_m_width_1[6],
                            align : col_m_align_1[6]
                        },
                        {
                            name       : col_m_name_1[7],
                            index      : col_m_index_1[7],
                            width      : col_m_width_1[7],
                            align      : col_m_align_1[7],
                            stype      :'select',
                            editoptions: {value:sexo_jqgrid}
                        },
                        {
                            name  : col_m_name_1[8],
                            index : col_m_index_1[8],
                            width : col_m_width_1[8],
                            align : col_m_align_1[8]
                        },
                        {
                            name       : col_m_name_1[9],
                            index      : col_m_index_1[9],
                            width      : col_m_width_1[9],
                            align      : col_m_align_1[9],
                            stype      :'select',
                            editoptions: {value:estado_civil_jqgrid}
                        },
                        {
                            name  : col_m_name_1[10],
                            index : col_m_index_1[10],
                            width : col_m_width_1[10],
                            align : col_m_align_1[10]
                        },
                        {
                            name  : col_m_name_1[11],
                            index : col_m_index_1[11],
                            width : col_m_width_1[11],
                            align : col_m_align_1[11]
                        },
                        {
                            name  : col_m_name_1[12],
                            index : col_m_index_1[12],
                            width : col_m_width_1[12],
                            align : col_m_align_1[12]
                        },


                        {
                            name  : col_m_name_1[13],
                            index : col_m_index_1[13],
                            width : col_m_width_1[13],
                            align : col_m_align_1[13]
                        },
                        {
                            name  : col_m_name_1[14],
                            index : col_m_index_1[14],
                            width : col_m_width_1[14],
                            align : col_m_align_1[14]
                        },
                        {
                            name       : col_m_name_1[15],
                            index      : col_m_index_1[15],
                            width      : col_m_width_1[15],
                            align      : col_m_align_1[15],
                            stype      :'select',
                            editoptions: {value:departamento_jqgrid}
                        },

                        {
                            name  : col_m_name_1[16],
                            index : col_m_index_1[16],
                            width : col_m_width_1[16],
                            align : col_m_align_1[16]
                        },
                        {
                            name  : col_m_name_1[17],
                            index : col_m_index_1[17],
                            width : col_m_width_1[17],
                            align : col_m_align_1[17]
                        },
                        {
                            name       : col_m_name_1[18],
                            index      : col_m_index_1[18],
                            width      : col_m_width_1[18],
                            align      : col_m_align_1[18],
                            stype      :'select',
                            editoptions: {value:departamento_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_1[19],
                                index : col_m_index_1[19],
                                width : col_m_width_1[19],
                                align : col_m_align_1[19],
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
                            var cl = ids[i];
                            ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : ed
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'municipio_nacimiento',
                            numberOfColumns: 3,
                            titleText      : 'LUGAR DE NACIMIENTO'
                        },
                        {
                            startColumnName: 'municipio_residencia',
                            numberOfColumns: 3,
                            titleText      : 'RESIDENCIA ACTUAL'
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
                @if(in_array(['codigo' => '0502'], $permisos))
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
                @if(in_array(['codigo' => '0503'], $permisos))
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
                // .navSeparatorAdd(pjqgrid1,{
                //   sepclass : "ui-separator"
                // })
                // .navButtonAdd(pjqgrid1,{
                //   "id"          : "print1",
                //   caption       : "",
                //   title         : 'Reportes',
                //   buttonicon    : "ui-icon ui-icon-print",
                //   onClickButton : function(){
                //       var valor1 = new Array();
                //       valor1[0]  = 13;
                //       utilitarios(valor1);
                //   }
                // })
                ;
                break;
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
                        _token    : csrf_token,
                        usuario_id: usuario_json.id
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
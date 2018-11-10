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
        var url_controller = "{!! url('/usuario') !!}";
        var csrf_token     = "{!! csrf_token() !!}";
        var public_url     = "{!! asset($public_url) !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",
            "IMAGEN",
            "ESTADO",
            "C.I.",
            "NOMBRE(S)",
            "AP. PATERNO",
            "AP. MATERNO",
            "AP. ESPOSO",
            "CORREO ELECTRONICO",
            "ROL",
            "LUGAR DE DEPENDENCIA",

            "",

            "¿i4?",

            "GRUPO"
        );
        var col_m_name_1  = new Array(
            "act",
            "imagen",
            "estado",
            "n_documento",
            "nombre",
            "ap_paterno",
            "ap_materno",
            "ap_esposo",
            "email",
            "rol",
            "lugar_dependencia",

            "val_json",

            "i4_funcionario_id_estado",
            "grupo"
        );
        var col_m_index_1 = new Array(
            "",
            "users.imagen",
            "users.estado",
            "a2.n_documento",
            "a2.nombre",
            "a2.ap_paterno",
            "a2.ap_materno",
            "a2.ap_esposo",
            "users.email",
            "a3.nombre",
            "users.lugar_dependencia",

            "",

            "users.i4_funcionario_id_estado",
            "a4.nombre"
        );
        var col_m_width_1 = new Array(
            33,
            150,
            100,
            90,
            100,
            100,
            100,
            100,
            250,
            250,
            350,

            10,

            90,
            200
        );
        var col_m_align_1 = new Array(
            "center",
            "center",
            "center",
            "right",
            "left",
            "left",
            "left",
            "left",
            "center",
            "center",
            "left",

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

    // === SI - NO ===
        var no_si_json   = $.parseJSON('{!! json_encode($no_si_array) !!}');
        var no_si_select = '';
        var no_si_jqgrid = ':Todos';

        $.each(no_si_json, function(index, value) {
            no_si_select += '<option value="' + index + '">' + value + '</option>';
            no_si_jqgrid += ';' + index + ':' + value;
        });

    // === ROL ===
        var rol_json   = $.parseJSON('{!! json_encode($rol_array) !!}');
        var rol_select = '';
        var rol_jqgrid = ':Todos';

        $.each(rol_json, function(index, value) {
            rol_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            rol_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });
    // === GRUPO ===
        var grupo_json   = $.parseJSON('{!! json_encode($grupo_array) !!}');
        var grupo_select = '';
        var grupo_jqgrid = ':Todos';

        $.each(grupo_json, function(index, value) {
            grupo_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            grupo_jqgrid += ';' + value.nombre + ':' + value.nombre;
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
            $('#persona_id').select2({
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
                            tipo      : 101,
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

            $('#i4_funcionario_id').select2({
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
                            tipo      : 110,
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
            $("#i4_funcionario_id").appendTo("#i4_funcionario_id_div");

            $('#rol_id').append(rol_select);
            $("#rol_id").select2({
                maximumSelectionLength: 1
            });
            $("#rol_id").appendTo("#rol_id_div");

            $('#lugar_dependencia').append(lugar_dependencia_select);
            $("#lugar_dependencia").select2();
            $("#lugar_dependencia").appendTo("#lugar_dependencia_div");

            $('#grupo_id').append(grupo_select);
            $("#grupo_id").select2({
                maximumSelectionLength: 1
            });
            $("#grupo_id").appendTo("#grupo_id_div");

        // === DROPZONE ===
            var valor1 = new Array();
            valor1[0]  = 17;
            utilitarios(valor1);

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

            $( "#navbar-minimalize-button" ).on( "click", function() {
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
                var edit1 = true;
                @if(in_array(['codigo' => '0103'], $permisos))
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
                    sortname     : 'users.id',
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
                        col_name_1[12],
                        col_name_1[3],
                        col_name_1[4],
                        col_name_1[5],
                        col_name_1[6],
                        col_name_1[7],
                        col_name_1[8],
                        col_name_1[9],
                        col_name_1[13],
                        col_name_1[10],
                        col_name_1[11]
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
                            name  : col_m_name_1[1],
                            index : col_m_index_1[1],
                            width : col_m_width_1[1],
                            align : col_m_align_1[1],
                            search: false
                        },
                        {
                            name       : col_m_name_1[2],
                            index      : col_m_index_1[2],
                            width      : col_m_width_1[2],
                            align      : col_m_align_1[2],
                            stype      :'select',
                            editoptions: {value:estado_jqgrid}
                        },
                        {
                            name       : col_m_name_1[12],
                            index      : col_m_index_1[12],
                            width      : col_m_width_1[12],
                            align      : col_m_align_1[12],
                            stype      :'select',
                            editoptions: {value:no_si_jqgrid}
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
                            name  : col_m_name_1[7],
                            index : col_m_index_1[7],
                            width : col_m_width_1[7],
                            align : col_m_align_1[7],
                            hidden: true
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
                            editoptions: {value:rol_jqgrid}
                        },
                        {
                            name       : col_m_name_1[13],
                            index      : col_m_index_1[13],
                            width      : col_m_width_1[13],
                            align      : col_m_align_1[13],
                            stype      :'select',
                            editoptions: {value:grupo_jqgrid}
                        },
                        {
                            name  : col_m_name_1[10],
                            index : col_m_index_1[10],
                            width : col_m_width_1[10],
                            align : col_m_align_1[10]
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
                @if(in_array(['codigo' => '0102'], $permisos))
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
                @if(in_array(['codigo' => '0103'], $permisos))
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
                var valor1 = new Array();
                valor1[0]  = 14;
                utilitarios(valor1);

                $('#modal_1_title').empty();
                $('#modal_1_title').append('Modificar usuario');
                $("#usuario_id").val(valor[1]);

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                if(val_json.imagen != null){
                    $('#image_user').removeAttr('scr');
                    $('#image_user').attr('src', public_url + '/' + val_json.imagen + '?' + Math.random());
                }

                $(".estado_class[value=" + val_json.estado + "]").prop('checked', true);

                if(ret.n_documento != ""){
                    var persona = ret.n_documento + ' - ' + $.trim(ret.ap_paterno + ' ' +  ret.ap_materno) + ' ' + ret.nombre;

                    $('#persona_id').append('<option value="' + val_json.persona_id + '">' + persona + '</option>');
                    $("#persona_id").select2("val", val_json.persona_id);
                }

                if(val_json.i4_funcionario_id_estado == 2){
                    var valor1 = new Array();
                    valor1[0]  = 150;
                    valor1[1]  = url_controller + '/send_ajax';
                    valor1[2]  = 'POST';
                    valor1[3]  = true;
                    valor1[4]  = 'tipo=111&_token=' + csrf_token + '&i4_funcionario_id=' + val_json.i4_funcionario_id;
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                }

                $("#email").val(ret.email);
                $("#rol_id").select2("val", val_json.rol_id);

                $("#grupo_id").select2("val", val_json.grupo_id);

                if(ret.lugar_dependencia != ""){
                    var valor1 = new Array();
                    valor1[0]  = 150;
                    valor1[1]  = url_controller + '/send_ajax';
                    valor1[2]  = 'POST';
                    valor1[3]  = true;
                    valor1[4]  = 'tipo=102&_token=' + csrf_token + '&usuario_id=' + valor[1];
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                }
                $('#modal_1').modal();
                break;
            // === REPORTES MODAL ===
            case 13:
                alert("REPORTE");
                break;
            // === RESETEAR FORMULARIO ===
            case 14:
                $('#modal_1_title').empty();
                $('#modal_1_title').append('Agregar nuevo usuario');

                $('#image_user').removeAttr('scr');
                $('#image_user').attr('src', "{!! asset('image/logo/user_default_1.png') !!}" + '?' + Math.random());

                $("#usuario_id").val('');

                $('#persona_id').select2("val", "");
                $('#i4_funcionario_id').select2("val", "");
                $('#rol_id').select2("val", "");
                $('#grupo_id').select2("val", "");
                $('#lugar_dependencia').select2("val", "");
                $('#persona_id option').remove();
                $('#i4_funcionario_id option').remove();
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
                        email:{
                            required : true,
                            email: true
                        },
                        password:{
                            minlength: 6,
                            maxlength: 16
                        },
                        password_c:{
                            equalTo: "#password"
                        },
                        rol_id:{
                            required: true
                        },
                        grupo_id:{
                            required: true
                        },
                        "lugar_dependencia[]":{
                            required: true
                        }
                    }
                });
                break;
            // === DROPZONE 1 ===
            case 17:
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
                    dictDefaultMessage: "<strong>Arrastra la imagen aquí o haz clic para subir.</strong>",
                    dictFallbackMessage:'Su navegador no soporta arrastrar y soltar la carga de archivos.',
                    dictFallbackText:'Utilice el formulario de reserva de abajo para subir tus archivos, como en los viejos tiempos.',
                    dictInvalidFileType:'El archivo no coincide con los tipos de archivo permitidos.',
                    dictFileTooBig:'El archivo es demasiado grande.',
                    dictMaxFilesExceeded:'Número máximo de archivos superado.',
                    init: function(){
                        this.on("sending", function(file, xhr, formData){
                            formData.append("usuario_id", $("#usuario_id").val());
                            formData.append("estado", $(".estado_class:checked").val());
                            formData.append("persona_id", $("#persona_id").val());
                            formData.append("email", $("#email").val());
                            formData.append("password", $("#password").val());
                            formData.append("rol_id", $("#rol_id").val());
                            formData.append("lugar_dependencia", $("#lugar_dependencia").val());
                            formData.append("enviar_mail", $("#enviar_mail:checked").val());
                        });
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
                            // === RELLENAR LUGAR DE DEPENDENCIA ===
                            case '102':
                                if(data.sw === 2){
                                    var ld_array = new Array();
                                    var i        = 0;
                                    $.each(data.consulta, function(index, value){
                                        ld_array[i] = value.lugar_dependencia_id;
                                        i++;
                                    });
                                    $("#lugar_dependencia").select2("val", ld_array);
                                }
                                break;
                            // === RELLENAR FUNCIONARIO DEL I4 ===
                            case '111':
                                if(data.sw === 2){
                                    $('#i4_funcionario_id').append('<option value="' + data.consulta.id + '">' + data.consulta.text + '</option>');
                                    $("#i4_funcionario_id").select2("val", data.consulta.id);
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
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
        var rol_id            = "{!! $rol_id !!}";
        var base_url          = "{!! url('') !!}";
        var url_controller    = "{!! url('/plataforma') !!}";
        var csrf_token        = "{!! csrf_token() !!}";

    // === FORMULARIOS ===
        var form_1 = "#form_1";

    // === JQGRID ===
        var jqgrid1  = "#jqgrid1";
        var pjqgrid1 = "#pjqgrid1";

    // === ESTADO ===
        var estado_json   = $.parseJSON('{!! json_encode($estado_array) !!}');
        var estado_select = '';
        var estado_jqgrid = ':Todos';

        $.each(estado_json, function(index, value) {
            estado_select += '<option value="' + index + '">' + value + '</option>';
            estado_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO DE ACTIVIDAD ===
        var tipo_actividad_json   = $.parseJSON('{!! json_encode($tipo_actividad_array) !!}');
        var tipo_actividad_select = '';
        var tipo_actividad_jqgrid = ':Todos';

        $.each(tipo_actividad_json, function(index, value) {
            tipo_actividad_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            tipo_actividad_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    // === DROPZONE ===
        Dropzone.autoDiscover = false;

    $(document).ready(function(){
        //=== INICIALIZAR ===
            var valor1 = new Array();
            valor1[0]  = 30;
            utilitarios(valor1);

            $('#tipo_actividad_id_1').append(tipo_actividad_select);

        //=== SELECT2 ===
            $("#tipo_actividad_id_1").select2({
                maximumSelectionLength: 1
            });
            $("#tipo_actividad_id_1").appendTo("#tipo_actividad_id_1_div");

            // $('#Muni_id').select2({
            //     maximumSelectionLength: 1,
            //     minimumInputLength    : 2,
            //     ajax                  : {
            //         url     : url_controller + '/send_ajax',
            //         type    : 'post',
            //         dataType: 'json',
            //         data    : function (params) {
            //             return {
            //                 q         : params.term,
            //                 page_limit: 20,
            //                 estado    : 1,
            //                 tipo      : 101,
            //                 _token    : csrf_token
            //             };
            //         },
            //         results: function (data, page) {
            //             return {
            //                 results: data
            //             };
            //         }
            //     }
            // });
            // $("#Muni_id").appendTo("#Muni_id_div");

        // === DROPZONE ===
            var valor1 = new Array();
            valor1[0]  = 80;
            utilitarios(valor1);

        //=== TOUCHSPIN ===
            // $("#dp_etapa_gestacion_semana").TouchSpin({
            //     buttondown_class: 'btn btn-white',
            //     buttonup_class: 'btn btn-white'
            // });

        //=== FLIPSWITCH ===
            // $("#dp_etapa_gestacion_estado").change(function(){
            //     if(this.checked){
            //         $("#dp_etapa_gestacion_semana").prop('disabled', false);
            //         $("#div_dp_etapa_gestacion_semana").slideDown("slow");
            //     }
            //     else{
            //         $("#dp_etapa_gestacion_semana").prop('disabled', true);
            //         $("#div_dp_etapa_gestacion_semana").slideUp("slow");
            //     }
            // });

        // === JQGRID ===
            // var valor1 = new Array();
            // valor1[0]  = 40;
            // utilitarios(valor1);

        // === VALIDATE 1 ===
            // var valor1 = new Array();
            // valor1[0]  = 60;
            // utilitarios(valor1);

        // Add responsive to jqGrid
            // $(window).bind('resize', function () {
            //     var width = $('.tab-content').width() - 35;
            //     $(jqgrid1).setGridWidth(width);
            // });

            // setTimeout(function(){
            //     $('.wrapper-content').removeClass('animated fadeInRight');
            //     var valor1 = new Array();
            //     valor1[0]  = 0;
            //     utilitarios(valor1);
            // },300);

            // $("#navbar-minimalize-button" ).on( "click", function() {
            //     setTimeout(function(){
            //         $('.wrapper-content').removeClass('animated fadeInRight');
            //         var valor1 = new Array();
            //         valor1[0]  = 0;
            //         utilitarios(valor1);
            //     },500);
            // });
    });

    $(window).on('resize.jqGrid', function() {
        // var valor1 = new Array();
        // valor1[0]  = 0;
        // utilitarios(valor1);
    });

    function utilitarios(valor){
        switch(valor[0]){
            // === JQGRID REDIMENCIONAR ===
            case 0:
                $(jqgrid1).jqGrid('setGridWidth', $(".jqGrid_wrapper").width());
                break;
            // === ABRIR MODAL ===
            case 10:
                if($("#caso_id_1").val() != ''){
                    $('#modal_1_title').empty();
                    $('#modal_1_title').append('REGISTRAR ACTIVIDAD');

                    $('#modal_1').modal();
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                    valor1[2]  = 'No existe CASO para añadir actividad.';
                    utilitarios(valor1);
                }
                break;
            // === EDICION MODAL ===
            case 20:
                var valor1 = new Array();
                valor1[0]  = 30;
                utilitarios(valor1);

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $('#modal_1_title').empty();
                $('#modal_1_title').append('MODIFICAR RECINTO CARCELARIO');

                $("#recinto_carcelario_id").val(valor[1]);

                $(".estado_class[value=" + val_json.estado + "]").prop('checked', true);
                $("#nombre").val(ret.nombre);
                if(val_json.ret != ""){
                    $('#Muni_id').append('<option value="' + val_json.Muni_id + '">' + ret.departamento + ', ' + ret.municipio + '</option>');
                    $("#Muni_id").select2("val", val_json.Muni_id);
                }
                if(ret.tipo_recinto != ""){
                    $("#tipo_recinto").select2("val", val_json.tipo_recinto);
                }

                $('#modal_1').modal();
                break;
            // === BORRAR INFORMACION ===
            case 30:
                $('#caso_b, #etapa_caso_b, #origen_caso_b, #estado_caso_b, #f_denuncia_b, #fiscal_asignado_b, #delito_principal_b, #modal_1_title, #actividad_tabla_b').empty();
                break;
            // === RESETEAR FORMULARIO 1 ===
            case 31:
                $("#caso_id_1").val('');

                $('#tipo_actividad_id_1').select2("val", "");
                $("#actvidad_1").val('');

                $(form_1)[0].reset();
                break;
            // === RESETEAR FORMULARIO 1 ===
            case 32:
                $('#tipo_actividad_id_1').select2("val", "");
                $("#actvidad_1").val('');
                break;
            // === JQGRID 1 ===
            case 40:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '2103'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid1).jqGrid({
                    // caption     : title_table,
                    url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
                    datatype    : 'json',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid1,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'RecintosCarcelarios.created_at',
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
                        "",

                        "ESTADO",
                        "TIPO DE RECINTO",
                        "NOMBRE",
                        "MUNICIPIO",
                        "DEPARTAMENTO",

                        ""
                    ],
                    colModel : [
                        {
                            name    : "act",
                            index   : "",
                            width   : ancho1,
                            align   : "center",
                            fixed   : true,
                            sortable: false,
                            resize  : false,
                            search  : false,
                            hidden  : edit1
                        },

                        {
                            name       : "estado",
                            index      : "RecintosCarcelarios.estado",
                            width      : 120,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:estado_jqgrid}
                        },
                        {
                            name       : "tipo_recinto",
                            index      : "RecintosCarcelarios.tipo_recinto",
                            width      : 165,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:tipo_recinto_jqgrid}
                        },

                        {
                            name : "nombre",
                            index: "RecintosCarcelarios.nombre",
                            width: 400,
                            align: "left"
                        },
                        {
                            name       : "municipio",
                            index      : "a2.Muni",
                            width      : 400,
                            align      : "left"
                        },
                        {
                            name       : "departamento",
                            index      : "a3.Dep",
                            width      : 150,
                            align      : "left",
                            stype      :'select',
                            editoptions: {value:departamento_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : "val_json",
                                index : "",
                                width : 10,
                                align : "center",
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

                            var ed = "";
                            @if(in_array(['codigo' => '2103'], $permisos))
                                ed = "<button type='button' class='btn btn-xs btn-success' title='Modificar recinto carcelario' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed)
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
                @if(in_array(['codigo' => '2102'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "add1",
                        caption       : "",
                        title         : 'Agregar nueva fila',
                        buttonicon    : "ui-icon ui-icon-plusthick",
                        onClickButton : function(){
                            var valor1 = new Array();
                            valor1[0]  = 30;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 10;
                            utilitarios(valor1);
                        }
                    })
                @endif
                @if(in_array(['codigo' => '2104'], $permisos))
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
                            valor1[0]  = 70;
                            utilitarios(valor1);
                        }
                    })
                @endif
                ;
                break;
            // === BUSQUEDA CASO ===
            case 50:
                var valor1 = new Array();
                valor1[0]  = 30;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 31;
                utilitarios(valor1);

                var concatenar_valores = 'tipo=100&_token=' + csrf_token;

                var caso = $("#caso").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(caso) != ''){
                    concatenar_valores += '&caso=' + caso;
                }
                else{
                    valor_sw    = false;
                    valor_error += 'El campo CASO está vacio.';
                }

                if(valor_sw){
                    swal({
                        title             : "BUSCANDO CASO",
                        text              : "Espere a que busque el caso.",
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
            // === PROCESO DE VERIFICACION ===
            case 51:
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
            case 60:
                $(form_1).validate({
                    rules: {
                        Muni_id:{
                            required : true
                        },
                        tipo_recinto:{
                            required : true
                        },
                        nombre:{
                            required : true,
                            maxlength: 500
                        }
                    }
                });
                break;
            // === REPORTE EXCEL ===
            case 70:
                var concatenar_valores = '?tipo=10';
                var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                win.focus();
                break;
            // === DROPZONE 1 ===
            case 80:
                $("#dropzoneForm_1").dropzone({
                    url              : url_controller + "/send_ajax",
                    method           : 'post',
                    addRemoveLinks   : true,
                    maxFilesize      : 5, // MB
                    dictResponseError: "Ha ocurrido un error en el server.",
                    acceptedFiles    : 'application/pdf',
                    paramName        : "file", // The name that will be used to transfer the file
                    maxFiles         : 1,
                    clickable        : true,
                    parallelUploads  : 1,
                    params: {
                        tipo  : 1,
                        _token: csrf_token
                    },
                    // forceFallback:true,
                    createImageThumbnails: true,
                    maxThumbnailFilesize : 1,
                    autoProcessQueue     : true,

                    dictRemoveFile              : 'Eliminar',
                    dictCancelUpload            : 'Cancelar',
                    dictCancelUploadConfirmation: '¿Confirme la cancelación?',
                    dictDefaultMessage          : "<strong>Arrastra el documento PDF aquí o haz clic para subir.</strong>",
                    dictFallbackMessage         : 'Su navegador no soporta arrastrar y soltar la carga de archivos.',
                    dictFallbackText            : 'Utilice el formulario de reserva de abajo para subir tus archivos, como en los viejos tiempos.',
                    dictInvalidFileType         : 'El archivo no coincide con los tipos de archivo permitidos.',
                    dictFileTooBig              : 'El archivo es demasiado grande.',
                    dictMaxFilesExceeded        : 'Número máximo de archivos superado.',
                    init: function(){
                        this.on("sending", function(file, xhr, formData){
                            formData.append("caso_id", $("#caso_id_1").val());
                            formData.append("tipo_actividad_id", $("#tipo_actividad_id_1").val());
                            formData.append("actvidad", $("#actvidad_1").val());
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

                            var valor1 = new Array();
                            valor1[0]  = 32;
                            utilitarios(valor1);

                            $('#actividad_tabla_b').empty();

                            if(data.sw_1 === 1){
                                var actividad_tabla = ""

                                var valor1 = new Array();
                                valor1[0]  = 90;
                                valor1[1]  = data.cosulta3;
                                actividad_tabla = utilitarios(valor1);

                                $('#actividad_tabla_b').append(actividad_tabla);
                            }

                            $('#modal_1').modal('hide');
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
            // === LLENAR TABLA 1 ===
            case 90:
                var respuesta = "";
                var c = 1;
                $.each(valor[1], function(index, value) {
                    respuesta += '<tr>';
                    respuesta += '<td class="text-right">' + c++ + '</td>';
                    respuesta += '<td class="text-center">' + value.Fecha + '</td>';
                    respuesta += '<td>' + value.TipoActividad + '</td>';

                    var actividad = "";
                    if(value.Actividad != null){
                        actividad = value.Actividad;
                    }
                    respuesta += '<td>' + actividad + '</td>';
                    respuesta += '</tr>';
                });
                return respuesta;
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
                var respuesta_ajax = false;
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
                                        valor1[0]  = 30;
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
                            // === BUSCANDO CASO ===
                            case '100':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $('#caso_b, #modal_2_title').append(data.cosulta1.Caso);
                                    $('#etapa_caso_b').append(data.cosulta1.etapa_caso);
                                    $('#origen_caso_b').append(data.cosulta1.origen_caso);
                                    $('#estado_caso_b').append(data.cosulta1.estado_caso);
                                    $('#f_denuncia_b').append(data.cosulta1.FechaDenuncia);
                                    $('#delito_principal_b').append(data.cosulta1.delito_principal);

                                    if(data.sw_1 === 1){
                                        $('#fiscal_asignado_b').append(data.cosulta2.funcionario);
                                    }

                                    if(data.sw_2 === 1){
                                        var actividad_tabla = ""

                                        var valor1 = new Array();
                                        valor1[0]  = 90;
                                        valor1[1]  = data.cosulta3;
                                        actividad_tabla = utilitarios(valor1);

                                        $('#actividad_tabla_b').append(actividad_tabla);
                                    }

                                    $("#caso_id_1").val(data.cosulta1.id)

                                    $("#caso").val('');
                                    $("#caso").focus();
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

                return respuesta_ajax;
                break;
            default:
                break;
        }
    }
</script>
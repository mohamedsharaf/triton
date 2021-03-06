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

    // === TIPO DE REPORTE ===
        var tipo_reporte_json   = $.parseJSON('{!! json_encode($tipo_reporte_array) !!}');
        var tipo_reporte_select = '';
        var tipo_reporte_jqgrid = ':Todos';

        $.each(tipo_reporte_json, function(index, value) {
            tipo_reporte_select += '<option value="' + index + '">' + value + '</option>';
            tipo_reporte_jqgrid += ';' + index + ':' + value;
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

            $('#tipo_reporte_2').append(tipo_reporte_select);

        //=== SELECT2 ===
            $("#tipo_actividad_id_1").select2({
                maximumSelectionLength: 1
            });
            $("#tipo_actividad_id_1").appendTo("#tipo_actividad_id_1_div");

            $("#tipo_reporte_2").select2({
                maximumSelectionLength: 1
            });
            $("#tipo_reporte_2").appendTo("#tipo_reporte_2_div");

            $('#division_id_2').select2({
                // maximumSelectionLength: 1,
                minimumInputLength    : 2,
                ajax                  : {
                    url     : url_controller + '/send_ajax',
                    type    : 'post',
                    dataType: 'json',
                    data    : function (params) {
                        return {
                            q         : params.term,
                            page_limit: 20,
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
            $("#division_id_2").appendTo("#division_id_2_div");

            $('#funcionario_id_2, #funcionario_id_21').select2({
                // maximumSelectionLength: 1,
                minimumInputLength    : 2,
                ajax                  : {
                    url     : url_controller + '/send_ajax',
                    type    : 'post',
                    dataType: 'json',
                    data    : function (params) {
                        return {
                            q         : params.term,
                            page_limit: 20,
                            estado    : 1,
                            tipo      : 102,
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
            $("#funcionario_id_2").appendTo("#funcionario_id_2_div");

        // === DROPZONE ===
            var valor1 = new Array();
            valor1[0]  = 80;
            utilitarios(valor1);

        //=== DATEPICKER 3 ===
            $('#fecha_del_2, #fecha_al_2').datepicker({
                // startView            : 2,
                // todayBtn          : "linked",
                // keyboardNavigation: false,
                // forceParse        : false,
                autoclose            : true,
                format               : "yyyy-mm-dd",
                startDate            : '-100y',
                endDate              : '+0d',
                language             : "es"
            });

        //=== CLOCKPICKER ===
            $('#hora_del_2, #hora_al_2').clockpicker({
                autoclose: true,
                placement: 'top',
                align    : 'left',
                donetext : 'Hecho'
            });
    });

    function utilitarios(valor){
        switch(valor[0]){
            // === ABRIR MODAL - REGISTRAR ACTIVIDAD ===
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
            // === ABRIR MODAL ===
            case 11:
                $('#modal_2').modal();
                break;
            // === BORRAR INFORMACION ===
            case 30:
                $('#caso_b, #etapa_caso_b, #origen_caso_b, #estado_caso_b, #f_denuncia_b, #fiscal_asignado_b, #delito_principal_b, #modal_1_title, #actividad_tabla_b, #denunciante_b, #denunciado_b').empty();
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
            // === REPORTE PDF - GENERAR RECIBIDO ===
            case 70:
                var concatenar_valores = '?tipo=1&id=' + valor[1];
                var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                win.focus();
                break;
            // === REPORTE PDF - REPORTES ===
            case 71:
                var concatenar_valores = '?tipo=2';

                var tipo_reporte     = $("#tipo_reporte_2").val();
                var division_id      = $("#division_id_2").val();
                var funcionario_id   = $("#funcionario_id_2").val();
                var funcionario_id_1 = $("#funcionario_id_21").val();
                var fecha_del        = $("#fecha_del_2").val();
                var hora_del         = $("#hora_del_2").val();
                var fecha_al         = $("#fecha_al_2").val();
                var hora_al          = $("#hora_al_2").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(tipo_reporte) != ''){
                    concatenar_valores += '&tipo_reporte=' + tipo_reporte;
                }
                else{
                    valor_sw    = false;
                    valor_error = 'El campo TIPO DE REPORTE es obligatorio';
                }

                if($.trim(division_id) != ''){
                    concatenar_valores += '&division_id=' + division_id;
                }

                if($.trim(funcionario_id) != ''){
                    concatenar_valores += '&funcionario_id=' + funcionario_id;
                }

                if($.trim(funcionario_id_1) != ''){
                    concatenar_valores += '&funcionario_id_1=' + funcionario_id_1;
                }

                if($.trim(fecha_del) != ''){
                    concatenar_valores += '&fecha_del=' + fecha_del;
                }
                else{
                    valor_sw    = false;
                    valor_error = 'El campo FECHA DEL es obligatorio';
                }

                if($.trim(hora_del) != ''){
                    concatenar_valores += '&hora_del=' + hora_del;
                }
                else{
                    valor_sw    = false;
                    valor_error = 'El campo HORA DEL es obligatorio';
                }

                if($.trim(fecha_al) != ''){
                    concatenar_valores += '&fecha_al=' + fecha_al;
                }
                else{
                    valor_sw    = false;
                    valor_error = 'El campo FECHA AL es obligatorio';
                }

                if($.trim(hora_al) != ''){
                    concatenar_valores += '&hora_al=' + hora_al;
                }
                else{
                    valor_sw    = false;
                    valor_error = 'El campo HORA AL es obligatorio';
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

                    @if(in_array(['codigo' => '2202'], $permisos) AND $i4_funcionario_id != '')
                        respuesta += '<td class="text-center">';

                        if(value.estado_triton == 1){
                            respuesta += '<button type="button" class="btn btn-xs btn-success" title="Generar recibido" onclick="utilitarios([70, ' + value.id + ']);">';
                            respuesta += '<i class="fa fa-print"></i>';
                            respuesta += '</button>';
                        }

                        respuesta += '</td>';
                    @endif

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

                                    $('#modal_2_title').empty();
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

                                    if(data.sw_3 === 1){
                                        $('#denunciante_b').append(data.cosulta4.denunciante);
                                    }

                                    if(data.sw_4 === 1){
                                        $('#denunciado_b').append(data.cosulta5.denunciado);
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
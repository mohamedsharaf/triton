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
    var url_controller    = "{!! url('/derivacion') !!}";
    var csrf_token        = "{!! csrf_token() !!}";

    // === FORMULARIOS ===
    var form_1 = "#form_1";
    var form_2 = "#form_2";

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

    // === TIPO REPORTE ===
    var tipo_reporte_json   = $.parseJSON('{!! json_encode($tipo_reporte_array) !!}');
    var tipo_reporte_select = '';

    $.each(tipo_reporte_json, function(index, value) {
        tipo_reporte_select += '<option value="' + index + '">' + value + '</option>';
    });

    $(document).ready(function(){
        //=== INICIALIZAR ===
        $('#tipo_reporte').append(tipo_reporte_select);
        $("#tipo_reporte").select2({
            maximumSelectionLength: 1
        });
        $("#tipo_reporte").appendTo("#tipo_reporte_div");

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

        $('#institucion_id').select2({
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
                        tipo      : 200,
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
        $("#institucion_id").appendTo("#institucion_id_div");

        $('#oficina_derivada').select2({
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
                        tipo      : 200,
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
        $("#oficina_derivada").appendTo("#oficina_derivada_div");

        $('#f_nacimiento').datepicker({
            startView            : 2,
            autoclose            : true,
            format               : "yyyy-mm-dd",
            startDate            : '-100y',
            endDate              : '+0d',
            language             : "es"
        });

        $('#fecha_del').datepicker({
            startView            : 2,
            autoclose            : true,
            format               : "yyyy-mm-dd",
            startDate            : '-100y',
            endDate              : '+0d',
            language             : "es"
        });

        $('#fecha_al').datepicker({
            startView            : 2,
            autoclose            : true,
            format               : "yyyy-mm-dd",
            startDate            : '-100y',
            endDate              : '+0d',
            language             : "es"
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

        $( "#navbar-minimalize-button" ).on( "click", function() {
            setTimeout(function(){
                $('.wrapper-content').removeClass('animated fadeInRight');
                var valor1 = new Array();
                valor1[0]  = 0;
                utilitarios(valor1);
            },500);
        });

        // === BUSCAR PERSONA CON CI ===
        $( "#btnBuscarCI" ).on( "click", function() {
            var n_documento = $("#n_documento").val();
            if (n_documento != '') {
                var valor1 = new Array();
                valor1[0]  = 150;
                valor1[1]  = url_controller + '/send_ajax';
                valor1[2]  = 'POST';
                valor1[3]  = true;
                valor1[4]  = 'q='+n_documento+'&tipo='+300+'&_token='+csrf_token;
                valor1[5]  = 'json';
                utilitarios(valor1);
            } else {
                var valor1 = new Array();
                valor1[0]  = 101;
                valor1[1]  = "ERROR";
                valor1[2]  = "El campo CEDULA DE IDENTIDAD está vacío.";
                utilitarios(valor1);
            }
        });

        // === ABRIR MODAL REPORTES ===
        $("#btnReportes").on( "click", function() {
            utilitarios([14]);
            $('#modal_2').modal();
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
                var ancho1     = 30;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '2401'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid1).jqGrid({
                    url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
                    datatype    : 'json',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid1,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'pvt_derivaciones.created_at',
                    sortorder   : "desc",
                    viewrecords : true,
                    shrinkToFit : false,
                    hidegrid    : false,
                    multiboxonly: true,
                    altRows     : true,
                    rownumbers  : true,
                    colNames : [
                        "",
                        "CÓDIGO",
                        "FECHA",
                        "NOMBRE",
                        "MOTIVO",
                        "OFICINA",
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
                            name : "codigo",
                            index: "pvt_derivaciones.codigo::text",
                            width: 100,
                            align: "center"
                        },
                        {
                            name : "fecha",
                            index: "pvt_derivaciones.fecha::text",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "nombre",
                            index: "CONCAT_WS(' ', p.ap_paterno, p.ap_materno, p.nombre)",
                            width: 300,
                            align: "left"
                        },
                        {
                            name : "motivo",
                            index: "pvt_derivaciones.motivo",
                            width: 300,
                            align: "left"
                        },
                        {
                            name : "oficina",
                            index: "i.nombre",
                            width: 200,
                            align: "left"
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
                            /* @if(in_array(['codigo' => '2401'], $permisos))
                                ed = "<button type='button' class='btn btn-xs btn-success' title='Modificar Derivación' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            @endif */
                            @if(in_array(['codigo' => '2401'], $permisos))
                                pd = " <button type='button' class='btn btn-xs btn-warning' title='Imprimir Derivación' onclick=\"utilitarios([13, " + cl + ", 1]);\"><i class='fa fa-print'></i></button>";
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed+pd)
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
                @if(in_array(['codigo' => '2601'], $permisos))
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
                            valor1[0]  = 14;
                            utilitarios(valor1);

                            var valor1 = new Array();
                            valor1[0]  = 11;
                            utilitarios(valor1);
                        }
                    })
                @endif
                @if(in_array(['codigo' => '2601'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
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
                /* var valor1 = new Array();
                valor1[0]  = 14;
                utilitarios(valor1);

                $('#modal_1_title').empty();
                $('#modal_1_title').append('Modificar institucion');

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $(".estado_class[value=" + val_json.estado + "]").prop('checked', true);
                if(ret.municipio != ""){
                    var dpm = ret.departamento + ', ' + ret.provincia + ', ' + ret.municipio;
                    $('#municipio_id').append('<option value="' + val_json.municipio_id + '">' + dpm + '</option>');
                    $("#municipio_id").select2("val", val_json.municipio_id);
                }
                $("#lugar_dependencia_id").select2("val", val_json.lugar_dependencia_id);
                $("#nombre").val(ret.nombre);
                $("#direccion").val(ret.direccion);
                $('#modal_1').modal();
                break; */
            // === REPORTES MODAL ===
            case 13:
                var concatenar_valores = '?tipo='+valor[2]+'&id=' + valor[1];
                var win = window.open(url_controller + '/reportes' + concatenar_valores ,  '_blank');
                win.focus();
                break;
            // === RESETEAR FORMULARIO ===
            case 14:
                $('#modal_1_title').empty();
                $('#modal_1_title').append('Registrar Derivación');
                $('#institucion_id').select2("val", "");
                $('#institucion_id option').remove();
                $('#municipio_id_nacimiento').select2("val", "");
                $('#municipio_id_nacimiento option').remove();
                $('#municipio_id_residencia').select2("val", "");
                $('#municipio_id_residencia option').remove();
                $('#estado_civil').select2("val", "");
                $("#persona_id").val('');
                $(form_1)[0].reset();
                $('#modal_2_title').empty();
                $('#modal_2_title').append('Reportes');
                $(form_2)[0].reset();
                break;
            // === GUARDAR REGISTRO ===
            case 15:
                if($(form_1).valid()){
                    var persona_id = $('#persona_id').val();
                    if (persona_id != '') {
                        $('#tipo1').val(2);
                    }
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
                        municipio_id_nacimiento:{
                            required: true
                        },
                        n_documento:{
                            required: true,
                            digits: true,
                            maxlength: 20
                        },
                        nombre:{
                            required: true,
                            maxlength: 50
                        },
                        ap_paterno:{
                            required: true,
                            maxlength: 50
                        },
                        f_nacimiento:{
                            required: true,
                            date: true
                        },
                        domicilio:{
                            maxlength:500
                        },
                        celular:{
                            required: true,
                            maxlength: 15
                        },
                        municipio_id_nacimiento:{
                            required: true
                        },
                        motivo:{
                            required: true
                        },
                        relato:{
                            required: true
                        },
                        institucion:{
                            required: true
                        }
                    }
                });
                break;
            // === FORMULARIO REPORTES ===
            case 17:
                if ($(form_2).valid()) {
                    var tipo_reporte_id = $('#tipo_reporte').val();
                    var oficina_derivada_id = $('#oficina_derivada').val();
                    var fecha_del = $('#fecha_del').val();
                    var fecha_al = $('#fecha_al').val();
                    var concatenar_valores = '?tipo=2&tipo_reporte_id='+tipo_reporte_id+'&oficina_derivada_id='+oficina_derivada_id+'&fecha_del='+fecha_del+'&fecha_al='+fecha_al;
                    var win = window.open(url_controller + '/reportes' + concatenar_valores ,  '_blank');
                    win.focus();
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
                                    var valor2 = new Array();
                                    valor2[0] = 13;
                                    valor2[1] = data.der_id;
                                    valor2[2] = 1;
                                    utilitarios(valor2);
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
                            case '2':
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
                                    var valor2 = new Array();
                                    valor2[0] = 13;
                                    valor2[1] = data.der_id;
                                    valor2[2] = 1;
                                    utilitarios(valor2);
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
                            case '300':
                                if(data.sw === 1) {
                                    $("#persona_id").val(data.results.id);
                                    $("#nombre").val(data.results.nombre);
                                    $("#ap_paterno").val(data.results.ap_paterno);
                                    $("#ap_materno").val(data.results.ap_materno);
                                    $("#ap_esposo").val(data.results.ap_esposo);
                                    $("#f_nacimiento").val(data.results.f_nacimiento);
                                    $('#estado_civil').select2("val", data.results.estado_civil);
                                    if (data.results.sexo == 'M')
                                        $("#sexo_m_id").prop("checked", true);
                                    else
                                        $("#sexo_f_id").prop("checked", true);
                                    $("#domicilio").val(data.results.domicilio);
                                    $("#telefono").val(data.results.telefono);
                                    $("#celular").val(data.results.celular);
                                    $("#email").val(data.results.email);
                                }
                                else if(data.sw === 0) {
                                    var valor1 = new Array();
                                    valor1[0]  = 101;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);
                                }
                                else if(data.sw === 2) {
                                    window.location.reload();
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
<script>
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
        var url_controller    = "{!! url('/central_notificacion') !!}";
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

    // === PERSONA ESTADO ===
        var persona_estado_json   = $.parseJSON('{!! json_encode($persona_estado_array) !!}');
        var persona_estado_select = '';
        var persona_estado_jqgrid = ':Todos';

        $.each(persona_estado_json, function(index, value) {
            persona_estado_select += '<option value="' + index + '">' + value + '</option>';
            persona_estado_jqgrid += ';' + index + ':' + value;
        });

    // === SI-NO ===
        var si_no_json   = $.parseJSON('{!! json_encode($si_no_array) !!}');
        var si_no_select = '';
        var si_no_jqgrid = ':Todos';

        $.each(si_no_json, function(index, value) {
            si_no_select += '<option value="' + index + '">' + value + '</option>';
            si_no_jqgrid += ';' + index + ':' + value;
        });

    // === DEPARTAMENTO ===
        var departamento_json   = $.parseJSON('{!! json_encode($departamento_array) !!}');
        var departamento_select = '';
        var departamento_jqgrid = ':Todos';

        $.each(departamento_json, function(index, value) {
            departamento_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            departamento_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    // === ESTADO NOTIFICACION ===
        var estado_notificacion_json     = $.parseJSON('{!! json_encode($estado_notificacion_array) !!}');
        var estado_notificacion_select   = '';
        var estado_notificacion_select_1 = '';
        var estado_notificacion_jqgrid   = ': Todos';

        $.each(estado_notificacion_json, function(index, value) {
            estado_notificacion_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            estado_notificacion_jqgrid += ';' + value.nombre + ':' + value.nombre;

            if(value.estado > 2){
                estado_notificacion_select_1 += '<option value="' + value.id + '">' + value.nombre + '</option>';
            }
        });

    // === CONTADOR DE GESTIONES ===
        var anio_filter = '';
        var f_inicial   = 2018
        var f_final     = {!! date('Y') !!};
        for(var i=f_inicial; i <= f_final; i++)
        {
            anio_filter += '<option value="' + i + '">' + i + '</option>';
        }

    // === DROPZONE ===
        Dropzone.autoDiscover = false;

    // === CARGAR EL DOM ===
        $(document).ready(function(){
            //=== INICIALIZAR ===
                $('#anio_filter').append(anio_filter);
                $("#anio_filter option[value=" + {!! date('Y') !!} +"]").attr("selected","selected");

                $('#estado_notificacion_id').append(estado_notificacion_select_1);

            //=== SELECT2 ===
                $("#estado_notificacion_id").select2({
                    maximumSelectionLength: 1
                });
                $("#estado_notificacion_id").appendTo("#estado_notificacion_id_div");

            //=== CLOCKPICKER ===
                $('#solicitud_h').clockpicker({
                    autoclose: true,
                    // placement: 'top',
                    align    : 'left',
                    donetext : 'Hecho'
                });

            //=== DATEPICKER 3 ===
                $('#solicitud_f').datepicker({
                    // startView            : 2,
                    // todayBtn          : "linked",
                    // keyboardNavigation: false,
                    // forceParse        : false,
                    autoclose            : true,
                    format               : "yyyy-mm-dd",
                    startDate            : '-20y',
                    endDate              : '+20d',
                    language             : "es"
                });

            // === CHANGE SELECT GESTION ===
                $("#anio_filter").on("change", function(){
                    if(this.value != ''){
                        $(jqgrid1).jqGrid('setGridParam',{
                            url: url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1&anio_filter=' + this.value,
                            datatype: 'json'
                        }).trigger('reloadGrid');
                    }
                    else{
                        $(jqgrid1).jqGrid('setGridParam',{
                            url: url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
                            datatype: 'json'
                        }).trigger('reloadGrid');
                    }
                });

            // === CHANGE SELECT GESTION ===
                $("#estado_notificacion_id").on("change", function(){
                    $('.class_testigo').slideUp();
                    switch(this.value){
                        case '4':
                            $('.class_testigo').slideDown();
                            break;
                        default:
                            break;
                    }
                });

            // === DROPZONE ===
                var valor1 = new Array();
                valor1[0]  = 70;
                utilitarios(valor1);

            // === VALIDATE 1 ===
                var valor1 = new Array();
                valor1[0]  = 50;
                utilitarios(valor1);

            // === JQGRID ===
                var valor1 = new Array();
                valor1[0]  = 40;
                utilitarios(valor1);

            // === Add responsive to jqGrid ===
                $(window).bind('resize', function () {
                    var width = $('.tab-content').width() - 35;
                    $(jqgrid1).setGridWidth(width);
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

    // === EVENTO CUANDO SE CAMBIA ===
        $(window).on('resize.jqGrid', function() {
            var valor1 = new Array();
            valor1[0]  = 0;
            utilitarios(valor1);
        });

    // === FUNCION UTILITARIOS ===
        function utilitarios(valor){
            switch(valor[0]){
                // === JQGRID REDIMENCIONAR ===
                case 0:
                    $(jqgrid1).jqGrid('setGridWidth', $(".jqGrid_wrapper").width());
                    break;
                // === ABRIR MODAL 1 ===
                case 10:
                    var valor1 = new Array();
                    valor1[0]  = 20;
                    utilitarios(valor1);

                    $('.class_testigo').slideUp();

                    $('#modal_1_title').empty();
                    $('#modal_1_title').append('NOTIFICAR');

                    $('#modal_2_title').empty();
                    $('#modal_2_title').append('CASO: ' + valor[2] + ' CODIGO: ' + valor[3]);

                    $('#notificacion_id').val(valor[1]);

                    var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                    var val_json = $.parseJSON(ret.val_json);

                    if(val_json.uso_entrega > 2){
                        $("#estado_notificacion_id").select2("val", val_json.estado_notificacion_id);

                        switch(val_json.estado_notificacion_id){
                            case '4':
                                $('.class_testigo').slideDown();
                                break;
                            default:
                                break;
                        }
                    }

                    if(ret.notificacion_fh != ''){
                        var notificacion_fh_array = (ret.notificacion_fh).split(' ');
                        $('#notificacion_f').val(notificacion_fh_array[0]);
                        if(notificacion_fh_array[1] == '00:00:00'){
                            $('#notificacion_h').val('');
                        }
                        else{
                            $('#notificacion_h').val(notificacion_fh_array[1]);
                        }
                    }

                    $('#notificacion_testigo_n_documento').val(val_json.notificacion_testigo_n_documento);
                    $('#notificacion_testigo_nombre').val(ret.notificacion_testigo_nombre);

                    $('#modal_1').modal();
                    break;
                // === ABRIR MODAL 2 ===
                case 11:
                    $('#notificacion_id_2').val(valor[1]);

                    $('#modal_2').modal();
                    break;
                // === RESETEAR - FORMULARIO 1 ===
                case 20:
                    $("#notificacion_id").val('');

                    $('#estado_notificacion_id').select2("val", "");

                    $(form_1)[0].reset();
                    break;
                // === PROCESO DE VERIFICACION ===
                case 30:
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
                // === MOSTRAR ARCHIVO PDF BINARIO 64 ===
                case 31:
                    swal({
                        title            : "ARCHIVO PDF",
                        text             : "Espere que se genere el ARCHIVO PDF.",
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
                    break;
                // === CERRAR NOTIFICACION ===
                case 32:
                    swal({
                        title             : "CERRAR NOTIFICACION",
                        text              : "¿Esta seguro de cerrar la NOTIFICACION con código " + valor[2] + "?",
                        type              : "warning",
                        showCancelButton  : true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText : "Cerrar",
                        cancelButtonText  : "Cancelar",
                        closeOnConfirm    : false,
                        closeOnCancel     : false
                    },
                    function(isConfirm){
                        if (isConfirm){
                            var concatenar_valores = '';

                            concatenar_valores += "tipo=4&_token=" + csrf_token + "&id=" + valor[1];

                            swal({
                                title             : "CERRANDO NOTIFICACION",
                                text              : "Espere a que se cierre la NOTIFICACION.",
                                allowEscapeKey    : false,
                                showConfirmButton : false,
                                type              : "info"
                            });
                            $(".sweet-alert div.sa-info").removeClass("sa-icon sa-info").addClass("fa fa-refresh fa-4x fa-spin");

                            var valor1    = new Array();
                            valor1[0]     = 150;
                            valor1[1]     = url_controller + '/send_ajax';
                            valor1[2]     = 'POST';
                            valor1[3]     = false;
                            valor1[4]     = concatenar_valores;
                            valor1[5]     = 'json';
                            var respuesta = utilitarios(valor1);
                        }
                        else{
                            swal.close();
                        }
                    });
                    return respuesta;
                    break;
                // === MOSTRAR ARCHIVO PDF BINARIO 64 DE LA ACTIVIDAD===
                case 33:
                    swal({
                        title            : "ARCHIVO PDF DE LA ACTIVIDAD",
                        text             : "Espere que se genere el ARCHIVO PDF.",
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
                    valor1[4]  = "tipo=5&id=" + valor[1] + "&_token=" + csrf_token;
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                    break;
                // === JQGRID 1 ===
                case 40:
                    var edit1      = true;
                    var ancho1     = 5;
                    var ancho_d    = 29;
                    @if(in_array(['codigo' => '2702'], $permisos))
                        edit1  = false;
                        ancho1 += ancho_d;
                    @endif
                    @if(in_array(['codigo' => '2703'], $permisos) || in_array(['codigo' => '2704'], $permisos))
                        edit1  = false;
                        ancho1 += ancho_d;
                    @endif
                    @if(in_array(['codigo' => '2705'], $permisos))
                        edit1  = false;
                        ancho1 += ancho_d;
                    @endif
                    @if(in_array(['codigo' => '2707'], $permisos))
                        edit1  = false;
                        ancho1 += ancho_d;
                    @endif
                    @if(in_array(['codigo' => '2708'], $permisos))
                        edit1  = false;
                        ancho1 += ancho_d;
                    @endif

                    $(jqgrid1).jqGrid({
                        url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1&anio_filter=' + $('#anio_filter').val(),
                        datatype    : 'json',
                        mtype       : 'post',
                        height      : 'auto',
                        pager       : pjqgrid1,
                        rowNum      : 10,
                        rowList     : [10, 20, 30],
                        sortname    : 'i4_noti_notificaciones.codigo',
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
                            "SITUACION",
                            "ESTADO NOTIFICACION",
                            "¿CON PDF?",

                            "CASO",

                            "CODIGO",
                            "SOLICITUD",
                            "NOTIFICACION",

                            "DEPARTAMENTO",

                            "TIPO DE ACTIVIDAD",

                            "NOMBRE",
                            "UBICACION",

                            "NOMBRE",
                            "UBICACION",

                            "ASUNTO",

                            "OBSERVACION",

                            "TESTIGO",

                            "SOLICITANTE",

                            "NOTIFICADOR",

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
                                index      : "i4_noti_notificaciones.estado",
                                width      : 90,
                                align      : "center",
                                stype      : 'select',
                                editoptions: {value:estado_jqgrid}
                            },
                            {
                                name       : "persona_estado",
                                index      : "i4_noti_notificaciones.persona_estado",
                                width      : 100,
                                align      : "center",
                                stype      : 'select',
                                editoptions: {value:persona_estado_jqgrid}
                            },
                            {
                                name       : "estado_notificacion",
                                index      : "a2.EstadoNotificacion",
                                width      : 160,
                                align      : "center",
                                stype      : 'select',
                                editoptions: {value:estado_notificacion_jqgrid}
                            },
                            {
                                name       : "notificacion_estado",
                                index      : "i4_noti_notificaciones.notificacion_estado",
                                width      : 80,
                                align      : "center",
                                stype      : 'select',
                                editoptions: {value:si_no_jqgrid}
                            },

                            {
                                name : "caso",
                                index: "a5.Caso",
                                width: 120,
                                align: "center"
                            },

                            {
                                name : "codigo",
                                index: "i4_noti_notificaciones.codigo",
                                width: 80,
                                align: "center"
                            },
                            {
                                name : "solicitud_fh",
                                index: "i4_noti_notificaciones.solicitud_fh",
                                width: 135,
                                align: "center"
                            },
                            {
                                name : "notificacion_fh",
                                index: "i4_noti_notificaciones.notificacion_fh",
                                width: 135,
                                align: "center"
                            },

                            {
                                name       : "departamento",
                                index      : "a9.Dep",
                                width      : 110,
                                align      : "center",
                                stype      :'select',
                                editoptions: {value:departamento_jqgrid}
                            },

                            {
                                name : "tipo_actividad",
                                index: "a13.TipoActividad",
                                width: 300,
                                align: "center"
                            },

                            {
                                name : "persona",
                                index: "a3.Persona",
                                width: 250,
                                align: "center"
                            },
                            {
                                name : "ubicacion_persona",
                                index: "CONCAT_WS(', ',i4_noti_notificaciones.persona_municipio,i4_noti_notificaciones.persona_zona,i4_noti_notificaciones.persona_direccion,i4_noti_notificaciones.persona_telefono,i4_noti_notificaciones.persona_celular,i4_noti_notificaciones.persona_email)",
                                width: 350,
                                align: "left"
                            },

                            {
                                name : "abogado",
                                index: "a4.Abogado",
                                width: 250,
                                align: "center"
                            },
                            {
                                name : "ubicacion_abogado",
                                index: "CONCAT_WS(', ',i4_noti_notificaciones.abogado_municipio,i4_noti_notificaciones.abogado_zona,i4_noti_notificaciones.abogado_direccion,i4_noti_notificaciones.abogado_telefono,i4_noti_notificaciones.abogado_celular,i4_noti_notificaciones.abogado_email)",
                                width: 350,
                                align: "left"
                            },

                            {
                                name : "solicitud_asunto",
                                index: "i4_noti_notificaciones.solicitud_asunto",
                                width: 300,
                                align: "left"
                            },

                            {
                                name : "notificacion_observacion",
                                index: "i4_noti_notificaciones.notificacion_observacion",
                                width: 200,
                                align: "left"
                            },

                            {
                                name : "notificacion_testigo_nombre",
                                index: "i4_noti_notificaciones.notificacion_testigo_nombre",
                                width: 200,
                                align: "left"
                            },

                            {
                                name : "funcionario_solicitante",
                                index: "a11.Funcionario",
                                width: 250,
                                align: "left"
                            },

                            {
                                name : "funcionario_notificador",
                                index: "a12.Funcionario",
                                width: 250,
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

                                var noti1 = "";
                                @if(in_array(['codigo' => '2702'], $permisos))
                                    noti1 = "<button type='button' class='btn btn-xs btn-primary' title='Notificar' onclick=\"utilitarios([10, " + cl + ", '" + ret.caso + "', '" + ret.codigo + "']);\"><i class='fa fa-bell'></i></button>";
                                @endif

                                var pdf1 = "";
                                @if(in_array(['codigo' => '2705'], $permisos))
                                    if(val_json.uso_entrega >= 3 && val_json.uso_entrega <= 4){
                                        pdf1 = " <button type='button' class='btn btn-xs btn-info' title='PDF de la notificación' onclick=\"utilitarios([60, " + cl + "]);\"><i class='fa fa-file-pdf-o'></i></button>";
                                    }
                                @endif

                                var upl1 = "";
                                @if(in_array(['codigo' => '2707'], $permisos))
                                    if(val_json.uso_entrega >= 3 && val_json.uso_entrega <= 4){
                                        upl1 = " <button type='button' class='btn btn-xs btn-success' title='Subir documento PDF' onclick=\"utilitarios([11, " + cl + "]);\"><i class='fa fa-upload'></i></button>";
                                    }
                                @endif

                                var cer1 = "";
                                @if(in_array(['codigo' => '2708'], $permisos))
                                    if(val_json.notificacion_estado == 2){
                                        cer1 = " <button type='button' class='btn btn-xs btn-warning' title='Cerrar NOTIFICACION' onclick=\"utilitarios([32, " + cl + ", '" + ret.codigo + "']);\"><i class='fa fa-lock'></i></button>";
                                    }
                                @endif

                                var anul1 = "";
                                @if(in_array(['codigo' => '2703'], $permisos))
                                    if(val_json.estado == 1){
                                        if(val_json.estado_notificacion_id == 1){
                                            anul1 = " <button type='button' class='btn btn-xs btn-danger' title='Anular notificación' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-trash'></i></button>";
                                        }
                                    }
                                @endif

                                var habi1 = "";
                                @if(in_array(['codigo' => '2704'], $permisos))
                                    if(val_json.estado == 2){
                                        habi1 = " <button type='button' class='btn btn-xs btn-success' title='Habilitar notificación' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-check'></i></button>";
                                    }
                                @endif

                                $(jqgrid1).jqGrid('setRowData', ids[i], {
                                    act : $.trim(noti1 + pdf1 + upl1 + cer1 + anul1 + habi1)
                                });
                            }
                        }
                    });

                    $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                            {
                                startColumnName: 'estado',
                                numberOfColumns: 4,
                                titleText      : 'ESTADOS'
                            },
                            {
                                startColumnName: 'solicitud_fh',
                                numberOfColumns: 2,
                                titleText      : 'FECHA Y HORA'
                            },
                            {
                                startColumnName: 'persona',
                                numberOfColumns: 2,
                                titleText      : 'PERSONA A NOTIFICAR'
                            },
                            {
                                startColumnName: 'abogado',
                                numberOfColumns: 2,
                                titleText      : 'ABOGADO A NOTIFICAR'
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
                    });
                    break;
                // === VALIDACION ===
                case 50:
                    $(form_1).validate({
                        rules: {
                            estado_notificacion_id:{
                                required : true
                            },
                            notificacion_f:{
                                required : true
                            },
                            notificacion_observacion:{
                                maxlength: 200
                            },
                            notificacion_testigo_n_documento:{
                                maxlength: 20
                            },
                            notificacion_testigo_nombre:{
                                maxlength: 200
                            }
                        }
                    });
                    break;
                // === REPORTES FILA CEDULA Y CONSTANCIA DE NOTIFICACION ===
                case 60:
                    var concatenar_valores = '';
                    concatenar_valores     += '?tipo=1&notificacion_id=' + valor[1];

                    var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                    win.focus();
                    break;
                // === DROPZONE 2 ===
                case 70:
                    $("#dropzoneForm_2").dropzone({
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
                                // === MOSTRAR DOCUMENTO PDF ===
                                case '3':
                                    if(data.sw === 1){
                                        var valor1 = new Array();
                                        valor1[0]  = 100;
                                        valor1[1]  = data.titulo;
                                        valor1[2]  = data.respuesta;
                                        utilitarios(valor1);

                                        $('#div_pdf').empty();
                                        $('#div_pdf').append('<object id="object_pdf" data="data:application/pdf;base64,' + data.pdf + '" type="application/pdf" style="min-height:500px;width:100%"></object>');

                                        $('#modal_3').modal();
                                        setTimeout(function(){
                                            $("#object_pdf").css("height", $( window ).height()-150 + 'px');
                                        }, 300);
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
                                // === CERRAR NOTIFICACION ===
                                case '4':
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
                                    }
                                    else if(data.sw === 2){
                                        window.location.reload();
                                    }
                                    swal.close();
                                    $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                    break;
                                // === MOSTRAR DOCUMENTO PDF  ===
                                case '5':
                                    if(data.sw === 1){
                                        var valor1 = new Array();
                                        valor1[0]  = 100;
                                        valor1[1]  = data.titulo;
                                        valor1[2]  = data.respuesta;
                                        utilitarios(valor1);

                                        $('#div_pdf').empty();
                                        $('#div_pdf').append('<object id="object_pdf" data="data:application/pdf;base64,' + data.pdf + '" type="application/pdf" style="min-height:500px;width:100%"></object>');

                                        $('#modal_3').modal();
                                        setTimeout(function(){
                                            $("#object_pdf").css("height", $( window ).height()-150 + 'px');
                                        }, 300);
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
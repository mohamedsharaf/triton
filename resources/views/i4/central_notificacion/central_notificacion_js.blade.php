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
                // === ABRIR MODAL  ===
                case 10:
                    var valor1 = new Array();
                    valor1[0]  = 20;
                    utilitarios(valor1);

                    $('#modal_1_title').empty();
                    $('#modal_1_title').append('NOTIFICAR');

                    $('#modal_2_title').empty();
                    $('#modal_2_title').append('CASO: ' + valor[2] + ' CODIGO: ' + valor[3]);

                    $('#notificacion_id').val(valor[1]);

                    $('#modal_1').modal();
                    break;
                // === RESETEAR - FORMULARIO 1 ===
                case 20:
                    $("#notificacion_id").val('');

                    $('#estado_notificacion_id').select2("val", "");

                    $(form_1)[0].reset();
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
                    @if(in_array(['codigo' => '2704'], $permisos))
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
                                    if(val_json.uso_entrega >= 3 && val_json.uso_entrega >= 4){
                                        pdf1 = " <button type='button' class='btn btn-xs btn-info' title='PDF notificación' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-file-pdf-o'></i></button>";
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
                                    act : $.trim(noti1 + pdf1 + anul1 + habi1)
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
                // === MENSAJE ERROR ===
                case 100:
                    toastr.success(valor[2], valor[1], options1);
                    break;
                // === MENSAJE ERROR ===
                case 101:
                    toastr.error(valor[2], valor[1], options1);
                    break;
                default:
                    break;
            }
        }
</script>
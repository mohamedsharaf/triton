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
        var grupo_id          = "{!! $grupo_id !!}";
        var i4_funcionario_id = "{!! $i4_funcionario_id !!}";
        var base_url          = "{!! url('') !!}";
        var url_controller    = "{!! url('/detencion_preventiva') !!}";
        var csrf_token        = "{!! csrf_token() !!}";

    // === FORMULARIOS ===
        var form_1 = "#form_1";
        var form_2 = "#form_2";
        var form_3 = "#form_3";
        var form_4 = "#form_4";
        var form_5 = "#form_5";

    // === JQGRID ===
        var jqgrid1  = "#jqgrid1";
        var pjqgrid1 = "#pjqgrid1";

        var jqgrid2  = "#jqgrid2";
        var pjqgrid2 = "#pjqgrid2";

        var jqgrid3  = "#jqgrid3";
        var pjqgrid3 = "#pjqgrid3";

        var jqgrid4  = "#jqgrid4";
        var pjqgrid4 = "#pjqgrid4";

        var jqgrid5  = "#jqgrid5";
        var pjqgrid5 = "#pjqgrid5";

    // === NO SI ===
        var no_si_json   = $.parseJSON('{!! json_encode($no_si_array) !!}');
        var no_si_select = '';
        var no_si_jqgrid = ':Todos';

        $.each(no_si_json, function(index, value) {
            no_si_select += '<option value="' + index + '">' + value + '</option>';
            no_si_jqgrid += ';' + index + ':' + value;
        });

    // === NUMERO DE DETENIDOS ===
        var no_si_json   = $.parseJSON('{!! json_encode($no_si_array) !!}');
        var no_si_select = '';
        var no_si_jqgrid = ':Todos';

        $.each(no_si_json, function(index, value) {
            no_si_select += '<option value="' + index + '">' + value + '</option>';
            no_si_jqgrid += ';' + index + ':' + value;
        });

        var n_detenidos_select = '';
        var n_detenidos_jqgrid = ': Todos';
        var n_inicial    = 1;
        var n_final      = 30;
        for (var i = n_inicial; i <= n_final; i++){
            n_detenidos_select += '<option value="' + i + '">' + i + '</option>';
            n_detenidos_jqgrid += ';' + i + ':' + i;
        }

    // === DP ESTADO ===
        var dp_estado_json   = $.parseJSON('{!! json_encode($dp_estado_array) !!}');
        var dp_estado_select = '';
        var dp_estado_jqgrid = ':Todos';

        $.each(dp_estado_json, function(index, value) {
            dp_estado_select += '<option value="' + index + '">' + value + '</option>';
            dp_estado_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO RECINTO ===
        var tipo_recinto_json   = $.parseJSON('{!! json_encode($tipo_recinto_array) !!}');
        var tipo_recinto_select = '';
        var tipo_recinto_jqgrid = ':Todos';

        $.each(tipo_recinto_json, function(index, value) {
            tipo_recinto_select += '<option value="' + index + '">' + value + '</option>';
            tipo_recinto_jqgrid += ';' + index + ':' + value;
        });

    // === DP SEMAFORO ===
        var dp_semaforo_json   = $.parseJSON('{!! json_encode($dp_semaforo_array) !!}');
        var dp_semaforo_select = '';
        var dp_semaforo_jqgrid = ':Todos';

        $.each(dp_semaforo_json, function(index, value) {
            dp_semaforo_select += '<option value="' + index + '">' + value + '</option>';
            dp_semaforo_jqgrid += ';' + index + ':' + value;
        });

    // === SEXO ===
        var sexo_json   = $.parseJSON('{!! json_encode($sexo_array) !!}');
        var sexo_select = '';
        var sexo_jqgrid = ':Todos';

        $.each(sexo_json, function(index, value) {
            sexo_select += '<option value="' + index + '">' + value + '</option>';
            sexo_jqgrid += ';' + index + ':' + value;
        });

    // === PELIGRO PROCESAL ===
        var peligro_procesal_json   = $.parseJSON('{!! json_encode($peligro_procesal_array) !!}');
        var peligro_procesal_select = '';
        var peligro_procesal_jqgrid = ':Todos';

        $.each(peligro_procesal_json, function(index, value) {
            peligro_procesal_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            peligro_procesal_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    // === ETAPA CASO ===
        var etapa_caso_json   = $.parseJSON('{!! json_encode($etapa_caso_array) !!}');
        var etapa_caso_select = '';
        var etapa_caso_jqgrid = ':Todos';

        $.each(etapa_caso_json, function(index, value) {
            etapa_caso_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            etapa_caso_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });
    // === DEPARTAMENTO ===
        var departamento_json   = $.parseJSON('{!! json_encode($departamento_array) !!}');
        var departamento_select = '';
        var departamento_jqgrid = ':Todos';

        $.each(departamento_json, function(index, value) {
            departamento_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            departamento_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    // === ESTADO DE LIBERTAD ===
        var estado_libertad_json   = $.parseJSON('{!! json_encode($estado_libertad_array) !!}');
        var estado_libertad_select = '';
        var estado_libertad_jqgrid = ':Todos';

        $.each(estado_libertad_json, function(index, value) {
            estado_libertad_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
            estado_libertad_jqgrid += ';' + value.nombre + ':' + value.nombre;
        });

    // === DROPZONE ===
        Dropzone.autoDiscover = false;

    $(document).ready(function(){
        //=== INICIALIZAR ===
            $('#peligro_procesal_id').append(peligro_procesal_select);

            $('#dp_semaforo_3').append(dp_semaforo_select);

            $('#departamento_id_3').append(departamento_select);

            $('#estado_libertad_id_5').append(estado_libertad_select);

        //=== SELECT2 ===
            $("#peligro_procesal_id, #dp_semaforo_3, #departamento_id_3").select2();
            $("#peligro_procesal_id").appendTo("#peligro_procesal_id_div");
            $("#dp_semaforo_3").appendTo("#dp_semaforo_3_div");
            $("#departamento_id_3").appendTo("#departamento_id_3_div");

            $("#estado_libertad_id_5").select2({
                maximumSelectionLength: 1
            });
            $("#estado_libertad_id_5").appendTo("#estado_libertad_id_5_div");

            $('#recinto_carcelario_id').select2({
                maximumSelectionLength: 1,
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
            $("#recinto_carcelario_id").appendTo("#recinto_carcelario_id_div");

            $('#delito_id_3').select2({
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
            $("#delito_id_3").appendTo("#delito_id_3_div");

            $('#funcionario_id_3').select2({
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
                            tipo      : 103,
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
            $("#funcionario_id_3").appendTo("#funcionario_id_3_div");

            $('#caso_id_4').select2({
                maximumSelectionLength: 1,
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
                            tipo      : 104,
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
            $("#caso_id_4").appendTo("#caso_id_4_div");

            $("#caso_id_4").on("change", function(){
                if($(this).val() != ''){
                    $(jqgrid2).jqGrid('setGridParam',{
                        url     : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=2&caso_id=' + $(this).val(),
                        datatype: 'json'
                    }).trigger('reloadGrid');
                }
                else{
                    $(jqgrid2).jqGrid('setGridParam',{
                        datatype: 'local'
                    }).trigger('reloadGrid');
                }
            });

        //=== DATEPICKER 3 ===
            $('#dp_fecha_detencion_preventiva, #dp_fecha_conclusion_detencion_5, #dp_madre_lactante_1_fecha_nacimiento_menor, #dp_custodia_menor_6_fecha_nacimiento_menor, #FechaNac, #fecha_denuncia_del_3, #fecha_denuncia_al_3').datepicker({
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

        //=== TOUCHSPIN ===
            $("#dp_etapa_gestacion_semana, #anio_sentencia_5, #mes_sentencia_5, #dia_sentencia_5").TouchSpin({
                buttondown_class: 'btn btn-white',
                buttonup_class: 'btn btn-white'
            });

        //=== FLIPSWITCH ===
            $("#dp_etapa_gestacion_estado").change(function(){
                if(this.checked){
                    $("#dp_etapa_gestacion_semana").prop('disabled', false);
                    $("#div_dp_etapa_gestacion_semana").slideDown("slow");
                }
                else{
                    $("#dp_etapa_gestacion_semana").prop('disabled', true);
                    $("#div_dp_etapa_gestacion_semana").slideUp("slow");
                }
            });

            $("#dp_enfermo_terminal_estado").change(function(){
                if(this.checked){
                    $("#dp_enfermo_terminal_tipo").prop('disabled', false);
                    $("#div_dp_enfermo_terminal_tipo").slideDown("slow");
                }
                else{
                    $("#dp_enfermo_terminal_tipo").prop('disabled', true);
                    $("#div_dp_enfermo_terminal_tipo").slideUp("slow");
                }
            });

            $("#dp_madre_lactante_1").change(function(){
                if(this.checked){
                    $("#dp_madre_lactante_1_fecha_nacimiento_menor").prop('disabled', false);
                    $("#div_dp_madre_lactante_1_fecha_nacimiento_menor").slideDown("slow");
                }
                else{
                    $("#dp_madre_lactante_1_fecha_nacimiento_menor").prop('disabled', true);
                    $("#div_dp_madre_lactante_1_fecha_nacimiento_menor").slideUp("slow");
                }
            });

            $("#dp_custodia_menor_6").change(function(){
                if(this.checked){
                    $("#dp_custodia_menor_6_fecha_nacimiento_menor").prop('disabled', false);
                    $("#div_dp_custodia_menor_6_fecha_nacimiento_menor").slideDown("slow");
                }
                else{
                    $("#dp_custodia_menor_6_fecha_nacimiento_menor").prop('disabled', true);
                    $("#div_dp_custodia_menor_6_fecha_nacimiento_menor").slideUp("slow");
                }
            });

            $('.sexo_id_class').change(function() {
                if(this.value == '1'){
                    $("#dp_etapa_gestacion_estado").prop('disabled', true);
                    $("#dp_madre_lactante_1").prop('disabled', true);

                    $("#div_dp_etapa_gestacion_estado").slideUp("slow");
                    $("#div_dp_madre_lactante_1").slideUp("slow");
                }
                else if(this.value == '2'){
                    $("#dp_etapa_gestacion_estado").prop('disabled', false);
                    $("#dp_madre_lactante_1").prop('disabled', false);

                    $("#div_dp_etapa_gestacion_estado").slideDown("slow");
                    $("#div_dp_madre_lactante_1").slideDown("slow");
                }
            });

        // === JQGRID ===
            var valor1 = new Array();
            valor1[0]  = 40;
            utilitarios(valor1);

            var valor1 = new Array();
            valor1[0]  = 41;
            utilitarios(valor1);

        // === VALIDATE 1 ===
            var valor1 = new Array();
            valor1[0]  = 60;
            utilitarios(valor1);

        // Add responsive to jqGrid
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
                $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
                break;

            // === EDICION MODAL ===
            case 20:
                var valor1 = new Array();
                valor1[0]  = 30;
                utilitarios(valor1);

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $('#modal_1_title, #modal_2_title').empty();
                $('#modal_1_title').append('MODIFICAR CARACTERISTICAS DEL DETENIDO');
                $('#modal_2_title').append(ret.Caso + ' - ' + $.trim(ret.ApPat + ' ' + ret.ApMat) + ' ' + ret.Nombres);

                $("#persona_id").val(valor[1]);
                $("#caso_id").val(val_json.caso_id);
                $("#delito_principal_id").val(val_json.delito_principal_id);

                // === IDENTIFICACION DEL CASO ===
                    $("#CodCasoJuz").val(ret.CodCasoJuz);

                // === PERSONA DETENIDA ===
                    $("#NumDocId").val(ret.NumDocId);
                    $("#FechaNac").val(ret.FechaNac);
                    $("#ApPat").val(ret.ApPat);
                    $("#ApMat").val(ret.ApMat);
                    $("#ApEsp").val(ret.ApEsp);
                    $("#Nombres").val(ret.Nombres);
                    if(val_json.sexo_id != "null"){
                        $(".sexo_id_class[value=" + val_json.sexo_id + "]").prop('checked', true);
                        if(val_json.sexo_id == 2){
                            $("#dp_etapa_gestacion_estado").prop('disabled', false);
                            $("#dp_madre_lactante_1").prop('disabled', false);

                            $("#div_dp_etapa_gestacion_estado").slideDown("slow");
                            $("#div_dp_madre_lactante_1").slideDown("slow");
                        }
                    }

                // === DATOS DEL PROCESO ===
                    if(ret.peligro_procesal != ""){
                        var peligro_procesal      = val_json.peligro_procesal_id
                        var peligro_procesal_id_array = peligro_procesal.split('::');
                        $("#peligro_procesal_id").select2().val(peligro_procesal_id_array).trigger("change");
                    }
                    $("#dp_fecha_detencion_preventiva").val(ret.dp_fecha_detencion_preventiva);
                    $("#dp_fecha_conclusion_detencion").val(ret.dp_fecha_conclusion_detencion);
                    if(val_json.recinto_carcelario_id != null){
                        $('#recinto_carcelario_id').append('<option value="' + val_json.recinto_carcelario_id + '">' + ret.recinto_carcelario + '</option>');
                        $("#recinto_carcelario_id").select2("val", val_json.recinto_carcelario_id);
                    }

                // === CARACTERISTICAS DEL DETENIDO ===
                    if(val_json.dp_etapa_gestacion_estado == 2){
                        $('#dp_etapa_gestacion_estado').prop('checked', true);
                        $("#dp_etapa_gestacion_semana").prop('disabled', false);
                        $("#div_dp_etapa_gestacion_semana").slideDown("slow");

                        $("#dp_etapa_gestacion_semana").val(val_json.dp_etapa_gestacion_semana);
                    }

                    if(val_json.dp_enfermo_terminal_estado == 2){
                        $('#dp_enfermo_terminal_estado').prop('checked', true);
                        $("#dp_enfermo_terminal_tipo").prop('disabled', false);
                        $("#div_dp_enfermo_terminal_tipo").slideDown("slow");

                        $("#dp_enfermo_terminal_tipo").val(val_json.dp_enfermo_terminal_tipo);
                    }

                    if(val_json.dp_madre_lactante_1 == 2){
                        $('#dp_madre_lactante_1').prop('checked', true);
                        $("#dp_madre_lactante_1_fecha_nacimiento_menor").prop('disabled', false);
                        $("#div_dp_madre_lactante_1_fecha_nacimiento_menor").slideDown("slow");

                        $("#dp_madre_lactante_1_fecha_nacimiento_menor").val(val_json.dp_madre_lactante_1_fecha_nacimiento_menor);
                    }

                    if(val_json.dp_custodia_menor_6 == 2){
                        $('#dp_custodia_menor_6').prop('checked', true);
                        $("#dp_custodia_menor_6_fecha_nacimiento_menor").prop('disabled', false);
                        $("#div_dp_custodia_menor_6_fecha_nacimiento_menor").slideDown("slow");

                        $("#dp_custodia_menor_6_fecha_nacimiento_menor").val(val_json.dp_custodia_menor_6_fecha_nacimiento_menor);
                    }

                    if(val_json.reincidencia == 2){
                        $('#reincidencia').prop('checked', true);
                    }

                // === SEGIP ===
                    if(val_json.estado_segip == 2){
                        $("#button_segip").slideUp("slow");

                        $("#NumDocId").prop('disabled', true);
                        $("#FechaNac").prop('disabled', true);
                        $("#ApPat").prop('disabled', true);
                        $("#ApMat").prop('disabled', true);
                        $("#Nombres").prop('disabled', true);

                        concatenar_valores = "tipo=3&_token=" + csrf_token + "&n_documento=" + ret.NumDocId;

                        var valor1    = new Array();
                        valor1[0]     = 150;
                        valor1[1]     = url_controller + '/send_ajax';
                        valor1[2]     = 'POST';
                        valor1[3]     = false;
                        valor1[4]     = concatenar_valores;
                        valor1[5]     = 'json';
                        var respuesta = utilitarios(valor1);
                    }

                $('#modal_1').modal();
                break;
            // === SENTENCIADOS ===
            case 21:
                $('#modal_5').modal();
                break;
            // === RESETEAR - FORMULARIO ===
            case 30:
                $("#persona_id").val('');
                $("#caso_id").val('');

                // === PERSONA DETENIDA ===
                    $("#NumDocId").prop('disabled', false);
                    $("#FechaNac").prop('disabled', false);
                    $("#ApPat").prop('disabled', false);
                    $("#ApMat").prop('disabled', false);
                    $("#Nombres").prop('disabled', false);

                // === CARACTERISTICAS DEL DETENIDO ===
                    $('#peligro_procesal_id').select2("val", "");
                    $('#recinto_carcelario_id').select2("val", "");
                    $('#recinto_carcelario_id option').remove();

                // === CARACTERISTICAS DEL DETENIDO ===
                    $("#dp_etapa_gestacion_semana").prop('disabled', true);
                    $("#dp_enfermo_terminal_tipo").prop('disabled', true);
                    $("#dp_madre_lactante_1_fecha_nacimiento_menor").prop('disabled', true);
                    $("#dp_custodia_menor_6_fecha_nacimiento_menor").prop('disabled', true);

                    $("#div_dp_etapa_gestacion_semana").slideUp("slow");
                    $("#div_dp_enfermo_terminal_tipo").slideUp("slow");
                    $("#div_dp_madre_lactante_1_fecha_nacimiento_menor").slideUp("slow");
                    $("#div_dp_custodia_menor_6_fecha_nacimiento_menor").slideUp("slow");

                    $("#dp_etapa_gestacion_estado").prop('disabled', true);
                    $("#dp_madre_lactante_1").prop('disabled', true);

                    $("#div_dp_etapa_gestacion_estado").slideUp("slow");
                    $("#div_dp_madre_lactante_1").slideUp("slow");

                // === SEGIP ===
                    $("#button_segip").slideDown("slow");
                    $('#div_segip').empty();

                $(form_1)[0].reset();
                break;
            // === JQGRID 1 ===
            case 40:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '2003'], $permisos))
                    if(grupo_id == 2 && i4_funcionario_id != ''){
                        edit1  = false;
                        ancho1 += ancho_d;
                    }
                @endif
                @if(in_array(['codigo' => '2005'], $permisos))
                    if(grupo_id == 2 && i4_funcionario_id != ''){
                        edit1  = false;
                        ancho1 += ancho_d;
                    }
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
                    sortname    : 'Caso.FechaDenuncia',
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

                        "¿CON SEGIP?",

                        "SEMAFORO",
                        "SEMAFORO DELITO",
                        "DETENIDOS",
                        "ESTADO DETENIDO",
                        "NUMERO DE CASO",
                        "IANUS / NUREJ",
                        "DEPARTAMENTO",

                        "DOCUMENTO DE IDENTIDAD",
                        "AP. PATERNO",
                        "AP. MATERNO",
                        "AP. ESPOSO",
                        "NOMBRE(S)",
                        "FECHA DE NACIMIENTO",
                        "SEXO",

                        "FECHA DENUNCIA",
                        "DELITO PRINCIPAL",
                        "DELITOS",

                        "DEL",
                        "AL",
                        "ETAPA",
                        "PELIGRO PROCESAL",

                        "RECINTO CARCELARIO",

                        "FISCAL RESPONSABLE",

                        "MUNICIPIO",
                        "OFICINA",
                        "DIVISION",

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
                            name       : "estado_segip",
                            index      : "a2.estado_segip",
                            width      : 100,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value: no_si_jqgrid},
                            hidden     : true
                        },

                        {
                            name       : "dp_semaforo",
                            index      : "a2.dp_semaforo",
                            width      : 100,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value:dp_semaforo_jqgrid}
                        },
                        {
                            name       : "dp_semaforo_delito",
                            index      : "a2.dp_semaforo_delito",
                            width      : 130,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:dp_semaforo_jqgrid},
                            hidden     : true
                        },
                        {
                            name       : "n_detenidos",
                            index      : "Caso.n_detenidos",
                            width      : 80,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value: n_detenidos_jqgrid}
                        },
                        {
                            name       : "dp_estado",
                            index      : "a2.dp_estado",
                            width      : 190,
                            align      : "center",
                            hidden     : true,
                            stype      :'select',
                            editoptions: {value:dp_estado_jqgrid}
                        },
                        {
                            name : "Caso",
                            index: "Caso.Caso",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "CodCasoJuz",
                            index: "Caso.CodCasoJuz",
                            width: 150,
                            align: "left"
                        },
                        {
                            name       : "departamento",
                            index      : "a15.Dep",
                            width      : 150,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:departamento_jqgrid}
                        },

                        {
                            name : "NumDocId",
                            index: "a2.NumDocId",
                            width: 190,
                            align: "left"
                        },
                        {
                            name : "ApPat",
                            index: "a2.ApPat",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "ApMat",
                            index: "a2.ApMat",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "ApEsp",
                            index: "a2.ApEsp",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "Nombres",
                            index: "a2.Nombres",
                            width: 200,
                            align: "left"
                        },
                        {
                            name : "FechaNac",
                            index: "a2.FechaNac",
                            width: 160,
                            align: "center"
                        },
                        {
                            name       : "Sexo",
                            index      : "a2.Sexo",
                            width      : 100,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:sexo_jqgrid}
                        },

                        {
                            name : "FechaDenuncia",
                            index: "Caso.FechaDenuncia",
                            width: 150,
                            align: "center"
                        },
                        {
                            name : "DelitoPrincipal",
                            index: "a3.Delito",
                            width: 500,
                            align: "left"
                        },
                        {
                            name : "delitos",
                            index: "a11.Delito",
                            width: 500,
                            align: "left"
                        },

                        {
                            name : "dp_fecha_detencion_preventiva",
                            index: "a2.dp_fecha_detencion_preventiva",
                            width: 100,
                            align: "center"
                        },
                        {
                            name : "dp_fecha_conclusion_detencion",
                            index: "a2.dp_fecha_conclusion_detencion",
                            width: 100,
                            align: "center"
                        },
                        {
                            name       : "EtapaCaso",
                            index      : "a4.EtapaCaso",
                            width      : 150,
                            align      : "center",
                            stype      : 'select',
                            editoptions: {value: etapa_caso_jqgrid}
                        },
                        {
                            name : "peligro_procesal",
                            index: "a9.nombre",
                            width: 300,
                            align: "left"
                        },

                        {
                            name : "recinto_carcelario",
                            index: "a5.nombre",
                            width: 300,
                            align: "center"
                        },

                        {
                            name : "fiscal_responsable",
                            index: "a6.Funcionario",
                            width: 300,
                            align: "left"
                        },

                        {
                            name       : "municipio",
                            index      : "a14.Muni",
                            width      : 300,
                            align      : "left"
                        },
                        {
                            name       : "oficina",
                            index      : "a15.Oficina",
                            width      : 300,
                            align      : "left"
                        },
                        {
                            name       : "division",
                            index      : "a16.Division",
                            width      : 500,
                            align      : "left"
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
                            @if(in_array(['codigo' => '2003'], $permisos))
                                if(grupo_id == 2 && i4_funcionario_id != ''){
                                    ed = "<button type='button' class='btn btn-xs btn-success' title='Modificar detención' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                                }
                            @endif

                            var sen = "";
                            @if(in_array(['codigo' => '2005'], $permisos))
                                if(grupo_id == 2 && i4_funcionario_id != ''){
                                    sen = " <button type='button' class='btn btn-xs btn-danger' title='Sentencia' onclick=\"utilitarios([21, " + cl + "]);\"><i class='fa fa-gavel'></i></button>";
                                }
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed + sen)
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'NumDocId',
                            numberOfColumns: 7,
                            titleText      : 'PERSONA DETENIDA',
                        },
                        {
                            startColumnName: 'dp_fecha_detencion_preventiva',
                            numberOfColumns: 2,
                            titleText      : 'FECHA DETENCION',
                        },
                        {
                            startColumnName: 'municipio',
                            numberOfColumns: 3,
                            titleText      : 'UBICACION DEL CASO',
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
                @if(in_array(['codigo' => '2002'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "add1",
                        caption       : "",
                        title         : 'Agregar nueva fila',
                        buttonicon    : "ui-icon ui-icon-plusthick",
                        onClickButton : function(){
                            $('#modal_4').modal();

                            setTimeout(function(){
                                $(jqgrid2).jqGrid('setGridWidth', $("#div_jqgrid2").width());
                            }, 300);
                        }
                    })
                @endif
                @if(in_array(['codigo' => '2004'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "print1",
                        caption       : "",
                        title         : 'Reportes',
                        buttonicon    : "ui-icon ui-icon-print",
                        onClickButton : function(){
                            if(grupo_id == 2 && i4_funcionario_id != ''){
                                var concatenar_valores = '?tipo=10';
                                var win = window.open(url_controller + '/reportes' + concatenar_valores,  '_blank');
                                win.focus();
                            }
                            else{
                                $('#dp_semaforo_3').select2("val", "");
                                $('#departamento_id_3').select2("val", "");
                                $('#delito_id_3').select2("val", "");
                                $('#funcionario_id_3').select2("val", "");
                                $(form_3)[0].reset();

                                $('#modal_3').modal();
                            }
                        }
                    })
                @endif
                ;
                break;
            // === JQGRID 2 ===
            case 41:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '2003'], $permisos))
                    if(grupo_id == 2 && i4_funcionario_id != ''){
                        edit1  = false;
                        ancho1 += ancho_d;
                    }
                @endif

                $(jqgrid2).jqGrid({
                    caption     : '',
                    datatype    : 'local',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid2,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'Persona.ApPat',
                    sortorder   : "asc",
                    viewrecords : true,
                    shrinkToFit : false,
                    hidegrid    : false,
                    multiboxonly: true,
                    altRows     : true,
                    // rownumbers  : true,
                    // subGrid     : subgrid_sw,
                    // multiselect  : true,
                    //autowidth     : true,
                    //gridview      :true,
                    //forceFit      : true,
                    //toolbarfilter : true,
                    colNames : [
                        "",

                        "ESTADO DE LIBERTAD",

                        "DOCUMENTO",
                        "AP. PATERNO",
                        "AP. MATERNO",
                        "NOMBRE(S)",

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
                            name : "EstadoLibertad",
                            index: "Persona.EstadoLibertad",
                            width: 180,
                            align: "center"
                        },

                        {
                            name : "NumDocId",
                            index: "a2.NumDocId",
                            width: 120,
                            align: "left"
                        },
                        {
                            name : "ApPat",
                            index: "Persona.ApPat",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "ApMat",
                            index: "Persona.ApMat",
                            width: 150,
                            align: "left"
                        },
                        {
                            name : "Nombres",
                            index: "Persona.Nombres",
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
                        var ids = $(jqgrid2).jqGrid('getDataIDs');
                        for(var i = 0; i < ids.length; i++){
                            var cl       = ids[i];
                            var ret      = $(jqgrid2).jqGrid('getRowData', cl);
                            var val_json = $.parseJSON(ret.val_json);

                            var ed = "";
                            @if(in_array(['codigo' => '2003'], $permisos))
                                if(grupo_id == 2 && i4_funcionario_id != ''){
                                    if(val_json.estado_libertad_id != 4){
                                        ed = "<button type='button' class='btn btn-xs btn-warning' title='Cambiar a detención preventiva' onclick=\"utilitarios([51, " + cl + "]);\"><i class='fa fa-random'></i></button>";
                                    }
                                }
                            @endif

                            $(jqgrid2).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed)
                            });
                        }
                    }
                });

                $(jqgrid2).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'NumDocId',
                            numberOfColumns: 4,
                            titleText      : 'DENUNCIADO',
                        }
                    ]
                });

                // $(jqgrid2).jqGrid('filterToolbar',{
                //     searchOnEnter : true,
                //     stringResult  : true,
                //     defaultSearch : 'cn'
                // });

                $(jqgrid2).jqGrid('navGrid', pjqgrid2, {
                    edit  : false,
                    add   : false,
                    del   : false,
                    search: false
                })
                ;
                break;
            // === EDITAR - PROCESO DE VERIFICACION ===
            case 50:
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
            // === AÑADIR - PERSONA CON DETENCION PREVENTIVA ===
            case 51:
                var ret      = $(jqgrid2).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                var nombre_completo = $.trim($.trim(ret.ApPat + " " + ret.ApMat) + " " + ret.Nombres);

                swal({
                    title             : "ESTADO DE LIBERTAD",
                    text              : "¿Esta seguro de cambiar a DETENCION PREVENTIVA a la persona " + nombre_completo + "?",
                    type              : "warning",
                    showCancelButton  : true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText : "Cambiar",
                    cancelButtonText  : "Cancelar",
                    closeOnConfirm    : false,
                    closeOnCancel     : false
                },
                function(isConfirm){
                    if (isConfirm){
                        // swal.close();

                        swal({
                            title            : "CAMBIANDO ESTADO DE LIBERTAD",
                            text             : "Espere que se cambie a DETENCION PREVENTIVA.",
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
                        valor1[4]  = "tipo=4&id=" + valor[1] + "&_token=" + csrf_token;
                        valor1[5]  = 'json';
                        utilitarios(valor1);
                    }
                    else{
                        swal.close();
                    }
                });
                break;
            // === VALIDACION ===
            case 60:
                $(form_1).validate({
                    rules: {
                        CodCasoJuz:{
                            required : true,
                            maxlength: 20
                        },
                        NumDocId:{
                            required : true,
                            maxlength: 20
                        },
                        FechaNac:{
                            required: true,
                            date    : true
                        },
                        ApPat:{
                            maxlength: 40
                        },
                        ApMat:{
                            maxlength: 40
                        },
                        ApEsp:{
                            maxlength: 40
                        },
                        Nombres:{
                            required : true,
                            maxlength: 40
                        },
                        sexo_id:{
                            // required: true
                        },
                        "peligro_procesal_id[]":{
                            required : true
                        },
                        dp_fecha_detencion_preventiva:{
                            required: true
                            // date    : true
                        },
                        recinto_carcelario_id:{
                            required: true
                        },

                        dp_enfermo_terminal_tipo:{
                            maxlength: 500
                        }
                    }
                });
                break;
            // === CONSULTA SEGIP ===
            case 70:
                var concatenar_valores = '';

                concatenar_valores += "tipo=2&_token=" + csrf_token;

                var persona_id   = $("#persona_id").val();
                var estado_segip = $("#estado_segip").val();
                var NumDocId     = $("#NumDocId").val();
                var FechaNac     = $("#FechaNac").val();
                var ApPat        = $("#ApPat").val();
                var ApMat        = $("#ApMat").val();
                var Nombres      = $("#Nombres").val();
                var sexo         = $(".sexo_id_class:checked").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(NumDocId) != ''){
                    concatenar_valores += '&NumDocId=' + NumDocId;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo DOCUMENTO DE IDENTIDAD es obligatorio.';
                }

                if($.trim(FechaNac) != ''){
                    concatenar_valores += '&FechaNac=' + FechaNac;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo FECHA DE NACIMIENTO es obligatorio.';
                }

                if($.trim(Nombres) != ''){
                    concatenar_valores += '&Nombres=' + Nombres;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo NOMBRES es obligatorio.';
                }

                if(($.trim(ApPat) != '') || ($.trim(ApMat) != '')){
                    concatenar_valores += '&ApPat=' + ApPat;
                    concatenar_valores += '&ApMat=' + ApMat;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo APELLIDO PATERNO o APELLIDO MATERNO es obligatorio.';
                }

                concatenar_valores += '&persona_id=' + persona_id;
                concatenar_valores += '&sexo=' + sexo;

                if(valor_sw){
                    swal({
                        title            : "VALIDANDO CON EL SEGIP",
                        text             : "Espere respuesta.",
                        allowEscapeKey   : false,
                        showConfirmButton: false,
                        type             : "info"
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

                    return respuesta;
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 101;
                    valor1[1]  = '<div class="text-center"><strong>ERROR DE VALIDACION</strong></div>';
                    valor1[2]  = valor_error;
                    utilitarios(valor1);

                    return false;
                }
                break;
            // === MOSTRAR CARACTERISTICAS DEL DETENIDO MODAL ===
            case 80:
                $(form_2)[0].reset();

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $('#modal_3_title').empty();
                $('#modal_3_title').append(ret.Caso + ' - ' + $.trim(ret.ApPat + ' ' + ret.ApMat) + ' ' + ret.Nombres);

                // === CARACTERISTICAS DEL DETENIDO ===
                    if(val_json.dp_etapa_gestacion_estado == 2){
                        $('#dp_etapa_gestacion_estado_1').prop('checked', true);
                        $("#dp_etapa_gestacion_semana_1").val(val_json.dp_etapa_gestacion_semana);
                    }

                    if(val_json.dp_enfermo_terminal_estado == 2){
                        $('#dp_enfermo_terminal_estado_1').prop('checked', true);
                        $("#dp_enfermo_terminal_tipo_1").val(val_json.dp_enfermo_terminal_tipo);
                    }

                    if(val_json.dp_madre_lactante_1 == 2){
                        $('#dp_madre_lactante_1_1').prop('checked', true);
                        $("#dp_madre_lactante_1_fecha_nacimiento_menor_1").val(val_json.dp_madre_lactante_1_fecha_nacimiento_menor);
                    }

                    if(val_json.dp_custodia_menor_6 == 2){
                        $('#dp_custodia_menor_6_1').prop('checked', true);
                        $("#dp_custodia_menor_6_fecha_nacimiento_menor_1").val(val_json.dp_custodia_menor_6_fecha_nacimiento_menor);
                    }

                    if(val_json.reincidencia == 2){
                        $('#reincidencia_1').prop('checked', true);
                    }

                    if(val_json.dp_persona_mayor_65 == 2){
                        $('#dp_custodia_menor_6_1').prop('checked', true);
                    }
                    $("#edad_1").val(val_json.Edad);

                    if(val_json.dp_delito_pena_menor_4 == 2){
                        $('#dp_delito_pena_menor_4_1').prop('checked', true);
                    }

                    if(val_json.dp_delito_patrimonial_menor_6 == 2){
                        $('#dp_delito_patrimonial_menor_6_1').prop('checked', true);
                    }

                    if(val_json.dp_etapa_preparatoria_dias_transcurridos_estado == 2){
                        $('#dp_etapa_preparatoria_dias_transcurridos_estado_1').prop('checked', true);
                    }
                    $("#dp_etapa_preparatoria_dias_transcurridos_numero_1").val(val_json.dp_etapa_preparatoria_dias_transcurridos_numero);

                    if(val_json.dp_mayor_3 == 2){
                        $('#dp_mayor_3_1').prop('checked', true);
                    }

                    if(val_json.dp_minimo_previsto_delito == 2){
                        $('#dp_minimo_previsto_delito_1').prop('checked', true);
                    }

                $('#modal_2').modal();
                break;
            // === REPORTES EXCEL ===
            case 81:
                var concatenar_valores = '?tipo=11';

                var dp_semaforo        = $("#dp_semaforo_3").val();
                var departamento_id    = $("#departamento_id_3").val();
                var delito_id          = $("#delito_id_3").val();
                var funcionario_id     = $("#funcionario_id_3_div").val();
                var fecha_denuncia_del = $("#fecha_denuncia_del_3").val();
                var fecha_denuncia_al  = $("#fecha_denuncia_al_3").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(dp_semaforo) != ''){
                    concatenar_valores += '&dp_semaforo=' + dp_semaforo;
                }

                if($.trim(departamento_id) != ''){
                    concatenar_valores += '&departamento_id=' + departamento_id;
                }

                if($.trim(delito_id) != ''){
                    concatenar_valores += '&delito_id=' + delito_id;
                }

                if($.trim(funcionario_id) != ''){
                    concatenar_valores += '&funcionario_id=' + funcionario_id;
                }

                if($.trim(fecha_denuncia_del) != ''){
                    concatenar_valores += '&fecha_denuncia_del=' + fecha_denuncia_del;
                }

                if($.trim(fecha_denuncia_al) != ''){
                    concatenar_valores += '&fecha_denuncia_al=' + fecha_denuncia_al;
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
                            // === CERTIFICACION SEGIP ===
                            case '2':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $('#div_segip').empty();
                                    $('#div_segip').append('<object id="object_pdf" data="data:application/pdf;base64,' + data.pdf + '" type="application/pdf" style="min-height:500px;width:100%"></object>');

                                    $("#button_segip").slideUp("slow");

                                    $("#NumDocId").prop('disabled', true);
                                    $("#FechaNac").prop('disabled', true);
                                    $("#ApPat").prop('disabled', true);
                                    $("#ApMat").prop('disabled', true);
                                    $("#Nombres").prop('disabled', true);

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
                            // === MOSTRAR CERTIFICACION SEGIP ===
                            case '3':
                                if(data.sw === 1){
                                    // var valor1 = new Array();
                                    // valor1[0]  = 100;
                                    // valor1[1]  = data.titulo;
                                    // valor1[2]  = data.respuesta;
                                    // utilitarios(valor1);

                                    $('#div_segip').empty();
                                    $('#div_segip').append('<object id="object_pdf" data="data:application/pdf;base64,' + data.pdf + '" type="application/pdf" style="min-height:500px;width:100%"></object>');
                                }
                                else if(data.sw === 0){
                                    // var valor1 = new Array();
                                    // valor1[0]  = 101;
                                    // valor1[1]  = data.titulo;
                                    // valor1[2]  = data.respuesta;
                                    // utilitarios(valor1);
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === ELIMINAR FUNCIONARIO DEL CARGO ===
                            case '4':
                                if(data.sw === 1){
                                    var valor1 = new Array();
                                    valor1[0]  = 100;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid2).trigger("reloadGrid");
                                }
                                else if(data.sw === 0){
                                    var valor1 = new Array();
                                    valor1[0]  = 101;
                                    valor1[1]  = data.titulo;
                                    valor1[2]  = data.respuesta;
                                    utilitarios(valor1);

                                    $(jqgrid2).trigger("reloadGrid");
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
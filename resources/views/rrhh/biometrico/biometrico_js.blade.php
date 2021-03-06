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
        var url_controller = "{!! url('/biometrico') !!}";
        var csrf_token     = "{!! csrf_token() !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
            "",
            "TIPO DE CONEXION",
            "ESTADO CONEXION",
            "SERVIDOR",
            "BIOMETRICO",
            "ULTIMO LOG DE ASISTENCIA",
            "LUGAR DE DEPENDENCIA",
            "UNIDAD DESCONCENTRADA",
            "CODIGO ACTIVO FIJO",
            "IP",
            "ID USUARIO",
            "LLAVE COM",
            "PUERTO SOAP",
            "PUERTO UDP",
            "CODIFICACION",
            "DESCRIPCION",

            ""
        );
        var col_m_name_1  = new Array(
            "act",
            "estado",
            "e_conexion",
            "fs_conexion",
            "fb_conexion",
            "f_log_asistencia",
            "lugar_dependencia",
            "unidad_desconcentrada",
            "codigo_af",
            "ip",
            "internal_id",
            "com_key",
            "soap_port",
            "udp_port",
            "encoding",
            "description",

            "val_json"
        );
        var col_m_index_1 = new Array(
            "",
            "rrhh_biometricos.estado",
            "rrhh_biometricos.e_conexion",
            "rrhh_biometricos.fs_conexion::text",
            "rrhh_biometricos.fb_conexion::text",
            "rrhh_biometricos.f_log_asistencia::text",
            "a3.nombre",
            "a2.nombre",
            "rrhh_biometricos.codigo_af",
            "rrhh_biometricos.ip",
            "rrhh_biometricos.internal_id::text",
            "rrhh_biometricos.com_key::text",
            "rrhh_biometricos.soap_port::text",
            "rrhh_biometricos.udp_port::text",
            "rrhh_biometricos.encoding",
            "rrhh_biometricos.description",

            ""
        );
        var col_m_width_1 = new Array(
            33,
            150,
            130,
            150,
            150,
            190,
            300,
            300,
            145,
            110,
            100,
            100,
            100,
            100,
            120,
            300,

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

    // === ESTADO CONEXION ===
        var e_conexion_json   = $.parseJSON('{!! json_encode($e_conexion_array) !!}');
        var e_conexion_select = '';
        var e_conexion_jqgrid = ':Todos';

        $.each(e_conexion_json, function(index, value) {
            e_conexion_select += '<option value="' + index + '">' + value + '</option>';
            e_conexion_jqgrid += ';' + index + ':' + value;
        });

    // === ENCODING ===
        var encoding_json   = $.parseJSON('{!! json_encode($encoding_array) !!}');
        var encoding_select = '';
        var encoding_jqgrid = ':Todos';

        $.each(encoding_json, function(index, value) {
            encoding_select += '<option value="' + index + '">' + value + '</option>';
            encoding_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO DE EMISOR ===
        var tipo_emisor_json   = $.parseJSON('{!! json_encode($tipo_emisor_array) !!}');
        var tipo_emisor_select = '';
        var tipo_emisor_jqgrid = ':Todos';

        $.each(tipo_emisor_json, function(index, value) {
            tipo_emisor_select += '<option value="' + index + '">' + value + '</option>';
            tipo_emisor_jqgrid += ';' + index + ':' + value;
        });

    // === TIPO DE ALERTA ===
        var tipo_alerta_json   = $.parseJSON('{!! json_encode($tipo_alerta_array) !!}');
        var tipo_alerta_select = '';
        var tipo_alerta_jqgrid = ':Todos';

        $.each(tipo_alerta_json, function(index, value) {
            tipo_alerta_select += '<option value="' + index + '">' + value + '</option>';
            tipo_alerta_jqgrid += ';' + index + ':' + value;
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
            $('#estado').append(estado_select);
            $("#estado").select2({
                    maximumSelectionLength: 1
                });
            $("#estado").appendTo("#estado_div");

            $('#lugar_dependencia_id').append(lugar_dependencia_select);
            $("#lugar_dependencia_id").select2({
                    maximumSelectionLength: 1
                });
            $("#lugar_dependencia_id").appendTo("#lugar_dependencia_id_div");

            $("#unidad_desconcentrada_id").select2({
                    maximumSelectionLength: 1
                });
            $("#unidad_desconcentrada_id").appendTo("#unidad_desconcentrada_id_div");

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

        // === SELECT CHANGE ===
            $("#lugar_dependencia_id").on("change", function(e) {
                $('#unidad_desconcentrada_id').select2('val','');
                $('#unidad_desconcentrada_id option').remove();
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

        // === SELECT CHANGE - TIPO DE CONEXION ===
            $("#estado").on("change", function(e) {
                $('#ip, #internal_id, #com_key, #soap_port, #udp_port').prop("disabled", false);
                switch ($.trim(this.value)){
                    case '2':
                        $('#ip, #internal_id, #com_key, #soap_port, #udp_port').prop("disabled", true);
                        break;
                    case '3':
                        $('#ip, #internal_id, #com_key, #soap_port, #udp_port').prop("disabled", true);
                        break;
                    case '4':
                        $('#internal_id, #com_key, #soap_port, #udp_port').prop("disabled", true);
                        break;
                    case '5':
                        $('#ip, #internal_id, #com_key, #soap_port, #udp_port').prop("disabled", true);
                        break;
                    default:
                        break;
                }
            });

        // === DROPZONE ===
            // var valor1 = new Array();
            // valor1[0]  = 17;
            // utilitarios(valor1);

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
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 28;
                var subgrid_sw = false;
                @if(in_array(['codigo' => '0603'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '0604'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '0605'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '0606'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '0607'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '0608'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '0610'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif
                @if(in_array(['codigo' => '0611'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '0609'], $permisos))
                    subgrid_sw = true;
                @endif

                var hidden1 = true;
                @if(in_array(['codigo' => '0602'], $permisos) || in_array(['codigo' => '0603'], $permisos))
                    hidden1 = false;
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
                    sortname     : 'rrhh_biometricos.id',
                    sortorder    : "desc",
                    viewrecords  : true,
                    shrinkToFit  : false,
                    hidegrid     : false,
                    multiboxonly : true,
                    altRows      : true,
                    rownumbers   : true,
                    subGrid      : subgrid_sw,
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
                        col_name_1[16]
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
                            name       : col_m_name_1[2],
                            index      : col_m_index_1[2],
                            width      : col_m_width_1[2],
                            align      : col_m_align_1[2],
                            stype      :'select',
                            editoptions: {value:e_conexion_jqgrid}
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
                            name       : col_m_name_1[6],
                            index      : col_m_index_1[6],
                            width      : col_m_width_1[6],
                            align      : col_m_align_1[6],
                            stype      :'select',
                            editoptions: {value:lugar_dependencia_jqgrid}
                        },
                        {
                            name  : col_m_name_1[7],
                            index : col_m_index_1[7],
                            width : col_m_width_1[7],
                            align : col_m_align_1[7]
                        },
                        {
                            name  : col_m_name_1[8],
                            index : col_m_index_1[8],
                            width : col_m_width_1[8],
                            align : col_m_align_1[8]
                        },
                        {
                            name  : col_m_name_1[9],
                            index : col_m_index_1[9],
                            width : col_m_width_1[9],
                            align : col_m_align_1[9],
                            hidden: hidden1
                        },
                        {
                            name  : col_m_name_1[10],
                            index : col_m_index_1[10],
                            width : col_m_width_1[10],
                            align : col_m_align_1[10],
                            hidden: hidden1
                        },
                        {
                            name  : col_m_name_1[11],
                            index : col_m_index_1[11],
                            width : col_m_width_1[11],
                            align : col_m_align_1[11],
                            hidden: hidden1
                        },
                        {
                            name  : col_m_name_1[12],
                            index : col_m_index_1[12],
                            width : col_m_width_1[12],
                            align : col_m_align_1[12],
                            hidden: hidden1
                        },
                        {
                            name  : col_m_name_1[13],
                            index : col_m_index_1[13],
                            width : col_m_width_1[13],
                            align : col_m_align_1[13],
                            hidden: hidden1
                        },
                        {
                            name  : col_m_name_1[14],
                            index : col_m_index_1[14],
                            width : col_m_width_1[14],
                            align : col_m_align_1[14],
                            stype      :'select',
                            editoptions: {value:encoding_jqgrid},
                            hidden: true
                        },
                        {
                            name  : col_m_name_1[15],
                            index : col_m_index_1[15],
                            width : col_m_width_1[15],
                            align : col_m_align_1[15],
                            hidden: true
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_1[16],
                                index : col_m_index_1[16],
                                width : col_m_width_1[16],
                                align : col_m_align_1[16],
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
                            var rc = "";
                            var sf = "";
                            var re = "";
                            var ap = "";
                            var lo = "";
                            var be = "";
                            var vb = "";

                            @if(in_array(['codigo' => '0603'], $permisos))
                                ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                            @endif

                            @if(in_array(['codigo' => '0604'], $permisos))
                                if(val_json.estado == '1'){
                                    rc = " <button type='button' class='btn btn-xs btn-danger' title='Revisar conexión' onclick=\"utilitarios([18, " + cl + "]);\"><i class='fa fa-plug'></i></button>";
                                }
                            @endif

                            @if(in_array(['codigo' => '0605'], $permisos))
                                if(val_json.estado == '1'){
                                    sf = " <button type='button' class='btn btn-xs btn-warning' title='Sincronizar fecha y hora' onclick=\"utilitarios([19, " + cl + "]);\"><i class='fa fa-clock-o'></i></button>";
                                }
                            @endif

                            @if(in_array(['codigo' => '0606'], $permisos))
                                if(val_json.estado == '1'){
                                    re = " <button type='button' class='btn btn-xs btn-primary' title='Reiniciar biométrico' onclick=\"utilitarios([20, " + cl + "]);\"><i class='fa fa-rotate-right'></i></button>";
                                }
                            @endif

                            @if(in_array(['codigo' => '0607'], $permisos))
                                if(val_json.estado == '1'){
                                    ap = " <button type='button' class='btn btn-xs' title='Apagar biométrico' onclick=\"utilitarios([21, " + cl + "]);\"><i class='fa fa-power-off'></i></button>";
                                }
                            @endif

                            @if(in_array(['codigo' => '0608'], $permisos))
                                if(val_json.estado == '1'){
                                    lo = " <button type='button' class='btn btn-xs btn-success' title='Obtener registro de asistencia' onclick=\"utilitarios([22, " + cl + "]);\"><i class='fa fa-database'></i></button>";
                                }
                            @endif

                            @if(in_array(['codigo' => '0610'], $permisos))
                                if(val_json.estado == '1'){
                                    be = " <button type='button' class='btn btn-xs btn-danger' title='Generar backup y eliminar el log del biométrico' onclick=\"utilitarios([23, " + cl + "]);\"><i class='fa fa-copy'></i></button>";
                                }
                            @endif

                            @if(in_array(['codigo' => '0611'], $permisos))
                                vb = " <button type='button' class='btn btn-xs btn-info' title='Ver log del backup del biométrico' onclick=\"utilitarios([24, " + cl + "]);\"><i class='fa fa-eye'></i></button>";
                            @endif

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed + rc + sf + re + ap + lo + be + vb)
                            });
                        }
                    },
                    subGridRowExpanded: function(subgrid_id, row_id) {
                        var subgrid_table_id, pager_id;
                        subgrid_table_id = subgrid_id+"_t";
                        pager_id = "p_"+subgrid_table_id;
                        $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                        $("#"+subgrid_table_id).jqGrid({
                            url: url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=2&biometrico_id=' + row_id,
                            datatype: 'json',
                            mtype: 'post',
                            colNames: [
                                '<small>FECHA Y HORA</small>',
                                '<small>EMISOR</small>',
                                '<small>TIPO DE ALERTA</small>',
                                '<small>MENSAJE</small>',
                                /*OTROS*/
                                '<span class="font-xs">JSON</span>'
                            ],
                            colModel: [
                                {
                                    name : 'f_alerta',
                                    index: 'f_alerta::text',
                                    width: 150,
                                    align: 'center'
                                },
                                {
                                    name       : 'tipo_emisor',
                                    index      : 'tipo_emisor',
                                    width      : 200,
                                    align      : 'center',
                                    stype      : 'select',
                                    editoptions: {value:tipo_emisor_jqgrid}
                                },
                                {
                                    name       : 'tipo_alerta',
                                    index      : 'tipo_alerta',
                                    width      : 200,
                                    align      : 'center',
                                    stype      : 'select',
                                    editoptions: {value:tipo_alerta_jqgrid}
                                },
                                {
                                    name : 'mensaje',
                                    index: 'mensaje',
                                    width: 800
                                },
                                /*OTROS*/
                                {
                                    name: 'val_json',
                                    index: '',
                                    width: 10,
                                    search: false,
                                    hidden: true
                                }
                            ],
                            pager: pager_id,
                            rowNum: 10,
                            rowList: [10, 20, 30],

                            sortname: 'f_alerta',
                            sortorder: "desc",

                            shrinkToFit: false,
                            altRows: true,
                            autowidth: true,

                            viewrecords: true,
                            gridview:true,
                            // rownumbers:true,
                            multiboxonly: true,

                            height: '100%'//,

                            // ondblClickRow: function(id_row){
                            //     var ret1      = $("#"+subgrid_table_id).jqGrid('getRowData', id_row);
                            //     var val_json1 = $.parseJSON(ret1.val_json);

                            //     var win = window.open(base_url + c_upload_dir + 'pdf/' + val_json1.upload_pdf,  '_blank');
                            //     win.focus();
                            // }
                        });

                        $("#"+subgrid_table_id).jqGrid('navGrid',"#"+pager_id,{
                            edit: false,
                            add: false,
                            del: false,
                            search: false
                        });

                        $("#"+subgrid_table_id).jqGrid('filterToolbar',{
                            searchOnEnter : true,
                            stringResult  : true,
                            defaultSearch : 'cn'
                        });
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'fs_conexion',
                            numberOfColumns: 2,
                            titleText      : 'FECHA Y HORA DE LA ULTIMA CONEXION'
                        },
                        {
                            startColumnName: 'ip',
                            numberOfColumns: 7,
                            titleText      : 'DATOS PARA LA CONEXION'
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
                @if(in_array(['codigo' => '0602'], $permisos))
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
                @if(in_array(['codigo' => '0603'], $permisos))
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
                $('#modal_1_title').append('Modificar datos del biometrico');
                $("#biometrico_id").val(valor[1]);

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                // $(".estado_class[value=" + val_json.estado + "]").prop('checked', true);
                $("#estado").select2("val", val_json.estado);

                if(val_json.estado == 1){

                }
                else if(val_json.estado == 4){
                    $('#internal_id, #com_key, #soap_port, #udp_port').prop("disabled", true);
                }
                else{
                    $('#ip, #internal_id, #com_key, #soap_port, #udp_port').prop("disabled", true);
                }

                $("#lugar_dependencia_id").select2("val", val_json.lugar_dependencia_id);

                // unidad_desconcentrada_select_1 = '<option value="' + val_json.unidad_desconcentrada_id + '">' + ret.unidad_desconcentrada + '</option>';
                // $('#unidad_desconcentrada_id').append(unidad_desconcentrada_select_1);
                $("#unidad_desconcentrada_id").select2("val", val_json.unidad_desconcentrada_id);

                $("#codigo_af").val(ret.codigo_af);
                $("#ip").val(ret.ip);
                $("#internal_id").val(ret.internal_id);
                $("#com_key").val(ret.com_key);
                $("#soap_port").val(ret.soap_port);
                $("#udp_port").val(ret.udp_port);

                $('#modal_1').modal();
                break;
            // === REPORTES MODAL ===
            case 13:
                alert("REPORTE");
                break;
            // === RESETEAR FORMULARIO ===
            case 14:
                $('#modal_1_title').empty();
                $('#modal_1_title').append('Agregar biometrico');

                $("#biometrico_id").val('');

                $('#estado').select2("val", "");
                $('#lugar_dependencia_id').select2("val", "");
                $('#unidad_desconcentrada_id').select2("val", "");
                $('#unidad_desconcentrada_id option').remove();
                $('#ip, #internal_id, #com_key, #soap_port, #udp_port').prop("disabled", false);
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
                    rules:{
                        estado:{
                            required: true
                        },
                        lugar_dependencia_id:{
                            required: true
                        },
                        unidad_desconcentrada_id:{
                            required: true
                        },
                        codigo_af:{
                            required: true
                        },
                        ip:{
                            required: true
                        },
                        internal_id:{
                            required: true,
                            digits: true
                        },
                        com_key:{
                            required: true,
                            digits: true
                        },
                        soap_port:{
                            required: true,
                            digits: true
                        },
                        udp_port:{
                            required: true,
                            digits: true
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
            // === RIVISAR CONEXION ===
            case 18:
                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                if(val_json.estado == 1){
                    swal({
                        title             : "REVISANDO CONEXION",
                        text              : "Espere a que se verifique la conexión.",
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
                    valor1[4]  = "tipo=2&id=" + valor[1] + "&_token=" + csrf_token;
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 102;
                    valor1[1]  = "ALERTA";
                    valor1[2]  = "BIOMETRICO SIN RED.";
                    utilitarios(valor1);
                }
                break;
            // === SINCRONIZAR FECHA Y HORA ===
            case 19:
                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                if(val_json.estado == 1){
                    swal({
                        title             : "SINCRONIZANDO FECHA Y HORA",
                        text              : "Espere a que se sincronice la fecha y la hora.",
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
                    valor1[4]  = "tipo=3&id=" + valor[1] + "&_token=" + csrf_token;
                    valor1[5]  = 'json';
                    utilitarios(valor1);
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 102;
                    valor1[1]  = "ALERTA";
                    valor1[2]  = "BIOMETRICO SIN RED.";
                    utilitarios(valor1);
                }
                break;
            // === REINICIAR BIOMETRICO ===
            case 20:
                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                if(val_json.estado == 1){
                    swal({
                        title: "REINICIAR BIOMETRICO",
                        text: "¿Esta seguro de reiniciar el biométrico?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Reiniciar",
                        cancelButtonText: "Cancelar",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm){
                        if (isConfirm){
                            // swal.close();

                            swal({
                                title             : "REINICIANDO BIOMETRICO",
                                text              : "Espere a que reinicie el biométrico.",
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
                            valor1[4]  = "tipo=4&id=" + valor[1] + "&_token=" + csrf_token;
                            valor1[5]  = 'json';
                            utilitarios(valor1);
                        }
                        else{
                            swal.close();
                        }
                    });
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 102;
                    valor1[1]  = "ALERTA";
                    valor1[2]  = "BIOMETRICO SIN RED.";
                    utilitarios(valor1);
                }
                break;
            // === APAGAR BIOMETRICO ===
            case 21:
                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                if(val_json.estado == 1){
                    swal({
                        title: "APAGAR BIOMETRICO",
                        text: "¿Esta seguro de apagar el biométrico?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Apagar",
                        cancelButtonText: "Cancelar",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm){
                        if (isConfirm){
                            // swal.close();

                            swal({
                                title             : "APAGAR BIOMETRICO",
                                text              : "Espere a que se apague el biométrico.",
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
                            valor1[4]  = "tipo=5&id=" + valor[1] + "&_token=" + csrf_token;
                            valor1[5]  = 'json';
                            utilitarios(valor1);
                        }
                        else{
                            swal.close();
                        }
                    });
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 102;
                    valor1[1]  = "ALERTA";
                    valor1[2]  = "BIOMETRICO SIN RED.";
                    utilitarios(valor1);
                }
                break;
            // === OBTENER REGISTRO DE ASISTENCIA ===
            case 22:
                $('#modal_2_title').empty();
                $('#modal_2_title').append('Obtener registro de asistencia');
                $("#biometrico_id_2").val(valor[1]);

                $('#modal_2').modal();
                break;
            // === GENERAR BACKUP Y ELIMINAR EL LOG DEL BIOMETRICO ===
            case 23:
                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                if(val_json.estado == 1){
                    swal({
                        title: "GENERAR BACKUP Y ELIMINAR",
                        text: "¿Está seguro de generar el backup y luego eliminar el registro de marcaciones?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Generar",
                        cancelButtonText: "Cancelar",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm){
                        if (isConfirm){
                            // swal.close();

                            swal({
                                title             : "GENERANDO BACKUP",
                                text              : "Espere a que se genere el backup y elimine marcaciones del biométrico.",
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
                            valor1[4]  = "tipo=7&id=" + valor[1] + "&_token=" + csrf_token;
                            valor1[5]  = 'json';
                            utilitarios(valor1);
                        }
                        else{
                            swal.close();
                        }
                    });
                }
                else{
                    var valor1 = new Array();
                    valor1[0]  = 102;
                    valor1[1]  = "ALERTA";
                    valor1[2]  = "BIOMETRICO SIN RED.";
                    utilitarios(valor1);
                }
                break;
            // === OBTENER REGISTRO DE ASISTENCIA ===
            case 24:
                var concatenar_valores = '';
                concatenar_valores     += 'tipo=6&_token=' + csrf_token;

                var biometrico_id = $("#biometrico_id_2").val();

                var fecha_del = $("#fecha_del_2").val();
                var fecha_al  = $("#fecha_al_2").val();

                var valor_sw    = true;
                var valor_error = '';

                if($.trim(biometrico_id) != ''){
                    concatenar_valores += '&id=' + biometrico_id;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>Seleccione un biométrico.';
                }

                if($.trim(fecha_del) != ''){
                    concatenar_valores += '&fecha_del=' + fecha_del;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo FECHA DEL es obligatorio.';
                }

                if($.trim(fecha_al) != ''){
                    concatenar_valores += '&fecha_al=' + fecha_al;
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>El campo FECHA AL es obligatorio.';
                }

                if(fecha_del <= fecha_al){
                }
                else{
                    valor_sw    = false;
                    valor_error += '<br>La FECHA DEL debe de ser menor o igual a FECHA AL.';
                }

                if(valor_sw){
                    swal({
                        title             : "OBTENIENDO REGISTROS",
                        text              : "Espere a que se obtenga el registro de asistencia del biométrico.",
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
                            // === REVISAR CONEXION ===
                            case '2':
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

                                    $(jqgrid1).trigger("reloadGrid");
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === SINCRONIZAR FECHA Y HORA ===
                            case '3':
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

                                    $(jqgrid1).trigger("reloadGrid");
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === REINICIAR BIOMETRICO ===
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

                                    $(jqgrid1).trigger("reloadGrid");
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === APAGAR BIOMETRICO ===
                            case '5':
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

                                    $(jqgrid1).trigger("reloadGrid");
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === OBTENER REGISTRO DE ASISTENCIA ===
                            case '6':
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

                                    $(jqgrid1).trigger("reloadGrid");
                                }
                                else if(data.sw === 2){
                                    window.location.reload();
                                }
                                swal.close();
                                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
                                break;
                            // === GENERAR BACKUP Y ELIMINAR EL LOG DEL BIOMETRICO ===
                            case '7':
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

                                    $(jqgrid1).trigger("reloadGrid");
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
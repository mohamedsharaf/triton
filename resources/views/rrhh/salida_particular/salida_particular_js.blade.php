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
        var url_controller = "{!! url('/salida_particular') !!}";
        var csrf_token     = "{!! csrf_token() !!}";
        var public_url     = "{!! asset($public_url) !!}";

    // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";

    // === FORMULARIOS ===
        var form_1 = "#form_1";
        var form_2 = "#form_2";
        var form_3 = "#form_3";
        var form_4 = "#form_4";
        var form_5 = "#form_5";
        var form_6 = "#form_6";
        var form_7 = "#form_7";
        var form_8 = "#form_8";
        var form_9 = "#form_9";

    // === ESTADO ===
        var estado_json   = $.parseJSON('{!! json_encode($estado_array) !!}');
        var estado_select = '';
        var estado_jqgrid = ':Todos';

        $.each(estado_json, function(index, value) {
            estado_select += '<option value="' + index + '">' + value + '</option>';
            estado_jqgrid += ';' + index + ':' + value;
        });

    // === CON SIN RETORNO ===
        var con_sin_retorno_json   = $.parseJSON('{!! json_encode($con_sin_retorno_array) !!}');
        var con_sin_retorno_select = '';
        var con_sin_retorno_jqgrid = ':Todos';

        $.each(con_sin_retorno_json, function(index, value) {
            con_sin_retorno_select += '<option value="' + index + '">' + value + '</option>';
            con_sin_retorno_jqgrid += ';' + index + ':' + value;
        });

    // === SI NO ===
        var no_si_json   = $.parseJSON('{!! json_encode($no_si_array) !!}');
        var no_si_select = '';
        var no_si_jqgrid = ':Todos';

        $.each(no_si_json, function(index, value) {
            no_si_select += '<option value="' + index + '">' + value + '</option>';
            no_si_jqgrid += ';' + index + ':' + value;
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

        // === JQGRID 1 ===
            var valor1 = new Array();
            valor1[0]  = 10;
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
            // === JQGRID 1 ===
            case 10:
                var edit1      = true;
                var ancho1     = 5;
                var ancho_d    = 29;
                @if(in_array(['codigo' => '1603'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                $(jqgrid1).jqGrid({
                    caption     : title_table,
                    url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
                    datatype    : 'json',
                    mtype       : 'post',
                    height      : 'auto',
                    pager       : pjqgrid1,
                    rowNum      : 10,
                    rowList     : [10, 20, 30],
                    sortname    : 'rrhh_salidas.f_salida',
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
                        "FECHA",

                        "C.I.",
                        "NOMBRE(S)",
                        "AP. PATERNO",
                        "AP. MATERNO",

                        "SALIDA",
                        "RETORNO",
                        "RETRASO",

                        "HORA SALIDA",
                        "HORA RETORNO",
                        "RETORNO",

                        "UNIDAD DESCONCENTRADA",
                        "LUGAR DE DEPENDENCIA",

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
                            index      : "rrhh_salidas.estado",
                            width      : 90,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:estado_jqgrid}
                        },
                        {
                            name : "fecha",
                            index: "rrhh_salidas.f_salida",
                            width: 80,
                            align: "center"
                        },

                        {
                            name : "n_documento",
                            index: "a2.n_documento",
                            width: 80,
                            align: "right"
                        },
                        {
                            name : "nombre_persona",
                            index: "a2.nombre",
                            width: 180,
                            align: "center"
                        },
                        {
                            name : "ap_paterno",
                            index: "a2.ap_paterno",
                            width: 150,
                            align: "center"
                        },
                        {
                            name : "ap_materno",
                            index: "a2.ap_materno",
                            width: 150,
                            align: "center"
                        },

                        {
                            name : "salida_s",
                            index: "rrhh_salidas.salida_s",
                            width: 250,
                            align: "center"
                        },
                        {
                            name : "salida_r",
                            index: "rrhh_salidas.salida_r",
                            width: 250,
                            align: "center"
                        },
                        {
                            name : "min_retrasos",
                            index: "rrhh_salidas.min_retrasos",
                            width: 65,
                            align: "center"
                        },

                        {
                            name : "h_salida",
                            index: "rrhh_salidas.h_salida::text",
                            width: 110,
                            align: "center"
                        },
                        {
                            name : "h_retorno",
                            index: "rrhh_salidas.h_retorno::text",
                            width: 110,
                            align: "center"
                        },
                        {
                            name       : "con_sin_retorno",
                            index      : "rrhh_salidas.con_sin_retorno",
                            width      : 100,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:con_sin_retorno_jqgrid}
                        },

                        {
                            name : "ud_funcionario",
                            index: "a3.nombre",
                            width: 400,
                            align: "center"
                        },
                        {
                            name       : "lugar_dependencia_funcionario",
                            index      : "a4.nombre",
                            width      : 400,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:lugar_dependencia_jqgrid}
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

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed)
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'salida_s',
                            numberOfColumns: 3,
                            titleText      : 'MARCACION EN EL BIOMETRICO'
                        },
                        {
                            startColumnName: 'h_salida',
                            numberOfColumns: 3,
                            titleText      : 'PERIODO DE LA SALIDA'
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
                ;
                break;

            default:
                break;
        }
    }

</script>
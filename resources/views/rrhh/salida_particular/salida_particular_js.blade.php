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
                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
                    edit1  = false;
                    ancho1 += ancho_d;
                @endif

                @if(in_array(['codigo' => '1003'], $permisos))
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

                        col_name_1[18]
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
                            editoptions: {value:no_si_jqgrid}
                        },
                        {
                            name       : col_m_name_1[3],
                            index      : col_m_index_1[3],
                            width      : col_m_width_1[3],
                            align      : col_m_align_1[3],
                            stype      :'select',
                            editoptions: {value:no_si_jqgrid}
                        },
                        {
                            name       : col_m_name_1[4],
                            index      : col_m_index_1[4],
                            width      : col_m_width_1[4],
                            align      : col_m_align_1[4],
                            stype      :'select',
                            editoptions: {value:no_si_jqgrid}
                        },

                        {
                            name : col_m_name_1[5],
                            index: col_m_index_1[5],
                            width: col_m_width_1[5],
                            align: col_m_align_1[5]
                        },
                        {
                            name       : col_m_name_1[6],
                            index      : col_m_index_1[6],
                            width      : col_m_width_1[6],
                            align      : col_m_align_1[6],
                            stype      :'select',
                            editoptions: {value:tipo_salida_jqgrid}
                        },
                        {
                            name : col_m_name_1[7],
                            index: col_m_index_1[7],
                            width: col_m_width_1[7],
                            align: col_m_align_1[7]
                        },

                        {
                            name : col_m_name_1[8],
                            index: col_m_index_1[8],
                            width: col_m_width_1[8],
                            align: col_m_align_1[8]
                        },
                        {
                            name : col_m_name_1[9],
                            index: col_m_index_1[9],
                            width: col_m_width_1[9],
                            align: col_m_align_1[9]
                        },
                        {
                            name : col_m_name_1[10],
                            index: col_m_index_1[10],
                            width: col_m_width_1[10],
                            align: col_m_align_1[10]
                        },
                        {
                            name : col_m_name_1[11],
                            index: col_m_index_1[11],
                            width: col_m_width_1[11],
                            align: col_m_align_1[11]
                        },

                        {
                            name : col_m_name_1[12],
                            index: col_m_index_1[12],
                            width: col_m_width_1[12],
                            align: col_m_align_1[12]
                        },
                        {
                            name : col_m_name_1[13],
                            index: col_m_index_1[13],
                            width: col_m_width_1[13],
                            align: col_m_align_1[13]
                        },

                        {
                            name : col_m_name_1[14],
                            index: col_m_index_1[14],
                            width: col_m_width_1[14],
                            align: col_m_align_1[14]
                        },
                        {
                            name : col_m_name_1[15],
                            index: col_m_index_1[15],
                            width: col_m_width_1[15],
                            align: col_m_align_1[15]
                        },
                        {
                            name : col_m_name_1[16],
                            index: col_m_index_1[16],
                            width: col_m_width_1[16],
                            align: col_m_align_1[16]
                        },
                        {
                            name       : col_m_name_1[17],
                            index      : col_m_index_1[17],
                            width      : col_m_width_1[17],
                            align      : col_m_align_1[17],
                            stype      :'select',
                            editoptions: {value:con_sin_retorno_jqgrid}
                        },

                        // === OCULTO ===
                            {
                                name  : col_m_name_1[18],
                                index : col_m_index_1[18],
                                width : col_m_width_1[18],
                                align : col_m_align_1[18],
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

                            if(val_json.estado == '1' || val_json.estado == '3'){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    pdf1 = " <button type='button' class='btn btn-xs btn-primary' title='Generar PAPELETA DE SALIDA' onclick=\"utilitarios([13, " + cl + "]);\"><i class='fa fa-file-pdf-o'></i></button>";
                                @else
                                    pdf1 = '';
                                @endif
                            }
                            else{
                                pdf1 = '';
                            }

                            if((val_json.validar_superior == '1') && (val_json.validar_rrhh == '1') && (val_json.estado == '1')){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                                @else
                                    ed = '';
                                @endif

                                @if(in_array(['codigo' => '1003'], $permisos))
                                    up1 = " <button type='button' class='btn btn-xs btn-info' title='Suber documentación' onclick=\"utilitarios([19, " + cl + ", '" + ret.codigo + "', 1]);\"><i class='fa fa-cloud-upload'></i></button>";
                                @else
                                    up1 = '';
                                @endif

                                @if(in_array(['codigo' => '1003'], $permisos))
                                    del1 = " <button type='button' class='btn btn-xs btn-danger' title='Anular PAPELETA DE SALIDA' onclick=\"utilitarios([17, " + cl + ", 2, 1]);\"><i class='fa fa-trash'></i></button>";
                                @else
                                    del1 = '';
                                @endif
                            }
                            else{
                                ed   = '';
                                del1 = '';
                                up1 = '';
                            }

                            if((val_json.validar_superior == '1') && (val_json.validar_rrhh == '1') && (val_json.estado == '2')){
                                @if(in_array(['codigo' => '1003'], $permisos))
                                    // del2 = " <button type='button' class='btn btn-xs btn-warning' title='Habilitar PAPELETA DE SALIDA' onclick=\"utilitarios([18, " + cl + ", 1, 1]);\"><i class='fa fa-check'></i></button>";
                                    del2 = '';
                                @else
                                    del2 = '';
                                @endif
                            }
                            else{
                                del2 = '';
                            }

                            $(jqgrid1).jqGrid('setRowData', ids[i], {
                                act : $.trim(ed + up1 + pdf1 + del1 + del2)
                            });
                        }
                    }
                });

                $(jqgrid1).jqGrid('setGroupHeaders', {
                    useColSpanStyle: true,
                    groupHeaders   :[
                        {
                            startColumnName: 'validar_superior',
                            numberOfColumns: 2,
                            titleText      : '¿VALIDADO'
                        },
                        {
                            startColumnName: 'n_documento',
                            numberOfColumns: 4,
                            titleText      : 'INMEDIATO SUPERIOR'
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
                @if(in_array(['codigo' => '1002'], $permisos))
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
                @if(in_array(['codigo' => '1003'], $permisos))
                    // .navButtonAdd(pjqgrid1,{
                    //     "id"          : "edit1",
                    //     caption       : "",
                    //     title         : 'Editar fila',
                    //     buttonicon    : "ui-icon ui-icon-pencil",
                    //     onClickButton : function(){
                    //         var id = $(jqgrid1).jqGrid('getGridParam','selrow');
                    //         if(id == null)
                    //         {
                    //             var valor1 = new Array();
                    //             valor1[0]  = 101;
                    //             valor1[1]  = '<div class="text-center"><strong>ERROR</strong></div>';
                    //             valor1[2]  = "¡Favor seleccione una fila!";
                    //             utilitarios(valor1);
                    //         }
                    //         else
                    //         {
                    //             utilitarios([12, id]);
                    //         }
                    //     }
                    // })
                @endif
                @if(in_array(['codigo' => '1004'], $permisos))
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    .navButtonAdd(pjqgrid1,{
                        "id"          : "print1",
                        caption       : "",
                        title         : 'Reportes',
                        buttonicon    : "ui-icon ui-icon-print",
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
                                utilitarios([13, id]);
                            }
                        }
                    })
                @endif
                ;
                break;

            default:
                break;
        }
    }

</script>
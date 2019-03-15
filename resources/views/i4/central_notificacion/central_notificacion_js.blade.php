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

    // === PERSONA ESTADO ===
        var notificacion_estado_json   = $.parseJSON('{!! json_encode($notificacion_estado_array) !!}');
        var notificacion_estado_select = '';
        var notificacion_estado_jqgrid = ':Todos';

        $.each(notificacion_estado_json, function(index, value) {
            notificacion_estado_select += '<option value="' + index + '">' + value + '</option>';
            notificacion_estado_jqgrid += ';' + index + ':' + value;
        });

    // === CONTADOR DE GESTIONES ===
        var anio_filter = '';
        var f_inicial   = 2019
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
                // === JQGRID 1 ===
                case 40:
                    var edit1      = true;
                    var ancho1     = 5;
                    var ancho_d    = 29;
                    @if(in_array(['codigo' => '2703'], $permisos))
                        // edit1  = false;
                        // ancho1 += ancho_d;
                    @endif

                    $(jqgrid1).jqGrid({
                        url         : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
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
                            "CODIGO",
                            "ESTADO NOTIFICACION",
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
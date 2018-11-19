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
            var url_controller    = "{!! url('/recinto_carcelario') !!}";
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

        // === TIPO RECINTO ===
            var tipo_recinto_json   = $.parseJSON('{!! json_encode($tipo_recinto_array) !!}');
            var tipo_recinto_select = '';
            var tipo_recinto_jqgrid = ':Todos';

            $.each(tipo_recinto_json, function(index, value) {
                tipo_recinto_select += '<option value="' + index + '">' + value + '</option>';
                tipo_recinto_jqgrid += ';' + index + ':' + value;
            });

        // === DEPARTAMENTO ===
            var departamento_json   = $.parseJSON('{!! json_encode($departamento_array) !!}');
            var departamento_select = '';
            var departamento_jqgrid = ':Todos';

            $.each(departamento_json, function(index, value) {
                departamento_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                departamento_jqgrid += ';' + value.nombre + ':' + value.nombre;
            });

        // === DROPZONE ===
            Dropzone.autoDiscover = false;

        $(document).ready(function(){
            //=== INICIALIZAR ===
                $('#tipo_recinto').append(tipo_recinto_select);

            //=== SELECT2 ===
                $("#tipo_recinto").select2({
                    maximumSelectionLength: 1
                });
                $("#tipo_recinto").appendTo("#tipo_recinto_div");

                $('#Muni_id').select2({
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
                $("#Muni_id").appendTo("#Muni_id_div");

            //=== TOUCHSPIN ===
                $("#dp_etapa_gestacion_semana").TouchSpin({
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

            // === JQGRID ===
                var valor1 = new Array();
                valor1[0]  = 40;
                utilitarios(valor1);

            // === VALIDATE 1 ===
                var valor1 = new Array();
                valor1[0]  = 60;
                utilitarios(valor1);

            // === VALIDATE 1 ===

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
                    break;
                // === ABRIR MODAL ===
                case 10:
                    $('#modal_1_title').empty();
                    $('#modal_1_title').append('REGISTRAR RECINTO CARCELARIO');

                    $('#modal_1').modal();
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
                // === RESETEAR - FORMULARIO ===
                case 30:
                    $("#recinto_carcelario_id").val('');

                    $('#Muni_id').select2("val", "");
                    $('#Muni_id option').remove();
                    $('#tipo_recinto').select2("val", "");

                    $(form_1)[0].reset();
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
                                @if(in_array(['codigo' => '2003'], $permisos))
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
                                $('#modal_2_title').empty();
                            }
                        })
                    @endif
                    ;
                    break;
                // === PROCESO DE VERIFICACION ===
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
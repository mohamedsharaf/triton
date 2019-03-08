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
    var url_controller    = "{!! url('/institucion') !!}";
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

    $(document).ready(function(){
        //=== INICIALIZAR ===
        $('#institucion_id_div').hide();
        $("#btnInstitucion").on("click", function(){
            $('#btnInstitucion').removeClass('btn-white');
            $('#btnInstitucion').addClass('btn-primary');
            $('#btnOficina').removeClass('btn-primary');
            $('#btnOficina').addClass('btn-white');
            $('#institucion_id_div').hide();
            $('#instituciontipo').val('1');
            $('#institucion_id').select2("val", "");
            $('#institucion_id option').remove();
        });
        $("#btnOficina").on("click", function(){
            $('#btnOficina').removeClass('btn-white');
            $('#btnOficina').addClass('btn-primary');
            $('#btnInstitucion').removeClass('btn-primary');
            $('#btnInstitucion').addClass('btn-white');
            $('#institucion_id_div').show();
            $('#instituciontipo').val('2');
        });

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

        $('#municipio_id').select2({
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
        $("#municipio_id").appendTo("#municipio_id_div");

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
    // === UTILITARIOS ===
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
                @if(in_array(['codigo' => '2401'], $permisos))
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
                    sortname    : 'inst_instituciones.created_at',
                    sortorder   : "desc",
                    viewrecords : true,
                    shrinkToFit : false,
                    hidegrid    : false,
                    multiboxonly: true,
                    altRows     : true,
                    rownumbers  : true,
                    colNames : [
                        "",
                        "ESTADO",
                        "OFICINA",
                        "INSTITUCION",
                        "MUNICIPIO",
                        "ZONA",
                        "DIRECCION",
                        "TELEFONO",
                        "CELULAR",
                        "EMAIL",
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
                            index      : "inst_instituciones.estado",
                            width      : 120,
                            align      : "center",
                            stype      :'select',
                            editoptions: {value:estado_jqgrid}
                        },
                        {
                            name : "nombre",
                            index: "inst_instituciones.nombre",
                            width: 250,
                            align: "left"
                        },
                        {
                            name : "institucion",
                            index: "inst_instituciones.nombre",
                            width: 250,
                            align: "left"
                        },
                        {
                            name       : "municipio",
                            index      : "c.nombre",
                            width      : 150,
                            align      : "left"
                        },
                        {
                            name       : "zona",
                            index      : "inst_instituciones.zona",
                            width      : 150,
                            align      : "left"
                        },
                        {
                            name       : "direccion",
                            index      : "inst_instituciones.direccion",
                            width      : 200,
                            align      : "left"
                        },
                        {
                            name       : "telefono",
                            index      : "inst_instituciones.telefono",
                            width      : 100,
                            align      : "left"
                        },
                        {
                            name       : "celular",
                            index      : "inst_instituciones.celular",
                            width      : 100,
                            align      : "left"
                        },
                        {
                            name       : "email",
                            index      : "inst_instituciones.email",
                            width      : 150,
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
                            @if(in_array(['codigo' => '2401'], $permisos))
                                ed = "<button type='button' class='btn btn-xs btn-success' title='Modificar Institucion' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
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
                @if(in_array(['codigo' => '2401'], $permisos))
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
                /* @if(in_array(['codigo' => '2401'], $permisos))
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
                @endif */
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
                $('#modal_1_title').append('Modificar institucion');
                $("#institucion_id").val(valor[1]);

                var ret      = $(jqgrid1).jqGrid('getRowData', valor[1]);
                var val_json = $.parseJSON(ret.val_json);

                $(".estado_class[value=" + val_json.estado + "]").prop('checked', true);
                /* if(ret.municipio != ""){
                    var dpm = ret.departamento + ', ' + ret.provincia + ', ' + ret.municipio;
                    $('#municipio_id').append('<option value="' + val_json.municipio_id + '">' + dpm + '</option>');
                    $("#municipio_id").select2("val", val_json.municipio_id);
                } */
                $("#edinstitucion_id").val(valor[1]);
                $("#nombre").val(ret.nombre);
                $("#email").val(ret.email);
                $("#zona").val(ret.zona);
                $("#direccion").val(ret.direccion);
                $("#telefono").val(ret.telefono);
                $("#celular").val(ret.celular);
                $('#modal_1').modal();
                break;
            // === REPORTES MODAL ===
            case 13:
                alert("REPORTE");
                break;
            // === RESETEAR FORMULARIO ===
            case 14:
                $('#modal_1_title').empty();
                $('#modal_1_title').append('Agregar nueva institución/oficina');

                $('#institucion_id').select2("val", "");
                $('#institucion_id option').remove();
                $('#btnInstitucion').click();

                $('#municipio_id').select2("val", "");
                $('#municipio_id option').remove();
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
                    rules: {
                        municipio_id:{
                            required: true
                        },
                        nombre:{
                            required : true,
                            maxlength: 1000
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
                break;
            default:
                break;
        }
    }
</script>
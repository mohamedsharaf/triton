@extends('inspinia_v27.app2')

@section('title', $title)

@section('css_plugins')
  <link href="{!! asset('inspinia_v27/css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css') !!}" rel="stylesheet">
  <link href="{!! asset('inspinia_v27/css/plugins/jqGrid/ui.jqgrid.css') !!}" rel="stylesheet">

  <!-- Toastr style -->
    <link href="{!! asset('inspinia_v27/css/plugins/toastr/toastr.min.css') !!}" rel="stylesheet">

  <!-- Sweet Alert -->
    <link href="{!! asset('inspinia_v27/css/plugins/sweetalert/sweetalert.css') !!}" rel="stylesheet">

    <link href="{!! asset('inspinia_v27/css/plugins/select2/select2.min.css') !!}" rel="stylesheet">
@endsection

@section('css')
  <style type="text/css">
    #alertmod_table_list_2 {
        top: 900px !important;
    }

    .select2-close-mask{
      z-index: 2099;
    }
    .select2-dropdown{
      z-index: 3051;
    }
  </style>
@endsection

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $title }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/home') }}">{{ $home }}</a>
                </li>
                <li>
                    {{ $sistema }}
                </li>
                <li class="active">
                    <strong>{{ $modulo }}</strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="jqGrid_wrapper">
                      <table id="jqgrid1"></table>
                      <div id="pjqgrid1"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- === MODAL === -->
      <div id="modal_1" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
              </button>

              <h4 class="modal-title">
                <span id="modal_1_title"></span>
              </h4>

              <!-- <small class="font-bold">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</small> -->
            </div>

            <div class="modal-body">
              <div class="row">
                <div class="col-sm-12">
                  <form id="form_1" role="form" action="#">
                    <input type="hidden" id="unidad_desconcentrada_id" name="id" value=""/>
                    <input type="hidden" id="tipo1" name="tipo" value="1"/>
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label>Estado</label>
                      <div>
                        <label>
                          <input type="radio" class="estado_class" name="estado" value="1" checked="checked" > {{ $estado_array['1'] }}
                        </label>
                      </div>
                      <div>
                        <label>
                          <input type="radio" class="estado_class" name="estado" value="2"> {{ $estado_array['2'] }}
                        </label>
                      </div>
                    </div>

                    <div id="municipio_id_div" class="form-group">
                      <label>Departamento, Provincia y Municipio</label>
                      <select name="municipio_id" id="municipio_id" data-placeholder="Departamento, Provincia y Municipio" multiple="multiple" style="width: 100%;">
                      </select>
                    </div>

                    <div id="lugar_dependencia_id_div" class="form-group">
                      <label>Lugar de dependencia</label>
                      <select name="lugar_dependencia_id" id="lugar_dependencia_id" data-placeholder="Lugar de dependencia" multiple="multiple" style="width: 100%;">
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Unidad desconcentrada</label>
                      <textarea rows="2" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la unidad desconcentrada"></textarea>
                    </div>

                    <div class="form-group">
                      <label>Dirección</label>
                      <textarea rows="2" class="form-control" id="direccion" name="direccion" placeholder="Dirección de la unidad desconcentrada"></textarea>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info" onclick="utilitarios([14]);">Limpiar formulario</button>
              <button type="button" class="btn btn-primary" onclick="utilitarios([15]);">Guardar</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
            </div>
          </div>
        </div>
      </div>
@endsection

@section('js_plugins')
  <!-- Peity -->
    <script src="{{ asset('inspinia_v27/js/plugins/peity/jquery.peity.min.js') }}"></script>

  <!-- jqGrid -->
    <script src="{{ asset('inspinia_v27/js/plugins/jqGrid/i18n/grid.locale-es.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/jqGrid/jquery.jqGrid.min.js') }}"></script>

  <!-- Custom and plugin javascript -->
    <script src="{{ asset('inspinia_v27/js/inspinia.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/pace/pace.min.js') }}"></script>

    <script src="{{ asset('inspinia_v27/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

  <!-- Jquery Validate -->
    <script src="{{ asset('inspinia_v27/js/plugins/validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/validate/messages_es.js') }}"></script>

  <!-- Toastr script -->
    <script src="{{ asset('inspinia_v27/js/plugins/toastr/toastr.min.js') }}"></script>

  <!-- Sweet alert -->
    <script src="{{ asset('inspinia_v27/js/plugins/sweetalert/sweetalert.min.js') }}"></script>

  <!-- Select2 -->
    <script src="{{ asset('inspinia_v27/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/select2/es.js') }}"></script>
@endsection

@section('js')
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
            var url_controller = "{!! url('/unidad_desconcentrada') !!}";
            var csrf_token     = "{!! csrf_token() !!}";

        // === JQGRID1 ===
            var title_table   = "{!! $title_table !!}";
            var jqgrid1       = "#jqgrid1";
            var pjqgrid1      = "#pjqgrid1";
            var col_name_1    = new Array(
                "",
                "ESTADO",
                "UNIDAD DESCONCENTRADA",
                "DIRECCION",

                "LUGAR DE DEPENDENCIA",

                "MUNICIPIO",

                "PROVINCIA",

                "DEPARTAMENTO",
                ""
            );
            var col_m_name_1  = new Array(
                "act",
                "estado",
                "nombre",
                "direccion",

                "lugar_dependencia",

                "municipio",

                "provincia",

                "departamento",

                "val_json"
            );
            var col_m_index_1 = new Array(
                "",
                "inst_unidades_desconcentradas.estado",
                "inst_unidades_desconcentradas.nombre",
                "inst_unidades_desconcentradas.direccion",

                "inst_lugares_dependencia.nombre",

                "ubge_municipios.nombre",

                "ubge_provincias.nombre",

                "ubge_departamentos.nombre",

                ""
            );
            var col_m_width_1 = new Array(
                33,
                100,
                500,
                300,

                400,

                150,

                150,

                150,

                10
            );
            var col_m_align_1 = new Array(
                "center",
                "center",
                "left",
                "left",

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

        // === DEPARTAMENTO ===
            var departamento_json   = $.parseJSON('{!! json_encode($departamento_array) !!}');
            var departamento_select = '';
            var departamento_jqgrid = ':Todos';

            $.each(departamento_json, function(index, value) {
                departamento_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                departamento_jqgrid += ';' + value.nombre + ':' + value.nombre;
            });

        // === LUGAR DE DEPENDENCIA ===
            var lugar_dependencia_json   = $.parseJSON('{!! json_encode($lugar_dependencia_array) !!}');
            var lugar_dependencia_select = '';
            var lugar_dependencia_jqgrid = ':Todos';

            $.each(lugar_dependencia_json, function(index, value) {
                lugar_dependencia_select += '<option value="' + value.id + '">' + value.nombre + '</option>';
                lugar_dependencia_jqgrid += ';' + value.nombre + ':' + value.nombre;
            });

        $(document).ready(function(){
            //=== INICIALIZAR ===
                $('#lugar_dependencia_id').append(lugar_dependencia_select);
                $("#lugar_dependencia_id").select2({
                    maximumSelectionLength: 1
                });
                $("#lugar_dependencia_id").appendTo("#lugar_dependencia_id_div");

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

        function utilitarios(valor){
            switch(valor[0]){
                // === JQGRID REDIMENCIONAR ===
                case 0:
                    $(jqgrid1).jqGrid('setGridWidth', $(".jqGrid_wrapper").width());
                    break;
                // === JQGRID 1 ===
                case 10:
                    var edit1 = true;
                    @if(in_array(['codigo' => '0203'], $permisos))
                        edit1 = false;
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
                        sortname     : 'inst_unidades_desconcentradas.id',
                        sortorder    : "desc",
                        viewrecords  : true,
                        shrinkToFit  : false,
                        hidegrid     : false,
                        multiboxonly : true,
                        altRows      : true,
                        rownumbers   : true,
                        // multiselect  : true,
                        //autowidth     : true,
                        //gridview      :true,
                        //forceFit      : true,
                        //toolbarfilter : true,
                        colNames : [
                            col_name_1[0],
                            col_name_1[1],
                            col_name_1[2],
                            col_name_1[4],
                            col_name_1[5],
                            col_name_1[6],
                            col_name_1[7],
                            col_name_1[3],
                            col_name_1[8]
                        ],
                        colModel : [
                            {
                                name    : col_m_name_1[0],
                                index   : col_m_index_1[0],
                                width   : col_m_width_1[0],
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
                                name  : col_m_name_1[2],
                                index : col_m_index_1[2],
                                width : col_m_width_1[2],
                                align : col_m_align_1[2]
                            },
                            {
                                name       : col_m_name_1[4],
                                index      : col_m_index_1[4],
                                width      : col_m_width_1[4],
                                align      : col_m_align_1[4],
                                stype      :'select',
                                editoptions: {value:lugar_dependencia_jqgrid}
                            },
                            {
                                name  : col_m_name_1[5],
                                index : col_m_index_1[5],
                                width : col_m_width_1[5],
                                align : col_m_align_1[5]
                            },
                            {
                                name  : col_m_name_1[6],
                                index : col_m_index_1[6],
                                width : col_m_width_1[6],
                                align : col_m_align_1[6]
                            },
                            {
                                name       : col_m_name_1[7],
                                index      : col_m_index_1[7],
                                width      : col_m_width_1[7],
                                align      : col_m_align_1[7],
                                stype      :'select',
                                editoptions: {value:departamento_jqgrid}
                            },

                            {
                                name  : col_m_name_1[3],
                                index : col_m_index_1[3],
                                width : col_m_width_1[3],
                                align : col_m_align_1[3]
                            },
                            // === OCULTO ===
                                {
                                    name  : col_m_name_1[8],
                                    index : col_m_index_1[8],
                                    width : col_m_width_1[8],
                                    align : col_m_align_1[8],
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
                                var cl = ids[i];
                                ed = "<button type='button' class='btn btn-xs btn-success' title='Editar fila' onclick=\"utilitarios([12, " + cl + "]);\"><i class='fa fa-pencil'></i></button>";
                                $(jqgrid1).jqGrid('setRowData', ids[i], {
                                    act : ed
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
                    .navSeparatorAdd(pjqgrid1,{
                        sepclass : "ui-separator"
                    })
                    @if(in_array(['codigo' => '0202'], $permisos))
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
                    @if(in_array(['codigo' => '0203'], $permisos))
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
                    $('#modal_1_title').append('Modificar unidad desconcentrada');
                    $("#unidad_desconcentrada_id").val(valor[1]);

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
                    break;
                // === REPORTES MODAL ===
                case 13:
                    alert("REPORTE");
                    break;
                // === RESETEAR FORMULARIO ===
                case 14:
                    $('#modal_1_title').empty();
                    $('#modal_1_title').append('Agregar nueva unidad desconcentrada');

                    $("#unidad_desconcentrada_id").val('');

                    $('#municipio_id').select2("val", "");
                    $('#municipio_id option').remove();
                    $('#lugar_dependencia_id').select2("val", "");
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
                            lugar_dependencia_id:{
                                required: true
                            },
                            nombre:{
                                required : true,
                                maxlength: 1000
                            },
                            direccion:{
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
@endsection

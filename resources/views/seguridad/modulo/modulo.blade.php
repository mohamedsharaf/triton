@extends('inspinia_v27.app2')

@section('title', $title)

@section('css_plugins')
  <link href="{!! asset('inspinia_v27/css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css') !!}" rel="stylesheet">
  <link href="{!! asset('inspinia_v27/css/plugins/jqGrid/ui.jqgrid.css') !!}" rel="stylesheet">

  <!-- Toastr style -->
    <link href="{!! asset('inspinia_v27/css/plugins/toastr/toastr.min.css') !!}" rel="stylesheet">

  <!-- Sweet Alert -->
    <link href="{!! asset('inspinia_v27/css/plugins/sweetalert/sweetalert.css') !!}" rel="stylesheet">
@endsection

@section('css')
    <style type="text/css">
        #alertmod_table_list_2 {
            top: 900px !important;
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
                <div class="col-sm-12 b-r">
                  <form id="form_1" role="form" action="#">
                    <input type="hidden" id="modulo_id" name="id" value=""/>
                    <input type="hidden" id="tipo1" name="tipo" value="10"/>
                    <input type="hidden" id="csrf_token1" name="_token" value="{{ csrf_token() }}"/>

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

                    <div class="form-group">
                      <label>Código</label>
                      <input type="text" class="form-control" id="codigo" placeholder="El código se generara automáticamente" disabled="disabled">
                    </div>

                    <div class="form-group">
                      <label>Módulo</label>
                      <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del módulo" >
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
      var url_controller = "{!! url('/modulo') !!}";
      var csrf_token     = "{!! csrf_token() !!}";

      // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
          "",
          "ESTADO",
          "CODIGO",
          "MODULO",
          ""
        );
        var col_m_name_1  = new Array(
          "act",
          "estado",
          "codigo",
          "nombre",
          "val_json"
        );
        var col_m_index_1 = new Array(
          "",
          "a1.estado",
          "a1.codigo",
          "a1.nombre",
          ""
        );
        var col_m_width_1 = new Array(
          50,
          150,
          80,
          500,
          10
        );
        var col_m_align_1 = new Array(
          "center",
          "center",
          "center",
          "left",
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

    $(document).ready(function(){
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
          $(jqgrid1).jqGrid({
            caption      : title_table,
            url          : url_controller + '/view_jqgrid?_token=' + csrf_token + '&tipo=1',
            datatype     : 'json',
            mtype        : 'post',
            height       : 'auto',
            pager        : pjqgrid1,
            rowNum       : 10,
            rowList      : [10, 20, 30],
            sortname     : 'a1.id',
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
            colNames: [
              col_name_1[0],
              col_name_1[1],
              col_name_1[2],
              col_name_1[3],
              col_name_1[4]
            ],
            colModel: [
              {
                name    : col_m_name_1[0],
                index   : col_m_index_1[0],
                width   : col_m_width_1[0],
                align   : col_m_align_1[0],
                fixed   : true,
                sortable: false,
                resize  : false,
                search  : false
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
                name  : col_m_name_1[3],
                index : col_m_index_1[3],
                width : col_m_width_1[3],
                align : col_m_align_1[3]
              },
              // === OCULTO ===
                {
                  name  : col_m_name_1[4],
                  index : col_m_index_1[4],
                  width : col_m_width_1[4],
                  align : col_m_align_1[4],
                  search: false,
                  hidden: true
                }
            ],
            loadComplete: function(){
              $("tr.jqgrow:odd").css("background", "#DDDDDC");
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
          .navButtonAdd(pjqgrid1,{
              "id": "add1",
              caption:"",
              title: 'Agregar nueva fila',
              buttonicon:"ui-icon ui-icon-plusthick",
              onClickButton: function(){
                var valor1 = new Array();
                valor1[0]  = 14;
                utilitarios(valor1);

                var valor1 = new Array();
                valor1[0]  = 11;
                utilitarios(valor1);
              }
          })
          .navButtonAdd(pjqgrid1,{
              "id": "edit1",
              caption:"",
              title: 'Editar fila',
              buttonicon:"ui-icon ui-icon-pencil",
              onClickButton: function(){
                  var valor1 = new Array();
                  valor1[0]  = 12;
                  utilitarios(valor1);
              }
          })
          .navSeparatorAdd(pjqgrid1,{
            sepclass : "ui-separator"
          })
          .navButtonAdd(pjqgrid1,{
              "id": "print1",
              caption:"",
              title: 'Reportes',
              buttonicon:"ui-icon ui-icon-print",
              onClickButton: function(){
                  var valor1 = new Array();
                  valor1[0]  = 13;
                  utilitarios(valor1);
              }
          })
          ;

          // $("#add1 div span").removeClass("ui-icon");
          // $(".ui-icon.ui-icon-refresh").removeClass().addClass("fa fa-refresh");
          // $("#refresh_jqgrid1 div span").removeClass().addClass("fa fa-refresh");
          break;
        // === ABRIR MODAL ===
        case 11:
          $('#modal_1').modal();
          break;
        // === EDICION MODAL ===
        case 12:
          alert("EDIT");
          break;
        // === REPORTES MODAL ===
        case 13:
          alert("REPORTE");
          break;
        // === RESETEAR FORMULARIO ===
        case 14:
          $('#modal_1_title').empty();
          $('#modal_1_title').append('Agregar nuevo módulo');

          $("#modulo_id").val('');
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

            setTimeout(function(){
                swal.close();
                $(".sweet-alert div.fa-refresh").removeClass("fa fa-refresh fa-4x fa-spin").addClass("sa-icon sa-info");
            },2000);
              // var valor = new Array();
              // valor[0]  = "<strong>ENVIANDO INFORMACIÓN</strong>";
              // valor[1]  = '<p class="text-center"><i class="fa fa-refresh fa-4x fa-spin"></i></p>';
              // mensaje(2, valor);
              //
              // var tipo  = 0;
              // var valor = new Array();
              // valor[0]  = url_controller + '/send_ajax';
              // valor[1]  = 'POST';
              // valor[2]  = true;
              // valor[3]  = $(form_1).serialize();
              // valor[4]  = 'json';
              // send_ajax(tipo, valor);
          }
          else{
            var valor1 = new Array();
            valor1[0]  = 101;
            valor1[1]  = "ERROR DE VALIDACION";
            valor1[2]  = "¡Favor complete o corrija los datos solicitados!";
            utilitarios(valor1);
          }
          break;
        // === VALIDACION ===
        case 16:
          $(form_1).validate({
            rules: {
              nombre:{
                required : true,
                maxlength: 500
              }
            }
          });
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
@endsection

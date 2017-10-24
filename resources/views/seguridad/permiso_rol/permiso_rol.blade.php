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

              <small class="font-bold" id="modal_1_subtitle"></small>
            </div>

            <div class="modal-body">
              <div class="row">
                <div class="col-sm-12 b-r">
                  <form id="form_1" role="form" action="#">
                    <input type="hidden" id="rol_id" name="rol_id" value=""/>
                    <input type="hidden" id="tipo1" name="tipo" value="1"/>
                    {{ csrf_field() }}
                  </form>
                </div>
              </div>
            </div>

            <div id="modal_1_footer" class="modal-footer">
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

    <!-- Nestable List -->
        <script src="{{ asset('inspinia_v27/js/plugins/nestable/jquery.nestable.js') }}"></script>
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
      var url_controller = "{!! url('/permiso_rol') !!}";
      var csrf_token     = "{!! csrf_token() !!}";

      // === JQGRID1 ===
        var title_table   = "{!! $title_table !!}";
        var jqgrid1       = "#jqgrid1";
        var pjqgrid1      = "#pjqgrid1";
        var col_name_1    = new Array(
          "",
          "ROL",
          ""
        );
        var col_m_name_1  = new Array(
          "act",
          "nombre",
          "val_json"
        );
        var col_m_index_1 = new Array(
          "",
          "seg_roles.nombre",
          ""
        );
        var col_m_width_1 = new Array(
          33,
          500,
          10
        );
        var col_m_align_1 = new Array(
          "center",
          "left",
          "center"
        );

      // === FORMULARIO 1 ===
        var form_1 = "#form_1";

    // === MP ===
          var mp_json     = $.parseJSON('{!! json_encode($mp_array) !!}');
          var mp_nestable = "";

          if(mp_json.length != 0){
              var mp_modulo    = '';
              var mp_modulo_sw = true;

              mp_nestable += '<div id="div_nestable" class="dd">';
              mp_nestable += '<ol class="dd-list">';
              $.each(mp_json, function(index, value){
                  if(mp_modulo != value.modulo_codigo){
                      if(mp_modulo_sw){
                          mp_modulo_sw = false;

                          mp_nestable += '<li class="dd-item" data-id="' + value.modulo_codigo + '">';
                      }
                      else{
                          mp_nestable += '</ol>';
                          mp_nestable += '</li>';
                          mp_nestable += '<li class="dd-item" data-id="' + value.modulo_codigo + '">';
                      }

                      mp_nestable += '<div class="dd-handle">';
                      mp_nestable += '<span class="label label-success">' + value.modulo_codigo + '</span> <b>' + value.mudulo_nombre + '</b>';
                      mp_nestable += '</div>';
                      mp_nestable += '<ol class="dd-list">';

                      mp_modulo = value.modulo_codigo;
                  }

                  mp_nestable += '<li class="dd-item dd-nodrag" data-id="' + value.codigo + '">';
                  mp_nestable += '<div class="dd-handle">';

                  mp_nestable += '<span class="pull-right">';
                  mp_nestable += '<div class="onoffswitch">';
                  mp_nestable += '<input type="checkbox" class="onoffswitch-checkbox" id="pc_' + value.id + '" name="permiso_id[]" value="' + value.id + '">';
                  mp_nestable += '<label class="onoffswitch-label" for="pc_' + value.id + '">';
                  mp_nestable += '<span class="onoffswitch-inner"></span>';
                  mp_nestable += '<span class="onoffswitch-switch"></span>';
                  mp_nestable += '</label>';
                  mp_nestable += '</div>';
                  mp_nestable += '</span>';

                  mp_nestable += '<span class="label label-info">' + value.codigo + '</span> ' + value.nombre;
                  mp_nestable += '</div>';
                  mp_nestable += '</li>';
              });

              mp_nestable += '</ol>';
              mp_nestable += '</li>';
              mp_nestable += '</ol>';
              mp_nestable += '</div>';
          }

    $(document).ready(function(){
        if(mp_nestable != ''){
            $('#form_1').append(mp_nestable);

            $('#div_nestable').nestable({
                group   : 0,
                maxDepth: 0
            });

            $('#div_nestable').nestable('collapseAll');

            var expand_all   = '<button type ="button" class ="btn btn-info pull-left" onclick    ="utilitarios([17]);">Expandir</button>';
            var collapse_all = '<button type ="button" class ="btn btn-success pull-left" onclick ="utilitarios([18]);">Contraer</button>';

            $('#modal_1_footer').append(expand_all);
            $('#modal_1_footer').append(collapse_all);
        }

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
            sortname     : 'seg_roles.id',
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
              col_name_1[2]
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
                search  : false
              },
              {
                name  : col_m_name_1[1],
                index : col_m_index_1[1],
                width : col_m_width_1[1],
                align : col_m_align_1[1]
              },
              // === OCULTO ===
                {
                  name  : col_m_name_1[2],
                  index : col_m_index_1[2],
                  width : col_m_width_1[2],
                  align : col_m_align_1[2],
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
          // .navButtonAdd(pjqgrid1,{
          //   "id"          : "add1",
          //   caption       : "",
          //   title         : 'Agregar nueva fila',
          //   buttonicon    : "ui-icon ui-icon-plusthick",
          //   onClickButton : function(){
          //     var valor1 = new Array();
          //     valor1[0]  = 14;
          //     utilitarios(valor1);
          //
          //     var valor1 = new Array();
          //     valor1[0]  = 11;
          //     utilitarios(valor1);
          //   }
          // })
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

          var ret = $(jqgrid1).jqGrid('getRowData', valor[1]);

          $('#modal_1_title, #modal_1_subtitle').empty();
          $('#modal_1_title').append('Asignación de permisos');
          $('#modal_1_subtitle').append(ret.nombre);
          $("#rol_id").val(valor[1]);

          var valor1 = new Array();
          valor1[0]  = 150;
          valor1[1]  = url_controller + '/send_ajax';
          valor1[2]  = 'POST';
          valor1[3]  = true;
          valor1[4]  = 'tipo=100&_token=' + csrf_token + '&rol_id=' + valor[1];
          valor1[5]  = 'json';
          utilitarios(valor1);

          $('#modal_1').modal();
          break;
        // === REPORTES MODAL ===
        case 13:
          alert("REPORTE");
          break;
        // === RESETEAR FORMULARIO ===
        case 14:
          $("#rol_id").val('');
          $(form_1)[0].reset();
          break;
        // === GUARDAR REGISTRO ===
        case 15:
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
        case 17:
            $('#div_nestable').nestable('expandAll');
            break;
        case 18:
            $('#div_nestable').nestable('collapseAll');
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

                    //   $(jqgrid1).trigger("reloadGrid");
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
                  // === ROL PERMISOS ===
                    case '100':
                      if(data.sw === 1){
                          if(data.consulta.length != 0){
                              $.each(data.consulta, function(index, value){
                                  $("#pc_" + value.permiso_id).prop('checked', true);
                              });
                          }
                      }
                      else if(data.sw === 0){
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

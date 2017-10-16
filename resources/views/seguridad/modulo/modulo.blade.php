@extends('inspinia_v27.app2')

@section('title', $title)

@section('css_plugins')
    <link href="{!! asset('inspinia_v27/css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css') !!}" rel="stylesheet">
    <link href="{!! asset('inspinia_v27/css/plugins/jqGrid/ui.jqgrid.css') !!}" rel="stylesheet">
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
@endsection

@section('js')
  <script>
    // === PLUGINS ===
    // === CONSTANTES NO TOCAR ===
    // === VARIABLES GLOBALES ===
      var base_url       = "{!! url('') !!}";
      var url_controller = "{!! url('/modulo') !!}";

      // === JQGRID1 ===
        var title_table = "{!! $title_table !!}";
        var jqgrid1  = "#jqgrid1";
        var pjqgrid1 = "#pjqgrid1";
        var col_name_1 = new Array(
          "",
          "ESTADO",
          "CODIGO",
          "MODULO",
          ""
        );
        var col_m_name_1 = new Array(
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
        valor1[0]  = 1;
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
        case 1:
          $(jqgrid1).jqGrid({
            caption      : title_table,
            url          : url_controller + '/view_jqgrid?_token=' + "{!! csrf_token() !!}" + '&tipo=1',
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
            multiselect  : true,
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
          });

          // $(".ui-icon.ui-icon-refresh").removeClass().addClass("fa fa-refresh");
          // $("#refresh_jqgrid1 div").addClass("btn btn-primary dim");
          break;
        default:
          break;
      }
    }
  </script>
@endsection

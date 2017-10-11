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

      var jqgrid1  = "#jqgrid1";
      var pjqgrid1 = "#pjqgrid1";

      var title_table = "{!! $title_table !!}";

      var col_name_1 = new Array("", "CODIGO", "MODULO", "");
      var col_width_1 = new Array(50, 75, 300, 10);

      // === JSON ===
        var estado_json = $.parseJSON('{!! json_encode($estado_array) !!}');

    // === ESTADO PDF ===
      var estado_select = '';
      var estado_jqgrid = ':Todos';

      $.each(estado_json, function(index, value) {
        estado_select += '<option value="' + index + '">' + value + '</option>';
        estado_jqgrid += ';' + index + ':' + value;
      });

    $(document).ready(function(){
        // Examle data for jqGrid
        var mydata = [
            {id: "1", invdate: "2010-05-24", name: "test", note: "note", tax: "10.00", total: "2111.00"} ,
            {id: "2", invdate: "2010-05-25", name: "test2", note: "note2", tax: "20.00", total: "320.00"},
            {id: "3", invdate: "2007-09-01", name: "test3", note: "note3", tax: "30.00", total: "430.00"},
            {id: "4", invdate: "2007-10-04", name: "test", note: "note", tax: "10.00", total: "210.00"},
            {id: "5", invdate: "2007-10-05", name: "test2", note: "note2", tax: "20.00", total: "320.00"},
            {id: "6", invdate: "2007-09-06", name: "test3", note: "note3", tax: "30.00", total: "430.00"},
            {id: "7", invdate: "2007-10-04", name: "test", note: "note", tax: "10.00", total: "210.00"},
            {id: "8", invdate: "2007-10-03", name: "test2", note: "note2", amount: "300.00", tax: "21.00", total: "320.00"},
            {id: "9", invdate: "2007-09-01", name: "test3", note: "note3", amount: "400.00", tax: "30.00", total: "430.00"},
            {id: "11", invdate: "2007-10-01", name: "test", note: "note", amount: "200.00", tax: "10.00", total: "210.00"},
            {id: "12", invdate: "2007-10-02", name: "test2", note: "note2", amount: "300.00", tax: "20.00", total: "320.00"},
            {id: "13", invdate: "2007-09-01", name: "test3", note: "note3", amount: "400.00", tax: "30.00", total: "430.00"},
            {id: "14", invdate: "2007-10-04", name: "test", note: "note", amount: "200.00", tax: "10.00", total: "210.00"},
            {id: "15", invdate: "2007-10-05", name: "test2", note: "note2", amount: "300.00", tax: "20.00", total: "320.00"},
            {id: "16", invdate: "2007-09-06", name: "test3", note: "note3", amount: "400.00", tax: "30.00", total: "430.00"},
            {id: "17", invdate: "2007-10-04", name: "test", note: "note", amount: "200.00", tax: "10.00", total: "210.00"},
            {id: "18", invdate: "2007-10-03", name: "test2", note: "note2", amount: "300.00", tax: "20.00", total: "320.00"},
            {id: "19", invdate: "2007-09-01", name: "test3", note: "note3", amount: "400.00", tax: "30.00", total: "430.00"},
            {id: "21", invdate: "2007-10-01", name: "test", note: "note", amount: "200.00", tax: "10.00", total: "210.00"},
            {id: "22", invdate: "2007-10-02", name: "test2", note: "note2", amount: "300.00", tax: "20.00", total: "320.00"},
            {id: "23", invdate: "2007-09-01", name: "test3", note: "note3", amount: "400.00", tax: "30.00", total: "430.00"},
            {id: "24", invdate: "2007-10-04", name: "test", note: "note", amount: "200.00", tax: "10.00", total: "210.00"},
            {id: "25", invdate: "2007-10-05", name: "test2", note: "note2", amount: "300.00", tax: "20.00", total: "320.00"},
            {id: "26", invdate: "2007-09-06", name: "test3", note: "note3", amount: "400.00", tax: "30.00", total: "430.00"},
            {id: "27", invdate: "2007-10-04", name: "test", note: "note", amount: "200.00", tax: "10.00", total: "210.00"},
            {id: "28", invdate: "2007-10-03", name: "test2", note: "note2", amount: "300.00", tax: "20.00", total: "320.00"},
            {id: "29", invdate: "2007-09-01", name: "test3", note: "note3", amount: "400.00", tax: "30.00", total: "430.00"}
        ];

        // Configuration for jqGrid Example 1
        $(jqgrid1).jqGrid({
          caption: title_table,
          data: mydata,
          datatype: "local",
          height: 'auto',
          // autowidth: true,
          shrinkToFit: true,
          rowNum: 10,
          rowList: [10, 20, 30],
          colNames: [
            'Inv No', 'Date', 'Client', 'Amount', 'Tax', 'Total', 'Notes'],
          colModel: [
              {name: 'id', index: 'id', width: 60, sorttype: "int"},
              {name: 'invdate', index: 'invdate', width: 90, sorttype: "date", formatter: "date"},
              {name: 'name', index: 'name', width: 500},
              {name: 'amount', index: 'amount', width: 80, align: "right", sorttype: "float", formatter: "number"},
              {name: 'tax', index: 'tax', width: 300, align: "right", sorttype: "float"},
              {name: 'total', index: 'total', width: 800, align: "right", sorttype: "float"},
              {name: 'note', index: 'note', width: 150, sortable: false}
          ],
          pager: pjqgrid1,
          viewrecords: true,
          hidegrid: false,
          //autowidth: true,
          //gridview:true,
          shrinkToFit: false,
          //forceFit: true,
          rownumbers:true,
          multiboxonly: true,
          altRows: true,
          multiselect : true,
          //toolbarfilter : true,
          loadComplete: function(){
              $("tr.jqgrow:odd").css("background", "#DDDDDC");
          }
        });

        $(jqgrid1).jqGrid('filterToolbar',{searchOnEnter : true, stringResult:true, defaultSearch: 'cn'});

        // Add responsive to jqGrid
        $(window).bind('resize', function () {
            var width = $('.jqGrid_wrapper').width();
            $(jqgrid1).setGridWidth(width);
        });


        setTimeout(function(){
            $('.wrapper-content').removeClass('animated fadeInRight');
            $(jqgrid1).jqGrid('setGridWidth', $(".jqGrid_wrapper").width());
        },0);

        $( "#navbar-minimalize-button" ).on( "click", function() {
            setTimeout(function(){
                $('.wrapper-content').removeClass('animated fadeInRight');
                $(jqgrid1).jqGrid('setGridWidth', $(".jqGrid_wrapper").width());
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
        case 0:
          $(jqgrid1).jqGrid('setGridWidth', $(".jqGrid_wrapper").width());
          break;
        default:
            break;
      }
    }
  </script>
@endsection

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

    <link href="{!! asset('inspinia_v27/css/plugins/datapicker/datepicker3.css') !!}" rel="stylesheet">

    <link href="{!! asset('inspinia_v27/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') !!}" rel="stylesheet">

    <link href="{!! asset('inspinia_v27/css/plugins/orgchart/jquery.orgchart.min.css') !!}" rel="stylesheet">

  <!-- Dropzone -->
    <link href="{!! asset('inspinia_v27/css/plugins/dropzone/basic.css') !!}" rel="stylesheet">
    <link href="{!! asset('inspinia_v27/css/plugins/dropzone/dropzone.css') !!}" rel="stylesheet">
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
        .ui-th-column-header{
            text-align: center;
            background-color: #b9cde5 !important;
        }

        .ui-search-clear{
          width: 15px;
        }

        .modal-xlg {
            width: 90%;
        }

        .orgchart .node{
          /*box-sizing:border-box;
          display   :inline-block;
          position  :relative;
          margin    :0;
          padding   :3px;
          border    :2px dashed transparent;
          text-align:center;*/
          width     :auto;
        }

        .oc-export-btn{
          right:15px;
          top  :92px;
        }

        .orgchart .node .title{
          text-align      :center;
          font-size       :12px;
          font-weight     :700;
          height          :20px;
          line-height     :20px;
          overflow        :hidden;
          text-overflow   :ellipsis;
          white-space     :nowrap;
          background-color:#4587BC;
          color           :#fff;
          border-radius   :2px 2px 2px 2px;
          padding-left    :4px;
          padding-right   :4px;
        }

        .orgchart .lines .topLine{
          border-top:2px solid #0069AA
        }
        .orgchart .lines .rightLine{
          border-right :1px solid #0069AA;
          float        :none;
          border-radius:0
        }
        .orgchart .lines .leftLine{
          border-left  :1px solid #0069AA;
          float        :none;
          border-radius:0
        }
        .orgchart .lines .downLine{
          background-color:#0069AA;
          margin          :0 auto;
          height          :20px;
          width           :2px;
          float           :none
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

  <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox float-e-margins">
          <div class="ibox-title" style="padding-top: 9px;">
            <h5 style="margin-top: 7px;"><i class="fa fa-table"></i> {{ $title_table }}</h5>

            <div class="ibox-tools">
              <span class="form-inline">
                <div class="form-group">
                  <label for="fecha_jqgrid" class="sr-only">Fecha</label>
                  <input type="text" placeholder="año-mes-día" id="fecha_jqgrid" class="form-control input-sm" style="width: 106px;" value="{{ date("Y-m-d") }}" data-mask="9999-99-99" onkeydown="return false;">
                </div>
              </span>

              {{-- <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-wrench"></i>
              </a>
              <ul class="dropdown-menu dropdown-user">
                <li>
                  <a href="#">Config option 1</a>
                </li>
                <li>
                  <a href="#">Config option 2</a>
                </li>
              </ul> --}}

              <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
              </a>
            </div>
          </div>

          <div class="ibox-content" style="padding: 0px 0px 0px 0px;">
            <div class="jqGrid_wrapper">
              <table id="jqgrid1"></table>
              <div id="pjqgrid1"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- === MODAL === -->

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

  <!-- Input Mask-->
    <script src="{{ asset('inspinia_v27/js/plugins/jasny/jasny-bootstrap.min.js') }}"></script>

  <!-- Data picker -->
    <script src="{{ asset('inspinia_v27/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>

  <!-- OrgChart -->
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/html2canvas.min.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/jspdf.min.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/jquery.orgchart.min.js') }}"></script>

  <!-- DROPZONE -->
    <script src="{{ asset('inspinia_v27/js/plugins/dropzone/dropzone.js') }}"></script>
@endsection

@section('js')
    @include('rrhh.asistencia.asistencia_js')
@endsection
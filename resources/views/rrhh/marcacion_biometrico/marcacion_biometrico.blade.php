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
          </div>

          <div class="modal-body">
            <div class="row">
              <form id="form_1" role="form" action="#">
                <div class="col-sm-12">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="fecha_del_1">Fecha del</label>
                        <input type="text" class="form-control" id="fecha_del_1" name="fecha_del" placeholder="año-mes-día" data-mask="9999-99-99" onkeydown="return false;" value="{{ date('Y-m-d') }}">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="fecha_al_1">Fecha al</label>
                        <input type="text" class="form-control" id="fecha_al_1" name="fecha_al" placeholder="año-mes-día" data-mask="9999-99-99" onkeydown="return false;" value="{{ date('Y-m-d') }}">
                      </div>
                    </div>
                  </div>

                  <div id="persona_id_div_1" class="form-group">
                    <label for="persona_id_1">Persona</label>
                    <select name="persona_id" id="persona_id_1" data-placeholder="Persona" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-info" onclick="utilitarios([13]);">Limpiar formulario</button>
            <button type="button" class="btn btn-primary" onclick="utilitarios([12]);">Obtener asistencias</button>
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>

    <div id="modal_2" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>

            <h4 class="modal-title">
              <span id="modal_2_title"></span>
            </h4>
          </div>

          <div class="modal-body">
            <div class="row">
              <form id="form_2" role="form" action="#">
                <div class="col-sm-12">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="fecha_del_2">Fecha del</label>
                        <input type="text" class="form-control" id="fecha_del_2" name="fecha_del" placeholder="año-mes-día" data-mask="9999-99-99" onkeydown="return false;" value="{{ date('Y-m-d') }}">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="fecha_al_2">Fecha al</label>
                        <input type="text" class="form-control" id="fecha_al_2" name="fecha_al" placeholder="año-mes-día" data-mask="9999-99-99" onkeydown="return false;" value="{{ date('Y-m-d') }}">
                      </div>
                    </div>
                  </div>

                  <div id="persona_id_div_2" class="form-group">
                    <label for="persona_id_2">Persona</label>
                    <select name="persona_id" id="persona_id_2" data-placeholder="Persona" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-info" onclick="utilitarios([16]);">Limpiar formulario</button>
            <button type="button" class="btn btn-primary" onclick="utilitarios([15]);">Imprimir marcaciones</button>
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
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

  <!-- Input Mask-->
    <script src="{{ asset('inspinia_v27/js/plugins/jasny/jasny-bootstrap.min.js') }}"></script>

  <!-- Data picker -->
    <script src="{{ asset('inspinia_v27/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>

  <!-- OrgChart -->
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/html2canvas.min.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/jspdf.min.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/jquery.orgchart.min.js') }}"></script>
@endsection

@section('js')
    @include('rrhh.marcacion_biometrico.marcacion_biometrico_js')
@endsection
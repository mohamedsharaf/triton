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

    <link href="{!! asset('inspinia_v27/css/plugins/clockpicker/clockpicker.css') !!}" rel="stylesheet">

    <link href="{!! asset('inspinia_v27/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') !!}" rel="stylesheet">

    <link href="{!! asset('inspinia_v27/css/plugins/orgchart/jquery.orgchart.min.css') !!}" rel="stylesheet">

  <!-- Dropzone -->
    <link href="{!! asset('inspinia_v27/css/plugins/dropzone/basic.css') !!}" rel="stylesheet">
    <link href="{!! asset('inspinia_v27/css/plugins/dropzone/dropzone.css') !!}" rel="stylesheet">

@endsection

@section('css')
    <style type="text/css">
      .clockpicker-popover {
          z-index: 999999;
      }
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
            <div class="tabs-container">
              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab-1"><i class="fa fa-clock-o"></i>Permiso por horas</a></li>
                <li class=""><a data-toggle="tab" href="#tab-2"><i class="fa fa-calendar"></i>Permiso por días</a></li>
              </ul>
              <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                  <div class="panel-body">
                    <div class="jqGrid_wrapper">
                      <table id="jqgrid1"></table>
                      <div id="pjqgrid1"></div>
                    </div>
                  </div>
                </div>
                <div id="tab-2" class="tab-pane">
                  <div class="panel-body">
                    <div class="jqGrid_wrapper">
                      <table id="jqgrid2"></table>
                      <div id="pjqgrid2"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
  </div>

  <!-- === MODAL === -->
    <div id="modal_1" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg">
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
                <input type="hidden" id="id_salida" name="id" value=""/>
                <input type="hidden" id="tipo1" name="tipo" value="1"/>
                {{ csrf_field() }}
                <div class="col-sm-12">
                  <div id="tipo_salida_id_div" class="form-group">
                    <label for="tipo_salida_id">Tipo de papeleta</label>
                    <select name="tipo_salida_id" id="tipo_salida_id" data-placeholder="Tipo de papeleta" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div class="row">
                    <div class="col-sm-6">
                      <div id="tipo_salida_div" class="form-group">
                        <label for="tipo_salida">Tipo de salida</label>
                        <select name="tipo_salida" id="tipo_salida" data-placeholder="Tipo de salida" multiple="multiple" style="width: 100%;">
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código">
                      </div>
                    </div>
                  </div>

                  <div id="funcionario_id_superior_div" class="form-group">
                    <label for="funcionario_id_superior">Inmediato superior</label>
                    <select name="funcionario_id_superior" id="funcionario_id_superior" data-placeholder="Inmediato superior" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="destino">Destino</label>
                    <input type="text" class="form-control" id="destino" name="destino" placeholder="Destino">
                  </div>

                  <div class="form-group">
                    <label for="motivo">Motivo</label>
                    <input type="text" class="form-control" id="motivo" name="motivo" placeholder="Motivo">
                  </div>

                  <div class="row">
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="f_salida">Fecha de salida</label>
                        <input type="text" class="form-control" id="f_salida" name="f_salida" placeholder="año-mes-día" data-mask="9999-99-99">
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="h_salida">Hora de salida</label>
                        <input type="text" class="form-control" id="h_salida" name="h_salida" placeholder="Hora de salida" data-mask="99:99">
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="h_retorno">Hora de retorno</label>
                        <input type="text" class="form-control" id="h_retorno" name="h_retorno" placeholder="Hora de retorno" data-mask="99:99">
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label>Salida</label>
                        <div>
                          <div class="radio radio-primary radio-inline">
                            <input type="radio" id="con_sin_retorno_1_id" class="con_sin_retorno_class" name="con_sin_retorno" value="1" checked="checked">
                            <label class="text-success" for="con_sin_retorno_1_id"> {{ $con_sin_retorno['1'] }} </label>
                          </div>
                          <div class="radio radio-danger radio-inline">
                            <input type="radio" id="con_sin_retorno_2_id" class="con_sin_retorno_class" name="con_sin_retorno" value="2">
                            <label class="text-danger" for="con_sin_retorno_2_id"> {{ $con_sin_retorno['2'] }} </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-info" onclick="utilitarios([14]);">Limpiar formulario</button>
            <button type="button" class="btn btn-primary" onclick="utilitarios([15]);">Guardar</button>
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

  <!-- Clock picker -->
    <script src="{{ asset('inspinia_v27/js/plugins/clockpicker/clockpicker.js') }}"></script>

  <!-- OrgChart -->
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/html2canvas.min.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/jspdf.min.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/orgchart/jquery.orgchart.min.js') }}"></script>

  <!-- DROPZONE -->
    <script src="{{ asset('inspinia_v27/js/plugins/dropzone/dropzone.js') }}"></script>
@endsection

@section('js')
    @include('rrhh.solicitud_salida.solicitud_salida_js')
@endsection
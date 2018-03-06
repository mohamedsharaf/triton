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
              <div class="jqGrid_wrapper">
                    <table id="jqgrid1"></table>
                    <div id="pjqgrid1"></div>
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
                <input type="hidden" id="id_funcionario" name="id" value=""/>
                <input type="hidden" id="cargo_id" name="cargo_id" value=""/>
                <input type="hidden" id="tipo_cargo_id" name="tipo_cargo_id" value=""/>
                <input type="hidden" id="tipo1" name="tipo" value="1"/>
                {{ csrf_field() }}
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Situación</label>
                    <div>
                      <div class="radio radio-primary radio-inline">
                        <input type="radio" id="situacion_1_id" class="situacion_class" name="situacion" value="1" checked="checked">
                        <label class="text-success" for="situacion_1_id"> {{ $situacion_array['1'] }} </label>
                      </div>
                      <div class="radio radio-danger radio-inline">
                        <input type="radio" id="situacion_2_id" class="situacion_class" name="situacion" value="2">
                        <label class="text-danger" for="situacion_2_id"> {{ $situacion_array['2'] }} </label>
                      </div>
                    </div>
                  </div>

                  <div id="persona_id_div" class="form-group">
                    <label for="persona_id">Funcionario</label>
                    <select name="persona_id" id="persona_id" data-placeholder="Funcionario" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                          <label for="f_ingreso">Fecha de ingreso</label>
                          <input type="text" class="form-control" id="f_ingreso" name="f_ingreso" placeholder="año-mes-día" data-mask="9999-99-99">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                          <label for="f_salida">Fecha de salida</label>
                          <input type="text" class="form-control" id="f_salida" name="f_salida" placeholder="año-mes-día" data-mask="9999-99-99">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                          <label for="sueldo">Sueldo</label>
                          <input type="text" class="form-control" id="sueldo" name="sueldo" placeholder="Sueldo">
                        </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <input type="text" class="form-control" id="observaciones" name="observaciones" placeholder="Observaciones">
                  </div>

                  <h3 class="text-success">
                    <b>UBICACION DEL FUNCIONARIO</b>
                  </h3>

                  <div id="lugar_dependencia_id_funcionario_div" class="form-group">
                    <label for="lugar_dependencia_id_funcionario">Lugar de dependencia</label>
                    <select name="lugar_dependencia_id_funcionario" id="lugar_dependencia_id_funcionario" data-placeholder="Lugar de dependencia" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div id="unidad_desconcentrada_id_div" class="form-group">
                    <label for="unidad_desconcentrada_id">Unidad desconcentrada</label>
                    <select name="unidad_desconcentrada_id" id="unidad_desconcentrada_id" data-placeholder="Unidad desconcentrada" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <h3 class="text-success">
                    <b>CONTROL DE ASISTENCIA</b>
                  </h3>

                  <div id="horario_id_1_div" class="form-group">
                    <label for="horario_id_1">Horario 1</label>
                    <select name="horario_id_1" id="horario_id_1" data-placeholder="Horario 1" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div id="horario_id_2_div" class="form-group">
                    <label for="horario_id_2">Horario 2</label>
                    <select name="horario_id_2" id="horario_id_2" data-placeholder="Horario 2" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <h3 class="text-success">
                    <b>UBICACION DEL CARGO</b>
                  </h3>

                  <div id="lugar_dependencia_id_cargo_div" class="form-group">
                    <label for="lugar_dependencia_id_cargo">Lugar de dependencia</label>
                    <select name="lugar_dependencia_id_cargo" id="lugar_dependencia_id_cargo" data-placeholder="Lugar de dependencia" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div id="auo_id_div" class="form-group">
                    <label for="auo_id">Área o unidad organizacional</label>
                    <select name="auo_id" id="auo_id" data-placeholder="Área o unidad organizacional" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div id="cargo_id_div" class="form-group">
                    <label for="cargo_id_d">Cargo</label>
                    <select name="cargo_id_d" id="cargo_id_d" data-placeholder="Cargo" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="utilitarios([15]);">Guardar</button>
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
              Subir documento
            </h4>

            <small class="font-bold" id="modal_2_subtitle">
            </small>
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <form action="#" class="dropzone" id="dropzoneForm_1">
                  <input type="hidden" id="id_funcionario_2" name="id" value=""/>
                  <div class="fallback">
                    <input name="file" type="file"/>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="modal_3" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-xlg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>

            <h4 class="modal-title">
              Marcaciones registradas
            </h4>
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <div id="div_jqgrid2" class="jqGrid_wrapper">
                  <table id="jqgrid2"></table>
                  <div id="pjqgrid2"></div>
                </div>
              </div>
            </div>

            <div class="row">
              <form id="form_3" role="form" action="#">
                <input type="hidden" id="persona_id_3" name="persona_id" value=""/>
                <div class="col-sm-12">
                  <br>
                  <h3 class="text-success">
                    <b>FILTRAR PARA GENERAR EL EXCEL</b>
                  </h3>

                  <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label for="f_marcacion_del_3">Marcación del</label>
                          <input type="text" class="form-control" id="f_marcacion_del_3" name="f_marcacion_del" placeholder="año-mes-día" data-mask="9999-99-99">
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                          <label for="f_marcacion_al_3">Marcación al</label>
                          <input type="text" class="form-control" id="f_marcacion_al_3" name="f_marcacion_al" placeholder="año-mes-día" data-mask="9999-99-99">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div id="lugar_dependencia_id_3_div" class="form-group">
                          <label for="lugar_dependencia_id_3">Lugar de dependencia</label>
                          <select name="lugar_dependencia_id_3" id="lugar_dependencia_id_3" data-placeholder="Lugar de dependencia" multiple="multiple" style="width: 100%;">
                          </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div id="unidad_desconcentrada_id_3_div" class="form-group">
                          <label for="unidad_desconcentrada_id_3">Unidad desconcentrada</label>
                          <select name="unidad_desconcentrada_id_3" id="unidad_desconcentrada_id_3" data-placeholder="Unidad desconcentrada" multiple="multiple" style="width: 100%;">
                          </select>
                        </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-info" onclick="utilitarios([25]);">Limpiar formulario</button>
            <button type="button" class="btn btn-primary" onclick="utilitarios([24]);">Excel</button>
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>

    <div id="modal_4" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>

            <h4 class="modal-title">
              Generar lista de funcionarios
            </h4>
          </div>

          <div class="modal-body">
            <div class="row">
              <form id="form_10" role="form" action="#">
                <div class="col-sm-12">
                  <div id="lugar_dependencia_id_r_div" class="form-group">
                    <label for="lugar_dependencia_id_r">Lugar de dependencia</label>
                    <select name="lugar_dependencia_id_r" id="lugar_dependencia_id_r" data-placeholder="Lugar de dependencia" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-success" onclick="utilitarios([27]);">Excel</button>
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
    @include('rrhh.funcionario.funcionario_js')
@endsection
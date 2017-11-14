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

        /*.modal-xlg {
            width: 90%;
        }*/
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
            </div>

            <div class="modal-body">
              <div class="row">
                <form id="form_1" role="form" action="#">
                  <input type="hidden" id="biometrico_id" name="id" value=""/>
                  <input type="hidden" id="tipo1" name="tipo" value="1"/>
                  {{ csrf_field() }}
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Estado</label>
                      <div>
                          <div class="radio radio-primary radio-inline">
                            <input type="radio" id="estado_1_id" class="estado_class" name="estado" value="1" checked="checked">
                            <label class="text-success" for="estado_1_id"> {{ $estado_array['1'] }} </label>
                          </div>
                          <div class="radio radio-danger radio-inline">
                              <input type="radio" id="estado_2_id" class="estado_class" name="estado" value="2">
                              <label class="text-danger" for="estado_2_id"> {{ $estado_array['2'] }} </label>
                          </div>
                          <div class="radio radio-warning radio-inline">
                              <input type="radio" id="estado_3_id" class="estado_class" name="estado" value="3">
                              <label class="text-warning" for="estado_3_id"> {{ $estado_array['3'] }} </label>
                          </div>
                      </div>
                    </div>

                    <div id="lugar_dependencia_id_div" class="form-group">
                      <label for="lugar_dependencia_id">Lugares de dependencia</label>
                      <select name="lugar_dependencia_id" id="lugar_dependencia_id" data-placeholder="Lugar de dependencia" multiple="multiple" style="width: 100%;">
                      </select>
                    </div>

                    <div id="unidad_desconcentrada_id_div" class="form-group">
                      <label for="unidad_desconcentrada_id">Unidad desconcentrada</label>
                      <select name="unidad_desconcentrada_id" id="unidad_desconcentrada_id" data-placeholder="Unidad desconcentrada" multiple="multiple" style="width: 100%;">
                      </select>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="codigo_af">Código activo fijo</label>
                              <input type="text" class="form-control" id="codigo_af" name="codigo_af" placeholder="Código activo fijo" data-mask="MP-999999">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="ip">IP</label>
                              <input type="text" class="form-control" id="ip" name="ip" placeholder="Confirmar contraseña">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="internal_id">ID usuario</label>
                              <input type="text" class="form-control" id="internal_id" name="internal_id" placeholder="ID usuario" value="1">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="com_key">Llave COM</label>
                              <input type="text" class="form-control" id="com_key" name="com_key" placeholder="Llave COM">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="soap_port">Puerto SOAP</label>
                              <input type="text" class="form-control" id="soap_port" name="soap_port" placeholder="Puerto SOAP" value="80">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="udp_port">Puerto UDP</label>
                              <input type="text" class="form-control" id="udp_port" name="udp_port" placeholder="Puerto UDP" value="4370">
                            </div>
                        </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <div class="modal-footer">
              <!-- <button type ="button" class ="btn btn-warning pull-left" onclick="utilitarios([17]);">Probar conexión</button> -->

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

  <!-- Input Mask-->
    <script src="{{ asset('inspinia_v27/js/plugins/jasny/jasny-bootstrap.min.js') }}"></script>

  <!-- Data picker -->
    <script src="{{ asset('inspinia_v27/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('inspinia_v27/js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>

  <!-- DROPZONE -->
    <script src="{{ asset('inspinia_v27/js/plugins/dropzone/dropzone.js') }}"></script>

@endsection

@section('js')
    @include('rrhh.biometrico.biometrico_js')
@endsection

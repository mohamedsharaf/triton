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

        .modal-xlg {
            width: 90%;
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
        <div class="modal-dialog modal-xlg">
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
                <form id="form_1" role="form" action="#">
                  <input type="hidden" id="persona_id" name="id" value=""/>
                  <input type="hidden" id="tipo1" name="tipo" value="1"/>
                  {{ csrf_field() }}
                  <div class="col-sm-6">
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
                        </div>
                      </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label>Cédula de Identidad</label>
                              <input type="text" class="form-control" id="n_documento" name="n_documento" placeholder="Cédula de Identidad">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label>Complemento</label>
                              <input type="text" class="form-control" id="n_documento_1" name="n_documento_1" placeholder="Complemento">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label>Nombre(s)</label>
                              <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre(s)">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label>Apellido paterno</label>
                              <input type="text" class="form-control" id="ap_paterno" name="ap_paterno" placeholder="Apellido paterno">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label>Apellido materno</label>
                              <input type="text" class="form-control" id="ap_materno" name="ap_materno" placeholder="Apellido materno">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label>Apellido esposo</label>
                              <input type="text" class="form-control" id="ap_esposo" name="ap_esposo" placeholder="Apellido esposo">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label>Fecha de nacimiento</label>
                              <input type="text" class="form-control" id="f_nacimiento" name="f_nacimiento" placeholder="año-mes-día" data-mask="9999-99-99">
                            </div>
                        </div>
                        <div id="estado_civil_div" class="col-sm-6">
                            <div class="form-group">
                              <label>Estado civil</label>
                              <select name="estado_civil" id="estado_civil" data-placeholder="Estado civil" multiple="multiple" style="width: 100%;">
                              </select>
                            </div>
                        </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                      <div class="form-group">
                        <label>Sexo</label>
                        <div>
                            <div class="radio radio-info radio-inline">
                              <input type="radio" id="sexo_f_id" class="sexo_class" name="sexo" value="F" checked="checked">
                              <label class="text-info" for="sexo_f_id"> {{ $sexo_array['F'] }} </label>
                            </div>
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" id="sexo_m_id" class="sexo_class" name="sexo" value="M">
                                <label class="text-success" for="sexo_m_id"> {{ $sexo_array['M'] }} </label>
                            </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label>Domicilio</label>
                        <input type="text" class="form-control" id="domicilio" name="domicilio" placeholder="Domicilio (Zona, Barrio, Avenida o Calle y Número)">
                      </div>

                      <div class="row">
                          <div class="col-sm-6">
                              <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
                              </div>
                          </div>
                          <div id="estado_civil_div" class="col-sm-6">
                              <div class="form-group">
                                <label>Celular</label>
                                <input type="text" class="form-control" id="celular" name="celular" placeholder="Celular" data-mask="99999999">
                              </div>
                          </div>
                      </div>

                      <div id="municipio_id_nacimiento_div" class="form-group">
                        <label>Lugar de nacimiento</label>
                        <select name="municipio_id_nacimiento" id="municipio_id_nacimiento" data-placeholder="Lugar de nacimiento" multiple="multiple" style="width: 100%;">
                        </select>
                      </div>

                      <div id="municipio_id_residencia_div" class="form-group">
                        <label>Residencia actual</label>
                        <select name="municipio_id_residencia" id="municipio_id_residencia" data-placeholder="Residencia actual" multiple="multiple" style="width: 100%;">
                        </select>
                      </div>
                  </div>
                </form>
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

    <!-- Input Mask-->
        <script src="{{ asset('inspinia_v27/js/plugins/jasny/jasny-bootstrap.min.js') }}"></script>

    <!-- Data picker -->
        <script src="{{ asset('inspinia_v27/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('inspinia_v27/js/plugins/datapicker/bootstrap-datepicker.es.min.js') }}"></script>

@endsection

@section('js')
    @include('rrhh.persona.persona_js')
@endsection

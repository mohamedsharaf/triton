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

              <!-- <small class="font-bold">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</small> -->
            </div>

            <div class="modal-body">
              <div class="row">
                <div class="col-sm-6">
                  <p class="text-center">
                    <img id="image_user" src="{!! asset('image/logo/user_default_1.png') !!}" class="img-thumbnail" alt="image" style="max-height: 150px;">
                  </p>
                </div>

                <div class="col-sm-6">
                  <form action="#" class="dropzone" id="dropzoneForm_1">
                    <div class="fallback">
                      <input name="file" type="file"/>
                    </div>
                  </form>
                </div>
              </div>

              <br>

              <div class="row">
                <form id="form_1" role="form" action="#">
                  <input type="hidden" id="usuario_id" name="id" value=""/>
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
                      </div>
                    </div>

                    <div id="persona_id_div" class="form-group">
                      <label for="persona_id">Persona</label>
                      <select name="persona_id" id="persona_id" data-placeholder="C.I. - Ap. paterno, Ap. materno, nombres" multiple="multiple" style="width: 100%;">
                      </select>
                    </div>

                    <div id="i4_funcionario_id_div" class="form-group">
                      <label for="i4_funcionario_id">Funcionarios i4</label>
                      <select name="i4_funcionario_id" id="i4_funcionario_id" data-placeholder="C.I. - Ap. paterno, Ap. materno, nombres" multiple="multiple" style="width: 100%;">
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="email">Correo electrónico</label>
                      <input type="text" class="form-control" id="email" name="email" placeholder="ejemplo@direccion.com">
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="password">Contraseña</label>
                              <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                              <label for="password_c">Confirmar contraseña</label>
                              <input type="password" class="form-control" id="password_c" name="password_c" placeholder="Confirmar contraseña">
                            </div>
                        </div>
                    </div>

                    <div id="rol_id_div" class="form-group">
                      <label for="rol_id">Rol</label>
                      <select name="rol_id" id="rol_id" data-placeholder="Rol del usuario" multiple="multiple" style="width: 100%;">
                      </select>
                    </div>

                    <div id="lugar_dependencia_div" class="form-group">
                      <label for="lugar_dependencia">Lugares de dependencia</label>
                      <select name="lugar_dependencia[]" id="lugar_dependencia" data-placeholder="Lugar de dependencia" multiple="multiple" style="width: 100%;">
                      </select>
                    </div>

                    <div class="form-group">
                      <div>
                          <div class="checkbox checkbox-warning">
                            <input type="checkbox" id="enviar_mail" name="enviar_mail" value="1">
                            <label class="text-warning" for="enviar_mail"> Enviar correo electrónico</label>
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
    @include('seguridad.usuario.usuario_js')
@endsection

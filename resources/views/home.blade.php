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
    </style>
@endsection

@section('content')
  {{-- <div class="row wrapper border-bottom white-bg page-heading">
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
  </div> --}}

  <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
      <div class="col-lg-12">
        <div class="tabs-container">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab-1"><i class="fa fa-address-card"></i>Información personal</a></li>
            <li class=""><a data-toggle="tab" href="#tab-2"><i class="fa fa-lock"></i>Cambiar contraseña</a></li>
          </ul>
          <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
              <div class="panel-body">
                <div class="row">
                  <div class="col-lg-12">
                    <p id="image_user_p" class="text-center">
                        <img id="image_user" src="{!! asset('image/logo/user_default_1.png') !!}" class="img-thumbnail" alt="image" style="max-height: 200px;">
                    </p>

                    <form id="dropzoneForm_1" action="#" class="dropzone" style="display: none;">
                      <div class="fallback">
                        <input name="file" type="file"/>
                      </div>
                    </form>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-12">
                    <p class="text-center">
                      <button type="button" class="btn btn-warning" onclick="utilitarios([17]);">
                        <i class="fa fa-upload"></i>
                        <strong>Subir fotografía</strong>
                      </button>
                    </p>
                  </div>
                </div>

                @if($persona_array_sw)
                  <div class="row">
                    <form id="form_1" role="form" action="#">
                      <input type="hidden" id="tipo1" name="tipo" value="1"/>
                      {{ csrf_field() }}

                      <div class="col-sm-6">
                        <h3>
                          <b>DATOS PERSONALES</b>
                        </h3>
                      </div>

                      <div class="col-sm-6">
                        <button type="button" class="btn btn-primary pull-right" onclick="utilitarios([15]);">
                          <i class="fa fa-floppy-o"></i>
                          <strong>Guardar</strong>
                        </button>
                      </div>

                      <br>

                      <div class="hr-line-dashed"></div>

                      <div class="col-sm-12">
                        <div class="row">
                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="n_documento">Cédula de Identidad</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-id-card"></i></span><input type="text" class="form-control" id="n_documento" name="n_documento" placeholder="Cédula de Identidad" disabled="disabled">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="n_documento_1">Complemento</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-id-card"></i></span><input type="text" class="form-control" id="n_documento_1" name="n_documento_1" placeholder="Complemento" disabled="disabled">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="f_nacimiento">Fecha de nacimiento</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="f_nacimiento" name="f_nacimiento" placeholder="año-mes-día" data-mask="9999-99-99">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-3">
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
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="nombre">Nombre(s)</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span><input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre(s)">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="ap_paterno">Apellido paterno</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span><input type="text" class="form-control" id="ap_paterno" name="ap_paterno" placeholder="Apellido paterno">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="ap_materno">Apellido materno</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span><input type="text" class="form-control" id="ap_materno" name="ap_materno" placeholder="Apellido materno">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-3">
                            <div class="form-group">
                              <label for="ap_esposo">Apellido esposo</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span><input type="text" class="form-control" id="ap_esposo" name="ap_esposo" placeholder="Apellido esposo">
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div id="estado_civil_div" class="col-sm-3">
                            <div class="form-group">
                              <label>Estado civil</label>
                              <select name="estado_civil" id="estado_civil" data-placeholder="Estado civil" multiple="multiple" style="width: 100%;">
                              </select>
                            </div>
                          </div>

                          <div id="municipio_id_nacimiento_div" class="col-sm-9">
                            <div class="form-group">
                              <label>Lugar de nacimiento</label>
                              <select name="municipio_id_nacimiento" id="municipio_id_nacimiento" data-placeholder="Lugar de nacimiento" multiple="multiple" style="width: 100%;">
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>

                      <h3>
                        <b>DATOS DE CONTACTO</b>
                      </h3>

                      <div class="hr-line-dashed"></div>

                      <div class="col-sm-12">
                        <div class="form-group">
                          <label for="domicilio">Domicilio</label>
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-map-marker"></i></span><input type="text" class="form-control" id="domicilio" name="domicilio" placeholder="Domicilio (Zona, Barrio, Avenida o Calle y Número)">
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-sm-2">
                            <div class="form-group">
                              <label for="telefono">Teléfono</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-phone"></i></span><input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
                              </div>
                            </div>
                          </div>

                          <div class="col-sm-2">
                            <div class="form-group">
                              <label>Celular</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-mobile"></i></span><input type="text" class="form-control" id="celular" name="celular" placeholder="Celular" data-mask="99999999">
                              </div>
                            </div>
                          </div>

                          <div id="municipio_id_residencia_div" class="col-sm-8">
                            <div class="form-group">
                              <label>Residencia actual</label>
                              <select name="municipio_id_residencia" id="municipio_id_residencia" data-placeholder="Residencia actual" multiple="multiple" style="width: 100%;">
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                @endif

              </div>
            </div>
            <div id="tab-2" class="tab-pane">
              <div class="panel-body">
                <strong>Donec quam felis</strong>

                <p>Thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects
                    and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>

                <p>I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite
                    sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment; and yet.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="" class="row">
      <div class="col-lg-12">
        <div class="alert alert-success">
          <h3>Obligatoriedad en el Registro de Ingreso y Salida (Art. 20)</h3>
          La omisión en el registro de entrada y/o salida será sancionado con el descuento de medio día de haber o según corresponda.
        </div>

        <div class="alert alert-warning">
          <h3>Atrasos y Multas (Art. 23 a).)</h3>
          <p>De <span class="badge badge-danger">21</span> a <span class="badge badge-danger">30</span> minutos al mes: <b>Medio día de haber</b>.</p>
          <p>De <span class="badge badge-danger">31</span> a <span class="badge badge-danger">50</span> minutos al mes: <b>Un día de haber</b>.</p>
          <p>De <span class="badge badge-danger">51</span> a <span class="badge badge-danger">70</span> minutos al mes: <b>Dos días de haber</b>.</p>
          <p>De <span class="badge badge-danger">71</span> a <span class="badge badge-danger">90</span> minutos al mes: <b>Tres días de haber</b>.</p>
          <p>De <span class="badge badge-danger">91</span> a <span class="badge badge-danger">120</span> minutos al mes: <b>Cuatro días de haber</b>.</p>
          <p>Más de <span class="badge badge-danger">120</span> minutos al mes: <b>Cinco días de haber y llamada de atención por escrito</b>.</p>
          <p><i class="fa fa-eye"></i> Tres llamadas de atención por escrito durante una misma gestión, se remitirán antecedentes para inicio de proceso interno.</p>
        </div>

        <div class="alert alert-danger">
          <h3>Inasistencia (Art. 23 c).)</h3>
          <p>Medio día de falta injustificada, <b>un día de haber</b> de sanción.</p>
          <p>Un día de falta o dos medios días alternos de falta injustificada durante el mes, <b>dos días de haber</b> de sanción.</p>
          <p>Un día y medio de falta o tres medios días alternos de falta injustificada durante el mes, <b>tres días de haber</b> de sanción.</p>
          <p>Dos días de falta o cuatro medios días alternos de falta injustificada en el mes, <b>cuatro días de haber</b> de sanción.</p>
          <p>Dos días y medio de falta o cinco medios días alternos de falta injustificada en el mes, <b>cinco días de haber</b> de sanción.</p>
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
  @include('home_js')
@endsection

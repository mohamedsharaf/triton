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
            @if($sw_asistencia)
              <li class=""><a data-toggle="tab" href="#tab-3"><i class="fa fa-check-square"></i>Asistencias</a></li>
              <li class=""><a data-toggle="tab" href="#tab-5"><i class="fa fa-newspaper-o"></i>Papeleta particular</a></li>
            @endif
            @if($sw_horario)
              <li class=""><a data-toggle="tab" href="#tab-4"><i class="fa fa-clock-o"></i>Mi horario</a></li>
            @endif
          </ul>
          <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
              <div class="panel-body">
                <div class="row">
                  <div class="col-lg-12">
                    <p class="text-right">
                      <a href="https://docs.google.com/document/d/1B_HWKJvku10m8sYdhCIgp9bXbOH9OnV7C0Y7_TwpZUY/edit?usp=sharing" class="btn btn-warning btn-xs" target="_blank">
                        {{-- <i class="fa fa-upload"></i> --}}
                        <strong>Manual</strong>
                      </a>
                    </p>
                  </div>
                </div>

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
                      <button type="button" class="btn btn-info" onclick="utilitarios([17]);">
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
                <div class="row">
                  <div class="col-lg-12">
                    <p class="text-right">
                      <a href="https://drive.google.com/open?id=1xZBuQMIKahkHzycp9IqHSkWRIKMSZ-Lm" class="btn btn-warning btn-xs" target="_blank">
                        <strong>Manual</strong>
                      </a>
                    </p>
                  </div>
                </div>

                <div class="row">
                  <form id="form_2" role="form" action="#">
                    <input type="hidden" id="tipo1" name="tipo" value="3"/>
                    {{ csrf_field() }}

                    <div class="col-sm-12">
                      <h3>
                        <b>ROL Y CORREO ELECTRONICO</b>
                      </h3>
                    </div>

                    <br>

                    <div class="hr-line-dashed"></div>

                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="rol">Su rol es</label>
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-bars"></i></span><input type="text" class="form-control" id="rol" name="rol" placeholder="Su rol es" disabled="disabled">
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-envelope"></i></span><input type="text" class="form-control" id="email" name="email" placeholder="Correo electrónico" disabled="disabled">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="alert alert-danger">
                        <p>Solicite el cambio de ROL o CORREO ELECTRONICO con el ENCARGADO DE INFORMATICA.</p>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <h3>
                        <b>CAMBIAR CONTRASEÑA</b>
                      </h3>
                    </div>

                    <div class="col-sm-6">
                      <button type="button" class="btn btn-primary pull-right" onclick="utilitarios([20]);">
                        <i class="fa fa-floppy-o"></i>
                        <strong>Guardar</strong>
                      </button>
                    </div>

                    <br>

                    <div class="hr-line-dashed"></div>

                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="a_contrasenia">Contraseña actual</label>
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-key"></i></span><input type="password" class="form-control" id="a_contrasenia" name="a_contrasenia" placeholder="Contraseña actual">
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="contrasenia">Nueva contraseña</label>
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-key"></i></span><input type="password" class="form-control" id="contrasenia" name="contrasenia" placeholder="Nueva contraseña">
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="c_contrasenia">Confirmar nueva contraseña</label>
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-key"></i></span><input type="password" class="form-control" id="c_contrasenia" name="c_contrasenia" placeholder="Confirmar nueva contraseña">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            @if($sw_asistencia)
              <div id="tab-3" class="tab-pane">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-lg-12">
                      <p class="text-right">
                        <a href="https://drive.google.com/open?id=1xZBuQMIKahkHzycp9IqHSkWRIKMSZ-Lm" class="btn btn-danger btn-xs" target="_blank">
                          <strong>Reglamento</strong>
                        </a>

                        <a href="https://drive.google.com/open?id=1xZBuQMIKahkHzycp9IqHSkWRIKMSZ-Lm" class="btn btn-warning btn-xs" target="_blank">
                          <strong>Manual</strong>
                        </a>
                      </p>
                    </div>
                  </div>

                  <div class="jqGrid_wrapper">
                    <table id="jqgrid1"></table>
                    <div id="pjqgrid1"></div>
                  </div>

                  <br/>

                  <div id="" class="row">
                    <div class="col-lg-12">
                      <div class="alert alert-warning">
                        <h3>ARTÍCULO 24. OBLIGATORIEDAD EN EL REGISTRO DE INGRESO Y SALIDA</h3>
                        <p>Toda servidora o servidor del Ministerio Público, tiene la obligación de registrar la hora de ingreso y de salida en el sistema biométrico o el medio habilitado para tal efecto, <b>cualquier omisión injustificada en el registro de asistencia al ingreso y/o salida será sancionado con el descuento de medio día de haber o según corresponda</b>.</p>
                      </div>

                      <div class="alert alert-danger">
                        <h3>ARTÍCULO 27. ATRASOS, INASISTENCIA Y ABANDONO</h3>
                        <p><b>a) ATRASOS Y MULTAS.-</b> Se considera atraso al registro que efectúa la o el servidor público al ingreso a la fuente laboral después del límite permitido como “tolerancia”; se otorgará excepcionalmente cinco minutos de tolerancia en los horarios de ingreso; pasado este límite el servidor será sancionado pecuniariamente de acuerdo a la siguiente escala:</p>
                        <ul>
                          <li>De <span class="badge badge-primary">21</span> a <span class="badge badge-primary">30</span> minutos al mes: <b>Medio día de haber</b>.</li>
                          <li>De <span class="badge badge-primary">31</span> a <span class="badge badge-primary">50</span> minutos al mes: <b>Un día de haber</b>.</li>
                          <li>De <span class="badge badge-primary">51</span> a <span class="badge badge-primary">70</span> minutos al mes: <b>Dos días de haber</b>.</li>
                          <li>De <span class="badge badge-primary">71</span> a <span class="badge badge-primary">90</span> minutos al mes: <b>Tres días de haber</b>.</li>
                          <li>De <span class="badge badge-primary">91</span> a <span class="badge badge-primary">120</span> minutos al mes: <b>Cuatro días de haber</b>.</li>
                          <li>Más de <span class="badge badge-primary">120</span> minutos al mes: <b>Cinco días de haber y llamada de atención por escrito</b>.</li>
                        </ul>
                        <p><i class="fa fa-eye"></i> Tres llamadas de atención por escrito durante una misma gestión, se remitirán antecedentes para inicio de proceso interno.</p>

                        <br/>

                        <p><b>b)  CÓMPUTO DE ATRASOS.-</b> Cuando una o un servidor público llegue después de los cinco minutos de la hora de ingreso oficial, el cómputo de retraso acumulativo se sumará a partir del horario de ingreso, sin tomar en cuenta los 5 minutos de tolerancia. A este efecto, el período de cómputo de asistencia se realizará desde fecha 16 del mes anterior hasta el 15 del mes en que se procese la planilla respectiva.</p>

                        <br/>

                        <p><b>c) INASISTENCIA.-</b> Cuando una o un servidor público no asistiera a su fuente laboral, sin justificativo alguno, será pasible a las siguientes sanciones:</p>

                        <p>Medio día de falta injustificada, <b>un día de haber</b> de sanción.</li>
                        <ul>
                          <li>Un día de falta o dos medios días alternos de falta injustificada durante el mes, <b>dos días de haber</b> de sanción.</li>
                          <li>Un día y medio de falta o tres medios días alternos de falta injustificada durante el mes, <b>tres días de haber</b> de sanción.</li>
                          <li>Dos días de falta o cuatro medios días alternos de falta injustificada en el mes, <b>cuatro días de haber</b> de sanción.</li>
                          <li>Dos días y medio de falta o cinco medios días alternos de falta injustificada en el mes, <b>cinco días de haber</b> de sanción.</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif

            @if($sw_horario)
              <div id="tab-4" class="tab-pane">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-lg-12">
                      <p class="text-right">
                        <a href="https://drive.google.com/open?id=1xZBuQMIKahkHzycp9IqHSkWRIKMSZ-Lm" class="btn btn-danger btn-xs" target="_blank">
                          <strong>Reglamento</strong>
                        </a>

                        <a href="https://drive.google.com/open?id=1xZBuQMIKahkHzycp9IqHSkWRIKMSZ-Lm" class="btn btn-warning btn-xs" target="_blank">
                          <strong>Manual</strong>
                        </a>
                      </p>
                    </div>
                  </div>

                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th class="text-center" rowspan="2">HORARIO</th>
                        <th class="text-center" rowspan="2">INGRESO</th>
                        <th class="text-center" rowspan="2">SALIDA</th>
                        <th class="text-center" rowspan="2">TOLERANCIA<br>(minutos)</th>
                        <th class="text-center" colspan="2">LIMITE ENTRADA</th>
                        <th class="text-center" colspan="2">LIMITE SALIDA</th>
                      </tr>
                      <tr>
                        <th class="text-center">HORA 1</th>
                        <th class="text-center">HORA 2</th>
                        <th class="text-center">HORA 1</th>
                        <th class="text-center">HORA 2</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="info">
                        <td class="text-center">
                          {{ $funcioario_horario_array['horario_1'] }}
                        </td>
                        <td class="text-center">
                          {{ $funcioario_horario_array['h_ingreso_1'] }}
                        </td>
                        <td class="text-center">
                          {{ $funcioario_horario_array['h_salida_1'] }}
                        </td>
                        <td class="text-center">
                          {{ $funcioario_horario_array['tolerancia_1'] }}
                        </td>
                        <td class="text-center">
                          {{ $funcioario_horario_array['marcacion_ingreso_del_1'] }}
                        </td>
                        <td class="text-center">
                          {{ $funcioario_horario_array['marcacion_ingreso_al_1'] }}
                        </td>
                        <td class="text-center">
                          {{ $funcioario_horario_array['marcacion_salida_del_1'] }}
                        </td>
                        <td class="text-center">
                          {{ $funcioario_horario_array['marcacion_salida_al_1'] }}
                        </td>
                      </tr>
                      @if($funcioario_horario_array['horario_2'] != '')
                        <tr class="success">
                          <td class="text-center">
                            {{ $funcioario_horario_array['horario_2'] }}
                          </td>
                          <td class="text-center">
                            {{ $funcioario_horario_array['h_ingreso_2'] }}
                          </td>
                          <td class="text-center">
                            {{ $funcioario_horario_array['h_salida_2'] }}
                          </td>
                          <td class="text-center">
                            {{ $funcioario_horario_array['tolerancia_2'] }}
                          </td>
                          <td class="text-center">
                            {{ $funcioario_horario_array['marcacion_ingreso_del_2'] }}
                          </td>
                          <td class="text-center">
                            {{ $funcioario_horario_array['marcacion_ingreso_al_2'] }}
                          </td>
                          <td class="text-center">
                            {{ $funcioario_horario_array['marcacion_salida_del_2'] }}
                          </td>
                          <td class="text-center">
                            {{ $funcioario_horario_array['marcacion_salida_al_2'] }}
                          </td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            @endif

            @if($sw_asistencia)
              <div id="tab-5" class="tab-pane">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-lg-12">
                      <p class="text-right">
                        <a href="https://drive.google.com/open?id=1xZBuQMIKahkHzycp9IqHSkWRIKMSZ-Lm" class="btn btn-danger btn-xs" target="_blank">
                          <strong>Reglamento</strong>
                        </a>

                        <a href="https://drive.google.com/open?id=1xZBuQMIKahkHzycp9IqHSkWRIKMSZ-Lm" class="btn btn-warning btn-xs" target="_blank">
                          <strong>Manual</strong>
                        </a>
                      </p>
                    </div>
                  </div>

                  <div class="jqGrid_wrapper_3">
                    <table id="jqgrid3"></table>
                    <div id="pjqgrid3"></div>
                  </div>

                  <br/>

                  <div id="" class="row">
                    <div class="col-lg-12">
                      <div class="alert alert-danger">
                        <h3>ARTÍCULO 34. SALIDAS DE EMERGENCIA (EN HORAS DE OFICINA)</h3>
                        <p>Se concederá permiso al personal para salidas particulares de emergencia y de índole personal hasta un máximo dos horas al mes, caso contrario <b>la ausencia después de estas horas se calculará para el descuento en base a la escala de atrasos vigente</b>, estas horas podrán utilizarse en un mismo día. Para dichas salidas deberá imprescindiblemente llenar la papeleta de salida particular establecida para tal efecto, con autorización del inmediato superior y firmada previamente por el Encargado de Control de Asistencia en la Fiscalía General del Estado o las Jefaturas Administrativas y Financieras en las Fiscalías Departamentales.</p>
                        <p>Queda prohibida la utilización de este tipo de salidas para justificar atrasos y/o abandono de funciones, salvo motivos de fuerza mayor que serán debidamente regularizadas y justificadas hasta 24 horas después.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- === MODAL === -->
    <div id="modal_3" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>

            <h4 class="modal-title">
              <span id="modal_3_title"></span>
            </h4>

            <small class="font-bold" id="modal_3_subtitle">
            </small>
          </div>

          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th class="text-center">UNIDAD DESCONCENTRADA</th>
                  <th class="text-center">LUGAR DE DEPENDENCIA</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td id="td_ud" class="text-center">1</td>
                  <td id="td_ld" class="text-center">1</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div id="modal_4" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>

            <h4 class="modal-title">
              <span id="modal_4_title"></span>
            </h4>

            <small class="font-bold" id="modal_4_subtitle">
            </small>
          </div>

          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th id="th_nombre_4" class="text-center"></th>
                  <th class="text-center">LUGAR DE DEPENDENCIA</th>
                  <th class="text-center">UNIDAD DESCONCENTRADA</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td id="td_nombre_4" class="text-center">1</td>
                  <td id="td_ld_4" class="text-center">1</td>
                  <td id="td_ud_4" class="text-center">1</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div id="modal_5" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
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
          </div>
        </div>
      </div>
    </div>

    <div id="modal_6" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>

            <h4 class="modal-title">
              <span id="modal_6_title"></span>
            </h4>

            <small class="font-bold" id="modal_6_subtitle">
            </small>
          </div>

          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th class="text-center">USUARIO QUE MODIFICO LA ASISTENCIA</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td id="td_persona" class="text-center">1</td>
                </tr>
              </tbody>
            </table>
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

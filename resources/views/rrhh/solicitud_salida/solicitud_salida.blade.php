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
                  <div class="panel-body" style="padding: 0px 0px 0px 0px;">
                    <div class="row">
                        <div class="col-lg-12">
                              <div class="ibox float-e-margins">
                                  <div class="ibox-title" style="padding-top: 12px; padding-bottom: 0px;">
                                      <div class="ibox-tools">
                                          De sus <span class="badge badge-danger">120</span> minutos (2 horas) al mes, ya uso <span class="badge badge-danger">{{ round($n_horas * 60, 0) }}</span> minutos, le quedan <span class="badge badge-danger">{{ round(120 - $n_horas * 60, 0) }}</span> minutos.

                                          @if(in_array(['codigo' => '1002'], $permisos))
                                              <button type="button" class="btn btn-primary btn-xs" onclick="utilitarios([23]);">
                                                  <strong>Nueva solicitud</strong>
                                              </button>
                                          @endif

                                          <select id="anio_filter" data-placeholder="Gestión">
                                              <option value="">Todos</option>
                                          </select>
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

                    <div class="row">
                      <div class="col-lg-12">
                        <div class="alert alert-danger">
                          <h3>Artículo 31. SALIDAS DE EMERGENCIA (EN HORA DE OFICINA)</h3>

                          <p class="text-justify">
                            Se concederá permiso al personal para <strong>salidas particulares de emergencia y de índole personal hasta un máximo dos horas al mes, caso contrario la ausencia después de estas horas se calculara para el descuento en base a la escala de atrasos vigentes, estas horas podrán utilizarse en un mismo día</strong>. Para dichas salidas deberá imprescindiblemente llenar la papeleta de salida particular establecida para tal efecto, con autorización del inmediato superior y firmada previamente por el Encargado de Control de Asistencia en la Fiscalía General del Estado o las Jefaturas Administrativas y Financieras en las Fiscalías Departamentales.
                          </p>

                          <p class="text-justify">
                            <strong>Queda prohibida la utilización de este tipo de salidas para justificar atrasos y/o abandono de funciones</strong>, salvo motivos de fuerza mayor que serán debidamente regularizadas y justificadas hasta 24 horas después.
                          </p>

                          <p class="text-justify">
                            Las salidas para <strong>atención médica y oficiales sólo serán aceptadas con el registro en la papeleta de la hora de ingreso, salida y firma del médico que realizó la atención</strong> en el ente gestor al que está afiliado el servidor, en el primer caso, y con el sello y firma de la persona o Institución que visitan, con hora de ingreso y salida para el segundo caso.
                          </p>

                          <p class="text-justify">
                            Cualquier salida no reportada, así como la ausencia no justificada en el lugar de trabajo serán consideradas como abandono de funciones.
                          </p>
                        </div>

                        <div class="alert alert-success">
                          <h3>Obligatoriedad en el Registro de Ingreso y Salida para SALIDAS PARTICULARES</h3>
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
                      </div>
                    </div>
                  </div>
                </div>

                <div id="tab-2" class="tab-pane">
                  <div class="panel-body" style="padding: 0px 0px 0px 0px;">
                      <div class="row">
                          <div class="col-lg-12">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title" style="padding-top: 12px; padding-bottom: 0px;">
                                        <div class="ibox-tools">
                                            @if(in_array(['codigo' => '1002'], $permisos))
                                                <button type="button" class="btn btn-primary btn-xs" onclick="utilitarios([57]);">
                                                    <strong>Nueva solicitud</strong>
                                                </button>
                                            @endif

                                            <select id="anio_filter_2" data-placeholder="Gestión">
                                                <option value="">Todos</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="ibox-content" style="padding: 0px 0px 0px 0px;">
                                        <div class="jqGrid_wrapper">
                                            <table id="jqgrid2"></table>
                                            <div id="pjqgrid2"></div>
                                        </div>
                                    </div>
                                </div>
                          </div>
                      </div>

                    <div class="row">
                      <div class="col-lg-12">
                        <div class="alert alert-danger">
                          <h3>Artículo 28. LICENCIAS CON GOCE DE HABERES.</h3>

                          <p class="text-justify">
                            Licencia es la autorización expresa que otorga el Superior Jerárquico de cada Unidad Organizacional para que un funcionario no asista a su fuente de trabajo, debiendo ser aprobada a través de la Jefatura Nacional de Recursos Humanos en la Fiscalía General y por las o los Fiscales Departamentales en cada Distrito del país. <strong>Toda Licencia debe tramitarse con 24 horas de  anticipación</strong> utilizando el formulario impreso del Sistema Integrado de Control de Personal.
                          </p>

                          <p class="text-justify">
                            Solo las <strong>licencias con carácter de emergencia pueden ser entregadas hasta 24 horas después de la misma debidamente justificadas</strong>, las licencias con goce de haber procederán en los siguientes casos:
                          </p>

                          <ol type="a">
                            <li class="text-justify">
                              Por fallecimiento de padres o suegros del funcionario,  cónyuge, hijos o hermanos del funcionario, <strong>hasta cinco días calendario</strong>. En estos casos, las licencias serán justificadas con los certificados correspondientes en el plazo máximo de cinco días de ocurrido el suceso.
                            </li>

                            <li class="text-justify">
                              Por nacimiento de hijos, <strong>tres días hábiles</strong>, que se computará a partir del día del parto, debiendo presentarse el certificado correspondiente.
                            </li>

                            <li class="text-justify">
                              Por asistencia a cursos de capacitación o entrenamiento, con patrocinio del Ministerio Público, el tiempo que sea declarado en comisión.
                            </li>

                            <li class="text-justify">
                              Por enfer­medad y/o maternidad conforme a lo dispuesto en el Código de Seguridad Social.
                            </li>

                            <li class="text-justify">
                              Por matrimonio del funcionario <strong>tres días hábiles</strong> incluyendo el día del matrimonio, debiendo presentarse el certificado correspondiente.
                            </li>

                            <li class="text-justify">
                              Por cumpleaños:  La  servidora  y servidor  del Ministerio Público  que  cumpla años <strong>en días laborales,  gozará de medio día laboral el mismo día</strong>, no compensable en dinero ni sujeto a uso en otro día distinto, previa presentación de copia de la cédula de identidad y llenado del formulario respectivo, con percepción  del 100% del haber mensual.
                            </li>

                            <li class="text-justify">
                              En caso de haber sido designado jurado electoral o Juez ciudadano, <strong>un día hábil</strong>, debiendo presentarse el certificado correspondiente o la designación efectuada por el Tribunal Departamental Electoral.
                            </li>

                            <li class="text-justify">
                              En caso de ser declarado en comisión por disposiciones superiores.
                            </li>

                            <li class="text-justify">
                              De conformidad al Decreto Supremo N°1496, las servidoras públicas del Ministerio Público del Estado, tendrán una tolerancia de <strong>un (1) día hábil al año</strong> para someterse al examen médico de Papanicolaou y/o Mamografía. Dicha tolerancia será fraccionada en dos medias jornadas: media jornada para la realización del examen médico y media jornada para conocer los resultados.
                            </li>
                          </ol>

                          <p class="text-justify">
                            En los  casos a) y b), el funcionario del Ministerio Público deberá sustentar la licencia con el documento pertinente dentro de los siguientes <strong>tres días hábiles</strong>, caso contrario será considerado como abandono de funciones.
                          </p>

                          <p class="text-justify">
                            En los casos c) y e) debe sustentarse con la <strong>certificación correspondiente después de 24 horas</strong>.
                          </p>

                          <p class="text-justify">
                            En el caso f), se debe <strong>adjuntar fotocopia de la cédula de identidad</strong>, a la papeleta de licencia.
                          </p>

                          <p class="text-justify">
                            En el caso g), deberá contarse con el <strong>memorando de designación y el certificado de sufragio emitido por el Tribunal Departamental Electoral</strong>.
                          </p>

                          <p class="text-justify">
                            En el caso d) ó i) la licencia debe contar con el <strong>formulario de atención médica y/o Baja Médica</strong> del ente gestor al que esta afiliado el servidor/a con firma del médico que lo atendió.
                          </p>

                          <p class="text-justify">
                            Para el caso h) debe contarse con la autorización de la autoridad competente.
                          </p>
                        </div>

                        <div class="alert alert-danger">
                          <h3>Artículo 30. LICENCIA SIN GOCE DE HABERES</h3>

                          <p class="text-justify">
                            Se podrá conceder licencia sin goce de haberes únicamente cuando el funcionario del Ministerio Público ya no tenga derecho a días de vacación pendiente y en  circunstancias especiales debidamente justificadas y documentadas, como ser:
                          </p>

                          <ol type="a">
                            <li class="text-justify">
                              <strong>Asistencia a cursos de capacitación, especialización, de posgrado, como participante particular</strong> (sin patrocinio del Ministerio Público).
                            </li>

                            <li class="text-justify">
                              Por razones de <strong>estudio o realización de trabajos de grado, hasta tres días calendario antes de su examen o defensa del trabajo de grado, previa certificación de la autoridad universitaria competente</strong>.
                            </li>

                            <li class="text-justify">
                              <strong>Por motivos de salud</strong> (tratamientos al funcionario o miembros de su familia) que no sean pagados por el seguro social) por períodos no mayores a 30 días calendarios.
                            </li>

                            <li class="text-justify">
                              Por otras causas de fuerza mayor u otras debidamente justificadas hasta 5 días hábiles.
                            </li>
                          </ol>

                          <p class="text-justify">
                            En los casos de los incisos a), b) y c) las licencias que no excedan a los <strong>15 días hábiles deberán ser aprobadas por la Jefatura Nacional de Recursos Humanos en la Fiscalía General del Estado y por los Fiscales Departamentales previa autorización expresa del superior inmediato</strong> del funcionario; cuando la <strong>solicitud de licencia sea mayor a los 15 días, la misma deberá ser aprobada mediante Resolución Expresa emitida por el Fiscal General del Estado</strong>. Estas solicitudes deberán presentarse por lo menos con 24 horas de anticipación y con toda la documentación de respaldo.
                          </p>
                        </div>
                      </div>
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

            <small class="font-bold">
              {{ $funcionario_array['n_documento'] . ' - ' . trim($funcionario_array['ap_paterno'] . ' ' . $funcionario_array['ap_materno']) . ' ' . $funcionario_array['nombre_persona'] }}
            </small>
          </div>

          <div class="modal-body">
            <div class="row">
              <form id="form_1" role="form" action="#">
                <input type="hidden" id="id_salida" name="id" value=""/>
                <input type="hidden" id="persona_id" name="persona_id" value="{{ $funcionario_array['persona_id'] }}"/>
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
                        <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código" disabled="disabled">
                      </div>
                    </div>
                  </div>

                  <div id="persona_id_superior_div" class="form-group">
                    <label for="persona_id_superior">Inmediato superior</label>
                    <select name="persona_id_superior" id="persona_id_superior" data-placeholder="Inmediato superior" multiple="multiple" style="width: 100%;">
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
                        <input type="text" class="form-control" id="f_salida" name="f_salida" placeholder="año-mes-día" data-mask="9999-99-99" value="{{ date("Y-m-d") }}">
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="h_salida">Hora de salida</label>
                        <input type="text" class="form-control" id="h_salida" name="h_salida" placeholder="Hora de salida" data-mask="99:99" value="{{ date("H:i") }}">
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
                          <div class="radio radio-primary">
                            <input type="radio" id="con_sin_retorno_1_id" class="con_sin_retorno_class" name="con_sin_retorno" value="1" checked="checked">
                            <label class="text-success" for="con_sin_retorno_1_id"> {{ $con_sin_retorno_array['1'] }} </label>
                          </div>
                          <div class="radio radio-danger">
                            <input type="radio" id="con_sin_retorno_2_id" class="con_sin_retorno_class" name="con_sin_retorno" value="2">
                            <label class="text-danger" for="con_sin_retorno_2_id"> {{ $con_sin_retorno_array['2'] }} </label>
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

    <div id="modal_2" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>

            <h4 class="modal-title">
              <span id="modal_2_title"></span>
            </h4>

            <small class="font-bold">
              {{ $funcionario_array['n_documento'] . ' - ' . trim($funcionario_array['ap_paterno'] . ' ' . $funcionario_array['ap_materno']) . ' ' . $funcionario_array['nombre_persona'] }}
            </small>
          </div>

          <div class="modal-body">
            <div class="row">
              <form id="form_2" role="form" action="#">
                <input type="hidden" id="id_salida_2" name="id" value=""/>
                <input type="hidden" id="persona_id_2" name="persona_id" value="{{ $funcionario_array['persona_id'] }}"/>
                <input type="hidden" id="tipo1_2" name="tipo" value="2"/>
                {{ csrf_field() }}
                <div class="col-sm-12">
                  <div id="tipo_salida_id_2_div" class="form-group">
                    <label for="tipo_salida_id_2">Tipo de papeleta</label>
                    <select name="tipo_salida_id" id="tipo_salida_id_2" data-placeholder="Tipo de papeleta" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div class="row">
                    <div class="col-sm-6">
                      <div id="tipo_salida_2_div" class="form-group">
                        <label for="tipo_salida_2">Tipo de salida</label>
                        <select name="tipo_salida" id="tipo_salida_2" data-placeholder="Tipo de salida" multiple="multiple" style="width: 100%;">
                        </select>
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="n_dias_2">Número de días</label>
                        <input type="text" class="form-control" id="n_dias_2" name="n_dias" placeholder="Se calculara automaticamente" disabled="disabled">
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="codigo_2">Código</label>
                        <input type="text" class="form-control" id="codigo_2" name="codigo" placeholder="Código" disabled="disabled">
                      </div>
                    </div>
                  </div>

                  <div id="persona_id_superior_2_div" class="form-group">
                    <label for="persona_id_superior_2">Inmediato superior</label>
                    <select name="persona_id_superior" id="persona_id_superior_2" data-placeholder="Inmediato superior" multiple="multiple" style="width: 100%;">
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="destino_2">Destino</label>
                    <input type="text" class="form-control" id="destino_2" name="destino" placeholder="Destino">
                  </div>

                  <div class="form-group">
                    <label for="motivo_2">Motivo</label>
                    <input type="text" class="form-control" id="motivo_2" name="motivo" placeholder="Motivo">
                  </div>

                  <div class="row">
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="f_salida_2">Fecha de salida</label>
                        <input type="text" class="form-control" id="f_salida_2" name="f_salida" placeholder="año-mes-día" data-mask="9999-99-99" value="{{ date("Y-m-d") }}">
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label>Periodo</label>
                        <div>
                          <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="periodo_salida_2_id" name="periodo_salida" value="2">
                            <label class="text-success" for="periodo_salida_2_id"> {{ $periodo_array['2'] }} </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="f_retorno_2">Fecha de retorno</label>
                        <input type="text" class="form-control" id="f_retorno_2" name="f_retorno" placeholder="año-mes-día" data-mask="9999-99-99" value="{{ date("Y-m-d") }}">
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label>Periodo</label>
                        <div>
                          <div class="checkbox checkbox-danger">
                            <input type="checkbox" id="periodo_retorno_1_id" class="periodo_retorno_class" name="periodo_retorno" value="1">
                            <label class="text-danger" for="periodo_retorno_1_id"> {{ $periodo_array['1'] }} </label>
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
            <button type="button" class="btn btn-info" onclick="utilitarios([54]);">Limpiar formulario</button>
            <button type="button" class="btn btn-primary" onclick="utilitarios([55]);">Guardar</button>
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>

    <div id="modal_3" class="modal inmodal fade" role="dialog" data-keyboard="false" data-backdrop="static">
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

            <small class="font-bold" id="modal_3_subtitle">
            </small>
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <form action="#" class="dropzone" id="dropzoneForm_1">
                  <input type="hidden" id="id_salida_3" name="id" value=""/>
                  <input type="hidden" id="dia_hora_3" name="dia_hora" value=""/>
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
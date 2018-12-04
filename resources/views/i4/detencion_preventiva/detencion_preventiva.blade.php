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

    <!-- TouchSpin -->
        <link href="{!! asset('inspinia_v27/css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') !!}" rel="stylesheet">
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

        .wizard>.steps>ul>li:nth-child(1){
            width: 15%;
        }
        .wizard>.steps>ul>li:nth-child(2){
            width: 15%;
        }
        .wizard>.steps>ul>li:nth-child(3){
            width: 15%;
        }
        .wizard>.steps>ul>li:nth-child(4){
            width: 20%;
        }
        .wizard>.steps>ul>li:nth-child(5){
            width: 15%;
        }
        .wizard>.steps>ul>li:nth-child(6){
            width: 20%;
        }

        #dropzone_1, #dropzone_2, #dropzone_3, #dropzone_4, #dropzone_5, #dropzone_6, #dropzone_7, #dropzone_8, #dropzone_9, #dropzone_10, #dropzone_11, #dropzone_12{
            height    : 135px;
            min-height: 100px;
        }

        .onoffswitch-inner:before {
            content: "SI";
        }
        .onoffswitch-inner:after {
            content: "NO";
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
                        <h5 style="margin-top: 6px;"><i class="fa fa-table"></i> {{ $title_table }}</h5>

                        <div class="ibox-tools" style="margin-top: 4px;">
                            <a href="https://docs.google.com/document/d/18kymkTHBqgmCiuChxyWU2e1QEpRVvS8w25xyhT1dGpw/edit?usp=sharing" class="btn btn-warning btn-xs" target="_blank" style="color: #FFFFFF">
                                <strong>Manual</strong>
                            </a>

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

                        <small class="font-bold" id="modal_2_title">

                        </small>
                    </div>

                    <div class="modal-body">
                        <form id="form_1" role="form" action="#">
                            <input type="hidden" id="persona_id" name="id" value=""/>
                            <input type="hidden" id="caso_id" name="caso_id" value=""/>
                            <input type="hidden" id="tipo1" name="tipo" value="1"/>
                            <input type="hidden" id="delito_principal_id" name="delito_principal_id" value=""/>
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-sm-4 b-r">
                                    <h3 class="m-t-none m-b text-success">IDENTIFICACION DEL CASO</h3>

                                    <div class="form-group">
                                        <label for="CodCasoJuz">NUREJ / IANUS</label>
                                        <input type="text" class="form-control" id="CodCasoJuz" name="CodCasoJuz" placeholder="NUREJ-IANUS U OTRO">
                                    </div>

                                    <h3 class="m-t-none m-b text-success">PERSONA DETENIDA</h3>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="NumDocId">Documento de identidad</label>
                                                <input type="text" class="form-control" id="NumDocId" name="NumDocId" placeholder="Documento de identidad">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="FechaNac">Fecha de nacimiento</label>
                                                <input type="text" class="form-control" id="FechaNac" name="FechaNac" placeholder="año-mes-día" data-mask="9999-99-99">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="ApPat">Apellido paterno</label>
                                                <input type="text" class="form-control" id="ApPat" name="ApPat" placeholder="Apellido paterno">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="ApMat">Apellido materno</label>
                                                <input type="text" class="form-control" id="ApMat" name="ApMat" placeholder="Apellido materno">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="ApEsp">Apellido esposo</label>
                                                <input type="text" class="form-control" id="ApEsp" name="ApEsp" placeholder="Apellido esposo">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="Nombres">Nombre(s)</label>
                                                <input type="text" class="form-control" id="Nombres" name="Nombres" placeholder="Nombre(s)">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Sexo</label>
                                        <br>
                                        <div class="radio radio-primary radio-inline">
                                            <input type="radio" id="sexo_id_1" value="1" name="sexo_id" class="sexo_id_class">
                                            <label for="sexo_id_1"> {!! $sexo_array['1'] !!} </label>
                                        </div>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="sexo_id_2" value="2" name="sexo_id" class="sexo_id_class">
                                            <label for="sexo_id_2"> {!! $sexo_array['2'] !!} </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4 b-r">
                                    <h3 class="m-t-none m-b text-success">DATOS DEL PROCESO</h3>

                                    <div id="peligro_procesal_id_div" class="form-group">
                                        <label for="peligro_procesal_id">Causal de la detención</label>
                                        <select name="peligro_procesal_id[]" id="peligro_procesal_id" data-placeholder="Peligro procesal" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="dp_fecha_detencion_preventiva">Fecha de la detención</label>
                                        <input type="text" class="form-control" id="dp_fecha_detencion_preventiva" name="dp_fecha_detencion_preventiva" placeholder="año-mes-día" data-mask="9999-99-99">
                                    </div>

                                    <div class="form-group">
                                        <label for="dp_fecha_conclusion_detencion">Fecha de la conclusión de la detención</label>
                                        <input type="text" class="form-control" id="dp_fecha_conclusion_detencion" name="dp_fecha_conclusion_detencion" placeholder="año-mes-día" data-mask="9999-99-99">
                                    </div>

                                    <div id="recinto_carcelario_id_div" class="form-group">
                                        <label for="recinto_carcelario_id">Recinto carcelario</label>
                                        <select name="recinto_carcelario_id" id="recinto_carcelario_id" data-placeholder="Recinto carcelario" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <h3 class="m-t-none m-b text-success">CARACTERISTICAS DEL DETENIDO</h3>

                                    <div id="div_dp_etapa_gestacion_estado" class="form-group">
                                        <strong>¿Mujer gestante?</strong>

                                        <span class="pull-right">
                                            <div class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" id="dp_etapa_gestacion_estado" name="dp_etapa_gestacion_estado" value="2">
                                                <label class="onoffswitch-label" for="dp_etapa_gestacion_estado">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </span>
                                    </div>

                                    <div id="div_dp_etapa_gestacion_semana" class="form-group" style="padding-left: 20px;">
                                        <label for="dp_etapa_gestacion_semana" class="text-warning">Semanas de gestación</label>
                                        <input type="text" class="form-control" id="dp_etapa_gestacion_semana" name="dp_etapa_gestacion_semana" placeholder="Semanas de gestación" value="0">
                                    </div>

                                    <div class="form-group">
                                        <strong>¿Con enfermedad terminal?</strong>

                                        <span class="pull-right">
                                            <div class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" id="dp_enfermo_terminal_estado" name="dp_enfermo_terminal_estado" value="2">
                                                <label class="onoffswitch-label" for="dp_enfermo_terminal_estado">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </span>
                                    </div>

                                    <div id="div_dp_enfermo_terminal_tipo" class="form-group" style="padding-left: 20px;">
                                        <label for="dp_enfermo_terminal_tipo" class="text-warning">Tipo de enfermedad terminal</label>
                                        <input type="text" class="form-control" id="dp_enfermo_terminal_tipo" name="dp_enfermo_terminal_tipo" placeholder="Tipo de enfermedad terminal">
                                    </div>

                                    <div id="div_dp_madre_lactante_1" class="form-group">
                                        <strong>¿Madre de menor lactante a un año?</strong>

                                        <span class="pull-right">
                                            <div class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" id="dp_madre_lactante_1" name="dp_madre_lactante_1" value="2">
                                                <label class="onoffswitch-label" for="dp_madre_lactante_1">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </span>
                                    </div>

                                    <div id="div_dp_madre_lactante_1_fecha_nacimiento_menor" class="form-group" style="padding-left: 20px;">
                                        <label for="dp_madre_lactante_1_fecha_nacimiento_menor" class="text-warning">Fecha de nacimiento del menor</label>
                                        <input type="text" class="form-control" id="dp_madre_lactante_1_fecha_nacimiento_menor" name="dp_madre_lactante_1_fecha_nacimiento_menor" placeholder="año-mes-día" data-mask="9999-99-99">
                                    </div>

                                    <div class="form-group">
                                        <strong>¿Custodia a menor de seis años?</strong>

                                        <span class="pull-right">
                                            <div class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" id="dp_custodia_menor_6" name="dp_custodia_menor_6" value="2">
                                                <label class="onoffswitch-label" for="dp_custodia_menor_6">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </span>
                                    </div>

                                    <div id="div_dp_custodia_menor_6_fecha_nacimiento_menor" class="form-group" style="padding-left: 20px;">
                                        <label for="dp_custodia_menor_6_fecha_nacimiento_menor" class="text-warning">Fecha de nacimiento del menor</label>
                                        <input type="text" class="form-control" id="dp_custodia_menor_6_fecha_nacimiento_menor" name="dp_custodia_menor_6_fecha_nacimiento_menor" placeholder="año-mes-día" data-mask="9999-99-99">
                                    </div>

                                    <div class="form-group">
                                        <strong>¿Es reincidente?</strong>

                                        <span class="pull-right">
                                            <div class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" id="reincidencia" name="reincidencia" value="2">
                                                <label class="onoffswitch-label" for="reincidencia">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-sm-12" id="div_segip">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        {{-- <button id="button_segip" type="button" class="btn btn-info" onclick="utilitarios([70]);">Validar SEGIP</button> --}}
                        <button type="button" class="btn btn-primary" onclick="utilitarios([50]);">Guardar</button>
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

    <!-- Steps -->
        <script src="{{ asset('inspinia_v27/js/plugins/steps/jquery.steps.js') }}"></script>

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

    <!-- TouchSpin -->
        <script src="{{ asset('inspinia_v27/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
@endsection

@section('js')
    @include('i4.detencion_preventiva.detencion_preventiva_js')
@endsection
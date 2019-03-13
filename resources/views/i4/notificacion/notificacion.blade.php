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

        <link href="{!! asset('inspinia_v27/css/plugins/clockpicker/clockpicker.css') !!}" rel="stylesheet">

    <!-- Dropzone -->
        <link href="{!! asset('inspinia_v27/css/plugins/dropzone/basic.css') !!}" rel="stylesheet">
        <link href="{!! asset('inspinia_v27/css/plugins/dropzone/dropzone.css') !!}" rel="stylesheet">

    <!-- TouchSpin -->
        <link href="{!! asset('inspinia_v27/css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') !!}" rel="stylesheet">
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

    <div class="row">
        <div class="col-lg-9">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0px;">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    @if(in_array(['codigo' => '2502'], $permisos) AND $i4_funcionario_id != '')
                                        {{-- <button type="button" class="btn btn-success btn-xs pull-right" onclick="utilitarios([10]);">
                                            <strong>Notificar</strong>
                                        </button> --}}
                                    @endif

                                    <h2><strong>Descripción del caso</strong></h2>
                                </div>
                                <dl class="dl-horizontal">
                                    <dt>Caso:</dt>
                                    <dd class="text-danger">
                                        <strong id="caso_b">
                                            FIS0000001
                                        </strong>
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <dl class="dl-horizontal">
                                    <dt>Etapa del caso:</dt>
                                    <dd id="etapa_caso_b">PREPARATORIA</dd>

                                    <dt>Origen del caso:</dt>
                                    <dd id="origen_caso_b">DE OFICIO</dd>
                                </dl>
                            </div>

                            <div class="col-lg-6" id="cluster_info">
                                <dl class="dl-horizontal">
                                    <dt>Estado del caso:</dt>
                                    <dd>
                                        <span id="estado_caso_b" class="label label-warning">ABIERTO</span>
                                    </dd>

                                    <dt>Fecha de la denuncia:</dt>
                                    <dd id="f_denuncia_b">25 DE ENERO 2019</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <dl class="dl-horizontal">
                                    <dt>Fiscal asignado:</dt>
                                    <dd id="fiscal_asignado_b">JUAN CARLOS PERES RIOS</dd>

                                    <dt>Delito principal:</dt>
                                    <dd id="delito_principal_b">VIOLENCIA FAMILIAR</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <dl class="dl-horizontal">
                                    <dt>Denunciante:</dt>
                                    <dd id="denunciante_b">MARIO ARIEL ARANCIBIA PEREZ, MARIA ANTONIETA SANCHEZ RIOS, CARLOS PAREDES MARIO, MARIA PEREZ SANCHEZ, CARLOS ROMERO MONTALVO</dd>

                                    <dt>Denunciado:</dt>
                                    <dd id="denunciado_b">JUAN PEREZ CASTRO MAMANI, SANDRA IRALA FLORES, MARIO ALACA RICO</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">N°</th>
                                        <th class="text-center">FECHA</th>
                                        <th class="text-center">TIPO DE ACTIVIDAD</th>
                                        <th class="text-center">ACTIVIDAD DESCRIPCION</th>
                                        @if(in_array(['codigo' => '2503'], $permisos) AND $i4_funcionario_id != '')
                                            <th></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="actividad_tabla_b">
                                    <tr>
                                        <td class="text-right">
                                            1
                                        </td>
                                        <td class="text-center">
                                            2018-01-01
                                        </td>
                                        <td>
                                            INFORME
                                        </td>
                                        <td>
                                            HOLA
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-success" title="Generar " onclick="utilitarios([70, 1]);">
                                                <i class="fa fa-print"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3" >
            <div class="wrapper wrapper-content project-manager">
                <h4>Busqueda del caso</h4>
                <div class="row m-b-sm m-t-sm">
                    <div class="col-md-12">
                        <div class="form-group">
                            <select name="caso_id" id="caso_id" data-placeholder="Caso" multiple="multiple" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                </div>

                {{-- <div class="row m-b-sm m-t-sm">
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" id="caso" placeholder="Caso" class="form-control input-lg m-b" autofocus autocomplete="off" onkeyup="if(event.keyCode == 13){utilitarios([50]);}">
                            <span class="input-group-btn">
                            <button type="button" class="btn btn-lg btn-primary" onclick="utilitarios([50]);"> Buscar</button> </span>
                        </div>
                    </div>
                </div> --}}

                <p class="small">
                    Para completar la busqueda debe de seleccionar una opción.
                </p>

                <div class="text-center m-t-md">
                    {{-- <a href="https://docs.google.com/document/d/18kymkTHBqgmCiuChxyWU2e1QEpRVvS8w25xyhT1dGpw/edit?usp=sharing" class="btn btn-warning btn-xs" target="_blank" style="color: #FFFFFF">
                        <strong>Manual</strong>
                    </a> --}}

                    <button type="button" class="btn btn-xs btn-info" onclick="utilitarios([30]);utilitarios([31]);"><strong>Limpiar</strong></button>

                    @if(in_array(['codigo' => '2203'], $permisos))
                        <button type="button" class="btn btn-xs btn-success" onclick="utilitarios([11]);"><strong>Reportes</strong></button>
                    @endif
                </div>

                <br>
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
                            <input type="hidden" id="caso_id_1" name="caso_id" value=""/>
                            <input type="hidden" id="actividad_id" name="actividad_id" value=""/>
                            <input type="hidden" id="tipo1" name="tipo" value="1"/>
                            {{ csrf_field() }}
                            <div class="row">
                                <div id="denunciado_tabla" class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="35px">
                                                    <input type="checkbox" name="denunciado_all_select" id="denunciado_all_select">
                                                </th>
                                                <th class="text-center" width="30%">DENUNCIADO</th>
                                                <th class="text-center" width="35%">UBICACION</th>
                                                <th class="text-center" width="35%">ABOGADO</th>
                                            </tr>
                                        </thead>
                                        <tbody id="denunciado_tabla_body">
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="denunciado_class" name="denunciado_select[]" value="">
                                                </td>
                                                <td>
                                                    JORGE PEREZ CAMACHO
                                                </td>
                                                <td>
                                                    CALLE LOA 562
                                                </td>
                                                <td class="text-center">
                                                    SI
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="denunciado_class" name="denunciado_select[]" value="">
                                                </td>
                                                <td>
                                                    JORGE PEREZ CAMACHO
                                                </td>
                                                <td>
                                                    CALLE LOA 562
                                                </td>
                                                <td class="text-center">
                                                    SI
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="denunciante_tabla" class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="35px">
                                                    <input type="checkbox" name="denunciante_all_select" id="denunciante_all_select">
                                                </th>
                                                <th class="text-center" width="30%">DENUNCIANTE</th>
                                                <th class="text-center" width="35%">UBICACION</th>
                                                <th class="text-center" width="35%">ABOGADO</th>
                                            </tr>
                                        </thead>
                                        <tbody id="denunciante_tabla_body">
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="denunciante_class" name="denunciante_select[]" value="">
                                                </td>
                                                <td>
                                                    JORGE PEREZ CAMACHO
                                                </td>
                                                <td>
                                                    CALLE LOA 562
                                                </td>
                                                <td class="text-center">
                                                    SI
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="denunciante_class" name="denunciante_select[]" value="">
                                                </td>
                                                <td>
                                                    JORGE PEREZ CAMACHO
                                                </td>
                                                <td>
                                                    CALLE LOA 562
                                                </td>
                                                <td class="text-center">
                                                    SI
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div id="victima_tabla" class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="35px">
                                                    <input type="checkbox" name="victima_all_select" id="victima_all_select">
                                                </th>
                                                <th class="text-center" width="30%">VICTIMA</th>
                                                <th class="text-center" width="35%">UBICACION</th>
                                                <th class="text-center" width="35%">ABOGADO</th>
                                            </tr>
                                        </thead>
                                        <tbody id="victima_tabla_body">
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="victima_class" name="victima_select[]" value="">
                                                </td>
                                                <td>
                                                    JORGE PEREZ CAMACHO
                                                </td>
                                                <td>
                                                    CALLE LOA 562
                                                </td>
                                                <td class="text-center">
                                                    SI
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="victima_class" name="victima_select[]" value="">
                                                </td>
                                                <td>
                                                    JORGE PEREZ CAMACHO
                                                </td>
                                                <td>
                                                    CALLE LOA 562
                                                </td>
                                                <td class="text-center">
                                                    SI
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="solicitud_asunto">Asunto</label>
                                        <input type="text" class="form-control" id="solicitud_asunto" name="solicitud_asunto" placeholder="Asunto">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="utilitarios([51]);">Notificar</button>

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
                            REPORTES
                        </h4>
                    </div>

                    <div class="modal-body">
                        <form id="form_2" role="form" action="#">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="tipo_reporte_2_div" class="form-group">
                                        <label for="tipo_reporte_2">Tipo de reporte</label>
                                        <select name="tipo_reporte_2" id="tipo_reporte_2" data-placeholder="Tipo de reporte" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div id="division_id_2_div" class="form-group">
                                        <label for="division_id_2">División</label>
                                        <select name="division_id_2" id="division_id_2" data-placeholder="Departamento" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div id="funcionario_id_2_div" class="form-group">
                                        <label for="funcionario_id_2">Fiscal</label>
                                        <select name="funcionario_id_2" id="funcionario_id_2" data-placeholder="Fiscal" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="fecha_del_2">Fecha del</label>
                                            <input type="text" class="form-control" id="fecha_del_2" name="fecha_del_2" placeholder="año-mes-día" data-mask="9999-99-99" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="hora_del_2">Hora del</label>
                                                <input type="text" class="form-control" id="hora_del_2" name="hora_del_2" placeholder="hora:minuto" data-mask="99:99" value="00:00">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="fecha_al_2">Fecha al</label>
                                                <input type="text" class="form-control" id="fecha_al_2" name="fecha_al_2" placeholder="año-mes-día" data-mask="9999-99-99" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="hora_al_2">Hora al</label>
                                                <input type="text" class="form-control" id="hora_al_2" name="hora_al_2" placeholder="hora:minuto" data-mask="99:99" value="{{ date('H:i') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="utilitarios([71]);">PDF</button>
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

    <!-- Clock picker -->
        <script src="{{ asset('inspinia_v27/js/plugins/clockpicker/clockpicker.js') }}"></script>

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
    @include('i4.notificacion.notificacion_js')
@endsection
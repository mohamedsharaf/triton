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

        .clockpicker-popover {
            z-index: 9999;
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
                            @if(in_array(['codigo' => '2706'], $permisos))
                                <button type="button" class="btn btn-info btn-xs">
                                    <strong>Reportes</strong>
                                </button>
                            @endif

                            {{-- <a href="https://docs.google.com/document/d/18kymkTHBqgmCiuChxyWU2e1QEpRVvS8w25xyhT1dGpw/edit?usp=sharing" class="btn btn-warning btn-xs" target="_blank" style="color: #FFFFFF; margin-right: 4px;">
                                <strong>Manual</strong>
                            </a> --}}

                            <select id="anio_filter" data-placeholder="Gestión">
                                <option value="">Todos</option>
                            </select>

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

                        <small class="font-bold" id="modal_2_title">
                        </small>
                    </div>

                    <div class="modal-body">
                        <form id="form_1" role="form" action="#">
                            <input type="hidden" id="notificacion_id" name="id" value=""/>
                            <input type="hidden" id="tipo1" name="tipo" value="1"/>
                            {{ csrf_field() }}

                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="estado_notificacion_id_div" class="form-group">
                                        <label for="estado_notificacion_id" class="text-success">Estado de la notificación</label>
                                        <select name="estado_notificacion_id" id="estado_notificacion_id" data-placeholder="Estado de la notificación" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="solicitud_f">Fecha de notificación</label>
                                            <input type="text" class="form-control" id="solicitud_f" name="solicitud_f" placeholder="año-mes-día" data-mask="9999-99-99" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="solicitud_h">Hora de notificación</label>
                                            <input type="text" class="form-control" id="solicitud_h" name="solicitud_h" placeholder="hora:minuto" data-mask="99:99">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="notificacion_observacion">Observación</label>
                                        <textarea class="form-control" id="notificacion_observacion" name="notificacion_observacion" placeholder="Observación" rows="2"></textarea>
                                    </div>

                                    <h3 class="m-t-none m-b text-success">Testigo</h3>

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="notificacion_documento">Número de documento</label>
                                            <input type="text" class="form-control" id="notificacion_documento" name="notificacion_documento" placeholder="Número de documento"">
                                            </div>
                                        </div>

                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label for="notificacion_testigo_nombre">Nombre</label>
                                            <input type="text" class="form-control" id="notificacion_testigo_nombre" name="notificacion_testigo_nombre" placeholder="Nombre"">
                                            </div>
                                        </div>
                                    </div>




                                    {{-- <div class="form-group">
                                        <label>Estado</label>
                                        <br>
                                        <div class="radio radio-primary radio-inline">
                                            <input type="radio" id="estado_1" value="1" name="estado" class="estado_class" checked="checked">
                                            <label for="estado_1"> {!! $estado_array['1'] !!} </label>
                                        </div>

                                        <div class="radio radio-danger radio-inline">
                                            <input type="radio" id="estado_2" value="2" name="estado" class="estado_class">
                                            <label for="estado_2"> {!! $estado_array['2'] !!} </label>
                                        </div>
                                    </div>

                                    <div id="Muni_id_div" class="form-group">
                                        <label for="Muni_id">Ubicación</label>
                                        <select name="Muni_id" id="Muni_id" data-placeholder="Departamento, Municipio" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div id="tipo_recinto_div" class="form-group">
                                        <label for="tipo_recinto">Tipo de recinto carcelario</label>
                                        <select name="tipo_recinto" id="tipo_recinto" data-placeholder="Tipo de recinto carcelario" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="nombre">Nombre del recinto carcelario</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del recinto carcelario">
                                    </div> --}}
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

    <!-- DROPZONE -->
        <script src="{{ asset('inspinia_v27/js/plugins/dropzone/dropzone.js') }}"></script>

    <!-- TouchSpin -->
        <script src="{{ asset('inspinia_v27/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
@endsection

@section('js')
    @include('i4.central_notificacion.central_notificacion_js')
@endsection
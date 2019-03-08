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
                    <form id="form_1" role="form" action="#">
                        <input type="hidden" id="edinstitucion_id" name="idedinstitucion" value=""/>
                        <input type="hidden" id="tipo1" name="tipo" value="1"/>
                        <input type="hidden" id="instituciontipo" name="instituciontipo" value="1"/>
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <div>
                                        <div class="radio radio-primary radio-inline">
                                            <input type="radio" id="estado_1_id" class="estado_class" name="estado" value="1" checked="checked">
                                            <label class="text-success" for="estado_1_id"> {{ $estado_array['1'] }} </label>
                                        </div>
                                        <br>
                                        <div class="radio radio-danger radio-inline">
                                            <input type="radio" id="estado_2_id" class="estado_class" name="estado" value="2">
                                            <label class="text-danger" for="estado_2_id"> {{ $estado_array['2'] }} </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-primary" id='btnInstitucion' type="button">Institución</button>
                                        <button class="btn btn-white" id='btnOficina' type="button">Oficina</button>
                                    </div>
                                </div>
                                <div id="institucion_id_div" class="form-group">
                                    <label for="institucion">Institución a la que pertenece</label>
                                    <select name="institucion" id="institucion_id" data-placeholder="Seleccione la institución" multiple="multiple" style="width: 100%;">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la institución/oficina">
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div id="municipio_id_div" class="form-group">
                                            <label for="municipio">Municipio</label>
                                            <select name="municipio" id="municipio_id" data-placeholder="Municipio" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Correo electrónico</label>
                                            <input type="text" class="form-control" id="email" name="email" placeholder="ejemplo@direccion.com">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="nombre">Zona</label>
                                            <input type="text" class="form-control" id="zona" name="zona">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="nombre">Direccion</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="nombre">Telefono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="nombre">Celular</label>
                                            <input type="text" class="form-control" id="celular" name="celular">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-info" onclick="utilitarios([14]);">Limpiar formulario</button>
                    <button type="button" class="btn btn-primary" onclick="utilitarios([15]);">Guardar</button>
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
@endsection

@section('js')
    @include('institucion.institucion.institucion_js')
@endsection
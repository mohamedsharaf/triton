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

    <!-- steps -->
        <link href="{!! asset('inspinia_v27/css/plugins/steps/jquery.steps.css') !!}" rel="stylesheet">
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
                            <span class="form-inline">
                                <div class="form-group">
                                    <label for="anio_filter" class="sr-only">Gestión</label>
                                    <select id="anio_filter" data-original-title="<i class='fa fa-warning txt-color-teal'></i> Gestión" data-html="true">
                                        <option value="" selected="selected">Todos</option>
                                    </select>
                                </div>
                            </span>

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
                            {{ $title_table }}<span id="modal_1_title"></span>
                        </h4>
                    </div>

                    <div class="modal-body">
                        <form id="form_1" action="#" class="wizard-big">
                            <h1>Solicitud</h1>
                            <fieldset>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div id="gestion_div" class="form-group">
                                                    <label for="gestion">Gestión</label>
                                                    <select name="gestion" id="gestion" data-placeholder="Gestión" multiple="multiple" style="width: 100%;">
                                                    </select>
                                                 </div>
                                            </div>

                                            <div class="col-sm-9">
                                                <div id="solicitante_div" class="form-group">
                                                    <label for="solicitante">Solicitado por</label>
                                                    <select name="solicitante" id="solicitante" data-placeholder="Solicitado por" multiple="multiple" style="width: 100%;">
                                                    </select>
                                                 </div>
                                            </div>
                                        </div>

                                        <div id="persona_id_solicitante_div" class="form-group">
                                            <label for="persona_id_solicitante">Nombre del solicitante</label>
                                            <select name="persona_id_solicitante" id="persona_id_solicitante" data-placeholder="Nombre del solicitante" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div id="municipio_id_div" class="form-group">
                                            <label for="municipio_id">Lugar</label>
                                            <select name="municipio_id" id="municipio_id" data-placeholder="Lugar" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="f_solicitud">Fecha de solicitud</label>
                                                    <input type="text" class="form-control" id="f_solicitud" name="f_solicitud" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="dropzone" id="dropzone_1">
                                                    <div class="fallback">
                                                      <input name="file" type="file"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="n_caso">N° de caso</label>
                                                    <input type="text" class="form-control" id="n_caso" name="n_caso" placeholder="N° de caso">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div id="etapa_proceso_div" class="form-group">
                                                    <label for="etapa_proceso">Etapa del proceso</label>
                                                    <select name="etapa_proceso" id="etapa_proceso" data-placeholder="Etapa del proceso" multiple="multiple" style="width: 100%;">
                                                    </select>
                                                 </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="denunciante">Denunciante</label>
                                            <input type="text" class="form-control" id="denunciante" name="denunciante" placeholder="Denunciante">
                                        </div>

                                        <div class="form-group">
                                            <label for="denunciado">Denunciado</label>
                                            <input type="text" class="form-control" id="denunciado" name="denunciado" placeholder="Denunciado">
                                        </div>

                                        <div class="form-group">
                                            <label for="victima">Víctima</label>
                                            <input type="text" class="form-control" id="victima" name="victima" placeholder="Víctima">
                                        </div>

                                        <div class="form-group">
                                            <label for="persona_protegida">Persona protegida</label>
                                            <input type="text" class="form-control" id="persona_protegida" name="persona_protegida" placeholder="Persona protegida">
                                        </div>
                                    </div>
                                </div>

                                <h3>Delito</h3>
                                <div class="row">
                                    <div class="col-sm-9">
                                        <div id="delito_id_div" class="form-group">
                                            <select name="delito_id" id="delito_id" data-placeholder="Delito" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="checkbox checkbox-primary checkbox-inline">
                                            <input type="checkbox" name="tentativa" id="tentativa" value="2">
                                            <label for="tentativa"> TENTATIVA </label>
                                        </div>
                                    </div>

                                    <div class="col-sm-1">
                                        <button type="button" class="btn btn-success btn-xs" title="Guardar delito">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="div_jqgrid2" class="jqGrid_wrapper">
                                            <table id="jqgrid2"></table>
                                            <div id="pjqgrid2"></div>
                                        </div>
                                    </div>
                                </div>

                                <br>

                                <h3>Recalificación del delito</h3>
                                <div class="row">
                                    <div class="col-sm-9">
                                        <div id="delito_id_r_div" class="form-group">
                                            <select name="delito_id_r" id="delito_id_r" data-placeholder="Recalificación del delito" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="checkbox checkbox-primary checkbox-inline">
                                            <input type="checkbox" name="tentativa_r" id="tentativa_r" value="2">
                                            <label for="tentativa_r"> TENTATIVA </label>
                                        </div>
                                    </div>

                                    <div class="col-sm-1">
                                        <button type="button" class="btn btn-info btn-xs" title="Guardar recalificación del delito">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="div_jqgrid3" class="jqGrid_wrapper">
                                            <table id="jqgrid3"></table>
                                            <div id="pjqgrid3"></div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <h1>Usuario</h1>
                            <fieldset>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div id="usuario_tipo_div" class="form-group">
                                            <label for="usuario_tipo">Tipo de usuario</label>
                                            <select name="usuario_tipo" id="usuario_tipo" data-placeholder="Tipo de usuario" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="usuario_tipo_descripcion">Tipo de usuario descripción</label>
                                            <input type="text" class="form-control" id="usuario_tipo_descripcion" name="usuario_tipo_descripcion" placeholder="Tipo de usuario descripción">
                                        </div>

                                        <div class="form-group">
                                            <label for="usuario_nombre">Nombre de usuario</label>
                                            <input type="text" class="form-control" id="usuario_nombre" name="usuario_nombre" placeholder="Nombre de usuario">
                                        </div>

                                        <div class="form-group">
                                            <label>Sexo</label>
                                            <br>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" id="usuario_sexo_1" value="1" name="usuario_sexo" class="usuario_sexo_class" checked="checked">
                                                <label for="usuario_sexo_1"> {!! $sexo_array['1'] !!} </label>
                                            </div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="usuario_sexo_2" value="2" name="usuario_sexo" class="usuario_sexo_class">
                                                <label for="usuario_sexo_2"> {!! $sexo_array['2'] !!} </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="usuario_celular">Teléfono y/o celular</label>
                                            <input type="text" class="form-control" id="usuario_celular" name="usuario_celular" placeholder="Teléfono y/o celular">
                                        </div>

                                        <div class="form-group">
                                            <label for="usuario_domicilio">Domicilio usuario</label>
                                            <input type="text" class="form-control" id="usuario_domicilio" name="usuario_domicilio" placeholder="Domicilio usuario">
                                        </div>

                                        <div class="form-group">
                                            <label for="usuario_otra_referencia">Otras referencias</label>
                                            <input type="text" class="form-control" id="usuario_otra_referencia" name="usuario_otra_referencia" placeholder="Otras referencias">
                                        </div>

                                        <div class="form-group">
                                            <label>Edad entre</label>
                                            <br>
                                            <div class="radio radio-primary radio-inline">
                                                <input type="radio" id="usuario_edad_1" value="1" name="usuario_edad" class="usuario_edad_class" checked="checked">
                                                <label for="usuario_edad_1"> {!! $edad_array['1'] !!} </label>
                                            </div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="usuario_edad_2" value="2" name="usuario_edad" class="usuario_edad_class">
                                                <label for="usuario_edad_2"> {!! $edad_array['2'] !!} </label>
                                            </div>
                                            <div class="radio radio-success radio-inline">
                                                <input type="radio" id="usuario_edad_3" value="3" name="usuario_edad" class="usuario_edad_class">
                                                <label for="usuario_edad_3"> {!! $edad_array['3'] !!} </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <input type="radio" id="usuario_edad_4" value="4" name="usuario_edad" class="usuario_edad_class">
                                                <label for="usuario_edad_4"> {!! $edad_array['4'] !!} </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <h1>Solicitud de trabajo</h1>
                            <fieldset>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div id="dirigido_a_psicologia_div" class="form-group">
                                            <label for="dirigido_a_psicologia">Psicología dirigido a</label>
                                            <select name="dirigido_a_psicologia" id="dirigido_a_psicologia" data-placeholder="Dirigido a" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div id="dirigido_psicologia_div" class="form-group">
                                            <label for="dirigido_psicologia">Psicología trabajo solicitado</label>
                                            <select name="dirigido_psicologia" id="dirigido_psicologia" data-placeholder="Psicología trabajo solicitado" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div class="dropzone" id="dropzone_9">
                                            <div class="fallback">
                                              <input name="file9" type="file"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div id="dirigido_a_trabajo_social_div" class="form-group">
                                            <label for="dirigido_a_trabajo_social">Trabajo social dirigido a</label>
                                            <select name="dirigido_a_trabajo_social" id="dirigido_a_trabajo_social" data-placeholder="Dirigido a" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div id="dirigido_trabajo_social_div" class="form-group">
                                            <label for="dirigido_trabajo_social">Trabajo social trabajo solicitado</label>
                                            <select name="dirigido_trabajo_social" id="dirigido_trabajo_social" data-placeholder="Trabajo social trabajo solicitado" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div class="dropzone" id="dropzone_10">
                                            <div class="fallback">
                                              <input name="file10" type="file"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div id="dirigido_a_otro_trabajo_div" class="form-group">
                                            <label for="dirigido_a_otro_trabajo">Otro trabajo dirigido a</label>
                                            <select name="dirigido_a_otro_trabajo" id="dirigido_a_otro_trabajo" data-placeholder="Dirigido a" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="dirigido_otro_trabajo">Otro trabajo solicitado</label>
                                            <textarea class="form-control" id="dirigido_otro_trabajo" name="dirigido_otro_trabajo" placeholder="Otro trabajo solicitado" rows="2"></textarea>
                                        </div>

                                        <div class="dropzone" id="dropzone_11">
                                            <div class="fallback">
                                              <input name="file11" type="file"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <h1>Solicitud trabajo complementario</h1>
                            <fieldset>
                                <div class="row">
                                    <div id="estado_div" class="form-group">
                                        <label for="estado">Estado</label>
                                        <select name="estado" id="estado" data-placeholder="Estado" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div id="complementario_dirigido_a_div" class="form-group">
                                        <label for="complementario_dirigido_a">Dirigido a</label>
                                        <select name="complementario_dirigido_a" id="complementario_dirigido_a" data-placeholder="Dirigido a" multiple="multiple" style="width: 100%;">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="complementario_trabajo_solicitado">Trabajo solicitado</label>
                                        <textarea class="form-control" id="complementario_trabajo_solicitado" name="complementario_trabajo_solicitado" placeholder="Trabajo solicitado" rows="4"></textarea>
                                    </div>

                                    <div class="dropzone" id="dropzone_12">
                                        <div class="fallback">
                                          <input name="file12" type="file"/>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <h1>Presentación de informes</h1>
                            <fieldset>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="plazo_fecha_solicitud">Fecha de solicitud</label>
                                                    <input type="text" class="form-control" id="plazo_fecha_solicitud" name="plazo_fecha_solicitud" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="plazo_fecha_recepcion">Fecha de recepción de la solicitud de trabajo</label>
                                                    <input type="text" class="form-control" id="plazo_fecha_recepcion" name="plazo_fecha_recepcion" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>
                                        </div>

                                        <h3>Informe psicologico</h3>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="plazo_psicologico_fecha_entrega_digital">Fecha de entrega digital</label>
                                                    <input type="text" class="form-control" id="plazo_psicologico_fecha_entrega_digital" name="plazo_psicologico_fecha_entrega_digital" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="plazo_psicologico_fecha_entrega_fisico">Fecha de entrega físico</label>
                                                    <input type="text" class="form-control" id="plazo_psicologico_fecha_entrega_fisico" name="plazo_psicologico_fecha_entrega_fisico" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dropzone" id="dropzone_2">
                                            <div class="fallback">
                                              <input name="file2" type="file"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <h3>Informe social</h3>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="plazo_social_fecha_entrega_digital">Fecha de entrega digital</label>
                                                    <input type="text" class="form-control" id="plazo_social_fecha_entrega_digital" name="plazo_social_fecha_entrega_digital" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="plazo_social_fecha_entrega_fisico">Fecha de entrega físico</label>
                                                    <input type="text" class="form-control" id="plazo_social_fecha_entrega_fisico" name="plazo_social_fecha_entrega_fisico" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dropzone" id="dropzone_3">
                                            <div class="fallback">
                                              <input name="file3" type="file"/>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="plazo_complementario_fecha">Fecha informe complementario</label>
                                                    <input type="text" class="form-control" id="plazo_complementario_fecha" name="plazo_complementario_fecha" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="dropzone" id="dropzone_4">
                                                    <div class="fallback">
                                                      <input name="file4" type="file"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <h1>Resoluciones del MP y seguimiento</h1>
                            <fieldset>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="resolucion_descripcion">Descripción de la resolución</label>
                                            <input type="text" class="form-control" id="resolucion_descripcion" name="resolucion_descripcion" placeholder="Descripción de la resolución">
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="resolucion_fecha_emision">Fecha emisión</label>
                                                    <input type="text" class="form-control" id="resolucion_fecha_emision" name="resolucion_fecha_emision" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="dropzone" id="dropzone_7">
                                                    <div class="fallback">
                                                      <input name="file7" type="file"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="resolucion_tipo_disposicion_div" class="form-group">
                                            <label for="resolucion_tipo_disposicion">Tipo de disposición</label>
                                            <select name="resolucion_tipo_disposicion" id="resolucion_tipo_disposicion" data-placeholder="Tipo de disposición" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div id="resolucion_medidas_proteccion_div" class="form-group">
                                            <label for="resolucion_medidas_proteccion">Medida de protección dispuesta</label>
                                            <select name="resolucion_medidas_proteccion" id="resolucion_medidas_proteccion" data-placeholder="Medida de protección dispuesta" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="resolucion_otra_medidas_proteccion">Otra medida de protección dispuesta</label>
                                            <input type="text" class="form-control" id="resolucion_otra_medidas_proteccion" name="resolucion_otra_medidas_proteccion" placeholder="Otra medida de protección dispuesta">
                                        </div>

                                        <div class="form-group">
                                            <label for="resolucion_instituciones_coadyuvantes">Instituciones coadyuvantes</label>
                                            <input type="text" class="form-control" id="resolucion_instituciones_coadyuvantes" name="resolucion_instituciones_coadyuvantes" placeholder="Instituciones coadyuvantes">
                                        </div>

                                        <div class="dropzone" id="dropzone_8">
                                            <div class="fallback">
                                              <input name="file8" type="file"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="fecha_inicio">Fecha de inicio</label>
                                                    <input type="text" class="form-control" id="fecha_inicio" name="fecha_inicio" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="fecha_entrega_digital">Fecha de entrega digital</label>
                                                    <input type="text" class="form-control" id="fecha_entrega_digital" name="fecha_entrega_digital" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="fecha_entrega_fisico">Fecha de entrega físico</label>
                                                    <input type="text" class="form-control" id="fecha_entrega_fisico" name="fecha_entrega_fisico" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="informe_seguimiento_fecha">Fecha informe seguimiento</label>
                                                    <input type="text" class="form-control" id="informe_seguimiento_fecha" name="informe_seguimiento_fecha" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="dropzone" id="dropzone_5">
                                                    <div class="fallback">
                                                      <input name="file5" type="file"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="complementario_fecha">Fecha informe complementario</label>
                                                    <input type="text" class="form-control" id="complementario_fecha" name="complementario_fecha" placeholder="año-mes-día" data-mask="9999-99-99">
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="dropzone" id="dropzone_6">
                                                    <div class="fallback">
                                                      <input name="file6" type="file"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <br>

                                        <button type="button" class="btn btn-info" onclick="utilitarios([14]);">Limpiar formulario</button>

                                        <button type="button" class="btn btn-success" onclick="utilitarios([15]);">Guardar resolución del MP y seguimiento</button>
                                    </div>
                                </div>

                                <br>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="div_jqgrid4" class="jqGrid_wrapper">
                                            <table id="jqgrid4"></table>
                                            <div id="pjqgrid4"></div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    {{-- <div class="modal-footer">
                        <button type="button" class="btn btn-info" onclick="utilitarios([14]);">Limpiar formulario</button>
                        <button type="button" class="btn btn-primary" onclick="utilitarios([15]);">Guardar</button>
                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Salir</button>
                    </div> --}}
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
        <script src="{{ asset('inspinia_v27/js/plugins/steps/jquery.steps.min.js') }}"></script>

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
@endsection

@section('js')
    @include('dpvt.solicitud.solicitud_js')
@endsection
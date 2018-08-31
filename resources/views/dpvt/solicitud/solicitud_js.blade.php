<script>
    // === PLUGINS ===
    // === CONSTANTES NO TOCAR ===
        var options1 = {
            "closeButton"      : true,
            "debug"            : false,
            "progressBar"      : true,
            "preventDuplicates": false,
            "positionClass"    : "toast-top-right",
            "onclick"          : null,
            "showDuration"     : "400",
            "hideDuration"     : "1000",
            "timeOut"          : "2000",
            "extendedTimeOut"  : "1000",
            "showEasing"       : "swing",
            "hideEasing"       : "linear",
            "showMethod"       : "fadeIn",
            "hideMethod"       : "fadeOut"
        };
    // === VARIABLES GLOBALES ===
        var base_url       = "{!! url('') !!}";
        var url_controller = "{!! url('/solicitud_dpvt') !!}";
        var csrf_token     = "{!! csrf_token() !!}";
        var public_dir     = "{!! asset($public_dir) !!}";


    // === ESTADO ===
        var estado_json   = $.parseJSON('{!! json_encode($estado_array) !!}');
        var estado_select = '';
        var estado_jqgrid = ':Todos';

        $.each(estado_json, function(index, value) {
            estado_select += '<option value="' + index + '">' + value + '</option>';
            estado_jqgrid += ';' + index + ':' + value;
        });

    // === CERRADO ABIERTO ===
        var cerrado_abierto_json   = $.parseJSON('{!! json_encode($cerrado_abierto_array) !!}');
        var cerrado_abierto_select = '';
        var cerrado_abierto_jqgrid = ':Todos';

        $.each(cerrado_abierto_json, function(index, value) {
            cerrado_abierto_select += '<option value="' + index + '">' + value + '</option>';
            cerrado_abierto_jqgrid += ';' + index + ':' + value;
        });

    // === SOLICITANTE ===
        var solicitante_json   = $.parseJSON('{!! json_encode($solicitante_array) !!}');
        var solicitante_select = '';
        var solicitante_jqgrid = ':Todos';

        $.each(solicitante_json, function(index, value) {
            solicitante_select += '<option value="' + index + '">' + value + '</option>';
            solicitante_jqgrid += ';' + index + ':' + value;
        });

    // === ETAPA PROCESO ===
        var etapa_proceso_json   = $.parseJSON('{!! json_encode($etapa_proceso_array) !!}');
        var etapa_proceso_select = '';
        var etapa_proceso_jqgrid = ':Todos';

        $.each(etapa_proceso_json, function(index, value) {
            etapa_proceso_select += '<option value="' + index + '">' + value + '</option>';
            etapa_proceso_jqgrid += ';' + index + ':' + value;
        });

    // === ESTADO PDF ===
        var estado_pdf_json   = $.parseJSON('{!! json_encode($estado_pdf_array) !!}');
        var estado_pdf_select = '';
        var estado_pdf_jqgrid = ':Todos';

        $.each(estado_pdf_json, function(index, value) {
            estado_pdf_select += '<option value="' + index + '">' + value + '</option>';
            estado_pdf_jqgrid += ';' + index + ':' + value;
        });

    // === USUARIO TIPO ===
        var usuario_tipo_json   = $.parseJSON('{!! json_encode($usuario_tipo_array) !!}');
        var usuario_tipo_select = '';
        var usuario_tipo_jqgrid = ':Todos';

        $.each(usuario_tipo_json, function(index, value) {
            usuario_tipo_select += '<option value="' + index + '">' + value + '</option>';
            usuario_tipo_jqgrid += ';' + index + ':' + value;
        });

    // === SEXO ===
        var sexo_json   = $.parseJSON('{!! json_encode($sexo_array) !!}');
        var sexo_select = '';
        var sexo_jqgrid = ':Todos';

        $.each(sexo_json, function(index, value) {
            sexo_select += '<option value="' + index + '">' + value + '</option>';
            sexo_jqgrid += ';' + index + ':' + value;
        });

    // === EDAD ===
        var edad_json   = $.parseJSON('{!! json_encode($edad_array) !!}');
        var edad_select = '';
        var edad_jqgrid = ':Todos';

        $.each(edad_json, function(index, value) {
            edad_select += '<option value="' + index + '">' + value + '</option>';
            edad_jqgrid += ';' + index + ':' + value;
        });

    // === DIRIGIDO A ===
        var dirigido_a_json   = $.parseJSON('{!! json_encode($dirigido_a_array) !!}');
        var dirigido_a_select = '';
        var dirigido_a_jqgrid = ':Todos';

        $.each(dirigido_a_json, function(index, value) {
            dirigido_a_select += '<option value="' + index + '">' + value + '</option>';
            dirigido_a_jqgrid += ';' + index + ':' + value;
        });

    // === RESOLUCION TIPO DISPOSICION ===
        var resolucion_tipo_disposicion_json   = $.parseJSON('{!! json_encode($resolucion_tipo_disposicion_array) !!}');
        var resolucion_tipo_disposicion_select = '';
        var resolucion_tipo_disposicion_jqgrid = ':Todos';

        $.each(resolucion_tipo_disposicion_json, function(index, value) {
            resolucion_tipo_disposicion_select += '<option value="' + index + '">' + value + '</option>';
            resolucion_tipo_disposicion_jqgrid += ';' + index + ':' + value;
        });

    // === CONTADOR DE GESTIONES ===
        var anio_filter = '';
        var gestion_i = {!! $gestion_i !!};
        var gestion_f = {!! $gestion_f !!};
        for (var i = gestion_i; i <= gestion_f; i++){
            anio_filter += '<option value="' + i + '">' + i + '</option>';
        }

    $(document).ready(function(){
        //=== INICIALIZAR ===
            $('#anio_filter').append(anio_filter);
            $("#anio_filter option[value=" + gestion_f +"]").attr("selected","selected");

        $("#wizard").steps();

        $('#modal_1').modal();
    });
</script>
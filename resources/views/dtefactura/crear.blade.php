@extends("theme.$theme.layout")
@section('titulo')
    Factura
@endsection

@section("styles")
    <link rel="stylesheet" href="{{asset("assets/js/bootstrap-fileinput/css/fileinput.min.css")}}">
@endsection

@section("scriptsPlugins")
    <script src="{{asset("assets/js/bootstrap-fileinput/js/fileinput.min.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/js/bootstrap-fileinput/js/locales/es.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/js/bootstrap-fileinput/themes/fas/theme.min.js")}}" type="text/javascript"></script>
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/dtefactura/crear.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscarnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/dtefactura/buscardteguiadesp.js")}}" type="text/javascript"></script>
    @if(app()->environment('local'))
    <script>
    $(document).ready(function() {
        $('#btndiagnosticar').on('click', function() {
            if (!$('#form-general').valid()) return;
            var $btn = $(this);
            $btn.prop('disabled', true).text('Procesando...');
            $('#modalDiagnosticoBody').html('<p><i class="fa fa-spinner fa-spin"></i> Ejecutando diagnóstico...</p>');
            $('#modalDiagnostico').modal('show');

            var formData = new FormData($('#form-general')[0]);

            $.ajax({
                url: '{{ route("diagnosticar_dtefactura") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    var html = '<div class="alert ' + (data.ok ? 'alert-success' : 'alert-danger') + '">'
                        + '<strong>' + data.resultado + '</strong>'
                        + '</div>';
                    html += '<p class="text-muted">' + data.nota + '</p>';
                    html += '<table class="table table-condensed table-bordered">'
                        + '<thead><tr><th>#</th><th>Paso</th><th>Estado</th><th>Detalle</th></tr></thead><tbody>';
                    $.each(data.pasos, function(i, p) {
                        var clase = p.ok ? 'success' : 'danger';
                        var icono = p.ok ? '&#10003;' : '&#10007;';
                        html += '<tr class="' + clase + '"><td>' + (i + 1) + '</td>'
                            + '<td>' + p.paso + '</td>'
                            + '<td>' + icono + '</td>'
                            + '<td>' + p.detalle + '</td></tr>';
                    });
                    html += '</tbody></table>';
                    $('#modalDiagnosticoBody').html(html);
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
                    $('#modalDiagnosticoBody').html('<div class="alert alert-danger"><strong>Error HTTP ' + xhr.status + ':</strong> ' + msg + '</div>');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fa fa-bug"></i> Diagnosticar');
                }
            });
        });
    });
    </script>
    @endif
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Crear Factura</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('dtefactura')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('guardar_dtefactura')}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    @include('dtefactura.form')
                </div>
                <div class="box-footer text-center">
                    @include('includes.boton-form-crear')
                    @if(app()->environment('local'))
                    &nbsp;
                    <button type="button" class="btn btn-warning" id="btndiagnosticar"
                        title="Prueba el guardado con folio ficticio (no guarda nada, no consume folios reales)">
                        <i class="fa fa-bug"></i> Diagnosticar
                    </button>
                    @endif
                </div>
            </form>
            @if(app()->environment('local'))
            {{-- Modal diagnóstico --}}
            <div class="modal fade" id="modalDiagnostico" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-bug"></i> Resultado del Diagnóstico</h4>
                        </div>
                        <div class="modal-body" id="modalDiagnosticoBody">
                            <p>Procesando...</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
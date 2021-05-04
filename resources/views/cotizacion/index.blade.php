@extends("theme.$theme.layout")
@section('titulo')
Cotización
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/cotizacion/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Cotización</h3>
                <!--@if (session('aux_aprocot') == '0')-->
                    <div class="box-tools pull-right">
                        <a href="{{route('crear_cotizacion')}}" class="btn btn-block btn-success btn-sm">
                            <i class="fa fa-fw fa-plus-circle"></i> Crear Cotización
                        </a>
                    </div>                        
                <!--@endif-->
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data-cotizacion">
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th class="width200">Fecha</th>
                                <th>Cliente</th>
                                <th class="ocultar">aprobstatus</th>
                                <th class="ocultar">aprobobs</th>
                                <th class="ocultar">contador</th>
                                <th class="width70"></th>
                            </tr>
                        </thead>
                        <tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
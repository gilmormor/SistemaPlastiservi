@extends("theme.$theme.layout")
@section('titulo')
Guia Despacho Interna
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/guiadespint/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Guia Despacho Interna</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_guiadespint')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Crear Guia Interna
                    </a>
                </div>                        
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data-cotizacion">-->
                    <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-cotizacion">
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th class="width200">Fecha</th>
                                <th class="width200">RUT</th>
                                <th>Cliente</th>
                                <th class='tooltipsC' title='Ver PDF'>PDF</th>
                                <th class="ocultar">aprobstatus</th>
                                <th class="ocultar">aprobobs</th>
                                <th class="width70">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection
@extends("theme.$theme.layout")
@section('titulo')
Guia Directa
@endsection

<?php 
    $aux_vista = 'F';
?>

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/dteguiadespdir/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Guia Directa DTE</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_dteguiadespdir')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nueva Guia
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id='tabla-data-factura' name='tabla-data-factura' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                        <thead>
                            <tr>
                                <th class="width70 tooltipsC" title='Guia'>ID</th>
                                <th class='tooltipsC' title='Fecha'>Fecha</th>
                                <th>RUT</th>
                                <th>Razón Social</th>
                                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                                <th class='tooltipsC' title='Guia'>Guia</th>
                                <th class='tooltipsC' title='Comuna'>Comuna</th>
                                <th class="ocultar">Obs Bloqueo</th>
                                <th class="ocultar">oc_folder</th>
                                <th class="ocultar">oc_file</th>
                                <th class="ocultar">nombrepdf</th>
                                <th class="ocultar">updated_at</th>
                                <th class="width70">Acción</th>
                            </tr>
                        </thead>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@include('generales.despachoanularguiafact')
@endsection
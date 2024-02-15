@extends("theme.$theme.layout")
@section('titulo')
Precios por Categoria y Giro
@endsection
@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportcategoriaprod_giro/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Precios por Categoria y Giro</h3>
                <div class="box-tools pull-right">
                    <div class="col-xs-12 col-md-5 col-sm-5">
                        <button type="button" id="btnpdfJS" name="btnpdfJS" class="btn btn-success tooltipsC" title="Reporte PDF" onclick="ejecutarConsulta(2)">
                            <i class='glyphicon glyphicon-print'></i> Reporte
                        </button>
                    </div>
                    <!--
                    <div class="col-xs-12 col-md-5 col-sm-5">
                        <button type="button" id="btnexportarExcel" name="btnexportarExcel" class="btn btn-success tooltipsC" title="Exportar Excel" onclick="ejecutarConsulta(3)">
                            <i class='fa fa-fw fa-file-excel-o'> </i> Excel
                        </button>
                    </div>
                    -->
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id='tabla-data-consulta' name='tabla-data-consulta' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='10'>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th style='text-align:right'>Distribuidor</th>
                                <th style='text-align:right'>Comercializadora</th>
                                <th style='text-align:right'>Cliente Final</th>
                                <th style='text-align:right'>Meson</th>
                                <th style='text-align:right'>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

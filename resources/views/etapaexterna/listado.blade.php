@extends("theme.$theme.layout")
@section('titulo')
Trazabilidad Etapa Externa
@endsection

@section("scripts")
    <script>
        const EXT_RUTA_LISTADO = "{{ route('etapaexterna_listadopage') }}";
        const EXT_IMP_ETIQUETA = "{{ url('etapaexterna/etiqueta') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/etapaexterna/listado.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Etiquetas impresas — Proceso Externo</h3>
            </div>
            <div class="box-body">
                {{-- Se listan las tiradas de etiquetas: son el registro mas fino
                     del proceso, y cada una indica de que planilla salio. --}}
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <label for="fechad">Fecha Desde</label>
                        <input type="text" id="fechad" name="fechad" class="form-control date-picker"
                               placeholder="DD/MM/AAAA">
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <label for="fechah">Fecha Hasta</label>
                        <input type="text" id="fechah" name="fechah" class="form-control date-picker"
                               placeholder="DD/MM/AAAA">
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <label for="f_opdetregprod_id">N° de Lote</label>
                        <input type="text" id="f_opdetregprod_id" name="f_opdetregprod_id"
                               class="form-control" maxlength="10">
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <label>&nbsp;</label><br>
                        <button type="button" id="btnconsultar" class="btn btn-success">
                            <i class="fa fa-search"></i> Consultar
                        </button>
                    </div>
                </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-condensed"
                           id="tabla-data" data-page-length="25">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Impresa</th>
                                <th>Lote</th>
                                <th title="Registros de producción que consumieron el lote">Producido en</th>
                                <th>Planilla</th>
                                <th>Etapa</th>
                                <th>Máquina</th>
                                <th>Operario</th>
                                <th>Fecha Prod.</th>
                                <th>Turno</th>
                                <th>Kg/Paq</th>
                                <th>Unid/Paq</th>
                                <th>Cant.</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Impresion directa, igual que en la pantalla principal. Ver la nota del
     iframe en index.blade.php sobre por que no se oculta con display:none. --}}
<iframe id="contimpresion" src="" title="Documento para imprimir"
        style="position:fixed; left:-10000px; top:0; width:996px; height:900px; border:0;"></iframe>
@endsection

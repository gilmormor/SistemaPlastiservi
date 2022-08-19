@extends("theme.$theme.layout")
@section('titulo')
Inventario Control
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/invcontrol/index.js")}}" type="text/javascript"></script>
@endsection
<?php
    use App\Models\CategoriaGrupoValMes;
    $aux_mesanno = CategoriaGrupoValMes::mesanno(date("Y") . date("m"));
?>
@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Cierre y apertura de Inventario Mensual</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-7 col-sm-7">
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="annomes" class="col-lg-3 control-label requerido">Fecha:</label>
                                <div class="col-lg-8">
                                    <input type="text" name="annomes" id="annomes" class="form-control date-picker" value="{{old('annomes', $aux_mesanno ?? '')}}" readonly required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
                                <div class="col-lg-8">
                                    <?php
                                        $sucursal_id = 0;
                                        if(count($sucursales) == 1){
                                            $sucursal_id = $sucursales[0]->id;
                                        }
                                    ?>
                                    <select name="sucursal_id" id="sucursal_id" class="form-control selectpicker" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($sucursales as $sucursal)
                                            <option
                                                value="{{$sucursal->id}}"
                                                @if ($sucursal->id == $sucursal_id))
                                                    {{'selected'}}
                                                @endif
                                            >
                                                {{$sucursal->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-5 col-sm-5">
                        <div class="col-xs-12 col-md-12 col-sm-12 text-center">
                            <button type="button" id="btnprocesar" name="btnprocesar" class="btn btn-success tooltipsC" title="Cerrar mes e iniciar mes siguiente">Procesar</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data" data-page-length="25">
                        <thead>
                            <tr>
                                <th class="width70">CodProd</th>
                                <th>Producto</th>
                                <th>Categoria</th>
                                <th>Bodega</th>
                                <th style='text-align:center'>Stock</th>
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
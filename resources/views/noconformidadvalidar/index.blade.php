@extends("theme.$theme.layout")
@section('titulo')
Validar No Conformidad
@endsection


@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/noconformidadrecep/index.js")}}" type="text/javascript"></script>

@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Validar No Conformidad</h3>
            </div>
            <div class="box-body">
                <input type="hidden" name="funcvalidarai" id="funcvalidarai" value="{{old('funcvalidarai', $funcvalidarai ?? '')}}">
                <table class="table display AllDataTables table-hover table-condensed tablascons" id="tabla-data" data-page-length='30'>
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th class="width30">Rec</th>
                            <th>Fecha</th>
                            <th>Punto Normativo Hallazgo</th>
                            <th class='tooltipsC' title='Editar'>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=0;
                        ?>
                        @foreach ($datas as $data)
                            <?php
                                $recibido = "fa-mail-reply";
                            ?>
                            @include('noconformidadvalidar.conttablanc')
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('noconformidadrecep.formncmodal')
@include('noconformidadvalidar.validarai')

@endsection
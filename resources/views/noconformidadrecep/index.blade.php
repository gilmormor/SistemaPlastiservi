@extends("theme.$theme.layout")
@section('titulo')
Recepción No Conformidad
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
                <h3 class="box-title">Recepción No Conformidad</h3>
            </div>
            <div class="box-body">
                <table class="table display AllDataTables table-hover table-condensed tablascons" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th class="width30">Rec</th>
                            <th>Fecha</th>
                            <th>persona_id</th>
                            <th>Punto Normativo Hallazgo</th>
                            <th class='tooltipsC' title='Acción Inmediata'>AI</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=0;
                        ?>
                        @foreach ($datas as $data)
                            @if ($data->persona_id== $usuario_id AND (NOW()<= date("Y-m-d H:i:s",strtotime($data->fechahora."+ 1 days")) 
                                OR (!is_null($data->accioninmediata) and $data->accioninmediata!='')))
                                <?php
                                    $recibido = "fa-mail-reply";
                                ?>
                                @include('noconformidadrecep.conttablanc')
                            @endif
                        @endforeach

                        @foreach ($arearesps as $data)
                            @if ($data->persona_id== $usuario_id AND (NOW()>= date("Y-m-d H:i:s",strtotime($data->fechahora."+ 1 days")) 
                                AND (is_null($data->accioninmediata) or $data->accioninmediata=='')))
                                <?php
                                    $recibido = "fa-mail-reply-all";
                                ?>
                                
                                @include('noconformidadrecep.conttablanc')
                            @endif
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('noconformidadrecep.formncmodal')

@endsection
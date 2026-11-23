@extends("theme.$theme.layout")
@section('titulo')
Registrar Producción por Lote
@endsection

@section("scripts")
    <script>
        const RPL_RUTA_CONSULTA = "{{ url('regprodlote/consultar') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/regprodlote/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Registrar Producción</h3>
            </div>
            <div class="box-body">
                {{-- Campo grande y con foco: esta pantalla se usa de pie, junto a la
                     maquina, y a veces desde una tablet. El operario solo tiene que
                     leer el numero de la etiqueta del rollo. --}}
                <form action="{{ route('regprodlote_buscar') }}" method="post" id="form-lote">
                    @csrf
                    <div class="row">
                        <div class="col-xs-12 col-md-6 col-md-offset-3 text-center">
                            <label for="lote_id" style="font-size:16px;">
                                Número de lote que va a procesar
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="text" name="lote_id" id="lote_id" class="form-control"
                                       style="font-size:28px; height:60px; text-align:center;"
                                       maxlength="10" autocomplete="off" autofocus
                                       placeholder="Ej: 50">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btn-lg" type="submit" id="btncontinuar"
                                            style="height:60px;">
                                        <i class="fa fa-arrow-right"></i> Continuar
                                    </button>
                                </span>
                            </div>
                            <small class="text-muted">
                                Es el número que viene en la etiqueta del rollo.
                            </small>
                        </div>
                    </div>
                </form>

                {{-- Ficha de confirmacion: aparece al escribir el lote, antes de
                     continuar, para que el operario vea que es el rollo correcto. --}}
                <div class="row" style="margin-top:22px;">
                    <div class="col-xs-12 col-md-6 col-md-offset-3">
                        <div id="rpl-ficha" style="display:none;">
                            <table class="table table-condensed table-bordered">
                                <tr><th style="width:38%;">Lote</th><td id="rpl-lote"></td></tr>
                                <tr><th>Producto</th><td id="rpl-producto"></td></tr>
                                <tr><th>Producción del lote</th><td id="rpl-kg"></td></tr>
                                <tr><th>Viene de</th><td id="rpl-origen"></td></tr>
                                <tr><th>Continúa en</th><td id="rpl-destino"></td></tr>
                            </table>
                        </div>
                        <div id="rpl-error" class="alert alert-danger" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lo que el supervisor le rechazo a ESTE operario. El controlador ya
             filtro por usuario, etapas y sucursales: aqui no se vuelve a decidir
             quien ve que. Si no tiene rechazos, el bloque no se dibuja. --}}
        @if(count($tablas['rechazados']) > 0)
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-exclamation-triangle text-red"></i>
                    Registros que el supervisor le devolvio para corregir
                    <span class="label label-danger">{{ count($tablas['rechazados']) }}</span>
                </h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th style="width:70px;">Registro</th>
                                <th style="width:110px;">OP / Item</th>
                                <th style="width:150px;">Etapa</th>
                                <th style="width:90px;" class="text-right">Kg Prod.</th>
                                <th style="width:90px;" class="text-right">Kg Scrap</th>
                                <th>Motivo del rechazo</th>
                                <th style="width:110px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($tablas['rechazados'] as $rech)
                            <tr>
                                <td><strong>#{{ $rech->id }}</strong></td>
                                <td>{{ $rech->op_id }} / {{ $rech->opdet_id }}</td>
                                <td>{{ $rech->etapa_nombre }}</td>
                                <td class="text-right">{{ number_format($rech->kgprod, 2, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($rech->kgscrap, 2, ',', '.') }}</td>
                                {{-- El motivo se muestra completo y a la vista, no en un tooltip:
                                     es justo lo que el operario tiene que leer para corregir. --}}
                                <td class="text-red">{{ $rech->aprobobs }}</td>
                                <td class="text-center">
                                    <a href="{{ route('regprodlote_corregir', ['id' => $rech->id]) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="fa fa-pencil"></i> Corregir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">
                    Al guardar la correccion, el registro vuelve a quedar enviado a aprobacion.
                </small>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

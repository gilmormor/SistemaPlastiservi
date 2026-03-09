@extends("theme.$theme.layout")
@section('titulo')
Migration
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/migration/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">

        @include('includes.mensaje')

        <div class="box box-danger">

            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-database"></i> Panel de Migraciones
                </h3>
            </div>

            <div class="box-body">

                {{-- <div class="row">

                    <div class="col-md-6">

                        <div class="panel panel-default">

                            <div class="panel-heading">
                                <strong>Ejecutar Migraciones</strong>
                            </div>

                            <div class="panel-body">

                                <p class="text-muted">
                                    Este proceso ejecutará las migraciones pendientes en la base de datos.
                                </p>

                                <form action="{{route('guardar_migration')}}" 
                                      id="form-general" 
                                      class="form-horizontal" 
                                      method="POST"
                                      autocomplete="off">

                                    @csrf

                                    <div class="form-group">
                                        <div class="col-sm-12">

                                            <button type="submit"
                                                name="action"
                                                value="migrate"
                                                class="btn btn-danger btn-lg">

                                                <i class="fa fa-play"></i>
                                                Ejecutar Migrate

                                            </button>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">

                                            <h3>Rollback</h3>
                                            <input type="number" name="steps" placeholder="Pasos (opcional)" min="1" value="1">
                                            <input type="number" name="batch" placeholder="Batch (opcional)" min="1">
                                            <button type="submit" name="action" value="rollback">Ejecutar Rollback</button>

                                            
                                        </div>
                                    </div>



                            </div>

                        </div>

                    </div>

                </div> --}}
                <form action="{{route('guardar_migration')}}" 
                    id="form-general" 
                    method="POST" 
                    autocomplete="off">

                    @csrf


                    <div class="row">

                        <div class="col-md-6">

                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <strong>Migrar Base de Datos</strong>
                                </div>

                                <div class="panel-body">

                                    <p class="text-muted">
                                        Ejecuta todas las migraciones pendientes.
                                    </p>

                                    <button type="submit"
                                            name="action"
                                            value="migrate"
                                            class="btn btn-danger">
                                        <i class="fa fa-play"></i> Ejecutar Migrate
                                    </button>

                                </div>
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="panel panel-warning">
                                <div class="panel-heading">
                                    <strong>Rollback</strong>
                                </div>

                                <div class="panel-body">

                                    <p class="text-muted">
                                        Revierte migraciones ejecutadas anteriormente.
                                    </p>

                                    <div class="form-group">
                                        <label>Pasos</label>
                                        <input type="number"
                                            name="steps"
                                            class="form-control"
                                            placeholder="Cantidad de pasos"
                                            min="1"
                                            value="1">
                                    </div>

                                    <div class="form-group">
                                        <label>Batch</label>
                                        <input type="number"
                                            name="batch"
                                            class="form-control"
                                            placeholder="Batch específico"
                                            min="1">
                                    </div>

                                    <button type="submit"
                                            name="action"
                                            value="rollback"
                                            class="btn btn-warning">
                                        <i class="fa fa-undo"></i> Ejecutar Rollback
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                </form>

                {{-- RESULTADO DE LA MIGRACION --}}

                @if(isset($output))

                    <div class="row">

                        <div class="col-md-12">

                            <div class="panel panel-info">

                                <div class="panel-heading">
                                    <strong>Resultado ({{ $action }})</strong>
                                </div>

                                <div class="panel-body">
                                    <pre style="
                                    background:#1e1e1e;
                                    color:#00ff9c;
                                    padding:15px;
                                    border-radius:4px;
                                    max-height:400px;
                                    overflow:auto;
                                    ">{!! trim($output) !!}</pre>
                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>
</div>
@endsection
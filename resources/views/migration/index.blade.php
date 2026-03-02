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
                <h3 class="box-title">Migration</h3>
            </div>
            <div class="box-body">
                <div class="container">
                    <h1>Panel de Migraciones</h1>
                    
                    {{-- <form method="GET">
                        <h3>Migrar</h3>
                        <button type="submit" name="action" value="migrate">Ejecutar Migrate</button>
                        
                        <h3>Rollback</h3>
                        <input type="number" name="steps" placeholder="Pasos (opcional)" min="1" value="1">
                        <input type="number" name="batch" placeholder="Batch (opcional)" min="1">
                        <button type="submit" name="action" value="rollback">Ejecutar Rollback</button>
                        
                    </form> --}}

                    <form action="{{route('guardar_migration')}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                        @csrf
                        <h3>Migrar</h3>
                        <button type="submit" name="action" value="migrate">Ejecutar Migrate</button>
                        
                        {{-- <h3>Rollback</h3>
                        <input type="number" name="steps" placeholder="Pasos (opcional)" min="1" value="1">
                        <input type="number" name="batch" placeholder="Batch (opcional)" min="1">
                        <button type="submit" name="action" value="rollback">Ejecutar Rollback</button> --}}
                        

                    </form>
                        {{-- <h3>Otras opciones</h3>
                        <button type="submit" name="action" value="reset">Reset</button>
                        <button type="submit" name="action" value="refresh">Refresh</button>
                        <button type="submit" name="action" value="fresh">Fresh</button> --}}

                    
                    @if(isset($output))
                        <div class="output">
                            <h4>Resultado ({{ $action }}):</h4>
                            <pre>{{ $output }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
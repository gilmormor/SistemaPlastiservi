@extends("theme.$theme.layout")
@section("titulo")
Permiso - Rol
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/permiso-rol/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Menús - Rol</h3>
            </div>
            <div class="box-body">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data"  name="tabla-data">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends("theme.$theme.layout")
@section('titulo')
Pesaje
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/pesaje/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pesaje</h3>
                    <div class="box-tools pull-right">
                        <a href="{{route('crear_pesaje')}}" class="btn btn-block btn-success btn-sm">
                            <i class="fa fa-fw fa-plus-circle"></i> Crear Movimiento
                        </a>
                    </div>                        
            </div>
            <div class="box-body">
                @csrf @method("delete")
                <div class="table-responsive">
                    <table class="table display AllDataTables table-hover table-condensed " id="tabla-data-pesaje">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th class="width30">Fecha</th>
                                <th title='Descripción'>Descripción</th>
                                <th class="width150">PDF</th>
                                <th class="ocultar">obsaprob</th>
                                <th class="ocultar">updated_at</th>
                                <th class="width150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="todo-list1">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
    @include('generales.modalpdf')
    @include('generales.verpdf')
@endsection
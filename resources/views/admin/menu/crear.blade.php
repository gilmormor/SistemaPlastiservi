@extends("theme.$theme.layout")
@section('titulo')
    Sistema Menús    
@endsection

@section('scripts')
    <script src="{{asset("assets/pages/scripts/admin/menu/crear.js")}}" type="text/javascript"></script>
@endsection
@section('contenido')
    @include('includes.form-error')
    @include('includes.mensaje')
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Crear Menú</h3>
        <div class="box-tools pull-right">
            <a href="{{route('menu')}}" class="btn btn-block btn-info btn-sm">
                <i class="fa fa-fw fa-reply-all"></i> Volver al listado
            </a>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form action="{{route('guardar_menu')}}" id="form-general" name="form-general" class="form-horizontal" method="POST" autocomplete="off">
            @csrf
            <div class="box-body">
                @include('admin.menu.form')
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
                @include('includes.boton-form-crear')
            </div>
            <!-- /.box-footer -->
        </form>
    </div>
@endsection
@extends("theme.$theme.layout")
@section('titulo')
Productos
@endsection

@section("scripts")
    <script type="text/javascript" src="{{asset("assets/js/jquery-barcode.js")}}"></script>
    <script src="{{asset("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/producto/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Productos</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_producto')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo registro
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <!--<table id="tabla-data-productos" class="table-striped table-hover display" style="width:100%">-->
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th>Nombre</th>
                                <th>Categoria</th>
                                <th>Cod-Int</th>
                                <th>Cod Barra</th>
                                <th>DiamExt</th>
                                <th>DiaPulg</th>
                                <th>Espesor mm</th>
                                <th>Largo</th>
                                <th>Peso</th>
                                <th>Tipo Union</th>
                                <th>Precio</th>
                                <th class="width70"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $aux_nfila = 0; ?>
                            @foreach ($datas as $data)
                            <?php $aux_nfila++; ?>
                            <tr>
                                <td>{{str_pad($data->id, 3, "0", STR_PAD_LEFT)}}</td>
                                <td>{{$data->nombre}}</td>
                                <td>{{$data->categoriaprod->nombre}}
                                <td>{{$data->codintprod}}</td>
                                <td id="barcodeTarget{{$aux_nfila}}" class="barcodeTarget" onLoad="generateBarcode()">{{$data->codbarra}}</td>
                                <td>{{$data->diamextmm}}</td>
                                <td>{{$data->diamextpg}}</td>
                                <td>{{$data->espesor}}</td>
                                <td>{{$data->long}}</td>
                                <td>{{$data->peso}}</td>
                                <td>{{$data->tipounion}}</td>
                                <td>{{$data->precioneto}}</td>
                                <td>
                                    <a href="{{route('editar_producto', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                        <i class="fa fa-fw fa-pencil"></i>
                                    </a>
                                    <form action="{{route('eliminar_producto', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
                                        @csrf @method("delete")
                                        <button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro">
                                            <i class="fa fa-fw fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box-footer text-center">
    <a href="{{route('listar_producto', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Listar Productos" target="_blank">
        <i class="fa fa-fw fa-file-pdf-o"></i>                                    
    </a>
</div>
@endsection
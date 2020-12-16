@extends("theme.$theme.layout")
@section('titulo')
Devolver Nota de Venta a Vendedor
@endsection

@section("scripts")
    <script type="text/javascript" src="{{asset("assets/js/jquery-barcode.js")}}"></script>
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Devolver Nota de Venta a Vendedor</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th class="width30">Nro Cot</th>
                                <th class="width30">Fecha</th>
                                <th>Cliente</th>
                                <th class="width30"><label for="" title='PDF' data-toggle='tooltip'>PDF</label></th>
                                <th class="width70"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $aux_nfila = 0; ?>
                            @foreach ($datas as $data)
                                <?php 
                                    $aux_nfila++; 
                                ?>
                                <tr id="fila{{$aux_nfila}}" name="fila{{$aux_nfila}}">
                                    <td>{{$data->id}}</td>
                                    <td >{{$data->cotizacion_id}}</td>
                                    <td class="width200">{{date('d-m-Y', strtotime($data->fechahora))}} {{date("h:i:s A", strtotime($data->fechahora))}}</td>
                                    <td >{{$data->razonsocial}}</td>
                                    <td>
                                        <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->id}},{{"1"}})' title='Nota de venta' data-toggle='tooltip'>
                                            <i class="fa fa-fw fa-file-pdf-o"></i>                                    
                                        </a>
                                        <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->id}},{{"2"}})' title='Precio x Kg' data-toggle='tooltip'>
                                            <i class="fa fa-fw fa-file-pdf-o"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route('editar_notaventa', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                            <i class="fa fa-fw fa-exchange"></i>
                                        </a>
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

@include('generales.modalpdf')
@endsection
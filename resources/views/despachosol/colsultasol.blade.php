@extends("theme.$theme.layout")
@section('titulo')
Solicitud Despacho
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/despachosol/colsultasol.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
@include('generales.filtrosconsultasoldespacho') <!--Esta es la pantalla de Filtros-->
@include('generales.buscarcliente')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection

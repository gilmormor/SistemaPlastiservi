@extends("theme.$theme.layout")
@section('titulo')
Jefatura
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/jefaturaareasuc/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')

<div class="box">
    <div class="box-body">
      <form method="GET" action="{{route('guardar_jefaturaAreaSuc')}}" accept-charset="UTF-8">
        {{ csrf_field() }}
        <div class="form-group">
          <label for="susursal_id">Sucursal</label>
          <select class="form-control" name="susursal_id" id="susursal_id">
            <option value="">Seleccione...</option>
            @foreach($sucursales as $sucursal)
              <option value="{{$sucursal->id}}"> 
                {{$sucursal->nombre}}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="area_id">Area</label>
          <select class="form-control" name="area_id" id="area_id">
            <option value="">Seleccione...</option>
          </select>
        </div>
      </form>
      <form method="POST" action="{{route('guardar_jefaturaAreaSuc')}}" accept-charset="UTF-8">
      {{ csrf_field() }}
      <input type="text" name="ids" id="ids" style="display: none">
      <table class="table table-striped table-bordered table-hover" id="dataTables-example">
        <thead>
            <tr>
                <th>Instrumento</th>
                <th>Fecha</th>
                <th>Porcentaje</th>
                <th>Objetivo</th>
            </tr>
        </thead>
        <tbody id="tbody">
        </tbody>
      </table>
      <div class="row" id="varios" style="display: none">
        <div class="col col-md-1">
          <button onclick="agregar()" class="btn btn-primary" type="button" id="btnFalta"><i class="fa fa-plus"></i> Agregar</button>
        </div>
        <div class="col col-md-1">
          <button onclick="ultimo()" class="btn btn-danger" type="button" id="btnFalta"><i class="fa fa-trash-o"></i> Borrar</button>
        </div>
        <div class="col col-md-4">
          <div class="input-group">
            <span class="input-group-addon">Faltante</span>
            <input type="number" value="100" disabled class="form-control" name="faltante" id="faltante">
            <span class="input-group-addon">%</span>
          </div>
        </div>
        <div class="col col-md-4">
          <div class="input-group">
            <span class="input-group-addon">Total</span>
            <input type="number" value="0" disabled class="form-control" name="total" id="total">
            <span class="input-group-addon">%</span>
          </div>
        </div>
        <div class="col col-md-2">
          <div class="form-group">
            <button class="btn btn-success" disabled="disabled" type="submit" id="btnGuardar"><i class="fa fa-upload"></i>  Guardar</button>
          </div>
        </div>
      </div>
      </form>
    </div>
  </div>

@endsection
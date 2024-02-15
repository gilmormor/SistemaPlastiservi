<input type="text" name="categoriaprod_id" id="categoriaprod_id" value="{{old('categoriaprod_id', $data->id ?? '')}}" style="display:none;" required readonly/>
<div class="form-group">
    <div class="col-lg-6 col-md-offset-3">
        <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
        <div class="col-lg-9">
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required readonly/>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-lg-6 col-md-offset-3">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Giro</h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data">
                        <thead>
                            <tr>
                                <th style='display:none;'>ID</th>
                                <th style="width: 70% !important;">Nombre</th>
                                <th style="width: 10% !important;text-align:right;">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($giros as $giro)
                                <input type="text" id="giro_id" name="giro_id[]" value="{{old('giro_id', $giro->giro_id ?? '')}}" style="display:none;"/>
                                <tr>
                                    <td style='display:none;'>{{$giro->giro_id}}</td>
                                    <td>{{$giro->nombre}}</td>
                                    <td style="text-align:right"><input class="numericopositivosindec" type="text" style="text-align:right" id="preciokg" name="preciokg[]" value="{{number_format(old('preciokg', $giro->preciokg ?? ''), 2, ".", "")}}"/></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
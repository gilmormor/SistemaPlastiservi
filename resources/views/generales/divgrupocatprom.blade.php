<div id="divgrupocatprom" name="divgrupocatprom" class="box-body" style="display:none;">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="tabla-data-grupocatprom" name="tabla-data-grupocatprom" style="font-size:14px;width: 40%;">
            <thead>
                <tr>
                    <th>Nombre Grupo (Precio Promedio)</th>
                    <th style="text-align:right">Total $</th>
                    <th style="text-align:right">Total Kg</th>
                    <th style="text-align:right" title="Precio Promedio">Promedio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tablas["grupocatproms"] as $grupocatprom)
                    <tr id="filagrupocatprom{{$grupocatprom->id}}" name="filagrupocatprom{{$grupocatprom->id}}" style="display:none;" data-cat="{{$grupocatprom->categoriaprod_ids}}">
                        <td id="grupocatprom_nombre{{$grupocatprom->id}}" name="grupocatprom_nombre{{$grupocatprom->id}}" categoriaprod_ids="{{$grupocatprom->categoriaprod_ids}}" class="grupocatprom_nombre" fila="{{$grupocatprom->id}}">
                            {{$grupocatprom->nombre}}
                        </td>
                        <td id="grupocatprom_dinero{{$grupocatprom->id}}" name="grupocatprom_dinero{{$grupocatprom->id}}" valor="0" style="text-align:right"title="{{$grupocatprom->nombre}}: Total $">
                        </td>
                        <td id="grupocatprom_kg{{$grupocatprom->id}}" name="grupocatprom_kg{{$grupocatprom->id}}" valor="0" style="text-align:right" title="{{$grupocatprom->nombre}}: Total Kilos">
                        </td>
                        <td id="grupocatprom_prom{{$grupocatprom->id}}" name="grupocatprom_prom{{$grupocatprom->id}}" valor="0" style="text-align:right" title="{{$grupocatprom->nombre}}: Precio Promedio">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
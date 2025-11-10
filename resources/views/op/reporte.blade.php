<!--<link rel="stylesheet" href="{{asset("assets/$theme/bower_components/bootstrap/dist/css/bootstrap.min.css")}}">-->
<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<script src="{{asset("assets/$theme/bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png")}}" style="max-width:1400%;width:auto;height:auto;">
					<p>RUT: {{$empresa[0]['rut']}}</p>
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div>
					<span class="h3">Op / {{$op->otdet->ot->sucursal->nombre}}</span>
					<p>Nro: <strong> {{ str_pad($op->id, 10, "0", STR_PAD_LEFT) }}</strong>
						@if ($op->opanul)
							<small class="btn btn-danger btn-xs">Anulado</small>
						@endif
					</p>
					<p>Fecha Act: {{date('d/m/Y h:i:s A')}}</p>
					<p>Fecha Elab: {{date('d/m/Y h:i:s A', strtotime($op->created_at))}}</p>
					<p>Usuario: {{$op->usuario->nombre}}</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3">Cliente</span>
					<table class="datos_cliente">
						<tr class="headt">
							<td style="width:10%"><label>Rut:</label></td><td style="width:50%"><p id="rutform" name="rutform">{{number_format( substr ( $op->otdet->ot->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $op->otdet->ot->cliente->rut, strlen($op->otdet->ot->cliente->rut) -1 , 1 )}}</p></td>
							<td style="width:10%"><label>Teléfono:</label></td><td style="width:30%"><p>{{$op->otdet->ot->cliente->telefono}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Nombre:</label></td><td style="width:50%"><p>{{$op->otdet->ot->cliente->razonsocial}}</p></td>
							<td style="width:10%"><label>Dirección:</label></td><td style="width:30%"><p>{{$op->otdet->ot->cliente->direccion}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Contacto:</label></td><td style="width:50%"><p>{{$op->otdet->ot->cliente->contactonombre}}</p></td>
							<td style="width:10%"><label>Comuna:</label></td><td style="width:30%"><p>{{$op->otdet->ot->cliente->comuna->nombre}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Producto:</label></td><td style="width:50%"><p>{{$op->otdet->producto->atributosProducto($op->otdet->producto_id)['nombre']}}</p></td>
							<td style="width:10%"><label>Cod:</label></td><td style="width:30%"><p>{{$op->otdet->producto_id}}</p></td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>
	<div>
		<table id="factura_detalle">
			<thead>
				<tr>
					<th width="150px" class="textleft">Etapa Produccion</th>
					<th width="150px" class="textleft">Obs</th>
					<th width="25px" class="textright">Cant</th>
					<th width="30px" class="textright">Kg</th>
					<th width="30px" class="textright">KgRec</th>
					<th width="30px" class="textright">Kgprod</th>
					<th width="30px" class="textright">KgScrap</th>
					<th width="30px" class="textright">Kg Saldo</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				@foreach($op->opdets as $opdet)
					<tr class="headt" style="height:150%;">
						<td class="textleft">{{$opdet->areaproduccionsucetapaprod->etapaprod->nombre}}</td>
						<td class="textleft">{{$opdet->obs}}</td>
						<td class="textright">{{number_format($opdet->cant, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($opdet->kg, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($opdet->kgrec, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($opdet->kgprod, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($opdet->kgscrap, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($opdet->saldokg, 0, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div>
		@if (!is_null($op->obs))
			<p class="nota"><strong> <H2>Observaciones: {{$op->obs}}</H2></strong></p>			
		@endif
	</div>
	{{-- <br>
	<div class="round1">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<tr>
				<td colspan="3" class="textleft" width="40%"><span><strong>Fec Estimada Desp: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d/m/Y', strtotime($op->fechaestdesp))}}</span></td>
			</tr>
			<tr>
				<td colspan="3" class="textleft" width="40%"><span><strong>Orden de Compra: </strong></span></td>
				<td class="textleft" width="50%"><span>{{isset($op->otoc) ? $op->otoc->oc_id : (isset($op->otnotaventa) ? $op->otnotaventa->notaventa->oc_id : "")}}</span></td>
			</tr>
			<tr>
				<td colspan="3" class="textleft" width="40%"><span><strong>No. Cotización: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad(isset($op->otnotaventa) ? $op->otnotaventa->notaventa->cotizacion_id : 0, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="3" class="textleft" width="40%"><span><strong>Nota de Venta: </strong></span></td>
				<td class="textleft" width="50%"><span>{{ str_pad(isset($op->otnotaventa) ? $op->otnotaventa->notaventa_id : 0, 10, "0", STR_PAD_LEFT) }}</span></td>
			</tr>
		</table>
	</div> --}}
</div>

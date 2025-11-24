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
					<span class="h3">Ot / {{$ot->sucursal->nombre}}</span>
					<p>Nro: <strong> {{ str_pad($ot->id, 10, "0", STR_PAD_LEFT) }}</strong>
						@if ($ot->despachosolanul)
							<small class="btn btn-danger btn-xs">Anulado</small>
						@endif
					</p>
					<p>Fecha Act: {{date('d/m/Y h:i:s A')}}</p>
					<p>Fecha Elab: {{date('d/m/Y h:i:s A', strtotime($ot->fechahora))}}</p>
					<p>Usuario: {{$ot->usuario->nombre}}</p>
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
							<td style="width:10%"><label>Rut:</label></td><td style="width:50%"><p id="rutform" name="rutform">{{number_format( substr ( $ot->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $ot->cliente->rut, strlen($ot->cliente->rut) -1 , 1 )}}</p></td>
							<td style="width:10%"><label>Teléfono:</label></td><td style="width:30%"><p>{{$ot->cliente->telefono}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Nombre:</label></td><td style="width:50%"><p>{{$ot->cliente->razonsocial}}</p></td>
							<td style="width:10%"><label>Dirección:</label></td><td style="width:30%"><p>{{$ot->cliente->direccion}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Contacto:</label></td><td style="width:50%"><p>{{$ot->cliente->contactonombre}}</p></td>
							<td style="width:10%"><label>Comuna:</label></td><td style="width:30%"><p>{{$ot->cliente->comuna->nombre}}</p></td>
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
					<th width="25px">Cod</th>
					<th width="30px">Cant.</th>
					<th width="25px" class="textcenter">UN</th>
					<th width="150px" class="textleft">Descripción</th>
					<th width="15px">Fab</th>
					<th width="30px" class="textright">Total Kg</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php
					$totalkg = 0;
				?>
				@foreach($ot->otdets as $otdet)
					<?php
						//$aux_promPonderadoPrecioxkilo += ($ot->notaventadetalle->precioxkilo * (($ot->notaventadetalle->totalkilos * 100) / $aux_sumtotalkilos)) / 100 ;
						$totalkg += $otdet->kg;
						$atributoProd = $otdet->producto->atributosProducto($otdet->producto_id);
						$aux_producto_nombre = $atributoProd["nombre"];
					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$otdet->producto_id}}</td>
						<td class="textcenter">{{number_format($otdet->cant, 0, ",", ".")}}</td>
						<td class="textcenter">{{$otdet->unidadmedida->nombre}}</td>
						<td class="textleft">{{$aux_producto_nombre}}
							@if ($otdet->obs)
								<br><span class="small-text">{{$otdet->obs}}</span>
							@endif
						</td>
						<td class="textcenter">{{$otdet->requiere_fabricacion == 1 ? "Si" : "No"}}</td>
						<td class="textright">{{number_format($otdet->kg, 2, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" class="textright"><span><strong>Total</strong></span></td>
					<td class="textright"><span><strong>{{number_format($totalkg, 2, ",", ".")}}</strong></span></td>
				</tr>
			</tfoot>
		</table>
	</div>
	{{-- <div>
		<table id="factura_detalle">
			<tr class="headt">
				<td colspan="3" class="textright" width="90%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($neto, $datosArray["monedaLocal"] ? 0 : 3, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="3" class="textright" width="90%"><span><strong>IVA {{$ot->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(($neto * $ot->piva)/100, $datosArray["monedaLocal"] ? 0 : 3, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="3" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($neto * ($ot->piva+100)/100, $datosArray["monedaLocal"] ? 0 : 3, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div> --}}
	<div>
		@if (!is_null($ot->obs))
			<p class="nota"><strong> <H2>Observaciones: {{$ot->obs}}</H2></strong></p>			
		@endif
	</div>
	<br>
	<div class="round1">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<tr>
				<td colspan="3" class="textleft" width="40%"><span><strong>Fec Estimada Desp: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d/m/Y', strtotime($ot->fechaestdesp))}}</span></td>
			</tr>
			<tr>
				<td colspan="3" class="textleft" width="40%"><span><strong>Orden de Compra: </strong></span></td>
				<td class="textleft" width="50%"><span>{{isset($ot->otoc) ? $ot->otoc->oc_id : (isset($ot->otnotaventa) ? $ot->otnotaventa->notaventa->oc_id : "")}}</span></td>
			</tr>
			<tr>
				<td colspan="3" class="textleft" width="40%"><span><strong>No. Cotización: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad(isset($ot->otnotaventa) ? $ot->otnotaventa->notaventa->cotizacion_id : 0, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="3" class="textleft" width="40%"><span><strong>Nota de Venta: </strong></span></td>
				<td class="textleft" width="50%"><span>{{ str_pad(isset($ot->otnotaventa) ? $ot->otnotaventa->notaventa_id : 0, 10, "0", STR_PAD_LEFT) }}</span></td>
			</tr>
		</table>
	</div>
</div>

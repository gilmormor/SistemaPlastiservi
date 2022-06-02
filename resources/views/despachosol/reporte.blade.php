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
				<!--
				<div>
					<span class="h2">COTIZACIÓN</span>
                    <p>{{$despachosol->notaventa->sucursal->direccion}}</p>
					<p>Teléfono: {{$despachosol->notaventa->sucursal->telefono1}}</p>
					<p>Email: {{$despachosol->notaventa->sucursal->email}}</p>
				</div>-->
			</td>
			<td class="info_factura">
				<div>
					<span class="h3">Solicitud de Despacho</span>
					<p>Nro: <strong> {{ str_pad($despachosol->id, 10, "0", STR_PAD_LEFT) }}</strong>
						@if ($despachosol->despachosolanul)
							<small class="btn btn-danger btn-xs">Anulado</small>
						@endif
					</p>
					<p>Fecha Act: {{date('d-m-Y h:i:s A')}}</p>
					<p>Fecha: {{date('d-m-Y', strtotime($despachosol->fechahora))}}</p>
					<p>Hora: {{date("h:i:s A", strtotime($despachosol->fechahora))}}</p>
					<p>Vendedor: {{$despachosol->notaventa->vendedor->persona->nombre . " " . $despachosol->notaventa->vendedor->persona->apellido}} </p>
					<p>Teléfono: {{$despachosol->notaventa->vendedor->persona->telefono}} </p>
					<p>email: {{$despachosol->notaventa->vendedor->persona->email}} </p>
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
							<td style="width:10%"><label>Rut:</label></td><td style="width:50%"><p id="rutform" name="rutform">{{number_format( substr ( $despachosol->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachosol->notaventa->cliente->rut, strlen($despachosol->notaventa->cliente->rut) -1 , 1 )}}</p></td>
							<td style="width:10%"><label>Teléfono:</label></td><td style="width:30%"><p>{{$despachosol->notaventa->cliente->telefono}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Nombre:</label></td><td style="width:50%"><p>{{$despachosol->notaventa->cliente->razonsocial}}</p></td>
							<td style="width:10%"><label>Dirección:</label></td><td style="width:30%"><p>{{$despachosol->notaventa->cliente->direccion}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Contacto:</label></td><td style="width:50%"><p>{{$despachosol->notaventa->cliente->contactonombre}}</p></td>
							<td style="width:10%"><label>Comuna:</label></td><td style="width:30%"><p>{{$despachosol->notaventa->cliente->comuna->nombre}}</p></td>
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
					<th width="30px">Cod</th>
					<th width="30px">Cant.</th>
					<th width="30px">Sol</th>
					<th class="textcenter">Unidad</th>
					<th class="textleft">Descripción</th>
					<th class="textleft">Diam</th>
					<th class="textleft">Clase</th>
					<th class="textright">Largo</th>
					<th class="textcenter">TU</th>
					<th class="textright">Peso</th>
					<!--<th class="textright">$ x Kg</th>-->
					<th class="textright">Total Kg</th>
					<th class="textright" width="90px">Precio Unit</th>
					<th class="textright" width="90px">Total Neto</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php
					$aux_sumprecioxkilo = 0;
					$aux_sumtotalkilos = 0;
					$aux_promPonderadoPrecioxkilo = 0;
					$neto = 0;
				?>
				@foreach($despachosoldets as $despachosoldet)
					<?php
						$aux_sumprecioxkilo += $despachosoldet->notaventadetalle->precioxkilo;
						$aux_sumtotalkilos += $despachosoldet->cantsoldesp * ($despachosoldet->notaventadetalle->totalkilos/$despachosoldet->notaventadetalle->cant);
					?>
				@endforeach
				@foreach($despachosoldets as $despachosoldet)
					<?php
						//$aux_promPonderadoPrecioxkilo += ($despachosoldet->notaventadetalle->precioxkilo * (($despachosoldet->notaventadetalle->totalkilos * 100) / $aux_sumtotalkilos)) / 100 ;
						$peso = $despachosoldet->notaventadetalle->totalkilos/$despachosoldet->notaventadetalle->cant;
						$totalkilos = $despachosoldet->cantsoldesp * $peso;
						$subtotal = $despachosoldet->cantsoldesp * $despachosoldet->notaventadetalle->preciounit;
						$neto += $subtotal;

					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$despachosoldet->notaventadetalle->producto_id}}</td>
						<td class="textcenter">{{number_format($despachosoldet->notaventadetalle->cant, 0, ",", ".")}}</td>
						<td class="textcenter">{{number_format($despachosoldet->cantsoldesp, 0, ",", ".")}}</td>
						<td class="textcenter">{{$despachosoldet->notaventadetalle->producto->categoriaprod->unidadmedidafact->nombre}}</td>
						<td class="textleft">{{$despachosoldet->notaventadetalle->producto->nombre}}</td>
						<td class="textcenter">
							{{$despachosoldet->notaventadetalle->producto->diametro}}
						</td>
						<td class="textcenter">{{$despachosoldet->notaventadetalle->producto->claseprod->cla_nombre}}</td>
						<td class="textright">{{$despachosoldet->notaventadetalle->producto->long}} mts</td>
						<td class="textcenter">{{$despachosoldet->notaventadetalle->producto->tipounion}}</td>
						<td class="textright">{{number_format($despachosoldet->notaventadetalle->producto->peso, 2, ",", ".")}}</td>
						<!--<td class="textright">{{number_format($despachosoldet->notaventadetalle->precioxkilo, 2, ",", ".")}}</td>-->
						<td class="textright">{{number_format($totalkilos, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($despachosoldet->notaventadetalle->preciounit, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($subtotal, 0, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10" class="textright"><span><strong>Totales</strong></span></td>
					<!--<td class="textright"><span><strong>{{number_format($aux_promPonderadoPrecioxkilo, 2, ",", ".")}}</strong></span></td>-->
					<td class="textright"><span><strong>{{number_format($aux_sumtotalkilos, 2, ",", ".")}}</strong></span></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div>
		<table id="factura_detalle">
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($neto, 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>IVA {{$despachosol->notaventa->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(round(($neto * $despachosol->notaventa->piva)/100), 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(round($neto * ($despachosol->notaventa->piva+100)/100), 0, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
	<div>
		<p class="nota"><strong>Observaciones: {{$despachosol->observacion}}</strong></p>
	</div>
	<br>
	<div class="round1">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<!--
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($despachosol->plazoentrega))}}</span></td>
			</tr>
			-->
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Comuna: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachosol->comunaentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachosol->lugarentrega}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachosol->notaventa->plazopago->descripcion}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachosol->tipoentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachosol->contacto}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto email: </strong></span></td>
				<td class="textleft" width="50%"><span>{{strtolower($despachosol->contactoemail)}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto Teléfono: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachosol->contactotelf}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Orden de Compra: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachosol->notaventa->oc_id}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>No. Cotización: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachosol->notaventa->cotizacion_id, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Nota de Venta: </strong></span></td>
				<td class="textleft" width="50%"><span>{{ str_pad($despachosol->notaventa_id, 10, "0", STR_PAD_LEFT) }}</span></td>
			</tr>
		</table>
	</div>
</div>

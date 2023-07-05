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
                    <p>{{$despachoord->notaventa->sucursal->direccion}}</p>
					<p>Teléfono: {{$despachoord->notaventa->sucursal->telefono1}}</p>
					<p>Email: {{$despachoord->notaventa->sucursal->email}}</p>
				</div>-->
			</td>
			<td class="info_factura">
				<div>
					<span class="h3">Orden de Despacho</span>
					<p>Nro: <strong> {{ str_pad($despachoord->id, 10, "0", STR_PAD_LEFT) }}</strong>
						@if ($despachoord->despachoordanul)
							<small class="btn btn-danger btn-xs">Anulado</small>
						@endif
					</p>
					<p>Fecha Act: {{date('d-m-Y h:i:s A')}}</p>
					<p>Fecha: {{date('d-m-Y', strtotime($despachoord->fechahora))}}</p>
					<p>Hora: {{date("h:i:s A", strtotime($despachoord->fechahora))}}</p>
					<p>Vendedor: {{$despachoord->notaventa->vendedor->persona->nombre . " " . $despachoord->notaventa->vendedor->persona->apellido}} </p>
					<p>Teléfono: {{$despachoord->notaventa->vendedor->persona->telefono}} </p>
					<p>email: {{$despachoord->notaventa->vendedor->persona->email}} </p>
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
							<td style="width:10%"><label>Rut:</label></td><td style="width:50%"><p id="rutform" name="rutform">{{number_format( substr ( $despachoord->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachoord->notaventa->cliente->rut, strlen($despachoord->notaventa->cliente->rut) -1 , 1 )}}</p></td>
							<td style="width:10%"><label>Teléfono:</label></td><td style="width:30%"><p>{{$despachoord->notaventa->cliente->telefono}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Nombre:</label></td><td style="width:50%"><p>{{$despachoord->notaventa->cliente->razonsocial}}</p></td>
							<td style="width:10%"><label>Dirección:</label></td><td style="width:30%"><p>{{$despachoord->notaventa->cliente->direccion}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Contacto:</label></td><td style="width:50%"><p>{{$despachoord->notaventa->cliente->contactonombre}}</p></td>
							<td style="width:10%"><label>Comuna:</label></td><td style="width:30%"><p>{{$despachoord->notaventa->cliente->comuna->nombre}}</p></td>
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
					<th width="30px">Sol</th>
					<th width="30px">Desp</th>
					<th class="textcenter">Unidad</th>
					<th width="120px" class="textleft">Descripción</th>
					<th class="textleft">Clase<br>Sello</th>
					<th class="textleft">Diam<br>Ancho</th>
					<th class="textright">Largo</th>
					<th class="textcenter">TU<br>Esp</th>
					<th class="textright">Peso</th>
					<!--<th class="textright">$ x Kg</th>-->
					<th class="textright">Total Kg</th>
					<th class="textright" width="60px">Precio Unit</th>
					<th class="textright" width="70px">Total Neto</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php
					$aux_sumprecioxkilo = 0;
					$aux_sumtotalkilos = 0;
					$aux_promPonderadoPrecioxkilo = 0;
					$neto = 0;
				?>
				@foreach($despachoorddets as $despachoorddet)
					<?php
						$aux_sumprecioxkilo += $despachoorddet->notaventadetalle->precioxkilo;
						//$aux_sumtotalkilos += $despachoorddet->notaventadetalle->totalkilos;
						$aux_sumtotalkilos += ($despachoorddet->notaventadetalle->totalkilos/$despachoorddet->notaventadetalle->cant) * $despachoorddet->cantdesp;
					?>
				@endforeach
				@foreach($despachoorddets as $despachoorddet)
					<?php
						//$aux_promPonderadoPrecioxkilo += ($despachoorddet->notaventadetalle->precioxkilo * (($despachoorddet->notaventadetalle->totalkilos * 100) / $aux_sumtotalkilos)) / 100 ;
						$peso = $despachoorddet->notaventadetalle->totalkilos/$despachoorddet->notaventadetalle->cant;
						$totalkilos = ($peso) * $despachoorddet->cantdesp;
						$subtotal = $despachoorddet->cantdesp * $despachoorddet->notaventadetalle->preciounit;
						$neto += $subtotal;

						$aux_ancho = $despachoorddet->notaventadetalle->producto->diametro;
						$aux_espesor = $despachoorddet->notaventadetalle->producto->tipounion;
						$aux_largo = $despachoorddet->notaventadetalle->producto->long;
						$aux_cla_sello_nombre = $despachoorddet->notaventadetalle->producto->claseprod->cla_nombre;
						$aux_producto_nombre = $despachoorddet->notaventadetalle->producto->nombre;
						$aux_categoria_nombre = $despachoorddet->notaventadetalle->producto->categoriaprod->nombre;
						if ($despachoorddet->notaventadetalle->producto->acuerdotecnico != null){
							$AcuTec = $despachoorddet->notaventadetalle->producto->acuerdotecnico;
							$aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
							$aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
							$aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
							$aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
							$aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
						}

					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$despachoorddet->notaventadetalle->producto_id}}</td>
						<td class="textcenter">{{number_format($despachoorddet->despachosoldet->cantsoldesp, 0, ",", ".")}}</td>
						<td class="textcenter">{{number_format($despachoorddet->cantdesp, 0, ",", ".")}}</td>
						<td class="textcenter">{{$despachoorddet->notaventadetalle->producto->categoriaprod->unidadmedidafact->nombre}}</td>
						<td class="textleft">{{$aux_producto_nombre}}</td>
						<td class="textcenter">{{$aux_cla_sello_nombre}}</td>
						<td class="textcenter">{{$aux_ancho}}</td>
						<td class="textright">{{$aux_largo}}</td>
						<td class="textcenter">{{$aux_espesor}}</td>
						<td class="textright">{{number_format($peso, 3, ",", ".")}}</td>
						<!--<td class="textright">{{number_format($despachoorddet->notaventadetalle->precioxkilo, 2, ",", ".")}}</td>-->
						<td class="textright">{{number_format($totalkilos, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($despachoorddet->notaventadetalle->preciounit, 0, ",", ".")}}</td>
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
				<td colspan="7" class="textright" width="90%"><span><strong>IVA {{$despachoord->notaventa->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(round(($neto * $despachoord->notaventa->piva)/100), 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(round($neto * ($despachoord->notaventa->piva+100)/100), 0, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
	<div>
		@if (!is_null($despachoord->observacion))
			<p class="nota"><strong> <H2>Observaciones: {{$despachoord->observacion}}</H2></strong></p>			
		@endif
	</div>
	<br>
	<div class="round1">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($despachoord->plazoentrega))}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Comuna: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoord->comunaentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoord->lugarentrega}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoord->notaventa->plazopago->descripcion}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoord->tipoentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoord->contacto}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto email: </strong></span></td>
				<td class="textleft" width="50%"><span>{{strtolower($despachoord->contactoemail)}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto Teléfono: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoord->contactotelf}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Orden de Compra: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoord->notaventa->oc_id}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>No. Cotización: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoord->notaventa->cotizacion_id, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Nota de Venta: </strong></span></td>
				<td class="textleft" width="50%"><span>{{ str_pad($despachoord->notaventa_id, 10, "0", STR_PAD_LEFT) }}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Solicitud de Despacho: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoord->despachosol_id, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Guia Despacho: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoord->guiadespacho , 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Nro Factura: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoord->numfactura, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
		</table>
	</div>
</div>

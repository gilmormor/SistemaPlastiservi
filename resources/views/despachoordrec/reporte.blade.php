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
					<span class="h3">Rechazo Orden de Despacho</span>
					<p>Nro: <strong> {{ str_pad($despachoordrec->id, 10, "0", STR_PAD_LEFT) }}</strong>
						@if ($despachoordrec->anulada != null)
							<small class="btn btn-danger btn-xs">Anulado</small>
						@endif
					</p>
					<p>Fecha: {{date('d-m-Y', strtotime($despachoordrec->fechahora))}}</p>
					<p>Vendedor: {{$despachoordrec->despachoord->notaventa->vendedor->persona->nombre . " " . $despachoordrec->despachoord->notaventa->vendedor->persona->apellido}} </p>
					<p>Teléfono: {{$despachoordrec->despachoord->notaventa->vendedor->persona->telefono}} </p>
					<p>email: {{$despachoordrec->despachoord->notaventa->vendedor->persona->email}} </p>
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
							<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $despachoordrec->despachoord->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachoordrec->despachoord->notaventa->cliente->rut, strlen($despachoordrec->despachoord->notaventa->cliente->rut) -1 , 1 )}}</p></td>
							<td><label>Teléfono:</label> <p>{{$despachoordrec->despachoord->notaventa->cliente->telefono}}</p></td>
						</tr>
						<tr class="headt">
							<td><label>Nombre:</label> <p>{{$despachoordrec->despachoord->notaventa->cliente->razonsocial}}</p></td>
							<td><label>Dirección:</label> <p>{{$despachoordrec->despachoord->notaventa->cliente->direccion}}</p></td>
						</tr>
						<tr class="headt">
							<td><label>Contacto:</label> <p>{{$despachoordrec->despachoord->notaventa->cliente->contactonombre}}</p></td>
							<td><label>Comuna:</label> <p>{{$despachoordrec->despachoord->notaventa->cliente->comuna->nombre}}</p></td>
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
					<th width="30px">Rechazo</th>
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
				@foreach($despachoordrecdets as $despachoordrecdet)
					<?php
						$aux_sumprecioxkilo += $despachoordrecdet->despachoorddet->notaventadetalle->precioxkilo;
						//$aux_sumtotalkilos += $despachoordrecdet->despachoorddet->notaventadetalle->totalkilos;
						$aux_sumtotalkilos += ($despachoordrecdet->despachoorddet->notaventadetalle->totalkilos/$despachoordrecdet->despachoorddet->notaventadetalle->cant) * $despachoordrecdet->cantrec;
					?>
				@endforeach
				@foreach($despachoordrecdets as $despachoordrecdet)
					<?php
						//$aux_promPonderadoPrecioxkilo += ($despachoordrecdet->despachoorddet->notaventadetalle->precioxkilo * (($despachoordrecdet->despachoorddet->notaventadetalle->totalkilos * 100) / $aux_sumtotalkilos)) / 100 ;
						$peso = $despachoordrecdet->despachoorddet->notaventadetalle->totalkilos/$despachoordrecdet->despachoorddet->notaventadetalle->cant;
						$totalkilos = ($peso) * $despachoordrecdet->cantrec;
						$subtotal = $despachoordrecdet->cantrec * $despachoordrecdet->despachoorddet->notaventadetalle->preciounit;
						$neto += $subtotal;
					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$despachoordrecdet->despachoorddet->notaventadetalle->producto_id}}</td>
						<td class="textcenter">{{number_format($despachoordrecdet->despachoorddet->despachosoldet->cantsoldesp, 0, ",", ".")}}</td>
						<td class="textcenter">{{number_format($despachoordrecdet->despachoorddet->cantdesp, 0, ",", ".")}}</td>
						<td class="textcenter">{{number_format($despachoordrecdet->cantrec, 0, ",", ".")}}</td>
						<td class="textcenter">{{$despachoordrecdet->despachoorddet->notaventadetalle->producto->categoriaprod->unidadmedidafact->nombre}}</td>
						<td class="textleft">{{$despachoordrecdet->despachoorddet->notaventadetalle->producto->nombre}}</td>
						<td class="textcenter">
							{{$despachoordrecdet->despachoorddet->notaventadetalle->producto->diametro}}
						</td>
						<td class="textcenter">{{$despachoordrecdet->despachoorddet->notaventadetalle->producto->claseprod->cla_nombre}}</td>
						<td class="textright">{{$despachoordrecdet->despachoorddet->notaventadetalle->producto->long}} mts</td>
						<td class="textcenter">{{$despachoordrecdet->despachoorddet->notaventadetalle->producto->tipounion}}</td>
						<td class="textright">{{number_format($peso, 3, ",", ".")}}</td>
						<!--<td class="textright">{{number_format($despachoordrecdet->despachoorddet->notaventadetalle->precioxkilo, 2, ",", ".")}}</td>-->
						<td class="textright">{{number_format($totalkilos, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($despachoordrecdet->despachoorddet->notaventadetalle->preciounit, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($subtotal, 0, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="11" class="textright"><span><strong>Totales</strong></span></td>
					<td class="textright"><span><strong>{{number_format($aux_sumtotalkilos, 2, ",", ".")}}</strong></span></td>
					<td class="textright"><span><strong>NETO</strong></span></td>
					<td class="textright"><span><strong>{{number_format($neto, 0, ",", ".")}}</strong></span></td>
				</tr>
				<tr>
					<td colspan="13" class="textright"><span><strong>IVA {{$despachoordrec->despachoord->notaventa->piva}}%</strong></span></td>
					<td class="textright"><span><strong>{{number_format(round(($neto * $despachoordrec->despachoord->notaventa->piva)/100), 0, ",", ".")}}</strong></span></td>
				</tr>
				<tr>
					<td colspan="13" class="textright"><span><strong>TOTAL</strong></span></td>
					<td class="textright"><span><strong>{{number_format(round($neto * ($despachoordrec->despachoord->notaventa->piva+100)/100), 0, ",", ".")}}</strong></span></td>
				</tr>
		
			</tfoot>
		</table>
	</div>
	<br>
	<div class="round1">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($despachoordrec->despachoord->plazoentrega))}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Comuna: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoordrec->despachoord->comunaentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoordrec->despachoord->lugarentrega}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoordrec->despachoord->notaventa->plazopago->descripcion}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoordrec->despachoord->tipoentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Contacto: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoordrec->despachoord->contacto}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Contacto email: </strong></span></td>
				<td class="textleft" width="50%"><span>{{strtolower($despachoordrec->despachoord->contactoemail)}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Contacto Teléfono: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoordrec->despachoord->contactotelf}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Orden de Compra: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$despachoordrec->despachoord->notaventa->oc_id}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>No. Cotización: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoordrec->despachoord->notaventa->cotizacion_id, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Nota de Venta: </strong></span></td>
				<td class="textleft" width="50%"><span>{{ str_pad($despachoordrec->despachoord->notaventa_id, 10, "0", STR_PAD_LEFT) }}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Solicitud de Despacho: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoordrec->despachoord->despachosol_id, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Orden de Despacho: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoordrec->despachoord_id, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Guia Despacho: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoordrec->despachoord->guiadespacho, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Nro Factura: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($despachoordrec->despachoord->numfactura, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
		</table>
	</div>
	<br>
	<div>
		@if (!is_null($despachosol->observacion))
			<p class="nota"><strong> <H2>Observaciones: {{$despachoordrec->obs}}</H2></strong></p>			
		@endif
	</div>
	<br>
</div>

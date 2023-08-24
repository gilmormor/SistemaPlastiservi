<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

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
                    <p>{{$notaventa->sucursal->direccion}}</p>
					<p>Teléfono: {{$notaventa->sucursal->telefono1}}</p>
					<p>Email: {{$notaventa->sucursal->email}}</p>
				</div>-->
			</td>
			<td class="info_factura">
				<div>
					<span class="h3">Nota Venta Interna / {{$notaventa->sucursal->nombre}}</span>
					<p>Nro: <strong> {{ str_pad($notaventa->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha Act: {{date('d-m-Y h:i:s A')}}</p>
					<p>Fecha: {{date('d-m-Y', strtotime($notaventa->fechahora))}}</p>
					<p>Hora: {{date("h:i:s A", strtotime($notaventa->fechahora))}}</p>
					<p>Vendedor: {{$notaventa->vendedor->persona->nombre . " " . $notaventa->vendedor->persona->apellido}} </p>
					<p>Teléfono: {{$notaventa->vendedor->persona->telefono}} </p>
					<p>email: {{$notaventa->vendedor->persona->email}} </p>
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
						<!--<tr class="headt">-->
						<tr>
							<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 )}}</p></td>
							<td><label>Teléfono:</label> <p>{{$notaventa->cliente->telefono}}</p></td>
						</tr>
						<tr>
							<td><label>Nombre:</label> <p>{{$notaventa->cliente->razonsocial}}</p></td>
							<td><label>Dirección:</label> <p>{{$notaventa->cliente->direccion}}</p></td>
						</tr>
						<tr>
							<td><label>Contacto:</label> <p>{{$notaventa->cliente->contactonombre}}</p></td>
							<td><label>Comuna:</label> <p>{{$notaventa->cliente->comuna->nombre}}</p></td>
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
						<th width="30px">Cant.</th>
						<th class="textcenter">UN</th>
						<th class="textleft">Descripción</th>
						<th class="textleft">Clase<br>Sello</th>
						<th class="textcenter">Diam<br>Ancho</th>
						<th class="textcenter">Largo</th>
						<th class="textcenter">TU<br>Esp</th>
						<th class="textright">Peso</th>
						<th class="textright">$ x Kg</th>
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
					?>
					@foreach($notaventaDetalles as $notaventaDetalle)
						<?php
							$aux_sumprecioxkilo += $notaventaDetalle->precioxkilo;
							$aux_sumtotalkilos += $notaventaDetalle->totalkilos;
						?>
					@endforeach
					@foreach($notaventaDetalles as $notaventaDetalle)
						<?php
							if($aux_sumtotalkilos > 0){
								$aux_promPonderadoPrecioxkilo += ($notaventaDetalle->precioxkilo * (($notaventaDetalle->totalkilos * 100) / $aux_sumtotalkilos)) / 100 ;
							}
							//$aux_promPonderadoPrecioxkilo += (($notaventaDetalle->totalkilos * 100) / $aux_sumtotalkilos) ;
							$aux_producto_nombre = $notaventaDetalle->producto->nombre;
							$aux_ancho = $notaventaDetalle->producto->diametro;
							$aux_largo = $notaventaDetalle->producto->long . "Mts";
							$aux_espesor = $notaventaDetalle->producto->tipounion;
							$aux_cla_sello_nombre = $notaventaDetalle->producto->claseprod->cla_nombre;
							if ($notaventaDetalle->cotizaciondetalle and $notaventaDetalle->cotizaciondetalle->acuerdotecnicotemp != null){
								$AcuTecTemp = $notaventaDetalle->cotizaciondetalle->acuerdotecnicotemp;
								$aux_producto_nombre = $AcuTecTemp->at_desc;
								$aux_ancho = $AcuTecTemp->at_ancho . " " . ($AcuTecTemp->at_ancho ? $AcuTecTemp->anchounidadmedida->nombre : "");
								$aux_largo = $AcuTecTemp->at_largo . " " . ($AcuTecTemp->at_largo ? $AcuTecTemp->largounidadmedida->nombre : "");
								$aux_espesor = number_format($AcuTecTemp->at_espesor, 3, ',', '.');
								$aux_cla_sello_nombre = $AcuTecTemp->claseprod->cla_nombre;
							}
							if ($notaventaDetalle->producto->acuerdotecnico != null){
								$AcuTec = $notaventaDetalle->producto->acuerdotecnico;
								$aux_producto_nombre = $AcuTec->at_desc;
								$aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
								$aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
								$aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
								$aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
							}
						?>
						<tr class="headt" style="height:150%;">
							<td class="textcenter">{{number_format($notaventaDetalle->cant, 0, ",", ".")}}</td>
							<td class="textcenter">{{$notaventaDetalle->producto->categoriaprod->unidadmedidafact->nombre}}</td>
							<td class="textleft">{{$aux_producto_nombre}}</td>
							<td class="textleft">{{$aux_cla_sello_nombre}}</td>
							<td class="textcenter">{{$aux_ancho}}</td>
							<td class="textcenter">{{$aux_largo}}</td>
							<td class="textcenter">{{$aux_espesor}}</td>
							<td class="textright">{{number_format($notaventaDetalle->producto->peso, 2, ",", ".")}}</td>
							<td class="textright">{{number_format($notaventaDetalle->precioxkilo, 0, ",", ".")}}</td>
							<td class="textright">{{number_format($notaventaDetalle->totalkilos, 2, ",", ".")}}</td>
							<td class="textright">{{number_format($notaventaDetalle->preciounit, 0, ",", ".")}}</td>
							<td class="textright">{{number_format($notaventaDetalle->subtotal, 0, ",", ".")}}</td>
						</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8" class="textright"><span><strong>Totales</strong></span></td>
						<td class="textright"><span><strong>{{number_format($aux_promPonderadoPrecioxkilo, 0, ",", ".")}}</strong></span></td>
						<td class="textright"><span><strong>{{number_format($aux_sumtotalkilos, 0, ",", ".")}}</strong></span></td>
						<td class="textright"><span><strong>NETO</strong></span></td>
						<td class="textright"><span><strong>{{number_format($notaventa->neto, 0, ",", ".")}}</strong></span></td>
					</tr>
					<tr>
						<td colspan="11" class="textright"><span><strong>IVA {{$notaventa->piva}}%</strong></span></td>
						<td class="textright"><span><strong>{{number_format($notaventa->iva, 0, ",", ".")}}</strong></span></td>
					</tr>
					<tr>
						<td colspan="11" class="textright"><span><strong>TOTAL</strong></span></td>
						<td class="textright"><span><strong>{{number_format($notaventa->total, 0, ",", ".")}}</strong></span></td>
					</tr>
		
				</tfoot>

		</table>
	</div>
	<!--
	<div>
		<table id="factura_detalle">
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->neto, 2, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>IVA {{$notaventa->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->iva, 2, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->total, 2, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
-->
	<br>
	<div class="round1" style="padding-bottom: 0px;">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<!--
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($notaventa->plazoentrega))}}</span></td>
			</tr>
			-->
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Comuna: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->comunaentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->lugarentrega}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->plazopago->descripcion}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->tipoentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->contacto}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto email: </strong></span></td>
				<td class="textleft" width="50%"><span>{{strtolower($notaventa->contactoemail)}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Contacto Teléfono: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->contactotelf}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Orden de Compra: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->oc_id}}</span></td>
			</tr>
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>No. Cotización: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($notaventa->cotizacion_id, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
		</table>
	</div>
	<br>
	<div>
		@if (!is_null($notaventa->observacion))
			<p class="nota"><strong> <H2>Observaciones: {{$notaventa->observacion}}</H2></strong></p>			
		@endif
	</div>
	<br>
</div>

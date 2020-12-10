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
					<span class="h3">Vista Previa Solicitud Despacho</span>
					<p>Nro Nota Venta: <strong> {{ str_pad($notaventa->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
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
						<tr class="headt">
							<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 )}}</p></td>
							<td><label>Teléfono:</label> <p>{{$notaventa->cliente->telefono}}</p></td>
						</tr>
						<tr class="headt">
							<td><label>Nombre:</label> <p>{{$notaventa->cliente->razonsocial}}</p></td>
							<td><label>Dirección:</label> <p>{{$notaventa->cliente->direccion}}</p></td>
						</tr>
						<tr class="headt">
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
						<th>Desp</th>
						<th>Solid</th>
						<th>Saldo</th>	
						<th class="textcenter">Unidad</th>
						<th class="textleft">Descripción</th>
						<th class="textleft">Diam</th>
						<th class="textleft">Clase</th>
						<th class="textright">Largo</th>
						<th class="textcenter">TU</th>
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
						$totalSubtotalItem = 0;
						$aux_totalkgItem = 0;
						$aux_sumtotalkilos1 = 0;
					?>
					@foreach($notaventaDetalles as $notaventaDetalle)
						<?php
							$aux_sumprecioxkilo += $notaventaDetalle->precioxkilo;
							$aux_sumtotalkilos += $notaventaDetalle->totalkilos;
						?>
					@endforeach
					@foreach($notaventaDetalles as $notaventaDetalle)
						<?php
							$aux_promPonderadoPrecioxkilo += ($notaventaDetalle->precioxkilo * (($notaventaDetalle->totalkilos * 100) / $aux_sumtotalkilos)) / 100 ;
							//$aux_promPonderadoPrecioxkilo += (($notaventaDetalle->totalkilos * 100) / $aux_sumtotalkilos) ;
							//SUMA TOTAL DE SOLICITADO
							$sql = "SELECT cantsoldesp
								FROM vista_sumsoldespdet
								WHERE notaventadetalle_id=$notaventaDetalle->id";
							$datasuma = DB::select($sql);
							$peso = $notaventaDetalle->totalkilos/$notaventaDetalle->cant;
							if(empty($datasuma)){
								$sumacantsoldesp= 0;
							}else{
								$sumacantsoldesp= $datasuma[0]->cantsoldesp;
							}
							/*************************/
							//SUMA TOTAL DESPACHADO
							$sql = "SELECT cantdesp
								FROM vista_sumorddespxnvdetid
								WHERE notaventadetalle_id=$notaventaDetalle->id";
							$datasumadesp = DB::select($sql);
							if(empty($datasumadesp)){
								$sumacantdesp= 0;
							}else{
								$sumacantdesp= $datasumadesp[0]->cantdesp;
							}
							/*************************/

							if($notaventaDetalle->cant > $sumacantsoldesp){
								$aux_saldo = $notaventaDetalle->cant - $sumacantsoldesp;
								$subtotalItem = $aux_saldo * $notaventaDetalle->preciounit;
								$totalSubtotalItem += $subtotalItem;
								$aux_totalkgItem = $aux_saldo * ($notaventaDetalle->totalkilos/$notaventaDetalle->cant);
								$aux_sumtotalkilos1 += $aux_totalkgItem;

						?>
						<tr class="headt" style="height:150%;">
							<td class="textcenter">{{number_format($notaventaDetalle->cant, 0, ",", ".")}}</td>
							<td class="textcenter">
								{{$sumacantdesp}}
							</td>		
							<td class="textcenter">
								{{$sumacantsoldesp}}
							</td>
							<td class="textcenter">
								{{$aux_saldo}}
							</td>	
							<td class="textcenter">{{$notaventaDetalle->producto->categoriaprod->unidadmedidafact->nombre}}</td>
							<td class="textleft">{{$notaventaDetalle->producto->nombre}}</td>
							<td class="textleft">
								@if ($notaventaDetalle->producto->categoriaprod->unidadmedida_id==3)
									{{$notaventaDetalle->producto->diamextpg}}								
								@else
									{{$notaventaDetalle->producto->diamextmm}}mm
								@endif
							</td>
							<td class="textleft">{{$notaventaDetalle->producto->claseprod->cla_nombre}}</td>
							<td class="textright">{{$notaventaDetalle->producto->long}} mts</td>
							<td class="textcenter">{{$notaventaDetalle->producto->tipounion}}</td>
							<td class="textright">{{number_format($notaventaDetalle->producto->peso, 2, ",", ".")}}</td>
							<td class="textright">{{number_format($notaventaDetalle->precioxkilo, 2, ",", ".")}}</td>
							<td class="textright">{{number_format($aux_totalkgItem, 2, ",", ".")}}</td>
							<td class="textright">{{number_format($notaventaDetalle->preciounit, 2, ",", ".")}}</td>
							<td class="textright">{{number_format($subtotalItem, 2, ",", ".")}}</td>
						</tr>
						<?php
							}
						?>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td colspan="11" class="textright"><span><strong>Totales</strong></span></td>
						<td class="textright"><span><strong>{{number_format($aux_promPonderadoPrecioxkilo, 2, ",", ".")}}</strong></span></td>
						<td class="textright"><span><strong>{{number_format($aux_sumtotalkilos1, 2, ",", ".")}}</strong></span></td>
					</tr>
				</tfoot>

		</table>
	</div>
	<div>
		<table id="factura_detalle">
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($totalSubtotalItem, 2, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>IVA {{$notaventa->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(round($totalSubtotalItem * $notaventa->piva/100), 2, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(round($totalSubtotalItem * (1 + ($notaventa->piva/100))), 2, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
	<br>
	<div class="round1">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($notaventa->plazoentrega))}}</span></td>
			</tr>
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
		<p class="nota"><strong>Observaciones: {{$notaventa->observacion}}</strong></p>
	</div>
	<br>
</div>

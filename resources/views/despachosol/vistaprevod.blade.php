<!--<link rel="stylesheet" href="{{asset("assets/$theme/bower_components/bootstrap/dist/css/bootstrap.min.css")}}">-->
<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<script src="{{asset("assets/$theme/bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
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
					<span class="h3">Vista Previa Ord Desp / {{$despachosol->notaventa->sucursal->nombre}}</span>
					<p>Nro SolDesp: <strong> {{ str_pad($despachosol->id, 10, "0", STR_PAD_LEFT) }}</strong>
						@if ($despachosol->despachosolanul)
							<small class="btn btn-danger btn-xs">Anulado</small>
						@endif
					</p>
					<p>Fecha Act: {{date('d-m-Y h:i:s A')}}</p>
					<p>Vendedor: {{$despachosol->notaventa->vendedor->persona->nombre . " " . $despachosol->notaventa->vendedor->persona->apellido}} </p>
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
						<tr>
							<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $despachosol->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachosol->notaventa->cliente->rut, strlen($despachosol->notaventa->cliente->rut) -1 , 1 )}}</p></td>
							<td><label>Teléfono:</label> <p>{{$despachosol->notaventa->cliente->telefono}}</p></td>
						</tr>
						<tr>
							<td><label>Nombre:</label> <p>{{$despachosol->notaventa->cliente->razonsocial}}</p></td>
							<td><label>Dirección:</label> <p>{{$despachosol->notaventa->cliente->direccion}}</p></td>
						</tr>
						<tr>
							<td><label>Contacto:</label> <p>{{$despachosol->notaventa->cliente->contactonombre}}</p></td>
							<td><label>Comuna:</label> <p>{{$despachosol->notaventa->cliente->comuna->nombre}}</p></td>
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
					<th width="30px">OrdDesp</th>
					<th width="30px">Saldo</th>
					<th class="textcenter">Unidad</th>
					<th class="textleft">Descripción</th>
					<th class="textleft">Clase<br>Sello</th>
					<th class="textleft">Diam<br>Ancho</th>
					<th class="textright">Largo</th>
					<th class="textcenter">TU<br>Esp</th>
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
						//$aux_sumtotalkilos += $despachosoldet->cantsoldesp * ($despachosoldet->notaventadetalle->totalkilos/$despachosoldet->notaventadetalle->cant);
					?>
				@endforeach
				<?php $aux_nfila = 0; $i = 0;?>
				@foreach($despachosoldets as $despachosoldet)
					<?php
						$sql = "SELECT cantdesp
								FROM vista_sumorddespdet
								WHERE despachosoldet_id=$despachosoldet->id";
						$datasuma = DB::select($sql);
						$peso = $despachosoldet->notaventadetalle->totalkilos/$despachosoldet->notaventadetalle->cant;
						if(empty($datasuma)){
							$sumacantorddesp= 0;
						}else{
							$sumacantorddesp= $datasuma[0]->cantdesp;
						}
						$aux_ancho = $despachosoldet->notaventadetalle->producto->diametro;
						$aux_largo = $despachosoldet->notaventadetalle->producto->long . "Mts";
						$aux_espesor = $despachosoldet->notaventadetalle->producto->tipounion;
						$aux_cla_sello_nombre = $despachosoldet->notaventadetalle->producto->claseprod->cla_nombre;
						$aux_producto_nombre = $despachosoldet->notaventadetalle->producto->nombre;
						//$aux_categoria_nombre = $despachosoldet->notaventadetalle->producto->categoriaprod->nombre;
						if ($despachosoldet->notaventadetalle->producto->acuerdotecnico != null){
							$AcuTec = $despachosoldet->notaventadetalle->producto->acuerdotecnico;
							$aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
							$aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
							$aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
							$aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
							$aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
						}
						if($despachosoldet->cantsoldesp > $sumacantorddesp){
							$aux_nfila++;
							$aux_saldo = $despachosoldet->cantsoldesp - $sumacantorddesp;


							//$aux_promPonderadoPrecioxkilo += ($despachosoldet->notaventadetalle->precioxkilo * (($despachosoldet->notaventadetalle->totalkilos * 100) / $aux_sumtotalkilos)) / 100 ;
							$peso = $despachosoldet->notaventadetalle->totalkilos/$despachosoldet->notaventadetalle->cant;
							$totalkilos = $aux_saldo * $peso;
							$subtotal = $aux_saldo * $despachosoldet->notaventadetalle->preciounit;
							$neto += $subtotal;
							$aux_sumtotalkilos += $totalkilos;
					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$despachosoldet->notaventadetalle->producto_id}}</td>
						<td class="textcenter">{{number_format($despachosoldet->cantsoldesp, 0, ",", ".")}}</td>
						<td class="textcenter">{{number_format($sumacantorddesp, 0, ",", ".")}}</td>
						<td class="textcenter">{{number_format($aux_saldo, 0, ",", ".")}}</td>
						<td class="textcenter">{{$despachosoldet->notaventadetalle->producto->categoriaprod->unidadmedidafact->nombre}}</td>
						<td class="textleft">{{$aux_producto_nombre}}</td>
						<td class="textcenter">{{$aux_cla_sello_nombre}}</td>
						<td class="textcenter">{{$aux_ancho}}</td>
						<td class="textcenter">{{$aux_largo}}</td>
						<td class="textcenter">{{$aux_espesor}}</td>
						<td class="textright">{{number_format($despachosoldet->notaventadetalle->producto->peso, 2, ",", ".")}}</td>
						<!--<td class="textright">{{number_format($despachosoldet->notaventadetalle->precioxkilo, 2, ",", ".")}}</td>-->
						<td class="textright">{{number_format($totalkilos, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($despachosoldet->notaventadetalle->preciounit, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($subtotal, 2, ",", ".")}}</td>
					</tr>
					<?php $i++;
						}
					?>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="11" class="textright"><span><strong>Totales</strong></span></td>
					<td class="textright"><span><strong>{{number_format($aux_sumtotalkilos, 2, ",", ".")}}</strong></span></td>
					<td class="textright"><span><strong>NETO</strong></span></td>
					<td class="textright"><span><strong>{{number_format($neto, 2, ",", ".")}}</strong></span></td>
				</tr>
				<tr>
					<td colspan="13" class="textright"><span><strong>IVA {{$despachosol->notaventa->piva}}%</strong></span></td>
					<td class="textright"><span><strong>{{number_format(round(($neto * $despachosol->notaventa->piva)/100), 2, ",", ".")}}</strong></span></td>
				</tr>
				<tr>
					<td colspan="13" class="textright"><span><strong>TOTAL</strong></span></td>
					<td class="textright"><span><strong>{{number_format(round($neto * ($despachosol->notaventa->piva+100)/100), 2, ",", ".")}}</strong></span></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<br>
	<div class="round1">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<tr>
				<td colspan="7" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($despachosol->plazoentrega))}}</span></td>
			</tr>
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
	<br>
	<div>
		@if (!is_null($despachosol->observacion))
			<p class="nota"><strong> <H2>Observaciones: {{$despachosol->observacion}}</H2></strong></p>			
		@endif
	</div>
	<br>
</div>

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
					<span class="h3">Solicitud Despacho / {{$despachosol->notaventa->sucursal->nombre}}</span>
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
					<th width="20px" class="textcenter">UN</th>
					<th width="120px" class="textleft">Descripción</th>
					<th width="40px" class="textcenter">Sello</th>
					<th width="20px" class="textcenter">Ancho</th>
					<th width="20px" class="textcenter">Largo</th>
					<th width="20px" class="textcenter">Esp</th>
					<th class="textright" width="50px">Precio Unit {{$datosArray["modena_desc"]}}</th>
					<th class="textright" width="60px">Total Neto {{$datosArray["modena_desc"]}}</th>
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

						$aux_ancho = $despachosoldet->notaventadetalle->producto->diametro;
						$aux_largo = $despachosoldet->notaventadetalle->producto->long . "Mts";
						$aux_espesor = $despachosoldet->notaventadetalle->producto->tipounion;
						$aux_cla_sello_nombre = $despachosoldet->notaventadetalle->producto->claseprod->cla_nombre;
						$aux_producto_nombre = $despachosoldet->notaventadetalle->producto->nombre;
						$aux_atribAcuTec = "";
                        $aux_staAT = false;
						//$aux_categoria_nombre = $despachosoldet->notaventadetalle->producto->categoriaprod->nombre;
						if ($despachosoldet->notaventadetalle->producto->acuerdotecnico != null){
							$AcuTec = $despachosoldet->notaventadetalle->producto->acuerdotecnico;
							$aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
							$aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
							$aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
							$aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
							$aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
							$aux_atribAcuTec = $AcuTec->color->nombre . " " . $AcuTec->materiaprima->nombre . " " . $AcuTec->at_impresoobs;
                            $aux_staAT = true;
						}
					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$despachosoldet->notaventadetalle->producto_id}}</td>
						<td class="textcenter">{{number_format($despachosoldet->notaventadetalle->cant, 0, ",", ".")}}</td>
						<td class="textcenter">{{number_format($despachosoldet->cantsoldesp, 0, ",", ".")}}</td>
						<td class="textcenter">{{$despachosoldet->notaventadetalle->unidadmedida->nombre}}</td>
						<td class="textleft">{{$aux_producto_nombre}}
							@if ($aux_staAT)
								<br><span class="small-text">{{$aux_atribAcuTec}}</span>
							@endif
						</td>
						<td class="textcenter">{{$aux_cla_sello_nombre}}</td>
						<td class="textcenter">{{$aux_ancho}}</td>
						<td class="textcenter">{{$aux_largo}}</td>
						<td class="textcenter">{{$aux_espesor}}</td>
						<td class="textright">{{number_format($despachosoldet->notaventadetalle->preciounit, $datosArray["monedaLocal"] ? 2 : 3, ",", ".")}}</td>
						<td class="textright">{{number_format($subtotal, $datosArray["monedaLocal"] ? 0 : 3, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr class="headt">
					<td colspan="10" class="textright" width="90%"><span><strong>NETO</strong></span></td>
					<td class="textright" width="10%"><span><strong>{{number_format($neto, $datosArray["monedaLocal"] ? 0 : 3, ",", ".")}}</strong></span></td>
				</tr>
				<tr class="headt">
					<td colspan="10" class="textright" width="90%"><span><strong>IVA {{$despachosol->notaventa->piva}}%</strong></span></td>
					<td class="textright" width="10%"><span><strong>{{number_format(($neto * $despachosol->notaventa->piva)/100, $datosArray["monedaLocal"] ? 0 : 3, ",", ".")}}</strong></span></td>
				</tr>
				<tr class="headt">
					<td colspan="10" class="textright" width="90%"><span><strong>TOTAL {{$datosArray["modena_desc"]}}</strong></span></td>
					<td class="textright" width="10%"><span><strong>{{number_format(($neto * ($despachosol->notaventa->piva+100))/100, $datosArray["monedaLocal"] ? 0 : 3, ",", ".")}}</strong></span></td>
				</tr>
				</tfoot>
		</table>
	</div>
	<div>
		@if (!is_null($despachosol->observacion))
			<p class="nota"><strong> <H2>Observaciones: {{$despachosol->observacion}}</H2></strong></p>			
		@endif
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
	@if ($datosArray["monedaLocal"] == false)
		<div>
			<p class="nota">
					<br><br>Valores en dólares americanos {{$datosArray["modena_desc"]}}. Tipo de cambio: dólar observado.
			</p>
		</div>
	@endif
</div>

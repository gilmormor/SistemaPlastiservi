<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<?php
	$aux_monedaLocal = true;
	$aux_modena_nombre = "";
	$aux_modena_desc = "";
	$aux_modena_simb = "";
	if($empresa[0]['moneda_id'] != $cotizacion->moneda_id){
		$aux_monedaLocal = false;
		$aux_modena_nombre = $cotizacion->moneda->nombre;
		$aux_modena_desc = $cotizacion->moneda->desc;
		$aux_modena_simb = $cotizacion->moneda->simbolo;
	}
?>
<br>
<br>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png")}}" style="max-width:1200%;width:auto;height:auto;">
					<p>{{$empresa[0]['nombre']}}</p>					
					<p>RUT: {{$empresa[0]['rut']}}</p>
					<p>{{$cotizacion->sucursal->direccion}}</p>
					<p>Teléfono: {{$cotizacion->sucursal->telefono1}}</p>
					<!--<p>Email: {{$cotizacion->sucursal->email}}</p>-->
				</div>
			</td>
			<td class="info_empresa">
				<!--
				<div>
					<span class="h2">COTIZACIÓN</span>
                    <p>{{$cotizacion->sucursal->direccion}}</p>
					<p>Teléfono: {{$cotizacion->sucursal->telefono1}}</p>
					<p>Email: {{$cotizacion->sucursal->email}}</p>
				</div>-->
			</td>
			<td class="info_factura">
				<div class="round" style="padding-bottom: 3px;">
					<span class="h3">Cotización</span>
					<p>No. Cotización: <strong> {{ str_pad($cotizacion->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha: {{date('d-m-Y', strtotime($cotizacion->fechahora))}}</p>
					<p>Vendedor: {{$cotizacion->vendedor->persona->nombre . " " . $cotizacion->vendedor->persona->apellido}} </p>
					<p>Teléfono: {{$cotizacion->vendedor->persona->telefono}} </p>
					<p>email: {{$cotizacion->vendedor->persona->email}} </p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h3">Cliente</span>
					<table class="datos_cliente" style="border-collapse: collapse;">
						@if (!empty($cotizacion->cliente_id))
							<!--<tr class="headt">-->
							<tr class="headt">
								<td style="width:10%"><label>Rut: </label></td><td style="width:50%"><p>{{number_format( substr ( $cotizacion->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->cliente->rut, strlen($cotizacion->cliente->rut) -1 , 1 )}}</p></td>
								<td style="width:10%">Teléfono:</td><td style="width:30%"><p>{{$cotizacion->cliente->telefono}}</p></td>
							</tr>
							<tr class="headt">
								<td style="width:10%"><label>Nombre:</label></td><td style="width:50%"><p>{{$cotizacion->cliente->razonsocial}}</p></td>
								<td style="width:10%"><label>Dirección:</label></td><td style="width:30%"><p>{{$cotizacion->cliente->direccion}}</p></td>
							</tr>
							<tr class="headt">
								<td style="width:10%"><label>Contacto:</label></td><td style="width:50%"><p>{{$cotizacion->cliente->contactonombre}}</p></td>
								<td style="width:10%"><label>Comuna:</label></td><td style="width:30%"><p>{{$cotizacion->cliente->comuna->nombre}}</p></td>
							</tr>
						@else
							<tr class="headt">
								<td style="width:10%"><label>Rut: </label></td><td style="width:50%"><p>{{number_format( substr ( $cotizacion->clientetemp->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->clientetemp->rut, strlen($cotizacion->clientetemp->rut) -1 , 1 )}}</p></td>
								<td style="width:10%"><label>Teléfono: </label></td><td style="width:30%"><p>{{$cotizacion->clientetemp->telefono}}</p></td>
							</tr>
							<tr class="headt">
								<td style="width:10%"><label>Nombre: </label></td><td style="width:50%"><p>{{$cotizacion->clientetemp->razonsocial}}</p></td>
								<td style="width:10%"><label>Dirección: </label></td><td style="width:30%"><p>{{$cotizacion->clientetemp->direccion}}</p></td>
							</tr>
							<tr class="headt">
								<td style="width:10%"><label>Contacto: </label></td><td style="width:50%"><p>{{$cotizacion->clientetemp->contactonombre}}</p></td>
								<td style="width:10%"><label>Comuna: </label></td><td style="width:30%"><p>{{$cotizacion->clientetemp->comuna->nombre}}</p></td>
							</tr>
						
						@endif
					</table>
				</div>
			</td>

		</tr>
	</table>

	<div class="round" style="padding-bottom: 0px;">
		<table id="factura_detalle">
			<thead>
				<tr>
					<th width="30px">Cod</th>
					<th width="50px">Cant.</th>
					<th class="textcenter" width="50px">Unid</th>
					<th class="textleft" width="190px">Descripción</th>
<!--San Bernardo
					<th class="textleft" width="60px">Clase</th>
					<th class="textcenter" width="35px">Diamet</th>
					<th class="textright">Largo</th>
					<th class="textcenter">TU</th>
-->
					<th class="textcenter" width="60px">Sello</th>
					<th class="textcenter" width="35px">Ancho</th>
					<th class="textcenter">Largo</th>
					<th class="textcenter">Espesor</th>
					<th class="textright" width="70px">Precio<br>Neto {{$aux_modena_desc}}</th>
					<th class="textright" width="90px">Total<br>Neto {{$aux_modena_desc}}</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				@foreach($cotizacionDetalles as $CotizacionDetalle)
					<?php 
						$aux_producto_nombre = $CotizacionDetalle->producto->nombre;
						$aux_ancho = $CotizacionDetalle->producto->diametro;
						$aux_espesor = 0; //$CotizacionDetalle->espesor;
						$aux_largo = $CotizacionDetalle->producto->long . " mts";
						$aux_cla_sello_nombre = $CotizacionDetalle->producto->claseprod->cla_nombre;
						if ($CotizacionDetalle->acuerdotecnicotemp != null){
							$AcuTecTemp = $CotizacionDetalle->acuerdotecnicotemp;
							$aux_producto_nombre = nl2br($CotizacionDetalle->producto->categoriaprod->nombre . ", " . $AcuTecTemp->at_desc);
							//$aux_producto_nombre = nl2br($AcuTecTemp->at_desc . "\n" . $AcuTecTemp->materiaprima->nombre . " " . $AcuTecTemp->materiaprima->desc . "\n". $AcuTecTemp->at_tiposelloobs);
							$aux_ancho = $AcuTecTemp->at_ancho . " " . $AcuTecTemp->anchounidadmedida->nombre;
							$aux_largo = $AcuTecTemp->at_largo . " " . ($AcuTecTemp->at_largo ? $AcuTecTemp->largounidadmedida->nombre : "") ;
							$aux_espesor = $AcuTecTemp->at_espesor;
							$aux_cla_sello_nombre = $AcuTecTemp->claseprod->cla_nombre;
						}
						if ($CotizacionDetalle->producto->acuerdotecnico != null){
							$AcuTec = $CotizacionDetalle->producto->acuerdotecnico;
							$aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
							//$aux_producto_nombre = nl2br($AcuTec->at_desc . "\n" . $AcuTec->materiaprima->nombre . " " . $AcuTec->materiaprima->desc . "\n". $AcuTec->at_tiposelloobs);
							$aux_ancho = $AcuTec->at_ancho . " " . $AcuTec->anchounidadmedida->nombre;
							$aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
							$aux_espesor = $AcuTec->at_espesor;
							$aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
						}
					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$CotizacionDetalle->producto_id}}</td>
						<td class="textcenter">{{number_format($CotizacionDetalle->cant, 0, ",", ".")}}</td>
						<td class="textcenter">{{$CotizacionDetalle->unidadmedida->nombre}}</td>
						<!--San Bernardo
						<td class="textleft">{{$CotizacionDetalle->producto->nombre}}</td>
						<td class="textleft">{{$CotizacionDetalle->producto->claseprod->cla_nombre}}</td>
						<td class="textcenter">
							{{$CotizacionDetalle->producto->diametro}}
						</td>
						<td class="textright">{{$CotizacionDetalle->producto->long}} mts</td>
						<td class="textcenter">{{$CotizacionDetalle->producto->tipounion}}</td>
						-->
						<td class="textleft">{!!$aux_producto_nombre!!}</td>
						<td class="textcenter">{{$aux_cla_sello_nombre}}</td>
						<td class="textcenter">{{$aux_ancho}}</td>
						<td class="textcenter">{{$aux_largo}}</td>
						<td class="textcenter">{{number_format($aux_espesor, 3, ',', '.')}}</td>
						<td class="textright">{{number_format($CotizacionDetalle->preciounit, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($CotizacionDetalle->subtotal, 0, ",", ".")}}&nbsp;</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="factura_detalle">
			<tr>
			<!--San Bernardo	
			<td colspan="9" class="textright" width="85%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->neto, 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr>
				<td colspan="9" class="textright" width="85%"><span><strong>IVA {{$cotizacion->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->iva, 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr>
				<td colspan="9" class="textright" width="85%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->total, 0, ",", ".")}}</strong></span></td>
			-->
				<td colspan="8" class="textright" width="85%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->neto, 0, ",", ".")}}&nbsp;</strong></span></td>
			</tr>
			<tr>
				<td colspan="8" class="textright" width="85%"><span><strong>IVA {{$cotizacion->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->iva, 0, ",", ".")}}&nbsp;</strong></span></td>
			</tr>
			<tr>
				<td colspan="8" class="textright" width="85%"><span><strong>TOTAL {{$aux_modena_desc}}</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->total, 0, ",", ".")}}&nbsp;</strong></span></td>
			</tr>
		</table>
	</div>
	<div class="round" style="margin-bottom: 3px;">
		<p class="nota"><strong>Observaciones: {{$cotizacion->observacion}}</strong></p>
	</div>
	<div>
		<table width="100%">
			<tr>
				<td width="30%">
					<div class="round2" style="padding-bottom: 0px;">
						<span class="h3">Información</span>
						<table>
							@if ($cotizacion->plaentdias > 0)
								<tr>
									<td colspan="9" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
									<td class="textleft" width="50%"><span>{{$cotizacion->plaentdias}} días hábiles</span></td>
								</tr>				
							@endif
							<tr>
								<td colspan="9" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
								<td class="textleft" width="50%"><span>{{$cotizacion->lugarentrega}}</span></td>
							</tr>
							<tr>
								<td colspan="9" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
								<td class="textleft" width="50%"><span>{{$cotizacion->plazopago->descripcion}}</span></td>
							</tr>
							<tr>
								<td colspan="9" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
								<td class="textleft" width="50%"><span>{{$cotizacion->tipoentrega->nombre}}</span></td>
							</tr>
						</table>
					</div>			
				</td>
				<td>
					<div style="padding-left: 20px;padding-right: 10px;">
						<p style="font-size: 9px;">
							<b> Según las políticas de devoluciones de productos, PLASTISERVI establece:</b>
						</p>
						<ol style="font-size: 10px;text-align: justify;">
							<li>No se aceptarán devoluciones por equivocaciones que cometió el cliente al momento de realizar la OC. Por ejemplo: No le sirvió para el propósito, no tiene espacio suficiente para almacenar, las medidas o material o el color no le sirvieron, etc.</li>
							<li>Sólo se recibirá el producto en buen estado, libre de contaminación física, química o biológica, con etiqueta y embalaje original y en condiciones como fue entregado al cliente.</li>
							<li>Sólo se recibirá con guía de despacho, indicando las unidades, dimensiones del producto y motivo de la devolución.</li>
							<li>La recepción de los productos no asegura la reposición, cambio o nota de crédito.</li>
							<li>En caso de que Control de Calidad determine que una parte de la mercadería no está en buen estado, esta no se aceptará y no se realizará nota de crédito por la mercadería defectuosa.</li>
							<li>El periodo para realizar la devolución es de 90 días posterior a la fecha de facturación.</li>
						</ol>
						
					</div>
				</td>
			</tr>
		</table>		
	</div>
	<div>
		<p class="nota">
			@if ($aux_monedaLocal == false)
				<br><br>Valores en dólares americanos {{$aux_modena_desc}}. Tipo de cambio: dólar observado.
			@endif
			<br><br>Si usted tiene preguntas sobre esta cotización, pongase en contacto con nombre, teléfono y Email
		</p>
		<!--<h4 class="label_gracias">¡Gracias por su compra!</h4>-->
	</div>
</div>

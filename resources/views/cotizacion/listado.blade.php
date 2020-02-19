<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
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
					<p>Email: {{$cotizacion->sucursal->email}}</p>
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
				<div class="round">
					<span class="h3">Cotización</span>
					<p>No. Cotización: <strong> {{ str_pad($cotizacion->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha: {{date('d-m-Y', strtotime($cotizacion->fechahora))}}</p>
					<p>Hora: {{date("h:i:s A", strtotime($cotizacion->fechahora))}}</p>
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
					<table class="datos_cliente">
						@if (empty($cotizacion->clientetemp_id))
							<tr class="headt">
								<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $cotizacion->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->cliente->rut, strlen($cotizacion->cliente->rut) -1 , 1 )}}</p></td>
								<td><label>Teléfono:</label> <p>{{$cotizacion->cliente->telefono}}</p></td>
							</tr>
							<tr class="headt">
								<td><label>Nombre:</label> <p>{{$cotizacion->cliente->razonsocial}}</p></td>
								<td><label>Dirección:</label> <p>{{$cotizacion->cliente->direccion}}</p></td>
							</tr>
							<tr class="headt">
								<td><label>Contacto:</label> <p>{{$cotizacion->cliente->contactonombre}}</p></td>
								<td><label>Comuna:</label> <p>{{$cotizacion->cliente->comuna->nombre}}</p></td>
							</tr>
						@else
							<tr class="headt">
								<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $cotizacion->clientetemp->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->clientetemp->rut, strlen($cotizacion->clientetemp->rut) -1 , 1 )}}</p></td>
								<td><label>Teléfono:</label> <p>{{$cotizacion->clientetemp->telefono}}</p></td>
							</tr>
							<tr class="headt">
								<td><label>Nombre:</label> <p>{{$cotizacion->clientetemp->razonsocial}}</p></td>
								<td><label>Dirección:</label> <p>{{$cotizacion->clientetemp->direccion}}</p></td>
							</tr>
							<tr class="headt">
								<td><label>Contacto:</label> <p>{{$cotizacion->clientetemp->contactonombre}}</p></td>
								<td><label>Comuna:</label> <p>{{$cotizacion->clientetemp->comuna->nombre}}</p></td>
							</tr>
						
						@endif
					</table>
				</div>
			</td>

		</tr>
	</table>

	<div class="round">
	<table id="factura_detalle">
			<thead>
				<tr>
					<th width="50px">Cant.</th>
					<th class="textcenter">Unidad</th>
					<th class="textleft">Descripción</th>
					<th class="textleft">Clase</th>
					<th class="textright">Diametro</th>
					<th class="textright">Largo</th>
					<th class="textright" width="150px">Precio Neto</th>
					<th class="textright" width="150px">Total Neto</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				@foreach($cotizacionDetalles as $CotizacionDetalle)
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{number_format($CotizacionDetalle->cant,0)}}</td>
						<td class="textcenter">{{$CotizacionDetalle->unidadmedida->nombre}}</td>
						<td class="textleft">{{$CotizacionDetalle->producto->nombre}}</td>
						<td class="textleft">{{$CotizacionDetalle->producto->claseprod->cla_nombre}}</td>
						<td class="textright">{{$CotizacionDetalle->producto->diamextmm}} mm</td>
						<td class="textright">{{$CotizacionDetalle->producto->long}} mts</td>
						<td class="textright">{{number_format($CotizacionDetalle->preciounit, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($CotizacionDetalle->subtotal, 2, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<!--
			<tfoot id="detalle_totales">
				<tr class="headt">
					<td colspan="7" class="textright"><span>NETO</span></td>
					<td class="textright"><span>{{number_format($cotizacion->neto, 2, ",", ".")}}</span></td>
				</tr>
				<tr class="headt">
					<td colspan="7" class="textright"><span>IVA {{$empresa[0]['iva']}}%</span></td>
					<td class="textright"><span>{{number_format($cotizacion->iva, 2, ",", ".")}}</span></td>
				</tr>
				<tr class="headt">
					<td colspan="7" class="textright"><span>TOTAL</span></td>
					<td class="textright"><span>{{number_format($cotizacion->total, 2, ",", ".")}}</span></td>
				</tr>
			</tfoot>
			-->
	</table>
	</div>

	<div class="round">
		<table id="factura_detalle">
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->neto, 2, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>IVA {{$empresa[0]['iva']}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->iva, 2, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->total, 2, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
	<br>
	<div class="round1">
		<span class="h3">Información</span>
		<table>
			<tr class="headt">
				<td colspan="7" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($cotizacion->plazoentrega))}}</span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$cotizacion->lugarentrega}}</span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$cotizacion->plazopago->descripcion}}</span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$cotizacion->tipoentrega->nombre}}</span></td>
			</tr>
		</table>
	</div>
	<br>
	<div class="round">
		<p class="nota"><strong>Observaciones: {{$cotizacion->observacion}}</strong></p>
	</div>
	<br>
	<div>
		<p class="nota">Si usted tiene preguntas sobre esta cotización, <br>pongase en contacto con nombre, teléfono y Email</p>
		<!--<h4 class="label_gracias">¡Gracias por su compra!</h4>-->
	</div>
</div>

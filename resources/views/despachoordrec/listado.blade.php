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
					<table class="datos_cliente">
						@if (empty($cotizacion->clientetemp_id))
							<!--<tr class="headt">-->
							<tr>
								<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $cotizacion->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->cliente->rut, strlen($cotizacion->cliente->rut) -1 , 1 )}}</p></td>
								<td><label>Teléfono:</label> <p>{{$cotizacion->cliente->telefono}}</p></td>
							</tr>
							<tr>
								<td><label>Nombre:</label> <p>{{$cotizacion->cliente->razonsocial}}</p></td>
								<td><label>Dirección:</label> <p>{{$cotizacion->cliente->direccion}}</p></td>
							</tr>
							<tr>
								<td><label>Contacto:</label> <p>{{$cotizacion->cliente->contactonombre}}</p></td>
								<td><label>Comuna:</label> <p>{{$cotizacion->cliente->comuna->nombre}}</p></td>
							</tr>
						@else
							<tr>
								<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $cotizacion->clientetemp->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->clientetemp->rut, strlen($cotizacion->clientetemp->rut) -1 , 1 )}}</p></td>
								<td><label>Teléfono:</label> <p>{{$cotizacion->clientetemp->telefono}}</p></td>
							</tr>
							<tr>
								<td><label>Nombre:</label> <p>{{$cotizacion->clientetemp->razonsocial}}</p></td>
								<td><label>Dirección:</label> <p>{{$cotizacion->clientetemp->direccion}}</p></td>
							</tr>
							<tr>
								<td><label>Contacto:</label> <p>{{$cotizacion->clientetemp->contactonombre}}</p></td>
								<td><label>Comuna:</label> <p>{{$cotizacion->clientetemp->comuna->nombre}}</p></td>
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
					<th class="textcenter" width="50px">Unidad</th>
					<th class="textleft" width="190px">Descripción</th>
					<th class="textleft" width="60px">Clase</th>
					<th class="textcenter" width="35px">Diamet</th>
					<th class="textright">Largo</th>
					<th class="textright" width="70px">Precio Neto</th>
					<th class="textright" width="90px">Total Neto</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				@foreach($cotizacionDetalles as $CotizacionDetalle)
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$CotizacionDetalle->producto_id}}</td>
						<td class="textcenter">{{number_format($CotizacionDetalle->cant, 0, ",", ".")}}</td>
						<td class="textcenter">{{$CotizacionDetalle->unidadmedida->nombre}}</td>
						<td class="textleft">{{$CotizacionDetalle->producto->nombre}}</td>
						<td class="textleft">{{$CotizacionDetalle->producto->claseprod->cla_nombre}}</td>
						<td class="textcenter">
							{{$CotizacionDetalle->producto->diametro}}
						</td>
						<td class="textright">{{$CotizacionDetalle->producto->long}} mts</td>
						<td class="textright">{{number_format($CotizacionDetalle->preciounit, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($CotizacionDetalle->subtotal, 0, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="factura_detalle">
			<tr>
				<td colspan="8" class="textright" width="85%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->neto, 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr>
				<td colspan="8" class="textright" width="85%"><span><strong>IVA {{$cotizacion->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->iva, 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr>
				<td colspan="8" class="textright" width="85%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($cotizacion->total, 0, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
	<div class="round" style="margin-bottom: 3px;">
		<p class="nota"><strong>Observaciones: {{$cotizacion->observacion}}</strong></p>
	</div>
	<div class="round1" style="padding-bottom: 0px;">
		<span class="h3">Información</span>
		<table>
			<!--<tr class="headt">-->
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($cotizacion->plazoentrega))}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$cotizacion->lugarentrega}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$cotizacion->plazopago->descripcion}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$cotizacion->tipoentrega->nombre}}</span></td>
			</tr>
		</table>
	</div>
	<br>
	<div>
		<p class="nota">Si usted tiene preguntas sobre esta cotización, <br>pongase en contacto con nombre, teléfono y Email</p>
		<!--<h4 class="label_gracias">¡Gracias por su compra!</h4>-->
	</div>
</div>

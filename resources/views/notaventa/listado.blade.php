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
					<p>{{$empresa[0]['nombre']}}</p>
					<p>RUT: {{$empresa[0]['rut']}}</p>
					<p>{{$notaventa->sucursal->direccion}}</p>
					<p>Teléfono: {{$notaventa->sucursal->telefono1}}</p>
					<!--<p>Email: {{$notaventa->sucursal->email}}</p>-->
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
				<div class="round" style="padding-bottom: 3px;">
					<span class="h3">Nota de Venta</span>
					<p>Nro: <strong> {{ str_pad($notaventa->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha: {{date('d-m-Y', strtotime($notaventa->fechahora))}}</p>
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
				<div class="round">
					<span class="h3">Cliente</span>
					<table class="datos_cliente">
						<!--<tr class="headt">-->
						<tr>
							<td style="width:10%">Rut: </td><td style="width:50%">{{number_format( substr ( $notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->cliente->rut, strlen($notaventa->cliente->rut) -1 , 1 )}}</td>
							<td style="width:10%">Teléfono: </td><td style="width:30%">{{$notaventa->cliente->telefono}}</td>
						</tr>
						<tr>
							<td style="width:10%">Nombre: </td><td style="width:50%">{{$notaventa->cliente->razonsocial}}</td>
							<td style="width:10%">Dirección: </td><td style="width:30%">{{$notaventa->cliente->direccion}}</td>
						</tr>
						<tr>
							<td style="width:10%">Contacto: </td><td style="width:50%">{{$notaventa->cliente->contactonombre}}</td>
							<td style="width:10%">Comuna: </td><td style="width:30%">{{$notaventa->cliente->comuna->nombre}}</td>
						</tr>
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
					<th class="textleft">Diamet</th>
					<th class="textleft" width="60px">Clase</th>
					<th class="textcenter">Largo</th>
					<th class="textcenter">TU</th>
					<th class="textright" width="70px">Precio Neto</th>
					<th class="textright" width="90px">Total Neto</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				@foreach($notaventaDetalles as $notaventaDetalle)
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$notaventaDetalle->producto_id}}</td>
						<td class="textcenter">{{number_format($notaventaDetalle->cant, 0, ",", ".")}}</td>
						<td class="textcenter">{{$notaventaDetalle->producto->categoriaprod->unidadmedidafact->nombre}}</td>
						<td class="textleft">{{$notaventaDetalle->producto->nombre}}</td>
						<td class="textleft">
							{{$notaventaDetalle->producto->diametro}}
						</td>
						<td class="textleft">{{$notaventaDetalle->producto->claseprod->cla_nombre}}</td>
						<td class="textcenter">{{$notaventaDetalle->producto->long}} mts</td>
						<td class="textcenter">{{$notaventaDetalle->producto->tipounion}}</td>
						<td class="textright">{{number_format($notaventaDetalle->preciounit, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($notaventaDetalle->subtotal, 0, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="factura_detalle">
			<tr>
				<td colspan="8" class="textright" width="90%"><span><strong>NETO </strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->neto, 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr>
				<td colspan="8" class="textright" width="90%"><span><strong>IVA {{$notaventa->piva}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->iva, 0, ",", ".")}}</strong></span></td>
			</tr>
			<tr>
				<td colspan="8" class="textright" width="90%"><span><strong>TOTAL </strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->total, 0, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
	<div class="round" style="margin-bottom: 3px;">
		<p class="nota"><strong>Observaciones: {{$notaventa->observacion}}</strong></p>
	</div>
	<div class="round1" style="padding-bottom: 0px;">
		<span class="h3">Información</span>
		<table id="factura_detalle">
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($notaventa->plazoentrega))}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Comuna: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->comunaentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->lugarentrega}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->plazopago->descripcion}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->tipoentrega->nombre}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Contacto: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->contacto}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Contacto email: </strong></span></td>
				<td class="textleft" width="50%"><span>{{strtolower($notaventa->contactoemail)}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Contacto Teléfono: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->contactotelf}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Orden de Compra: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$notaventa->oc_id}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>No. Cotización: </strong></span></td>
				<td class="textleft" width="50%"><span>{{str_pad($notaventa->cotizacion_id, 10, "0", STR_PAD_LEFT)}}</span></td>
			</tr>
			<tr>
				<td colspan="8" class="textleft" width="40%"><span><strong>Pago a Nombre de: </strong></span></td>
				<td class="textleft" width="50%"><span>{{strtoupper($empresa[0]['nombre'])}}</span></td>
			</tr>
		</table>
	</div>
	<br>
	<div>
		<p class="nota">Si usted tiene preguntas sobre esta Nota de Venta, <br>pongase en contacto con nombre, teléfono y Email</p>
		<!--<h4 class="label_gracias">¡Gracias por su compra!</h4>-->
	</div>
</div>

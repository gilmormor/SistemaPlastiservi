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
					<p>{{$guiadespint->sucursal->direccion}}</p>
					<p>Teléfono: {{$guiadespint->sucursal->telefono1}}</p>
					<!--<p>Email: {{$guiadespint->sucursal->email}}</p>-->
				</div>
			</td>
			<td class="info_empresa">
				<!--
				<div>
					<span class="h2">COTIZACIÓN</span>
                    <p>{{$guiadespint->sucursal->direccion}}</p>
					<p>Teléfono: {{$guiadespint->sucursal->telefono1}}</p>
					<p>Email: {{$guiadespint->sucursal->email}}</p>
				</div>-->
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h3">Guia Despacho Interna</span>
					<p>No. Guia DespInt: <strong> {{ str_pad($guiadespint->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha: {{date('d-m-Y', strtotime($guiadespint->fechahora))}}</p>
					<p>Hora: {{date("h:i:s A", strtotime($guiadespint->fechahora))}}</p>
					<p>Vendedor: {{$guiadespint->vendedor->persona->nombre . " " . $guiadespint->vendedor->persona->apellido}} </p>
					<p>Teléfono: {{$guiadespint->vendedor->persona->telefono}} </p>
					<p>email: {{$guiadespint->vendedor->persona->email}} </p>
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
						<tr class="headt">
							<td><label>Rut:</label><p id="rutform" name="rutform">{{number_format( substr ( $guiadespint->cli_rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $guiadespint->cli_rut, strlen($guiadespint->cli_rut) -1 , 1 )}}</p></td>
							<td><label>Teléfono:</label> <p>{{$guiadespint->cli_tel}}</p></td>
						</tr>
						<tr class="headt">
							<td><label>Nombre:</label> <p>{{$guiadespint->cli_nom}}</p></td>
							<td><label>Dirección:</label> <p>{{$guiadespint->cli_dir}}</p></td>
						</tr>
						<tr class="headt">
							<td><label>Comuna:</label> <p>{{$guiadespint->comuna->nombre}}</p></td>
						</tr>
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
				@foreach($guiadespintDetalles as $guiadespintDetalle)
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{number_format($guiadespintDetalle->cant, 0, ",", ".")}}</td>
						<td class="textcenter">{{$guiadespintDetalle->unidadmedida->nombre}}</td>
						<td class="textleft">{{$guiadespintDetalle->producto_nombre}}</td>
						<td class="textleft">{{$guiadespintDetalle->producto->claseprod->cla_nombre}}</td>
						<td class="textright">
							{{$guiadespintDetalle->producto->diametro}}
						</td>
						<td class="textright">{{$guiadespintDetalle->producto->long}} mts</td>
						<td class="textright">{{number_format($guiadespintDetalle->preciounit, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($guiadespintDetalle->subtotal, 0, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
	</table>
	</div>

	<div class="round">
		<table id="factura_detalle">
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($guiadespint->total, 0, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
	<br>
	<div class="round1">
		<span class="h3">Información</span>
		<table>
			<tr class="headt">
				<td colspan="7" class="textleft" width="40%"><span><strong>Plazo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{date('d-m-Y', strtotime($guiadespint->plazoentrega))}}</span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textleft" width="40%"><span><strong>Lugar de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$guiadespint->lugarentrega}}</span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textleft" width="40%"><span><strong>Condición de Pago: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$guiadespint->plazopago->descripcion}}</span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textleft" width="40%"><span><strong>Tipo de Entrega: </strong></span></td>
				<td class="textleft" width="50%"><span>{{$guiadespint->tipoentrega->nombre}}</span></td>
			</tr>
		</table>
	</div>
	<br>
	<div class="round">
		<p class="nota"><strong>Observaciones: {{$guiadespint->observacion}}</strong></p>
	</div>
	<br>
</div>

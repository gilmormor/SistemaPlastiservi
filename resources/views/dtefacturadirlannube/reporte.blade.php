<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<br>
<br>
<br>
<br>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round" style="padding-bottom: 3px;">
					<span class="facturaformalibre_h3">Factura</span>
					<p class="fontnrofactura"><strong> Factura Nro: {{ str_pad($dte->id, 6, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha emision: {{date('d/m/Y', strtotime($dte->fechahora))}}</p>
					<p>Fecha Vencimiento: {{date('d/m/Y', strtotime($dte->fechahora))}}</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="facturaformalibre_h3">Cliente</span>
					<table class="datos_cliente">
						<!--<tr class="headt">-->
						<tr class="headt">
							<td style="width:100%"><strong>N° de RIF ó C.I N°:</strong> {{$dte->cliente->rut}}</td>
						</tr>
						<tr class="headt">
							<td style="width:100%"><strong>Nombre y Apellido ó Razón Social:</strong> {{strtoupper($dte->cliente->razonsocial)}}</td>
						</tr>
						<tr class="headt">
							<td style="width:100%"><strong>Domicilio Fiscal:</strong> {{strtoupper($dte->cliente->direccion)}}</td>
						</tr>
						<tr class="headt">
							<td style="width:100%"><strong>Teléfono:</strong> {{strtoupper($dte->cliente->telefono)}}</td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>
	<div class="round" style="padding-bottom: 0px;">
		<table id="facturaformalibre_detalle">
			<thead>
				<tr>
					<th class="textcenter" width="30px">Código</th>
					<th class="textleft" width="190px">Descripción</th>
					<th width="50px">Cantidad</th>
					<th class="textright" width="50px">Precio Unitario</th>
					<th class="textright" width="50px">I.V.A</th>
					<th class="textright" width="70px">Total</th>
				</tr>
			</thead>
			<tbody>
				@foreach($dte->dtedets as $dtedet)
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{str_pad($dtedet->producto_id, 5, "0", STR_PAD_LEFT)}}</td>
						<td class="textleft">{{strtoupper($dtedet->nmbitem)}}</td>
						<td class="textcenter">{{number_format($dtedet->qtyitem, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($dtedet->prcitem, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($dte->tasaiva, 1, ",", ".")}}</td>
						<td class="textright">{{number_format($dtedet->montoitem, 2, ",", ".")}}&nbsp;</td>
					</tr>
				@endforeach
				@for ($i = 1; $i < (22 - count($dte->dtedets)); $i++)
					<tr class="headt" style="height:150%;">
						<td class="textleft"></td>
						<td class="textleft"></td>
						<td class="textcenter"></td>
						<td class="textright"></td>
						<td class="textright"></td>
						<td class="textright">&nbsp;</td>
					</tr>
				
				@endfor
			</tbody>
		</table>
	</div>
	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="facturaformalibre_detalle">
			<tr>
				<td class="textleft" width="30%"><span><strong>Monto Total Bruto:</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($dte->mntneto, 2, ",", ".")}}&nbsp;</strong></span></td>
				<td colspan="3" class="textright" width="90%"><span><strong>Monto total Base Imponible {{$dte->tasaiva}}% {{$empresa->moneda->simbolo}}</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($dte->mntneto, 2, ",", ".")}}&nbsp;</strong></span></td>
			</tr>
			<tr>
				<td class="textleft" width="30%"><span><strong>Monto Total Flete:</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(0, 2, ",", ".")}}&nbsp;</strong></span></td>
				<td colspan="3" class="textright" width="90%"><span><strong>Impuesto al Valor Agregado IVA {{$dte->tasaiva}}% {{$empresa->moneda->simbolo}}</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($dte->iva, 2, ",", ".")}}&nbsp;</strong></span></td>
			</tr>
			<tr>
				<td class="textleft" width="30%"><span><strong>Monto Total Descuento:</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(0, 2, ",", ".")}}&nbsp;</strong></span></td>
				<td colspan="3" class="textright" width="90%"><span><strong>Monto total exento o exonerado {{$empresa->moneda->simbolo}}</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format(0, 2, ",", ".")}}&nbsp;</strong></span></td>
			</tr>
			<tr>
				<td class="textleft" width="30%"><span><strong>Monto Total Neto:</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($dte->mntneto, 2, ",", ".")}}&nbsp;</strong></span></td>
				<td colspan="3" class="textright" width="90%"><span><strong>Valor Total de la Venta de los Bienes ó de la Prestacion de Servicios {{$empresa->moneda->simbolo}}</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($dte->mnttotal, 2, ",", ".")}}&nbsp;</strong></span></td>
			</tr>
		</table>
	</div>
	<span><strong>Depositar o transferir a Nombre de: </strong>{{$empresa->nombre}}
	@foreach ($empresa->bancos as $banco)
		<strong>Cuenta {{$banco->bancotipocta->desc}}: </strong>{{$banco->nombre}} <strong>Nro:</strong>{{$banco->numcta}} </span><br>
	@endforeach
	<span><strong>SON: {{montoaLetras($dte->mnttotal)}}</strong></span>
</div>

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
					<p>{{$datas->sucursal->direccion}}</p>
					<p>Teléfono: {{$datas->sucursal->telefono1}}</p>
					<!--<p>Email: {{$datas->sucursal->email}}</p>-->
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round" style="padding-bottom: 3px;">
					<span class="h3">Movimiento de Inventario</span>
					<p>Nro: <strong> {{ str_pad($datas->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha: {{date('d-m-Y', strtotime($datas->fechahora))}}</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h3">Datos</span>
					<table class="datos_cliente">
						<!--<tr class="headt">-->
						<tr class="headt">
							<td style="width:10%">Descripción: </td><td style="width:100%">{{$datas->desc}}</td>
						</tr>
						<tr class="headt">
							<td style="width:10%">Mes: </td><td style="width:50%">{{$datas->annomes}}</td>
						</tr>
						<tr class="headt">
							<td style="width:10%">Módulo: </td><td style="width:50%">{{$datas->invmovmodulo->nombre}}</td>
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
					<th width="30px">CodProd</th>
					<th width="150px">Nombre Producto</th>
					<th width="150px">Bodega</th>
					<th class="textleft">Diamet</th>
					<th class="textleft" width="60px">Clase</th>
					<th class="textcenter">Largo</th>
					<th class="textcenter">TU</th>
					<th class="textcenter" width="70px">UniMed</th>
					<th class="textcenter" width="70px">Cant</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php $aux_totalcant = 0; ?>
				@foreach($datas->invmovdets as $invmovdet)
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$invmovdet->invbodegaproducto->producto_id}}</td>
						<td class="textleft">{{$invmovdet->invbodegaproducto->producto->nombre}}</td>
						<td class="textleft">{{$invmovdet->invbodegaproducto->invbodega->nombre}}</td>
						<td class="textcenter">{{$invmovdet->invbodegaproducto->producto->diametro}}</td>
						<td class="textcenter">{{$invmovdet->invbodegaproducto->producto->cla_nombre}}</td>
						<td class="textcenter">{{$invmovdet->invbodegaproducto->producto->long}} mts</td>
						<td class="textcenter">{{$invmovdet->invbodegaproducto->producto->tipounion}}</td>
						<td class="textcenter">{{$invmovdet->unidadmedida->nombre}}</td>
						<td class="textcenter">{{number_format($invmovdet->cant, 0, ",", ".")}}</td>
					</tr>
					<?php $aux_totalcant += $invmovdet->cant; ?>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="round" style="padding-bottom: 0px;padding-top: 8px;margin-bottom: 3px;">
		<table id="factura_detalle">
			<tr>
				<td colspan="8" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textcenter" width="10%"><span><strong>{{number_format($aux_totalcant, 0, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
</div>

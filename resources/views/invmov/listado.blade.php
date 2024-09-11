<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<div id="page_pdf">
	<table id="factura_head" style="width: 100%;border-collapse: collapse;">
		<tr>
			<td class="logo_factura" style="width: 150px;">
				<div>
					<img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png")}}" style="max-width:1400%;width:auto;height:auto;">
					<!--<p>{{$empresa[0]['nombre']}}</p>-->
					<p>RUT: {{$empresa[0]['rut']}}</p>
					<p>{{$datas->sucursal->direccion}}</p>
					<p>Teléfono: {{$datas->sucursal->telefono1}}</p>
					<!--<p>Email: {{$datas->sucursal->email}}</p>-->
				</div>
			</td>
			<td class="info_empresa" style="width: 40px;">
			</td>
			<td class="info_factura" style="width: 100px;">
				<div class="round" style="padding-bottom: 3px;">
					<span class="h3">Movimiento de Inventario</span>
					<p>Nro: <strong> {{ str_pad($datas->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha Creación: {{date('d/m/Y h:i:s A', strtotime($datas->created_at))}}</p>
					<p>Fecha Inv: {{date('d/m/Y', strtotime($datas->fechahora))}}</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round" style="padding-bottom: 3px;">
					<span class="h3"style="margin-bottom: 0px;">Datos</span>
					<table class="datos_cliente" style="padding-top: 0px;">
						<!--<tr class="headt">-->
						<tr class="headt">
							<td style="width:10%">Descripción: </td><td style="width:100%">{{$datas->desc}}</td>
						</tr>
						<tr class="headt">
							<!--<td style="width:10%">Mes: </td><td style="width:50%">{{$datas->annomes}}</td>-->
							<td style="width:10%">Mes: </td><td style="width:50%">{{substr($datas->annomes,4,2) . "-" . substr($datas->annomes,0,4)}}</td>
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
					<th width="30px">Cod</th>
					<th width="100px">Nombre Producto</th>
					<th width="100px">Categoria</th>
					<th width="100px">Bodega</th>
					<th class="textleft" width="60px">Clase<br>Sello</th>
					<th class="textleft">Diam<br>Ancho</th>
					<th class="textcenter">Largo</th>
					<th class="textcenter">TU<br>Esp</th>
					<th class="textcenter" width="70px">UniMed</th>
					<th class="textcenter" width="70px">Cant</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php $aux_totalcant = 0; ?>
				@foreach($datas->invmovdets as $invmovdet)
					<?php 
						$aux_ancho = $invmovdet->invbodegaproducto->producto->diametro;
						$aux_largo = $invmovdet->invbodegaproducto->producto->long . "Mts";
						$aux_espesor = $invmovdet->invbodegaproducto->producto->tipounion;
						$aux_cla_sello_nombre = $invmovdet->invbodegaproducto->producto->claseprod->cla_nombre;
						$aux_producto_nombre = $invmovdet->invbodegaproducto->producto->nombre;
						//$aux_categoria_nombre = $invmovdet->invbodegaproducto->producto->categoriaprod->nombre;
						if ($invmovdet->invbodegaproducto->producto->acuerdotecnico != null){
							$AcuTec = $invmovdet->invbodegaproducto->producto->acuerdotecnico;
							$aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
							$aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
							$aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
							$aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
							$aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
						}
					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$invmovdet->invbodegaproducto->producto_id}}</td>
						<td class="textleft">{{$invmovdet->invbodegaproducto->producto->nombre}}</td>
						<td class="textleft">{{$invmovdet->invbodegaproducto->producto->categoriaprod->nombre}}</td>
						<td class="textleft">{{$invmovdet->invbodegaproducto->invbodega->nombre}} / {{$invmovdet->invbodegaproducto->invbodega->sucursal->nombre}}</td>
						<td class="textcenter">{{$invmovdet->invbodegaproducto->producto->claseprod->cla_nombre}}</td>
						<td class="textcenter">{{$aux_ancho}}</td>
						<td class="textcenter">{{$aux_largo}}</td>
						<td class="textcenter">{{$aux_espesor}}</td>
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
				<td colspan="9" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textcenter" width="10%"><span><strong>{{number_format($aux_totalcant, 0, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
</div>

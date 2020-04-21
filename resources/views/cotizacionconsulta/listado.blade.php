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
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h3">Reporte Cotizaciones</span>
					<p>Fecha: {{date("d-m-Y")}}</p>
					<p>Hora: {{date("h:i:s A")}}</p>
					<p>Vendedor: {{$nomvendedor}} </p>
					<p>Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th width="50px">Cot Id.</th>
						<th class="textcenter">Fecha</th>
						<th class="textleft">RUT</th>
						<th class="textleft">Razón Social</th>
						<th class="textleft">Observación</th>
						<th class="textright" width="150px">Total</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$aux_total = 0;
					?>
					@foreach($cotizaciones as $cotizacion)
					<?php
						$aux_total += $cotizacion->total;
						$rut = number_format( substr ( $cotizacion->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $cotizacion->rut, strlen($cotizacion->rut) -1 , 1 );
						$aux_mensaje= "";
						$aux_icono = "";
						$aux_color = "";
						if ($cotizacion->aprobstatus=='1'){
							$aux_mensaje = "Aprobado Vendedor";
						}
						if ($cotizacion->aprobstatus=='2'){
							$aux_mensaje= "Precio menor en Tabla";
						}
						if ($cotizacion->aprobstatus=='3'){
							$aux_mensaje= "Precio menor Aprobado por supervisor";
						}
						if(empty($cotizacion->cliente_id)){
							$aux_mensaje = $aux_mensaje . " Cliente Nuevo debe ser Validado";
						}else{
							if(!empty($cotizacion->clientetemp_id)){
								$aux_mensaje= $aux_mensaje . " - Cliente Nuevo";
							}
						}
					?>
						<tr class="headt" style="height:150%;">
							<td class="textcenter">{{number_format($cotizacion->id,0)}}</td>
							<td class="textcenter">{{date('d-m-Y', strtotime($cotizacion->fechahora))}} {{date("h:i:s A", strtotime($cotizacion->fechahora))}}</td>
							<td class="textleft">{{$rut}}</td>
							<td class="textleft">{{$cotizacion->razonsocial}}</td>
							<td class="textleft">{{$aux_mensaje}}</td>
							<td class="textright">{{number_format($cotizacion->total, 2, ",", ".")}}</td>
						</tr>
					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr class="headt">
						<th colspan="5" style='text-align:left'>TOTAL</th>
						<th class="textright">{{number_format($aux_total, 2, ",", ".")}}</th>
					</tr>
				</tfoot>
			</table>
	</div>

	<!--
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
	-->

</div>

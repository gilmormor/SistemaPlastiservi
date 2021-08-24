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
					<span class="h3">{{$request->aux_titulo}}</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Area Producción: {{$nombreAreaproduccion}}</p>
					<p>Giro: {{$nombreGiro}} </p>
					<p>Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
				</div>
			</td>
		</tr>
	</table>
	<div class="round">
		<table id="factura_detalle">
			<thead>
				<tr>
					<th>NV</th>
					<th>Fecha</th>
					<th>Razón Social</th>
					<th>OC</th>
					<th>Comuna</th>
					<th>Kg Pend</th>
					<th>$ Pend</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php
					$aux_Ttotalkilos = 0;
					$aux_Tsubtotal = 0;
				?>
				@foreach($datas as $data)
					<?php 
						$aux_totalkilos = $data->totalkilos - $data->totalkgsoldesp;
						$aux_subtotal = $data->subtotal - $data->totalsubtotalsoldesp;
						$aux_Ttotalkilos += $aux_totalkilos;
						$aux_Tsubtotal += $aux_subtotal;
					?>
					<tr class='btn-accion-tabla'>
						<td>{{$data->id}}</td>
						<td>{{date('d-m-Y', strtotime($data->fechahora))}}</td>
						<td>{{$data->razonsocial}}</td>
						<td>{{$data->oc_id}}</td>
						<td>{{$data->comunanombre}}</td>
						<td style='text-align:right'>{{number_format($aux_totalkilos, 2, ",", ".")}}</td>
						<td style='text-align:right'>{{number_format($aux_subtotal, 0, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<th colspan='5' style='text-align:right'>TOTAL</th>
                    <th style='text-align:right'>{{number_format($aux_Ttotalkilos, 2, ",", ".")}}</th>
                    <th style='text-align:right'>{{number_format($aux_Tsubtotal, 0, ",", ".")}}</th>
				</tr>
			</tfoot>		
		</table>
	</div>
</div>
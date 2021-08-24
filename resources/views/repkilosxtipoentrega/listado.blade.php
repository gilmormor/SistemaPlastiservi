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
					@if (($request->numrep == '9' or $request->numrep == 10) != true)
						<p>Area Producción: {{$nombreAreaproduccion}}</p>
						<p>Giro: {{$nombreGiro}} </p>
						<p>Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
					@endif
				</div>
			</td>
		</tr>
	</table>
	<div style="text-align:center;">
		<div class="round" style="width: 50%;">
			<table id="factura_detalle">
				<thead>
					<tr>
						<th>Tipo entrega</th>
						<th style='text-align:right'>TOTAL Kg</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$aux_totalkilos = 0;
					?>
					@foreach($datas['kilosxtipoentrega'] as $kilosxtipoentrega)
						<tr>
							<td>{{$kilosxtipoentrega->nombre}}</td>
							<td style='text-align:right'>{{number_format($kilosxtipoentrega->totalkilos, 2, ",", ".")}}</td>
						</tr>
						<?php
							$aux_totalkilos += $kilosxtipoentrega->totalkilos;
						?>
					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr>
						<th>TOTAL</th>
						<th style='text-align:right'>{{number_format($aux_totalkilos, 2, ",", ".")}}</th>
					</tr>
				</tfoot>
			</table>
		</div>
	
	</div>
</div>

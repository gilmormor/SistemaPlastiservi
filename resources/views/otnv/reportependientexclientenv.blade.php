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
					<th>Razón Social</th>
					<th>Comuna</th>
					<th style='text-align:right'>Kg Pend</th>
					<th style='text-align:right'>$ Pend</th>	
				</tr>	
			</thead>
			<tbody id="detalle_productos">
				<?php
					$aux_kgpend = 0;
					$aux_platapend = 0;
					$razonsocial = "";
					$aux_comuna  = "";
					$aux_totalkg = 0;
					$aux_totalplata = 0;
					if($datas){
						$aux_clienteid = $datas[0]->cliente_id . $datas[0]->comunanombre;
					}
				?>
				@foreach($datas as $data)
					@if(($data->cliente_id . $data->comunanombre)!=$aux_clienteid){
						<tr>
							<td>{{$razonsocial}}</td>
							<td>{{$aux_comuna}}</td>
							<td style='text-align:right'>{{number_format($aux_kgpend, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($aux_platapend, 0, ",", ".")}}</td>
						</tr>
						<?php
							$aux_kgpend = 0;
							$aux_platapend = 0;
							$aux_clienteid = $data->cliente_id . $data->comunanombre;
						?>
					@endif
					<?php
						$aux_kgpend += ($data->totalkilos - $data->totalkgsoldesp);
						$aux_platapend += ($data->subtotal - $data->totalsubtotalsoldesp);
						$aux_totalkg += ($data->totalkilos - $data->totalkgsoldesp);
						$aux_totalplata += ($data->subtotal - $data->totalsubtotalsoldesp);
						$razonsocial = $data->razonsocial;
						$aux_comuna  = $data->comunanombre;
					?>
				@endforeach
				<tr>
					<td>{{$razonsocial}}</td>
					<td>{{$aux_comuna}}</td>
					<td style='text-align:right'>{{number_format($aux_kgpend, 2, ",", ".")}}</td>
					<td style='text-align:right'>{{number_format($aux_platapend, 0, ",", ".")}}</td>
				</tr>
			</tbody>
			<tfoot id="detalle_totales">
                <tr>
                    <th colspan='2' style='text-align:left'>TOTAL</th>
                    <th style='text-align:right'>{{number_format($aux_totalkg, 2, ",", ".")}}</th>
                    <th style='text-align:right'>{{number_format($aux_totalplata, 0, ",", ".")}}</th>
                </tr>
			</tfoot>		
		</table>
	</div>
</div>
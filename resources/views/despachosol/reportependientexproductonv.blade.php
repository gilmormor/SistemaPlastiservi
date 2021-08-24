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
					<th>Descripción</th>
					<th>Cod<br>Prod</th>
					<th>Diametro</th>
					<th>Clase</th>
					<th>Largo</th>
					<th>Peso</th>
					<th>TU</th>
					<th style='text-align:right'>Cant Pend</th>
					<th style='text-align:right'>Kg Pend</th>
					<th style='text-align:right'>$ Pend</th>
				</tr>	
			</thead>
			<tbody id="detalle_productos">
				<?php
					$aux_totalkg = 0;
					$aux_totalplata = 0;
				?>
				@foreach($datas as $data)
					@if($data->saldoplata>0){
						<?php
							$aux_totalkg += $data->saldokg;
							$aux_totalplata += $data->saldoplata;
						?>	
						<tr>
							<td>{{$data->nombre}}</td>
							<td style='text-align:center'>{{$data->producto_id}}</td>
							<td style='text-align:center'>{{$data->diametro}}</td>
							<td style='text-align:center'>{{$data->cla_nombre}}</td>
							<td style='text-align:center'>{{$data->long}}</td>
							<td style='text-align:center'>{{$data->peso}}</td>
							<td style='text-align:center'>{{$data->tipounion}}</td>
							<td style='text-align:right'>{{number_format($data->saldocant, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->saldokg, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->saldoplata, 0, ",", ".")}}</td>
						</tr>    
					@endif
				@endforeach
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
                    <th colspan='8' style='text-align:right'>TOTAL</th>
					<th style='text-align:right'>{{number_format($aux_totalkg, 2, ",", ".")}}</th>
                    <th style='text-align:right'>{{number_format($aux_totalplata, 0, ",", ".")}}</th>
				</tr>
			</tfoot>		
		</table>
	</div>
</div>
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
	Kilos
	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th>Productos</th>
						@foreach($datas['vendedores'] as $vendedor)
							<th style='text-align:right' >{{$vendedor->nombre}}</th>
						@endforeach
						<th style='text-align:right' class='tooltipsC' title='Total'>TOTAL</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$totalgeneral = 0;
					?>
					@foreach($datas['productos'] as $producto)
						<tr class='btn-accion-tabla tooltipsC'>
							<td>{{$producto->gru_nombre}}</td>
							@foreach($datas['vendedores'] as $vendedor)
								<?php
									$aux_encontrado = false;
									foreach($datas['totales'] as $total){
										if($total->grupoprod_id == $producto->id and $total->persona_id==$vendedor->id){
											$aux_encontrado = true;
											?>
											<td style='text-align:right'>{{number_format($total->totalkilos, 2, ",", ".")}}</td>
											<?php
										} 
									}
									if($aux_encontrado==false){ ?>
										<td style='text-align:right'>0.00</td>
										<?php
									}
								?>
							@endforeach
							<td style='text-align:right'>{{number_format($producto->totalkilos, 2, ",", ".")}}</td>
						</tr>
						<?php
							$totalgeneral += $producto->totalkilos;
						?>
					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr>
						<th>TOTAL KG</th>
						@foreach($datas['vendedores'] as $vendedor)
							<th style='text-align:right'>{{number_format($vendedor->totalkilos, 2, ",", ".")}}</th>
						@endforeach
						<th style='text-align:right'>{{number_format($totalgeneral, 2, ",", ".")}}</th>
					</tr>
				</tfoot>		
		</table>
	</div>
</div>

<div id="page_pdf">
	NV ($)
	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th>Productos</th>
						@foreach($datas['vendedores'] as $vendedor)
							<th style='text-align:right' >{{$vendedor->nombre}}</th>
						@endforeach
						<th style='text-align:right' class='tooltipsC' title='Total'>TOTAL $</th>
						<th style='text-align:right' class='tooltipsC' title='Total'>KG</th>
						<th style='text-align:right' class='tooltipsC' title='Total'>Prom $</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$totalgeneral = 0;
						$totalgeneralKilos = 0;
					?>
					@foreach($datas['productos'] as $producto)
						<tr class='btn-accion-tabla tooltipsC'>
							<td>{{$producto->gru_nombre}}</td>
							@foreach($datas['vendedores'] as $vendedor)
								<?php
									$aux_encontrado = false;
									foreach($datas['totales'] as $total){
										if($total->grupoprod_id == $producto->id and $total->persona_id==$vendedor->id){
											$aux_encontrado = true;
											?>
											<td style='text-align:right'>{{number_format($total->subtotal, 0, ",", ".")}}</td>
											<?php
										} 
									}
									if($aux_encontrado==false){ ?>
										<td style='text-align:right'>0.00</td>
										<?php
									}
								?>
							@endforeach
							<?php
								$aux_prom = 0;
								if($producto->totalkilos>0){
									$aux_prom = $producto->subtotal/$producto->totalkilos;
								}
							?>
							<td style='text-align:right'>{{number_format($producto->subtotal, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($producto->totalkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($aux_prom, 2, ",", ".")}}</td>
						</tr>
						<?php
							$totalgeneral += $producto->subtotal;
							$totalgeneralKilos += $producto->totalkilos;
						?>
					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr>
						<th>TOTAL $</th>
						@foreach($datas['vendedores'] as $vendedor)
							<th style='text-align:right'>{{number_format($vendedor->subtotal, 0, ",", ".")}}</th>
						@endforeach
						<?php
							$aux_prom = 0;
							if($totalgeneralKilos>0){
								$aux_prom = $totalgeneral/$totalgeneralKilos;
							}
						?>

						<th style='text-align:right'>{{number_format($totalgeneral, 0, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($totalgeneralKilos, 2, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($aux_prom, 2, ",", ".")}}</th>
					</tr>
				</tfoot>
		</table>
	</div>
</div>
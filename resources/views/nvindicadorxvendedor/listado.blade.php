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
	@if ($request->numrep=='1')
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
	@endif
</div>


@if ($request->numrep=='2' or $request->numrep=='5')
	<div id="page_pdf">
		NV ($)
		<div class="round">
			<table id="factura_detalle">
					<thead>
						<tr>
							<th>Productos</th>
							@if ($request->numrep=='2')
								@foreach($datas['vendedores'] as $vendedor)
									<th style='text-align:right' >{{$vendedor->nombre}}</th>
								@endforeach
								<th style='text-align:right'>TOTAL $</th>
							@endif
							@if ($request->numrep=='5')
								<th style='text-align:right'>Meta Comercial KG</th>
								<th style='text-align:right'>KG</th>
								<th style='text-align:right'>Precio Kg <br> Promedio $</th>
							@endif
						</tr>
					</thead>
					<tbody id="detalle_productos">
						<?php
							$totalgeneral = 0;
							$totalgeneralKilos = 0;
							$totalMCkg = 0;
						?>
						@foreach($datas['productos'] as $producto)
							<tr class='btn-accion-tabla tooltipsC'>
								<td>{{$producto->gru_nombre}}</td>
								@if ($request->numrep=='2')
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
									<td style='text-align:right'>{{number_format($producto->subtotal, 0, ",", ".")}}</td>
								@endif
								<?php
									$aux_prom = 0;
									if($producto->totalkilos>0){
										$aux_prom = $producto->subtotal/$producto->totalkilos;
									}
								?>
								@if ($request->numrep=='5')
									<td style='text-align:right'>{{number_format($producto->metacomerkg, 2, ",", ".")}}</td>
									<td style='text-align:right'>{{number_format($producto->totalkilos, 2, ",", ".")}}</td>
									<td style='text-align:right'>{{number_format($aux_prom, 2, ",", ".")}}</td>
								@endif
							</tr>
							<?php
								$totalgeneral += $producto->subtotal;
								$totalgeneralKilos += $producto->totalkilos;
								$totalMCkg += $producto->metacomerkg;
							?>
						@endforeach
					</tbody>
					<tfoot id="detalle_totales">
						<tr>
							<th>TOTAL</th>
							@if ($request->numrep=='2')
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
							@endif
							@if ($request->numrep=='5')
								<th style='text-align:right'>{{number_format($totalMCkg, 2, ",", ".")}}</th>
								<th style='text-align:right'>{{number_format($totalgeneralKilos, 2, ",", ".")}}</th>
								<th style='text-align:right'>{{number_format($aux_prom, 2, ",", ".")}}</th>
							@endif
						</tr>
					</tfoot>
			</table>
		</div>
	</div>
@endif
@if ($request->numrep=='6')
	<div id="page_pdf">
		Indicadores Comerciales al {{$aux_fhasta}}.
		<div class="round">
			<table id="factura_detalle">
					<thead>
						<tr>
							<th>Area Produccion</th>
							<th style='text-align:right'>Kg Facturado<br>al dia {{$aux_fhasta}}</th>
							<th style='text-align:right'>Kg Facturado<br>Acumulado</th>
							<th style='text-align:right'>Precio<br>Promedio Kg</th>
						</tr>
					</thead>
					<tbody id="detalle_productos">
						<?php
							$aux_totalkiloshoy = 0;
							$aux_totalkgfacacum = 0;
							$aux_totalmonto = 0;
						?>
						@foreach($datas['areaproduccion'] as $areaproduccion)
							<?php 
								$aux_promkilo = 0;
								if($areaproduccion->totalkilos>0){
									$aux_promkilo = $areaproduccion->subtotal/$areaproduccion->totalkilos;
								}
								$aux_kiloshoy = 0;
								foreach($datas['areaproduccionhoy'] as $areaproduccionhoy){
									if($areaproduccionhoy->id == $areaproduccion->id){
										$aux_kiloshoy = $areaproduccionhoy->totalkilos;
									}  
								}
							?>
							<tr class='btn-accion-tabla'>
								<td>{{$areaproduccion->nombre}}</td>
								<td style='text-align:right'>{{number_format($aux_kiloshoy, 2, ",", ".")}}</td>
								<td style='text-align:right'>{{number_format($areaproduccion->totalkilos, 2, ",", ".")}}</td>
								<td style='text-align:right'>{{number_format($aux_promkilo, 2, ",", ".")}}</td>
							</tr>
							<?php
								$aux_totalkgfacacum += $areaproduccion->totalkilos;
								$aux_totalmonto += $areaproduccion->subtotal;
								$aux_totalkiloshoy += $aux_kiloshoy;
							?>
						@endforeach
						<?php 
							$aux_promkilogen = 0;
							if($aux_totalkgfacacum > 0){
								$aux_promkilogen = $aux_totalmonto / $aux_totalkgfacacum;
							}
						?>
					</tbody>
					<tfoot id="detalle_totales">
						<tr>
							<th>TOTAL</th>
							<th style='text-align:right'>{{number_format($aux_totalkiloshoy, 2, ",", ".")}}</th>
							<th style='text-align:right'>{{number_format($aux_totalkgfacacum, 2, ",", ".")}}</th>
							<th style='text-align:right'>{{number_format($aux_promkilogen, 2, ",", ".")}}</th>
						</tr>
					</tfoot>
			</table>
		</div>
	</div>
@endif

@if ($request->numrep=='1' or $request->numrep=='2' or $request->numrep=='5')
<div id="page_pdf">
	<div class="round">
		<img src="{{session('grafico')}}" style="width:auto;height:auto;text-align:center;">	
	</div>
</div>
@endif
@if ($request->numrep=='4')
	<div id="page_pdf">
		<div class="round">
			<img src="{{session('grafico1')}}" style="width:550;height:300;text-align:center;">	
		</div>
	</div>
	<div id="page_pdf">
		<div class="round">
			<img src="{{session('grafico2')}}" style="width:550;height:300;text-align:center;">	
		</div>
	</div>
@endif
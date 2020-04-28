<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<?php 
	use App\Models\Producto;
?>
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
					<span class="h3">Informe Producto x Nota Venta</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Area Producción: {{$nombreAreaproduccion}}</p>
					<p>Categoria: {{$nombreCategoria}} </p>
					<p>Vendedor: {{$nomvendedor}} </p>
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
						<th style='text-align:left'>Descripción</th>
						<th style='text-align:left'>Diametro</th>
						<th style='text-align:left'>Clase</th>
						<th style='text-align:left'>Long</th>
						<th style='text-align:right'>Peso x Unidad</th>
						<th style='text-align:center'>U</th>
						<th style='text-align:right'>$</th>
						<th style='text-align:right'>Precio Prom Unit</th>
						<th style='text-align:right'>Precio Prom Kilo</th>	
						<th style='text-align:right'>Unid</th>
						<th style='text-align:right'>Total KG</th>
						<th style='text-align:right'>%</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$i=0;
						$aux_totalkilos = 0;
						$totalsumsubtotal = 0;
            			$totalsumcant = 0;

					?>
					<?php
						$aux_totalkilosP = 0;
						$aux_totalporcenkg = 0;
						foreach ($notaventas as $notaventa) {
							$aux_totalkilosP += $notaventa->sumtotalkilos;
						}
					?>
					@foreach($notaventas as $notaventa)
						<?php
							$i++;
							$aux_totalkilos = $aux_totalkilos + $notaventa->sumtotalkilos;
							$totalsumsubtotal += $notaventa->sumsubtotal;
							$totalsumcant += $notaventa->sumcant;
							$porcentajeKg = ($notaventa->sumtotalkilos * 100) / $aux_totalkilosP;
                			$aux_totalporcenkg += $porcentajeKg;
						?>
						<tr class='btn-accion-tabla tooltipsC'>
							<td>{{$notaventa->nombre}}</td>
							<td>
								<?php
									$producto = Producto::findOrFail($notaventa->producto_id);
									$aum_uniMed = '';
									if ($producto->categoriaprod->unidadmedida_id==3){
										$aum_uniMed = $producto->diamextpg;
									}
									else{
										$aum_uniMed = $producto->diamextmm . 'mm';
									}
								?>
								{{$aum_uniMed}}
							</td>
							<td>{{$notaventa->cla_nombre}}</td>
							<td>{{$notaventa->long}}</td>
							<td style='text-align:right'>{{number_format($notaventa->peso, 2, ",", ".")}}</td>
							<td style='text-align:center'>{{$notaventa->tipounion}}</td>
							<td style='text-align:right'>{{number_format($notaventa->sumsubtotal, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->prompreciounit, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->promprecioxkilo, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->sumcant, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->sumtotalkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($porcentajeKg, 2, ",", ".")}}</td>
						</tr>

					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr class="headt">
						<th colspan="6" style='text-align:left'>TOTAL</th>
						<th class="textright">{{number_format($totalsumsubtotal, 2, ",", ".")}}</th>
						<th style="text-align:right">{{number_format($totalsumsubtotal/$totalsumcant, 2, ",", ".")}}</th>
                        <th style="text-align:right">{{number_format($totalsumsubtotal/$aux_totalkilos, 2, ",", ".")}}</th>
						<th class="textright">{{number_format($totalsumcant, 0, ",", ".")}}</th>
						<th class="textright">{{number_format($aux_totalkilos, 2, ",", ".")}}</th>
						<th class="textright">{{number_format($aux_totalporcenkg, 2, ",", ".")}}</th>
					</tr>
				</tfoot>
		</table>
	</div>
</div>

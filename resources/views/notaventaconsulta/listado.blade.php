<?php
	use App\Models\Comuna;
?>
<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!-- Theme style -->
<link rel="stylesheet" href="{{asset("assets/$theme/dist/css/AdminLTE.min.css")}}">
<!-- AdminLTE App -->
<script src="{{asset("assets/$theme/dist/js/adminlte.min.js")}}"></script>

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
					<span class="h3">Reporte Nota de Venta</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Area Producción: {{$nombreAreaproduccion}}</p>
					<p>Vendedor: {{$nomvendedor}} </p>
					<p>Giro: {{$nombreGiro}} </p>
					<p>Tipo Entrega: {{$nombreTipoEntrega}} </p>
					<p>Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
<!--
					<tr>
						<th style='text-align:left'>#</th>
						<th style='text-align:left'>NV ID</th>
						<th class="textcenter">Fecha</th>
						<th class="textleft">Razón Social</th>
						<th style='text-align:right'>PVC Kg</th>
						<th style='text-align:right'>PVC $</th>
						<th style='text-align:right'>Cañeria Kg</th>
						<th style='text-align:right'>Cañeria $</th>
						<th style='text-align:right'>Total Kg</th>
						<th style='text-align:right'>Total $</th>
					</tr>
-->
					<tr>
						<th style='text-align:left'>#</th>
						<th style='text-align:left'>NV ID</th>
						<th style='text-align:left'>D</th>
						<th style='text-align:left'>OC</th>
						<th class="textcenter">Fecha</th>
						<th class="textleft">Razón Social</th>
						<th class="textleft">Comuna</th>
						<th style='text-align:right'>Total Kg</th>
						<th style='text-align:right'>Total $</th>
						<th style='text-align:right'>Prom</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$i=0;
						$aux_totalKG = 0;
						$aux_totalps = 0;
					?>
					@foreach($notaventas as $notaventa)
						<?php
							if(empty($notaventa->anulada)){
								$i++;
								$aux_totalKG += $notaventa->totalkilos;
								$aux_totalps += $notaventa->totalps;
							}
							$rut = number_format( substr ( $notaventa->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $notaventa->rut, strlen($notaventa->rut) -1 , 1 );
							$colorFila = "";
							$aux_data_toggle = "";
							$aux_title = "";
							if(!empty($notaventa->anulada)){
								$colorFila = 'background-color: #87CEEB;';
								$aux_data_toggle = "tooltip";
								$aux_title = "Anulada Fecha:" . $notaventa->anulada;
							}
							$aux_prom = 0;
							if($notaventa->totalkilos>0){
								$aux_prom = $notaventa->subtotal / $notaventa->totalkilos;
							}
							$comuna = Comuna::findOrFail($notaventa->comunaentrega_id);

						?>
<!--
						<tr style='{{$colorFila}}' title='{{$aux_title}}' data-toggle='{{$aux_data_toggle}}' class='btn-accion-tabla tooltipsC'>
							<td>{{$i}}</td>
							<td>{{$notaventa->id}}</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($notaventa->fechahora))}}</td>
							<td>{{$notaventa->razonsocial}}</td>
							<td style='text-align:right'>{{number_format($notaventa->pvckg, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->pvcpesos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->cankg, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->canpesos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->totalkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->totalps, 2, ",", ".")}}</td>
						</tr>
-->
						<?php
							$sql = "SELECT notaventa_id,sum(cantdesp) AS cantdesp 
							FROM despachoord JOIN despachoorddet 
							ON despachoord.id = despachoorddet.despachoord_id
							WHERE NOT(despachoord.id IN (SELECT despachoordanul.despachoord_id FROM despachoordanul))
							and despachoord.numfactura is not null
							and despachoord.notaventa_id=$notaventa->id
							and isnull(despachoord.deleted_at) and isnull(despachoorddet.deleted_at)
							group by despachoord.notaventa_id;";
							//dd("$sql");
							$datas = DB::select($sql);
							$aux_cant = 0;
							if($datas){
								$aux_cant = $datas[0]->cantdesp;
							}
							$ifd = "";
							if($aux_cant > 0){
								$ifd = "starb";
								if($notaventa->cant == $aux_cant){
									$ifd = "starl";
								}
							}

						?>
						<tr style='{{$colorFila}}' title='{{$aux_title}}' data-toggle='{{$aux_data_toggle}}' class='btn-accion-tabla tooltipsC'>
							<td>{{$i}}</td>
							<td>{{$notaventa->id}}</td>
							<td>
								@if (!empty($ifd))
									<div></div><img src="{{asset("assets/$theme/dist/img/$ifd.png")}}" style="max-width:100%;width:10;height:10;">	
								@endif
							</td>
							
							<td>{{$notaventa->oc_id}}</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($notaventa->fechahora))}}</td>
							<td>{{$notaventa->razonsocial}}</td>
							<td>{{$comuna->nombre}}</td>
							<td style='text-align:right'>{{number_format($notaventa->totalkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->totalps, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($aux_prom, 2, ",", ".")}}</td>
						</tr>

					@endforeach
				</tbody>
				<?php
					$aux_promGeneral = 0;
					if($aux_totalKG>0){
						$aux_promGeneral = $aux_totalps / $aux_totalKG;
					}
				?>

				<tfoot id="detalle_totales">
					<tr class="headt">
						<td colspan="7" class="textright"><b>TOTALES</b></td>
						<td class="textright"><b>{{number_format($aux_totalKG, 2, ",", ".")}}</b></td>
						<td class="textright"><b>{{number_format($aux_totalps, 2, ",", ".")}}</b></td>
						<td class="textright"><b>{{number_format($aux_promGeneral, 2, ",", ".")}}</b></td>
				</tfoot>
		</table>
	</div>

	<!--
	<div class="round">
		<table id="factura_detalle">
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>NETO</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->pvckg, 2, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>IVA {{$empresa[0]['iva']}}%</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->pvckg, 2, ",", ".")}}</strong></span></td>
			</tr>
			<tr class="headt">
				<td colspan="7" class="textright" width="90%"><span><strong>TOTAL</strong></span></td>
				<td class="textright" width="10%"><span><strong>{{number_format($notaventa->pvckg, 2, ",", ".")}}</strong></span></td>
			</tr>
		</table>
	</div>
	<br>
	-->

</div>

<?php
	use App\Models\Comuna;
	use App\Models\NotaVenta;
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
					<p>Sucursal: {{$nombreSucursal}}</p>
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
					<tr>
						<th style='text-align:left'>#</th>
						<th style='text-align:left'>NV ID</th>
						<th style='text-align:left'>Inf</th>
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
								$sql = "SELECT sum(despachoordrecdet.cantrec) AS cantrec
									FROM despachoordrecdet INNER JOIN despachoordrec
									ON despachoordrecdet.despachoordrec_id=despachoordrec.id AND ISNULL(despachoordrec.anulada) AND ISNULL(despachoordrec.deleted_at) AND ISNULL(despachoordrecdet.deleted_at)
									INNER JOIN despachoord
									ON despachoord.id = despachoordrec.despachoord_id AND ISNULL(despachoord.deleted_at)
									WHERE despachoord.notaventa_id=$notaventa->id
									AND despachoordrec.aprobstatus=2
									and NOT(despachoord.id IN (SELECT despachoordanul.despachoord_id FROM despachoordanul WHERE ISNULL(despachoordanul.deleted_at)));";
								$datas = DB::select($sql);
								if($datas){
									$aux_cant -= $datas[0]->cantrec;
								}    
							}
							$aux_aprobstatus = explode ( ",", $request->aprobstatus );
							if(in_array('5',$aux_aprobstatus)){
								if($aux_cant >= $notaventa->cant){
									continue;
								}
							}
							if(in_array('6',$aux_aprobstatus)){
								if($notaventa->cant != $aux_cant){
									continue;
								}
							}
							$i++;

							$ifd = "";
							if($aux_cant > 0){
								$ifd = "starb";
								if($notaventa->cant == $aux_cant){
									$ifd = "starl";
								}
							}
							$aux_iconiInf = '';
							$aux_ban = '';
							if(!empty($notaventa->anulada)){
								$aux_ban = 'A';
							}
							$notaventacerrada = NotaVenta::findOrFail($notaventa->id)
                                    			->notaventacerradas;
							if(count($notaventacerrada)>0){
								$aux_ban .= 'C';
							}
							$aux_razonsocial = ucwords(strtolower($notaventa->razonsocial));
							$aux_razonsocial = ucwords($aux_razonsocial,".");

						?>
						<tr style='{{$colorFila}}' title='{{$aux_title}}' data-toggle='{{$aux_data_toggle}}' class='btn-accion-tabla tooltipsC'>
							<td>{{$i}}</td>
							<td>{{$notaventa->id}}</td>
							<td>
								@if (!empty($ifd))
									<img src="{{asset("assets/$theme/dist/img/$ifd.png")}}" style="max-width:100%;width:10;height:10;">	
								@endif
								{{$aux_ban}}
							</td>
							
							<td>{{$notaventa->oc_id}}</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($notaventa->fechahora))}}</td>
							<td>{{$aux_razonsocial}}</td>
							<td>{{$comuna->nombre}}</td>
							<td style='text-align:right'>{{number_format($notaventa->totalkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->totalps, 0, ",", ".")}}</td>
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
					@foreach($totalareaprods as $totalareaprod)
						<?php
							$aux_promAreaProd = 0;
							if($totalareaprod->totalkilos > 0){
								$aux_promAreaProd = $totalareaprod->totalps / $totalareaprod->totalkilos;
							}
						?>
						<tr class="headt">
							<td colspan="7" class="textright"><b>{{$totalareaprod->nombre}}</b></td>
							<td class="textright"><b>{{number_format($totalareaprod->totalkilos, 2, ",", ".")}}</b></td>
							<td class="textright"><b>{{number_format($totalareaprod->totalps, 0, ",", ".")}}</b></td>
							<td class="textright"><b>{{number_format($aux_promAreaProd, 2, ",", ".")}}</b></td>
						</tr>
					@endforeach
					<tr class="headt">
						<td colspan="7" class="textright"><b>TOTAL</b></td>
						<td class="textright"><b>{{number_format($aux_totalKG, 2, ",", ".")}}</b></td>
						<td class="textright"><b>{{number_format($aux_totalps, 0, ",", ".")}}</b></td>
						<td class="textright"><b>{{number_format($aux_promGeneral, 2, ",", ".")}}</b></td>
					</tr>
				</tfoot>
		</table>
	</div>
	<br>
	<a>
		<i class='fa fa-fw $aux_icodespachoNew text-danger'>
			<p><small>
				<img src="{{asset("assets/$theme/dist/img/starb.png")}}" style="max-width:100%;width:10;height:10;">= Inicio Despacho,
				<img src="{{asset("assets/$theme/dist/img/starl.png")}}" style="max-width:100%;width:10;height:10;">= Fin Despacho,
				A= Anulada,
				C= Cerrada.
			</small></p>
		</i>                                    
	</a>
</div>

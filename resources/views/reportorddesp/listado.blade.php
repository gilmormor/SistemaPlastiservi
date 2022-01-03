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
					<span class="h3">Ordenes de Despacho</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Area Producción: {{$nombreAreaproduccion}}</p>
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
					<th style='text-align:center'>OD</th>
					<th style='text-align:center'>Fecha</th>
					<th style='text-align:center'>Fecha ED</th>
					<th style='text-align:left'>Razón Social</th>
					<th style='text-align:center'>SD</th>
					<th style='text-align:center'>OC</th>
					<th style='text-align:center'>NV</th>
					<th style='text-align:left'>Comuna</th>
					<th style='text-align:left'>TE</th>
					<th style='text-align:right'>Total Kg</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php
					$aux_totalkilos = 0;
				?>
				@foreach($datas as $data)
					<?php
						$aux_totalkilos = $aux_totalkilos + round($data->totalkilos, 2);
						$aux_anulado ="";
						if($data->despachoordanul_fechahora){
							$aux_anulado = "A";
						}
					?>
					<tr class='btn-accion-tabla tooltipsC'>
						<td style='text-align:center'>{{$data->id . $aux_anulado}}{{$data->despachoordrec_id ? '(' . $data->despachoordrec_id . ')': '' }}</td>
						<td style='text-align:center'>{{date('d-m-Y', strtotime($data->fechahora))}}</td>
						<td style='text-align:center'>{{date('d-m-Y', strtotime($data->fechaestdesp))}}</td>
						<td style='text-align:left'>{{$data->razonsocial}}</td>
						<td style='text-align:center'>{{$data->despachosol_id}}</td>
						<td style='text-align:center'>{{$data->oc_id}}</td>
						<td style='text-align:center'>{{$data->notaventa_id}}</td>
						<td style='text-align:left'>{{$data->comunanombre}}</td>
						<td style='text-align:left'>{{$data->tipentnombre}}</td>
						<td style='text-align:right'>{{number_format($data->totalkilos, 2, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot id="detalle_totales">
				<tr class="headt">
					<th colspan="9" style='text-align:right'>TOTAL</th>
					<th class="textright">{{number_format($aux_totalkilos, 2, ",", ".")}}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

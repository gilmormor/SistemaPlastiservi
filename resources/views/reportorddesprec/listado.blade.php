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
					<span class="h3">Informe Rechazo Orden Despacho</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th style='text-align:center'>ID</th>
						<th style='text-align:center'>Fecha</th>
						<th style='text-align:left'>Razon Social</th>
						<th style='text-align:center'>NV</th>
						<th style='text-align:center'>SD</th>
						<th style='text-align:center'>OD</th>
						<th style='text-align:center'>OC</th>
						<th style='text-align:left'>Comuna</th>
						<th style='text-align:right'>Total Kg</th>	
						<th style='text-align:right'>Monto</th>
						<th style='text-align:center'>Guia</th>
						<th style='text-align:center'>FGuia</th>
						<th style='text-align:center'>Fact</th>
						<th style='text-align:center'>FFact</th>
						<th style='text-align:left'>Motivo</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$aux_totalkilos = 0;
						$aux_totaldinero = 0;
					?>
					@foreach($datas as $data)
						<?php
							$aux_totalkilos += $data->totalkilos;
							$aux_totaldinero += $data->subtotal;
						?>
						<tr class='btn-accion-tabla tooltipsC'>
							<td style='text-align:center'>{{$data->id . $data->sta_anulada}}</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($data->fechahora))}}</td>
							<td style='text-align:left'>{{$data->razonsocial}}</td>
							<td style='text-align:center'>{{$data->notaventa_id}}</td>
							<td style='text-align:center'>{{$data->despachosol_id}}</td>
							<td style='text-align:center'>{{$data->despachoord_id}}</td>
							<td style='text-align:center'>{{$data->oc_id}}</td>
							<td style='text-align:left'>{{$data->comunanombre}}</td>
							<td style='text-align:right'>{{number_format($data->totalkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->subtotal, 0, ",", ".")}}</td>
							<td style='text-align:center'>{{$data->guiadespacho}}</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($data->guiadespachofec))}}</td>
							<td style='text-align:center'>{{$data->numfactura}}</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($data->fechafactura))}}</td>
							<td style='text-align:left'>{{$data->recmotivonombre}}</td>
						</tr>
					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr class="headt">
						<th colspan="8" style='text-align:right'>TOTAL</th>
						<th style="text-align:right">{{number_format($aux_totalkilos, 2, ",", ".")}}</th>
						<th style="text-align:right">{{number_format($aux_totaldinero, 0, ",", ".")}}</th>
					</tr>
				</tfoot>
		</table>
	</div>
	<br>
	<a>
		<i class='fa fa-fw $aux_icodespachoNew text-danger'>
			<p><small>
				A= Anulada,
			</small></p>
		</i>                                    
	</a>

</div>

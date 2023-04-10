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
					<span class="h3">Estado Cliente</span>
					<p>Fecha: {{date("d/m/Y h:i:s A")}}</p>
					<p>Centro Economico: {{$request->sucursal_nombre}}</p>
					<p>Desde: {{$request->fechad}} Hasta: {{$request->fechah}}</p>
				</div>
			</td>
		</tr>
	</table>

	<?php
		$aux_totalmnttotal = 0;
		$aux_rut = "";
		$count = 0;
	?>

	@foreach($datas as $data)
		@if ($data->rut != $aux_rut)
			@if ($count > 0)
						<tfoot id="detalle_totales">
							<tr class="headt">
								<th colspan="5" style='text-align:right'>TOTAL</th>
								<th class="textright">{{number_format($aux_totalmnttotal, 0, ",", ".")}}</th>
								<th class="textright">{{number_format(0, 0, ",", ".")}}</th>
								<th class="textright">{{number_format(0, 0, ",", ".")}}</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<br>
			@endif
			<?php
				$aux_totalmnttotal = 0;
			?>
			<div class="round">
				<table id="reporte_detalle">
				<thead>
					<tr>
						<th colspan="2" style='text-align:left'>Rut: {{number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 )}}</th>
						<th colspan="6" style='text-align:left'>Nombre: {{$data->razonsocial}}</th>
					</tr>
					<tr>
						<th>ID</th>
						<th>Fecha</th>
						<th style='text-align:center'>OC</th>
						<th style='text-align:center'>DTE Doc</th>
						<th style='text-align:center'>Tipo</th>
						<th style='text-align:right'>Monto</th>
						<th style='text-align:right'>Pagado</th>
						<th style='text-align:right'>Pendiente</th>
					</tr>
				</thead>
		@endif
				<tbody id="detalle_productos">
						<tr class='btn-accion-tabla tooltipsC'>
							<td style='text-align:center'>{{$data->id}}</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($data->fechahora))}}</td>
							<td style='text-align:center'>{{$data->oc_id}}</td>
							<td style='text-align:center'>{{$data->nrodocto}}</td>
							<td style='text-align:center'>{{$data->foliocontrol_doc}}</td>
							<td style='text-align:right'>{{number_format($data->mnttotal, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format(0, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format(0, 0, ",", ".")}}</td>
						</tr>
						<?php 
							$aux_rut = $datas[0]->rut;
						?>
				
				</tbody>
				<?php
					$aux_totalmnttotal += round($data->mnttotal, 2);
					$aux_rut = $data->rut;
					$count++;
				?>
	@endforeach
			<tfoot id="detalle_totales">
				<tr class="headt">
					<th colspan="5" style='text-align:right'>TOTAL</th>
					<th class="textright">{{number_format($aux_totalmnttotal, 0, ",", ".")}}</th>
					<th class="textright">{{number_format(0, 0, ",", ".")}}</th>
					<th class="textright">{{number_format(0, 0, ",", ".")}}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

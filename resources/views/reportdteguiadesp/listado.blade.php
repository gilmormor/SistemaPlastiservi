<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">
<?php 
	use App\Models\dtedte;
	//dd($datas);
?>
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
					<span class="h3">Guia Despacho</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th style='text-align:center' class='width10'>DTE</th>
						<th style='text-align:center' class='width10'>Fecha Emisión</th>
						<th style='text-align:center' class='width10'>RUT</th>
						<th style='text-align:left' class='width90'>Razón Social</th>
						<th style='text-align:right' class='width40'>Monto DTE</th>
						<th style='text-align:center' class='width30'>Estado</th>
						<th style='text-align:center' class='width30'>OC</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					@foreach($datas as $data)
						<tr class='btn-accion-tabla tooltipsC'>
							<td style='text-align:center'>{{$data->nrodocto}}</td>
							<td style='text-align:center'>{{date("d-m-Y", strtotime($data->fechahora))}}</td>
							<td>{{$data->rut}}</td>
							<td>{{$data->razonsocial}}</td>
							<td style='text-align:right'>{{number_format($data->mnttotal, 0, ",", ".")}}</td>
							<?php 
								$aux_estado = "";
								if($data->indtraslado == 6){
									$aux_estado = "Guia solo traslado";
								}
								if($data->indtraslado == 1){
									if(isnnul($data->dter_id)){
										$aux_estado = "Guia solo traslado";
									}
								}
								$dtedte = 

							?>
							<td></td>
							<td style='text-align:center'>{{$data->oc_id}}</td>
						</tr>
					@endforeach
				</tbody>
		</table>
	</div>
</div>
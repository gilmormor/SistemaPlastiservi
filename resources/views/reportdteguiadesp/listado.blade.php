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
					<p>Fecha: {{date("d/m/Y h:i:s A")}}</p>
					<p>Sucursal: {{$request->sucursal_nombre}}</p>
					<p>Estatus: {{$request->aprobstatusdesc}}</p>
					<p>Desde: {{$request->fechad}} Hasta: {{$request->fechah}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle" style="table-layout:fixed;width: 100%;">
				<thead>
					<tr>
						<th style='text-align:center;width: 5% !important;'>DTE</th>
						<th style='text-align:center;width: 8% !important;'>Fecha Emisión</th>
						<th style='text-align:center;width: 7% !important;'>RUT</th>
						<th style='text-align:left;width: 25% !important;'>Razón Social</th>
						<th style='text-align:right;width: 7% !important;'>Monto</th>
						<th style='text-align:center;width: 12% !important;'>Estado</th>
						<th style='text-align:center;width: 6% !important;'>OC</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					@foreach($datas as $data)
						<tr class='btn-accion-tabla tooltipsC'>
							<td style='text-align:center;width: 5% !important;'>{{$data->nrodocto}}</td>
							<td style='text-align:center;width: 8% !important;'>{{date("d/m/Y", strtotime($data->fechahora))}}</td>
							<td style='text-align:center;width: 7% !important;'>{{$data->rut}}</td>
							<td style='text-align:left;width: 25% !important;'>{{$data->razonsocial}}</td>
							<td style='text-align:right;width: 7% !important;'>{{number_format($data->mnttotal, 0, ",", ".")}}&nbsp;&nbsp;</td>
							<?php 
								$aux_estado = "";
								if($data->indtraslado == 6){
									$aux_estado = "Guia solo traslado";
								}
								if($data->indtraslado == 1){
									if(is_null($data->dter_id)){
										$aux_estado = "Pendiente de Facturar";
									}else{
										$aux_estado = "Guia facturada ($data->fact_nrodocto)";
									}
								}
								if($data->dteanul_obs){
									$aux_estado = "Anulada";
								}
								//$dtedte = 
							?>
							<td style='width: 12% !important;'>&nbsp;&nbsp;{{$aux_estado}}</td>
							<td style='text-align:center;width: 6% !important;'>{{$data->oc_id}}</td>
						</tr>
					@endforeach
				</tbody>
		</table>
	</div>
</div>
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
					<span class="h3">Libro de Ventas DTE</span>
					<p>Fecha: {{date("d/m/Y h:i:s A")}}</p>
					<p>Centro Economico: {{$request->sucursal_nombre}}</p>
					<p>Desde: {{$request->fechad}} Hasta: {{$request->fechah}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle" style="table-layout:fixed;width: 100%;">
			<thead>
				<tr>
					<th style='text-align:center;width: 5% !important;'>Tipo</th>
					<th style='text-align:center;width: 7% !important;'>Número</th>
					<th style='text-align:center;width: 7% !important;'>RUT</th>
					<th style='text-align:left;width: 25% !important;'>Razón Social</th>
					<th style='text-align:center;width: 7% !important;'>Fecha</th>
					<th style='text-align:right;width: 7% !important;'>Neto</th>
					<th style='text-align:right;width: 7% !important;'>IVA</th>
					<th style='text-align:right;width: 7% !important;'>Total</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php 
					$i = 0;
					$total_mntneto = 0;
					$total_iva = 0;
					$total_mnttotal_a = 0;
					$total_mntnetoF = 0;
					$total_ivaF = 0;
					$total_mnttotal_aF = 0;
					$total_mntnetoNC = 0;
					$total_ivaNC = 0;
					$total_mnttotal_aNC = 0;
					$total_mntnetoND = 0;
					$total_ivaND = 0;
					$total_mnttotal_aND = 0;
					$total_mntnetoEX = 0;
					$total_ivaEX = 0;
					$total_mnttotal_aEX = 0;
				?>
				@foreach($datas as $data)
					<tr class='btn-accion-tabla tooltipsC'>
						<td style='text-align:center;width: 5% !important;'>{{$data->tipodocto}}</td>
						<td style='text-align:center;width: 5% !important;'>{{$data->nrodocto}}</td>
						<td style='text-align:center;width: 7% !important;font-size: 9px;'>{{formatearRUT($data->rut)}}</td>
						<td style='text-align:left;width: 25% !important;font-size: 8px;'>{{$data->razonsocial}}</td>
						<td style='text-align:center;width: 5% !important;'>{{date("d/m/Y", strtotime($data->fchemis))}}</td>
						<td style='text-align:right;width: 7% !important;'>{{number_format($data->mntneto, 0, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7% !important;'>{{number_format($data->iva, 0, ",", ".")}}&nbsp;&nbsp;</td>
						<td style='text-align:right;width: 7% !important;'>{{number_format($data->mnttotal_a, 0, ",", ".")}}&nbsp;&nbsp;</td>
					</tr>
					<?php 
						$i += 1;
						$total_mntneto += $data->mntneto;
						$total_iva += $data->iva;
						$total_mnttotal_a += $data->mnttotal_a;
						if($data->tipodocto == 33){
							$total_mntnetoF += $data->mntneto;
							$total_ivaF += $data->iva;
							$total_mnttotal_aF += $data->mnttotal_a;
						}
						if($data->tipodocto == 61){
							$total_mntnetoNC += $data->mntneto;
							$total_ivaNC += $data->iva;
							$total_mnttotal_aNC += $data->mnttotal_a;
						}
						if($data->tipodocto == 56){
							$total_mntnetoND += $data->mntneto;
							$total_ivaND += $data->iva;
							$total_mnttotal_aND += $data->mnttotal_a;
						}
						if($data->tipodocto == 34){
							$total_mntnetoEX += $data->mntneto;
							$total_ivaEX += $data->iva;
							$total_mnttotal_aEX += $data->mnttotal_a;
						}
					?>
				@endforeach
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<th colspan='5' style='text-align:right'>TOTAL</th>
					<th style='text-align:right'>{{number_format($total_mntneto, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_iva, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_mnttotal_a, 0, ",", ".")}}&nbsp;&nbsp;</th>
				</tr>
				<tr>
					<th colspan='5' style='text-align:right'>Facturas:</th>
					<th style='text-align:right'>{{number_format($total_mntnetoF, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_ivaF, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_mnttotal_aF, 0, ",", ".")}}&nbsp;&nbsp;</th>
				</tr>
				<tr>
					<th colspan='5' style='text-align:right'>Notas de Crédito:</th>
					<th style='text-align:right'>{{number_format($total_mntnetoNC, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_ivaNC, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_mnttotal_aNC, 0, ",", ".")}}&nbsp;&nbsp;</th>
				</tr>
				<tr>
					<th colspan='5' style='text-align:right'>Notas de Débito:</th>
					<th style='text-align:right'>{{number_format($total_mntnetoND, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_ivaND, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_mnttotal_aND, 0, ",", ".")}}&nbsp;&nbsp;</th>
				</tr>
				<tr>
					<th colspan='5' style='text-align:right'>Exento:</th>
					<th style='text-align:right'>{{number_format($total_mntnetoEX, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_ivaEX, 0, ",", ".")}}&nbsp;&nbsp;</th>
					<th style='text-align:right'>{{number_format($total_mnttotal_aEX, 0, ",", ".")}}&nbsp;&nbsp;</th>
				</tr>
			</tfoot>

		</table>
	</div>
</div>

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
					<span class="h3">Movimiento Inventario</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Mes Stock: {{$request->annomes}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
			<thead>
				<tr>
					<th style='text-align:left' class='width10'>Id</th>
					<th style='text-align:left' class='width10'>IdDet</th>
					<th style='text-align:left' class='width90'>Fecha</th>
					<th style='text-align:left' class='width90'>Descripcion</th>
					<th style='text-align:left' class='width30'>CodProd</th>
					<th style='text-align:left' class='width40'>Producto</th>
					<th style='text-align:left' class='width10'>Modulo</th>
					<th style='text-align:left' class='width90'>Bodega</th>
					<th style='text-align:center' class='width40'>Cant</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php 
					$aux_total = 0;
				?>
				@foreach($datas as $data)
					<tr class='btn-accion-tabla tooltipsC'>
						<td style='text-align:center'>{{$data->id}}</td>
						<td style='text-align:center'>{{$data->invmovdet_id}}</td>
						<td style='text-align:center'>{{date('d-m-Y', strtotime($data->fechahora))}}</td>
						<td>{{$data->desc}}</td>
						<td style='text-align:center'>{{$data->producto_id}}</td>
						<td style='text-align:left'>{{$data->producto_nombre}}</td>
						<td style='text-align:left'>{{$data->invmovmodulo_nombre}}</td>
						<td style='text-align:left'>{{$data->invbodega_nombre}}</td>
						<td style='text-align:center'>{{$data->cant}}</td>
					</tr>
					<?php 
						$aux_total += $data->cant;
					?>

				@endforeach
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<th colspan='8' style='text-align:right'>TOTAL</th>
					<th style='text-align:center'>{{number_format($aux_total, 0, ",", ".")}}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<?php 
	use App\Models\Producto;
	use App\Models\Vendedor;
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
					<span class="h3">Precio promedio NV Agrupado x Categoria</span>
					<p>Fecha: {{date("d/m/Y h:i:s A")}}</p>
					<p>Sucursal: {{$request->sucursal_nombre}}</p>
					<p>Desde: {{$request->fechad}} Hasta: {{$request->fechah}}</p>
				</div>
			</td>
		</tr>
	</table>

	<?php
		$aux_vendedor_id = "";
		$count = 0;
	?>

	@foreach($datas as $data)
		@if ($data->vendedor_id != $aux_vendedor_id)
			<div class="round">
				<table id="reporte_detalle">
				<thead>
					<tr>
						<!--<th colspan="2" style='text-align:left'>Rut: {{number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 )}}</th> -->
						<th colspan="1" style='text-align:left'>ID: {{$data->vendedor_id}}</th>
						<th colspan="7" style='text-align:left'>Nombre: {{$vendedor->persona->nombre . " " . $vendedor->persona->apellido}}</th>
					</tr>
					<tr>
						<th>Nombre Grupo (Precio promedio)</th>
						<th style='text-align:right'>Total $</th>
						<th style='text-align:right'>Total Kg</th>
						<th style='text-align:right'>Promedio</th>
					</tr>
				</thead>
		@endif
				<tbody id="detalle_productos">
					<tr class='btn-accion-tabla tooltipsC'>
						<td style='text-align:center'>{{$data->nombre}}</td>
						<td style='text-align:center'>{{number_format($data->subtotal, 0, ",", ".")}}</td>
						<td style='text-align:center'>{{number_format($data->totalkilos, 2, ",", ".")}}</td>
						<td style='text-align:center'>{{number_format($data->promedio, 2, ",", ".")}}</td>
					</tr>
				</tbody>
				<?php
					$aux_vendedor_id = $data->vendedor_id;
					$count++;
				?>
	@endforeach
		</table>
	</div>
</div>

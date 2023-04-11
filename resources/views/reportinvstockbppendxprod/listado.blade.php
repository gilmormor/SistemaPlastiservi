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
					<span class="h3">Stock Productos/Pendiente Desp</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Sucursal: {{$request->sucursal_nombre}}</p>
					<p>Mes Stock: {{$request->mesanno}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
			<thead>
				<tr>
					<th style='text-align:center' class='width10'>Cod</th>
					<th style='text-align:left' class='width90'>Producto</th>
					<th style='text-align:left' class='width90'>Categoria</th>
					<th style='text-align:center' class='width30'>Diam</th>
					<th style='text-align:center' class='width40'>Clase</th>
					<th style='text-align:center' class='width10'>L</th>
					<th style='text-align:center' class='width30'>Peso</th>
					<th style='text-align:center' class='width10'>TU</th>
					<th style='text-align:center' class='width40'>Bodega</th>
					<th style='text-align:center' class='width40'>Picking</th>
					<th style='text-align:center' class='width40'>Stock</th>
					<th style='text-align:center' class='width40'>Pend</th>
					<th style='text-align:center' class='width40'>Dif</th>
					<!--<th style='text-align:center' class='width50'>Kg</th>-->
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php 
					$aux_totalstockkg = 0;
				?>
				@foreach($datas as $data)
					<tr class='btn-accion-tabla tooltipsC'>
						<td style='text-align:center'>{{$data->producto_id}}</td>
						<td>{{$data->producto_nombre}}</td>
						<td>{{$data->categoria_nombre}}</td>
						<td style='text-align:center'>{{$data->diametro}}</td>
						<td style='text-align:center'>{{$data->cla_nombre}}</td>
						<td style='text-align:center'>{{$data->long}}</td>
						<td style='text-align:center'>{{$data->peso}}</td>
						<td style='text-align:center'>{{$data->tipounion}}</td>
						<td style='text-align:center'>{{$data->stockBodProdTerm}}</td>
						<td style='text-align:center'>{{$data->stockPiking}}</td>
						<td style='text-align:center'>{{$data->stock}}</td>
						<td style='text-align:center'>{{$data->cantpend}}</td>
						<td style='text-align:center'>{{$data->difcantpend}}</td>
						<!--<td style='text-align:right'>{{number_format($data->stock * $data->peso, 2, ",", ".")}}</td> -->
					</tr>
					<?php 
						$aux_totalstockkg += $data->stock * $data->peso;
					?>
				@endforeach
			</tbody>
			<!--
			<tfoot id="detalle_totales">
				<tr class="headt">
					<th colspan="13" style='text-align:right'>TOTAL</th>
					<th class="textright">{{number_format($aux_totalstockkg, 2, ",", ".")}}</th>
				</tr>
			</tfoot>
			-->
		</table>
	</div>
</div>
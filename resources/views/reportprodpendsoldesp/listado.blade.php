<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<?php 
	use App\Models\Producto;
	use App\Models\Comuna;
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
					<span class="h3">Producto pendiente  Solicitud Despacho</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Area Producción: {{$nombreAreaproduccion}}</p>
					<p>Vendedor: {{$nomvendedor}} </p>
					<p>Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
			<thead>
				<tr>
					<th>Cod<br>Prod</th>
					<th>Descripción</th>
					<th>Diametro</th>
					<th>Clase</th>
					<th>Largo</th>
					<th>Peso</th>
					<th>TU</th>
					<th style='text-align:right'>Cant<br>Solicit</th>
					<th style='text-align:right'>Kg<br>Solicit</th>
					<th style='text-align:right'>Cant<br>Desp</th>
					<th style='text-align:right'>Kg<br>Desp</th>
					<th style='text-align:right'>Cant<br>Pendiente</th>
					<th style='text-align:right'>Kg<br>Pendientes</th>	
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php
					$aux_totalcanpend = 0;
					$aux_totalkgpend = 0;
				?>
				@foreach($datas as $data)
					<?php
						$aux_cantpend = ($data->cantsoldesp - $data->cantorddesp);
						$aux_kgpend = ($data->kgsoldesp - $data->kgorddesp);
						$aux_totalcanpend += $aux_cantpend;
						$aux_totalkgpend += $aux_kgpend;
					?>
					<tr>
						<td>{{$data->producto_id}}</td>
						<td>{{$data->nombre}}</td>
						<td style='text-align:center'>{{$data->diametro}}</td>
						<td style='text-align:center'>{{$data->cla_nombre}}</td>
						<td style='text-align:center'>{{$data->long}}</td>
						<td style='text-align:center'>{{$data->peso}}</td>
						<td style='text-align:center'>{{$data->tipounion}}</td>
						<td style='text-align:right'>{{number_format($data->cantsoldesp, 0, ",", ".")}}</td>
						<td style='text-align:right'>{{number_format($data->kgsoldesp, 2, ",", ".")}}</td>
						<td style='text-align:right'>{{number_format($data->cantorddesp, 0, ",", ".")}}</td>
						<td style='text-align:right'>{{number_format($data->kgorddesp, 2, ",", ".")}}</td>
						<td style='text-align:right'>{{number_format($aux_cantpend, 0, ",", ".")}}</td>
						<td style='text-align:right'>{{number_format($aux_kgpend, 2, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<th colspan='11' style='text-align:right'>TOTALES</th>
					<th style='text-align:right'>{{number_format($aux_totalcanpend, 0, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format($aux_totalkgpend, 2, ",", ".")}}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

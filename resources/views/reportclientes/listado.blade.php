<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<?php 
	use App\Models\Comuna;
	//dd($datas);
?>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png")}}" style="max-width:1200%;width:auto;height:auto;">
					<p>{{$datosv['empresa'][0]['nombre']}}</p>					
					<p>RUT: {{$datosv['empresa'][0]['rut']}}</p>
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h3">Clientes</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>{{$datosv['bloqueado']}} </p>
					<p>Vendedor: {{$datosv['nomvendedor']}} </p>
					<p>Giro: {{$datosv['nombreGiro']}} </p>
					<p>Fecha creación: {{$datosv['aux_fdesde']}} Hasta: {{$datosv['aux_fhasta']}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
			<thead>
				<tr>
					<th class="textleft">ID</th>
					<th class="textleft">RUT</th>
					<th class="textleft">Razón Social</th>
					<th class="textleft">Dirección</th>
					<th class="textleft">Comuna</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				@foreach($datas as $data)
					<tr class='btn-accion-tabla tooltipsC'>
						<td>{{$data->id}}</td>
						<td>{{$data->rut}}</td>
						<td>{{$data->razonsocial}}</td>
						<td>{{$data->direccion}}</td>
						<td>{{$data->nombrecomuna}}</td>
					</tr>
				@endforeach
			</tbody>				
		</table>
	</div>
</div>

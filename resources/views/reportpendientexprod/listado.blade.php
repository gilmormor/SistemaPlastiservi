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
					<span class="h3">Informe Producto x Nota Venta</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Area Producción: {{$nombreAreaproduccion}}</p>
					<p>Vendedor: {{$nomvendedor}} </p>
					<p>Giro: {{$nombreGiro}} </p>
					<p>Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th>NV</th>
						<th>OC</th>
						<th>Fecha</th>
						<th>Razón Social</th>
						<th class='tooltipsC' title='Código Producto'>CP</th>
						<th>Descripción</th>
						<th>Diametro</th>
						<th>Clase</th>
						<th>Largo</th>
						<th>Peso</th>
						<th>TU</th>
						<th>Cant</th>
						<th style='text-align:right' class='tooltipsC' title='Cantidad Despachada'>Desp</th>
						<th style='text-align:right' class='tooltipsC' title='Cantidad Solicitada'>Solid</th>
						<th style='text-align:right' class='tooltipsC' title='Cantidad Pendiente'>Cant<br>Pend</th>		
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$aux_totalcant = 0;
						$aux_totalcantdesp = 0;
						$aux_totalcantsol = 0;
						$aux_totalcantpend = 0;
					?>
					@foreach($datas as $data)
						<?php
							//SUMA TOTAL DE SOLICITADO
							/*************************/
							$sql = "SELECT cantsoldesp
							FROM vista_sumsoldespdet
							WHERE notaventadetalle_id=$data->id";
							$datasuma = DB::select($sql);
							
							if(empty($datasuma)){
								$sumacantsoldesp= 0;
							}else{
								$sumacantsoldesp= $datasuma[0]->cantsoldesp;
							}
							/*************************/
							//SUMA TOTAL DESPACHADO
							/*************************/
							$sql = "SELECT cantdesp
								FROM vista_sumorddespxnvdetid
								WHERE notaventadetalle_id=$data->id";
							$datasumadesp = DB::select($sql);
							//dd($datasumadesp);
							if(empty($datasumadesp)){
								$sumacantdesp= 0;
							}else{
								$sumacantdesp= $datasumadesp[0]->cantdesp;
							}
							//$aux_totalkg += $data->saldokg; // ($data->totalkilos - $data->kgsoldesp);
							//$aux_totalplata += $data->saldoplata; // ($data->subtotal - $data->subtotalsoldesp);
							$aux_cantsaldo = $data->cant-$sumacantdesp;
						?>
						<tr class='btn-accion-tabla tooltipsC'>
							<td>{{$data->notaventa_id}}</td>
							<td>{{$data->oc_id}}</td>
							<td>{{date('d-m-Y', strtotime($data->fechahora))}}</td>
							<td>{{$data->razonsocial}}</td>
							<td>{{$data->producto_id}}</td>
							<td>{{$data->nombre}}</td>
							<td>{{$data->diametro}}</td>
							<td>{{$data->cla_nombre}}</td>
							<td>{{$data->long}}</td>
							<td>{{$data->peso}}</td>
							<td>{{$data->tipounion}}</td>
							<td style='text-align:right'>{{$data->cant}}</td>
							<td style='text-align:right'>{{$sumacantdesp}}</td>
							<td style='text-align:right'>{{$sumacantsoldesp}}</td>
							<td style='text-align:right'>{{$aux_cantsaldo}}</td>
						</tr>
						<?php
							$aux_totalcant += $data->cant;
							$aux_totalcantdesp += $sumacantdesp;
							$aux_totalcantsol += $sumacantsoldesp;
							$aux_totalcantpend += $aux_cantsaldo;    
						?>
					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr>
						<th colspan='11' style='text-align:left'>TOTALES</th>
						<th style='text-align:right'>{{number_format($aux_totalcant, 0, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($aux_totalcantdesp, 0, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($aux_totalcantsol, 0, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($aux_totalcantpend, 0, ",", ".")}}</th>
					</tr>
				</tfoot>
					
		</table>
	</div>
</div>

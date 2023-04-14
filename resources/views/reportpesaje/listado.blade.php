<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png")}}" style="max-width:1400%;width:auto;height:auto;">
					<p>{{$empresa[0]['nombre']}}</p>
					<p>RUT: {{$empresa[0]['rut']}}</p>
					<p>{{$sucursal->direccion}}</p>
					<p>Teléfono: {{$sucursal->telefono1}}</p>
					<!--<p>Email: {{$sucursal->email}}</p>-->
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round" style="padding-bottom: 3px;">
					<span class="h3">Pesaje</span>
					<p>Fecha emision: {{date("d-m-Y h:i:s A")}}</p>
					<p>Sucursal: {{$sucursal->nombre}}</p>
					<p>Período: {{$request->fechad}} al {{$request->fechah}}</p>
				</div>
			</td>
		</tr>
	</table>
	<div class="round" style="padding-bottom: 0px;">
		<table id="factura_detalle">
			<thead>
				<tr>
					<th width="20px">Cod</th>
					<th width="90px">Producto</th>
					<th width="30px">PNorm</th>
					<th width="30px">Linea</th>
					<th class="textcenter" width="20px">Turn</th>
					<th class="textcenter" width="30px">Carro</th>
					<th class="textcenter" width="20px">Tara</th>
					<th class="textcenter" width="20px">Cant</th>
					<th class="textcenter" width="40px">PesoBal</th>
					<th class="textcenter" width="20px">PesoU</th>
					<th class="textcenter" width="50px">PesoProd</th>
					<th class="textcenter" width="50px">PTNorma</th>
					<th class="textcenter" width="20px">DifKg</th>
					<th class="textcenter" width="20px">Var%</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">
				<?php 
					$total_tara = 0;
					$total_pesobaltotal = 0;
					$total_pesototalprodbal = 0;
					$total_pesototalnorma = 0;
					$total_difkg = 0;
				?>
				@foreach($datas as $pesajedet)
					<?php 
						$total_tara += $pesajedet->tara;
						$total_pesobaltotal += $pesajedet->pesobaltotal;
						$total_pesototalprodbal += $pesajedet->pesototalprodbal;
						$total_pesototalnorma += $pesajedet->pesototalnorma;
						$total_difkg += $pesajedet->difkg;
					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$pesajedet->producto_id}}</td>
						<td class="textleft">{{$pesajedet->producto_nombre}}</td>
						<td class="textcenter">{{number_format($pesajedet->pesounitnom, 3, ",", ".")}}</td>
						<td class="textcenter">{{$pesajedet->areaproduccionsuclinea_nombre}}</td>
						<td class="textcenter">{{$pesajedet->turno_nombre}}</td>
						<td class="textcenter">{{substr($pesajedet->pesajecarro_nombre, 6, 4)}}</td>
						<td class="textcenter">{{number_format($pesajedet->tara, 2, ",", ".")}}</td>
						<td class="textcenter">{{number_format($pesajedet->cant, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->pesobaltotal, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->pesopromunibal, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->pesototalprodbal, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->pesototalnorma, 3, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->difkg, 2, ",", ".")}}</td>
						<td width="60px" class="textright">{{number_format((($pesajedet->difkg / $pesajedet->pesototalnorma) * 100), 6, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<th colspan='5' style='text-align:right'>Total: {{$request->fechah}}</th>
					<th></th>
					<th style='text-align:right'>{{number_format($total_tara, 2, ",", ".")}}</th>
					<th></th>
					<th style='text-align:right'>{{number_format($total_pesobaltotal, 2, ",", ".")}}</th>
					<th></th>
					<th style='text-align:right'>{{number_format($total_pesototalprodbal, 2, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format($total_pesototalnorma, 2, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format($total_difkg, 2, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format((($total_difkg / $total_pesototalnorma) * 100), 6, ",", ".")}}</th>
				</tr>
				<tr>
					<th colspan='5' style='text-align:right'>Total período: {{$request->fechad}} al {{$request->fechah}}</th>
					<th></th>
					<th style='text-align:right'>{{number_format($totalesPeriodo["subtotalTara"], 2, ",", ".")}}</th>
					<th></th>
					<th style='text-align:right'>{{number_format($totalesPeriodo["subtotalPesoBal"], 2, ",", ".")}}</th>
					<th></th>
					<th style='text-align:right'>{{number_format($totalesPeriodo["subtotalPesoTotalProdBal"], 2, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format($totalesPeriodo["subtotalPesoTotalNorma"], 2, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format($totalesPeriodo["subtotalDifKg"], 2, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format((($totalesPeriodo["subtotalDifKg"] / $totalesPeriodo["subtotalPesoTotalNorma"]) * 100), 6, ",", ".")}}</th>
				</tr>
			</tfoot>

		</table>
	</div>
</div>

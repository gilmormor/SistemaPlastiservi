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
					<span class="h3">Pesaje Grupo</span>
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
					<th width="90px">Producto</th>
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
						$total_tara += $pesajedet->sumtara;
						$total_pesobaltotal += $pesajedet->sumpesobaltotal;
						$total_pesototalprodbal += $pesajedet->pesototalprodbal;
						$total_pesototalnorma += $pesajedet->pesototalnorma;
						$total_difkg += $pesajedet->difkg;
					?>
					<tr class="headt" style="height:150%;">
						<td class="textleft">{{$pesajedet->categoriaprodgrupo_nombre}}</td>
						<td class="textright">{{number_format($pesajedet->sumtara, 2, ",", ".")}}</td>
						<td class="textcenter">{{number_format($pesajedet->cant, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->sumpesobaltotal, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->pesopromunibal, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->pesototalprodbal, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->pesototalnorma, 3, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->difkg, 2, ",", ".")}}</td>
						<td width="70px" class="textcenter">{{number_format((($pesajedet->difkg / $pesajedet->pesototalnorma) * 100), 6, ",", ".")}}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot id="detalle_totales">
				<tr>
					<th style='text-align:right'>Total</th>
					<th style='text-align:right'>{{number_format($total_tara, 2, ",", ".")}}</th>
					<th></th>
					<th style='text-align:right'>{{number_format($total_pesobaltotal, 2, ",", ".")}}</th>
					<th></th>
					<th style='text-align:right'>{{number_format($total_pesototalprodbal, 2, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format($total_pesototalnorma, 2, ",", ".")}}</th>
					<th style='text-align:right'>{{number_format($total_difkg, 2, ",", ".")}}</th>
					<th style='text-align:center'>{{number_format((($total_difkg / $total_pesototalnorma) * 100), 6, ",", ".")}}</th>
				</tr>
			</tfoot>

		</table>
	</div>
</div>

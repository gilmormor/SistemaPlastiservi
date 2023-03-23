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
					<p>{{$datas->sucursal->direccion}}</p>
					<p>Teléfono: {{$datas->sucursal->telefono1}}</p>
					<!--<p>Email: {{$datas->sucursal->email}}</p>-->
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round" style="padding-bottom: 3px;">
					<span class="h3">Pesaje</span>
					<p>Nro: <strong> {{ str_pad($datas->id, 10, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha: {{date('d-m-Y', strtotime($datas->fechahora))}}</p>
					<p>Estado: 
						@switch($datas->staaprob)
							@case(0)
								Sin aprobar
								@break
							@case(1)
								Enviado para aprobacion
								@break
							@case(2)
								Aprobado
								@break
							@case(3)
								Rechazado
								@break
							@default
						@endswitch
					</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h3">Datos</span>
					<table class="datos_cliente">
						<!--<tr class="headt">-->
						<tr class="headt">
							<td style="width:10%">Descripción: </td><td style="width:100%">{{$datas->desc}}</td>
						</tr>
						<tr class="headt">
							<td style="width:10%">Mes-Año: </td>
							@if (empty($datas->annomes))
								<td style="width:50%">Mes-Año sin aprobar</td>
							@else
								<td style="width:50%">{{substr($datas->annomes,4,2) . "-" . substr($datas->annomes,0,4)}}</td>
							@endif
						</tr>
						<tr class="headt">
							<td style="width:10%">Módulo: </td><td style="width:50%">{{$datas->invmovmodulo->nombre}}</td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>
	<div class="round" style="padding-bottom: 0px;">
		<table id="factura_detalle">
			<thead>
				<tr>
					<th width="20px">Cod</th>
					<th width="90px">Nombre Producto</th>
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
					$total_cant = 0;
					$total_pesobaltotal = 0;
					$total_pesobalprodtotal = 0;
					$total_pesobalprodunit = 0;
					$total_PesoTotNorma = 0;
					$total_DiferenciaKg = 0;
					$total_DiferenciaPorc = 0;
				?>
				@foreach($datas->pesajedets as $pesajedet)
					<?php 
						$aux_producto_nombre = $pesajedet->invbodegaproducto->producto->nombre . " D:" . $pesajedet->invbodegaproducto->producto->diametro . " C:" . $pesajedet->invbodegaproducto->producto->claseprod->cla_nombre . " L:" . $pesajedet->invbodegaproducto->producto->long . " TU:" . $pesajedet->invbodegaproducto->producto->tipounion;
						$total_tara += $pesajedet->tara;
						$total_cant += $pesajedet->cant;
						$total_pesobaltotal += $pesajedet->pesobaltotal;
						$pesobalprodunit = round(($pesajedet->pesobaltotal - $pesajedet->tara) / $pesajedet->cant,2);
						$total_pesobalprodunit += $pesobalprodunit;
						$pesobalprodtotal = ($pesajedet->pesobaltotal - $pesajedet->tara);
						$total_pesobalprodtotal += $pesobalprodtotal;
						$PesoTotNorma = round($pesajedet->cant * $pesajedet->peso,2);
						$total_PesoTotNorma += $PesoTotNorma;
						$DiferenciaKg = round($pesobalprodtotal - $PesoTotNorma,2);
						$total_DiferenciaKg += $DiferenciaKg;
						$DiferenciaPorc = round(($DiferenciaKg / $PesoTotNorma) * 100,2);
						$total_DiferenciaPorc += $DiferenciaPorc;

					?>
					<tr class="headt" style="height:150%;">
						<td class="textcenter">{{$pesajedet->invbodegaproducto->producto_id}}</td>
						<td class="textleft">{{$aux_producto_nombre}}</td>
						<td class="textcenter">{{number_format($pesajedet->peso, 2, ",", ".")}}</td>
						<td class="textcenter">{{$pesajedet->areaproduccionsuclinea->nombre}}</td>
						<td class="textcenter">{{$pesajedet->turno->nombre}}</td>
						<td class="textcenter">{{$pesajedet->pesajecarro->nombre}}</td>
						<td class="textcenter">{{number_format($pesajedet->tara, 0, ",", ".")}}</td>
						<td class="textcenter">{{number_format($pesajedet->cant, 0, ",", ".")}}</td>
						<td class="textright">{{number_format($pesajedet->pesobaltotal, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($pesobalprodunit, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($pesobalprodtotal, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($PesoTotNorma, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($DiferenciaKg, 2, ",", ".")}}</td>
						<td class="textright">{{number_format($DiferenciaPorc, 2, ",", ".")}}</td>
					</tr>
				@endforeach
				<tr>
					<td colspan="6" class="textright"><span><strong>TOTAL</strong></span></td>
					<td class="textcenter"><span><strong>{{number_format($total_tara, 0, ",", ".")}}</strong></span></td>
					<td class="textcenter"><span><strong></strong></span></td>
					<td class="textright"><span><strong>{{number_format($total_pesobaltotal, 2, ",", ".")}}</strong></span></td>
					<td class="textright"><span><strong></strong></span></td>
					<td class="textright"><span><strong>{{number_format($total_pesobalprodtotal, 2, ",", ".")}}</strong></span></td>
					<td class="textright"><span><strong>{{number_format($total_PesoTotNorma, 2, ",", ".")}}</strong></span></td>
					<td class="textright"><span><strong>{{number_format($total_DiferenciaKg, 2, ",", ".")}}</strong></span></td>
					<td class="textright"><span><strong>{{number_format(($total_DiferenciaKg / $total_PesoTotNorma) * 100, 2, ",", ".")}}</strong></span></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<!--<img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png")}}" style="max-width:1200%;width:auto;height:auto;">-->
					<p><H1>{{$empresa[0]['nombre']}}</H1></p>					
					<p>RUT: {{$empresa[0]['rut']}}</p>
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h3">Informe Materias Primas Precio X Kilo</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Periodo: {{$aux_fdesde}} al {{$aux_fhasta}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th>Dia</th>
						<th>Docum</th>
						<th>Razón Social</th>
						<th>Producto</th>
						<th>Medidas</th>
						<th>Materia<br>Prima</th>
						<th>Unid</th>
						<th style='text-align:right'>Valor<br>Neto</th>
						<th style='text-align:right'>Kilos</th>
						<th style='text-align:right'>Conver<br>Kilos</th>
						<th style='text-align:right'>Difer<br>Kilos</th>
						<th style='text-align:right'>Precio<br>Kilo</th>
						<th style='text-align:right'>Precio<br>Costo</th>
						<th style='text-align:right'>Difer<br>Precio</th>
						<th style='text-align:right'>Difer<br>Val</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$i = 0;
						$aux_totalsubtotal = 0;
						$aux_totalkilos = 0;
						$aux_totaldiferenciakilos = 0;
						$aux_totalvalorcosto = 0;
						$aux_totaldiferenciaval = 0;
					?>
					@foreach($datas as $data)
						<?php
							$i++;
							$aux_totalsubtotal += $data->subtotal;
							$aux_totalkilos += $data->kilos;
							$aux_totaldiferenciakilos += $data->diferenciakilos;
							$aux_totalvalorcosto += $data->valorcosto;
						?>

						<tr id='fila{{$i}}' name='fila{{$i}}'>
							<td>{{date('d', strtotime($data->fechadocumento))}}</td>
							<td>{{$data->numerodocumento}}</td>
							<td>{{$data->razonsocial}}</td>
							<td>{{$data->descripcion}}</td>
							<td>{{$data->medidas}}</td>
							<td>{{$data->matprimdesc}}</td>
							<td style='text-align:right'>{{number_format($data->unidades, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->subtotal, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->kilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->conversionkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->diferenciakilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->precioxkilo, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->valorcosto, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->diferenciaprecio, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->diferenciaval, 0, ",", ".")}}</td>
						</tr>

					@endforeach
				</tbody>
				<?php
					$aux_promprecioxkilo = round($aux_totalsubtotal / $aux_totalkilos,2);
					$aux_promvalorcosto = round($aux_totalvalorcosto / $i ,2);
					$aux_diferenciaprecio = $aux_promprecioxkilo - $aux_promvalorcosto;
					$aux_totaldiferenciaval = $aux_totalkilos * $aux_diferenciaprecio;
				?>
				<tfoot id="detalle_totales">
					<tr class="headt">
						<b>
							<th colspan='7' style='text-align:right'>TOTALES</th>
							<th style='text-align:right'>{{number_format($aux_totalsubtotal, 0, ",", ".")}}</th>
							<th style='text-align:right'>{{number_format($aux_totalkilos, 2, ",", ".")}}</th>
							<th></th>
							<th style='text-align:right'>{{number_format($aux_totaldiferenciakilos, 2, ",", ".")}}</th>
							<th style='text-align:right'>{{number_format($aux_promprecioxkilo, 2, ",", ".")}}</th>
							<th style='text-align:right'>{{number_format($aux_promvalorcosto, 2, ",", ".")}}</th>
							<th style='text-align:right'>{{number_format($aux_diferenciaprecio, 2, ",", ".")}}</th>
							<th style='text-align:right'>{{number_format($aux_totaldiferenciaval, 0, ",", ".")}}</th>
						</b>
				</tfoot>
		</table>
	</div>
</div>

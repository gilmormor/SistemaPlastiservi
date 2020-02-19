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
					<span class="h3">DESPACHO PVC</span>
					<p>Fecha: {{date("d-m-Y")}}</p>
					<p>Hora: {{date("h:i:s A")}}</p>
					<p>Vendedor: {{$usuario->persona ? $usuario->persona->nombre . " " . $usuario->persona->apellido : ""}} </p>
					<p>Teléfono: {{$usuario->persona ? $usuario->persona->telefono : ""}} </p>
					<p>email: {{$usuario->persona ? $usuario->persona->email : ""}} </p>
					<p>Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th style='text-align:left'>Descripción</th>
						<th style='text-align:left'>Diametro</th>
						<th style='text-align:left'>Long</th>
						<th style='text-align:left'>Clase</th>
						<th style='text-align:right'>Peso x Unidad</th>
						<th style='text-align:center'>TU</th>
						<th style='text-align:right'>Unid</th>
						<th style='text-align:right'>KG</th>
						<th style='text-align:right'>Precio Prom Unit</th>
						<th style='text-align:right'>Precio Kilo</th>	
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$i=0;
						$aux_totalkilos = 0;
					?>
					@foreach($notaventas as $notaventa)
						<?php
							$i++;
							$aux_totalkilos = $aux_totalkilos + $notaventa->sumtotalkilos;
						?>
						<tr class='btn-accion-tabla tooltipsC'>
							<td>{{$notaventa->nombre}}</td>
							<td>{{$notaventa->diamextmm}}</td>
							<td>{{$notaventa->long}}</td>
							<td>{{$notaventa->cla_nombre}}</td>
							<td style='text-align:right'>{{number_format($notaventa->peso, 2, ",", ".")}}</td>
							<td style='text-align:center'>{{$notaventa->tipounion}}</td>
							<td style='text-align:right'>{{number_format($notaventa->sumcant, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->sumtotalkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->prompreciounit, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($notaventa->promprecioxkilo, 2, ",", ".")}}</td>
						</tr>

					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr class="headt">
						<th colspan="7" style='text-align:left'>TOTAL</th>
						<th class="textright">{{number_format($aux_totalkilos, 2, ",", ".")}}</th>
					</tr>
				</tfoot>

		</table>
	</div>
</div>

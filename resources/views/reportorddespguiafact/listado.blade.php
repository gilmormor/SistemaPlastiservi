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
					<span class="h3">Consultar Orden Despacho, Guia, Factura, cerrada</span>
					<p>Fecha: {{date("d-m-Y h:i:s A")}}</p>
					<p>Sucursal: {{$request->sucursal_nombre}}</p>
					<p>Desde: {{$request->fechad}} Hasta: {{$request->fechah}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th width="20px" style='text-align:center'>OD</th>
						<th width="50px" style='text-align:center'>Fecha</th>
						<th width="100px" style='text-align:center'>Razon Social</th>
						<th width="30px" style='text-align:center'>SD</th>
						<th width="40px" style='text-align:center'>OC</th>
						<th width="30px" style='text-align:center'>NV</th>
						<th width="60px" style='text-align:left'>Comuna</th>	
						<th width="40px" style='text-align:right'>Kg</th>
						<th width="40px" style='text-align:right'>Monto<br>Documento</th>
						<th width="40px" style='text-align:center'>NumGuia</th>
						<th width="50px" style='text-align:center'>F Guia</th>
						<th width="40px" style='text-align:center'>NumFact</th>
						<th width="50px" style='text-align:left'>F Fact</th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$i=0;
						$aux_totalkilos = 0;
						$totalsumsubtotal = 0;
            			$totalsumcant = 0;
					?>
					@foreach($datas as $data)
						<?php
							$i++;
							$aux_totalkilos += $data->totalkilos;
							$totalsumsubtotal += $data->subtotal;
						?>
						<tr class='btn-accion-tabla tooltipsC'>
							<td style='text-align:center'>
								{{$data->id}}
								@if ($data->despachoordanul_id != null)
									A
								@endif
							</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($data->fechahora))}}</td>
							<td>{{$data->razonsocial}}</td>
							<td style='text-align:center'>{{$data->despachosol_id}}</td>
							<td style='text-align:center'>{{$data->oc_id}}</td>
							<td style='text-align:center'>{{$data->notaventa_id}}</td>
							<td>{{$data->comunanombre}}</td>
							<td style='text-align:right'>{{number_format($data->totalkilos, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->subtotal, 0, ",", ".")}}</td>
							<td style='text-align:center'>{{$data->guiadespacho}}</td>
							<td style='text-align:center'>{{date('d-m-Y', strtotime($data->guiadespachofec))}}</td>
							<td style='text-align:center'>{{$data->numfactura}}</td>
							<td style='text-align:left'>{{date('d-m-Y', strtotime($data->fechafactura))}}</td>
						</tr>
					@endforeach
				</tbody>
				<tfoot id="detalle_totales">
					<tr class="headt">
						<th colspan="7" style='text-align:left'>TOTAL</th>
						<th style="text-align:right">{{number_format($aux_totalkilos, 2, ",", ".")}}</th>
						<th style="text-align:right">{{number_format($totalsumsubtotal, 0, ",", ".")}}</th>
					</tr>
				</tfoot>
		</table>
	</div>
	<br>
	<a>
		<i class='fa fa-fw text-danger'>
			<p><small>
				A= Anulada
			</small></p>
		</i>                                    
	</a>

</div>

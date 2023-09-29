<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<?php 
	use App\Models\Producto;
	use App\Models\Vendedor;
?>
<div id="page_pdf">
	<?php
		$aux_vendedor_id = "";
		$count = 0;
	?>

	@foreach($datas as $data)
		@if ($data->vendedor_id != $aux_vendedor_id)
			@if ($count > 0)
						<tfoot id="detalle_totales">
							<tr class="headt">
								<th colspan="6" style='text-align:right'>TOTAL</th>
								<th class="textright">{{number_format($aux_totalmontoitem, 0, ",", ".")}}</th>
								<th class="textright"></th>
								<th class="textright">{{number_format($aux_totalcomision, 0, ",", ".")}}&nbsp;&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<br>
			@endif
			<?php
				$aux_totalmontoitem = 0;
				$aux_totalcomision = 0;
				$vendedor = Vendedor::findOrFail($data->vendedor_id);
			?>
			@if ($count > 0)
				<div style="page-break-before: always;">
				</div>
			@endif
		
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
							<span class="h3">Comisión Ventas</span>
							<p>Fecha: {{date("d/m/Y h:i:s A")}}</p>
							<p>Sucursal: {{$request->sucursal_nombre}}</p>
							<p>Desde: {{$request->fechad}} Hasta: {{$request->fechah}}</p>
						</div>
					</td>
				</tr>
			</table>
		
			<div class="round">
				<table id="reporte_detalle">
				<thead>
					<tr>
						<!--<th colspan="2" style='text-align:left'>Rut: {{number_format( substr ( $data->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->rut, strlen($data->rut) -1 , 1 )}}</th> -->
						<th colspan="1" style='text-align:left'>ID: {{$data->vendedor_id}}</th>
						<th colspan="7" style='text-align:left'>Nombre: {{$vendedor->persona->nombre . " " . $vendedor->persona->apellido}}</th>
					</tr>
					<tr>
						<th style='text-align:center'>Doc</th>
						<th style='text-align:center'>Tipo</th>
						<th>Fecha</th>
						<th>RUT</th>
						<th style='text-align:left'>Razon Social</th>
						<th style='text-align:left'>Producto</th>
						<th style='text-align:right'>Monto</th>
						<th style='text-align:right'>%</th>
						<th style='text-align:right'>Comisión</th>
					</tr>
				</thead>
		@endif
				<tbody id="detalle_productos">
					<tr class='btn-accion-tabla tooltipsC'>
						<td style='text-align:center'>{{$data->nrodocto}}</td>
						<td style='text-align:center'>{{$data->foliocontrol_doc}}</td>
						<td style='text-align:center'>{{date('d/m/Y', strtotime($data->fechahora))}}</td>
						<td style='text-align:center'>{{$data->rut}}</td>
						<td style='text-align:left;font-size: 8px;'>{{$data->razonsocial}}</td>
						<td style='text-align:left;font-size: 8px;'>{{$data->nmbitem}}</td>
						<td style='text-align:right'>{{number_format($data->montoitem, 0, ",", ".")}}</td>
						<td style='text-align:right'>{{number_format($data->porc_comision, 2, ",", ".")}}</td>
						<td style='text-align:right'>{{number_format(round($data->comision, 0), 0, ",", ".")}}&nbsp;&nbsp;</td>
					</tr>
					<?php 
						$aux_vendedor_id = $datas[0]->vendedor_id;
					?>
			
				</tbody>
				<?php
					$aux_totalmontoitem += round($data->montoitem, 0);
					$aux_totalcomision += $data->comision;
					$aux_vendedor_id = $data->vendedor_id;
					$count++;
				?>
	@endforeach
			<tfoot id="detalle_totales">
				<tr class="headt">
					<th colspan="6" style='text-align:right'>TOTAL</th>
					<th class="textright">{{number_format($aux_totalmontoitem, 0, ",", ".")}}</th>
					<th class="textright"></th>
					<th class="textright">{{number_format($aux_totalcomision, 0, ",", ".")}}&nbsp;&nbsp;</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

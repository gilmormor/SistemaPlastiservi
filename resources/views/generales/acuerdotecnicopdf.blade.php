<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<script src="{{asset("assets/$theme/bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<?php 
	$at = $producto->acuerdotecnico;
?>
<br>
<br>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png")}}" style="max-width:1400%;width:auto;height:auto;">
					<p>RUT: {{$empresa[0]['rut']}}</p>
				</div>
			</td>
			<td class="info_empresa">
			</td>
			<td class="info_factura">
				<div>
					<span class="h3">Acuerdo Tecnico</span>
					<p>Nro: <strong> {{ str_pad($at->id, 5, "0", STR_PAD_LEFT) }}</strong></p>
					<p>Fecha Act: {{date('d-m-Y h:i:s A')}}</p>
					<p>Fecha: {{date('d-m-Y h:i:s A', strtotime($at->created_at))}}</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3">Cliente</span>
					<table class="datos_cliente">
						<tr class="headt">
							<td>Descripción:</td>
							<td><p>{{$at->at_desc}}</p></td>
							<td>Entrega Muestra:</td>
							<td><p>{{$at->at_entmuestra == '1' ? 'Si' : 'No' }}</p></td>
						</tr>
						<tr class="headt">
							<td>Color:</td>
							<td><p>{{$at->color->nombre}}</p></td>
							<td>Descrip Color:</td>
							<td><p>{{$at->at_colordesc}}</p></td>
						</tr>
						<tr class="headt">
							<td>Numero Pantone:</td>
							<td><p>{{$at->at_npantone}}</p></td>
							<td>Translucidez:</td>
							<td><p>{{$at->at_translucidez == '1' ? 'No translucido' : $at->at_translucidez == '2' ? 'Opaco semi translucido' : $at->at_translucidez == '3' ? 'Alta Transparencia' :''}}</p></td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>
</div>

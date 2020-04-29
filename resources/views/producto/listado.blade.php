<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<div id="page_pdf">
			<td class="info_cliente">
				<div class="round">
					<span class="h3">Listado de Productos</span>
					<table id="factura_detalle">
						<thead>
							<tr>
								<th width="50px">id</th>
								<th class="textcenter">Nombre</th>
								<th class="textleft">Cod.</th>
								<th class="textleft">Clase</th>
								<th class="textleft">Diametro</th>
								<th class="textright">Largo</th>
								<th class="textright">Espesor</th>
								<th class="textright">Peso</th>
								<th class="textright">Tipo Union</th>
								<th class="textright" width="150px">Precio Neto</th>
							</tr>
						</thead>
						<tbody id="detalle_productos">
							<?php $i=0; ?>
							@foreach($productos as $producto)
								<?php $i++; ?>
								<tr class="headt" style="height:150%;">
									<td class="textcenter">{{number_format($producto->id,0)}}</td>
									<td class="textcenter">{{$producto->categoriaprod->nombre}}</td>
									<td class="textleft">{{$producto->codintprod}}</td>
									<td class="textleft">{{$producto->claseprod->cla_nombre}}</td>
									<td class="textleft">{{$producto->diamextmm}}</td>
									<td class="textright">{{$producto->long}}</td>
									<td class="textright">{{$producto->espesor}}</td>
									<td class="textright">{{$producto->peso}}</td>
									<td class="textright">{{$producto->tipounion}}</td>
									<td class="textright">{{number_format($producto->precioneto, 2, ",", ".")}}</td>
								</tr>
<!--								
								@if ($i==30)
									<?php $i=0; ?>
									<br>
										<tr style="page-break-after:always;"></tr>
									<br>
								@endif
-->								
							@endforeach
						</tbody>
					</table>
				</div>
				


			</td>
</div>

<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<script src="{{asset("assets/$theme/bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<?php 
	//$at = $producto->acuerdotecnico;
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
					<span class="h3">Acuerdo Tecnico {{$aux_tituloreporte}}</span>
					<p><strong>Categoria: </strong>{{$categoria_nombre}}</p>
					<p><strong>Cod AT: </strong>{{ str_pad($acuerdotecnico->id, 5, "0", STR_PAD_LEFT) }}</p>
					<p><strong>Fecha: </strong>{{date('d/m/Y h:i:s A')}}</p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3" style="margin-bottom: 0px;">Descripción</span>
					<table class="datos_cliente" style="padding-top: 0px;">
						<tr class="headtarial">
							<td style="width: 16% !important;"><strong>Fecha Creacion:</strong></td>
							<td style="width: 25% !important;"><p>{{date('d/m/Y h:i:s A', strtotime($acuerdotecnico->created_at))}}</td>
							<td style="width: 10% !important;"></td>
							<td style="width: 15% !important;"><p></p></td>
							<td style="width: 6% !important;"></td>
							<td style="width: 10% !important;"><p></p></td>
						</tr>
						<tr class="headtarial">
							<td style="width: 16% !important;"><strong>Descripción:</strong></td>
							<td style="width: 25% !important;"><p>{{$acuerdotecnico->at_desc}}</p></td>
							<td style="width: 10% !important;"><strong>Ent. Muestra:</strong></td>
							<td style="width: 15% !important;"><p>{{$acuerdotecnico->at_entmuestra == '1' ? 'Si' : 'No' }}</p></td>
							<td style="width: 6% !important;"><strong>Tipo:</strong></td>
							<td style="width: 10% !important;"><p>{{$acuerdotecnico->claseprod->cla_nombre}}</p></td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3" style="margin-bottom: 0px;">Caracteristicas Extrusión</span>
					<table class="datos_cliente" style="padding-top: 0px;">
						<tr class="headtarial">
							<td style="width: 16% !important;"><strong>Materia Prima:</strong></td>
							<td style="width: 20% !important;"><p>{{$acuerdotecnico->materiaprima->nombre}}</td>
							<td style="width: 6% !important;"><strong>Color:</strong></td>
							<td style="width: 5% !important;">{{$acuerdotecnico->color->nombre}}</td>
							<td style="width: 6% !important;"><strong>Pigmento:</strong></td>
							<td style="width: 4% !important;">{{$acuerdotecnico->at_pigmentacion}}</td>
						</tr>
						<tr class="headtarial">
							<td style="width: 5% !important;"><strong>Nro Pantone:</strong></td>
							<td style="width: 5% !important;"><p>{{$acuerdotecnico->at_npantone}}</p></td>
						</tr>
						<tr class="headtarial">
							<td><strong>Translucidez:</strong></td>
							<td><p>{{$acuerdotecnico->at_translucidez == '1' ? 'No translucido' : $acuerdotecnico->at_translucidez == '2' ? 'Opaco semi translucido' : $acuerdotecnico->at_translucidez == '3' ? 'Alta Transparencia' :''}}</p></td>
							<td><strong>Observación:</strong></td>
							<td><p>{{$acuerdotecnico->at_materiaprimaobs}}</p></td>
							<td><strong>Uso Previsto:</strong></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3" style="margin-bottom: 0px;">Aditivos</span>
					<table class="datos_cliente" style="padding-top: 0px;">
						<tr class="headtarial">
							<td style="width: 15% !important;"><strong>UV: </strong> {{$acuerdotecnico->at_uv == '1' ? 'Si' : 'No'}}</td>
							<td style="width: 35% !important;"><strong>Obs: </strong>{{$acuerdotecnico->at_uvobs}}</td>

							<td style="width: 15% !important;"><strong>Antiblock: </strong>{{$acuerdotecnico->at_antiblock == '1' ? 'Si' : 'No'}}</td>
							<td style="width: 35% !important;"><strong>Obs:</strong>{{$acuerdotecnico->at_antiblockobs}}</td>
						</tr>
						<tr class="headtarial">
							<td style="width: 15% !important;"><strong>Antideslizante: </strong>{{$acuerdotecnico->at_antideslizante == '1' ? 'Si' : 'No'}}</td>
							<td style="width: 35% !important;"><strong>Obs: </strong>{{$acuerdotecnico->at_antideslizanteobs}}</td>

							<td style="width: 15% !important;"><strong>Aditivo Otro:</strong> {{$acuerdotecnico->at_aditivootro == '1' ? 'Si' : 'No'}}</td>
							<td style="width: 35% !important;"><strong>Obs: </strong>{{$acuerdotecnico->at_aditivootroobs}}</td>
						</tr>
						<tr class="headtarial">
							<td style="width: 15% !important;"><strong>Antiestatico: </strong>{{$acuerdotecnico->at_antiestatico == '1' ? 'Si' : 'No'}}</td>
							<td style="width: 35% !important;"><strong>Obs: </strong>{{$acuerdotecnico->at_antiestaticoobs}}</td>

							<td style="width: 15% !important;"></td>
							<td style="width: 35% !important;"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3" style="margin-bottom: 0px;">Dimensiones</span>
					<table class="datos_cliente" style="padding-top: 0px;">
						<tr class="headtarial">
							<td style="width: 5% !important;"><strong>Dimensiones</strong></td>
							<td style="width: 5% !important;text-align:center;"><strong>Medida</strong></td>
							<td style="width: 5% !important;"><strong>Desv</strong></td>
							<td style="width: 5% !important;"><strong>Dimensiones</strong></td>
							<td style="width: 5% !important;text-align:center;"><strong>Medida</strong></td>
							<td style="width: 5% !important;"><strong>Desv</strong></td>
						</tr>
						<tr class="headtarial">
							<td style="width: 5% !important;"><strong>Ancho</strong></td>
							<td style="width: 5% !important;text-align:center;">{{$acuerdotecnico->at_ancho}} {{$acuerdotecnico->at_ancho ? $acuerdotecnico->anchounidadmedida->nombre : ""}}</td>
							<td style="width: 5% !important;"><p>{{$acuerdotecnico->at_anchodesv}}</p></td>
							<td style="width: 5% !important;"><strong>Fuelle</strong></td>
							<td style="width: 5% !important;text-align:center;">{{$acuerdotecnico->at_fuelle}}  {{$acuerdotecnico->at_fuelle ? $acuerdotecnico->fuelleunidadmedida->nombre : ""}}</td>
							<td style="width: 5% !important;"><p>{{$acuerdotecnico->at_fuelledesv}}</p></td>

						</tr>
						<tr class="headtarial">
							<td style="width: 5% !important;"><strong>Largo</strong></td>
							<td style="width: 5% !important;text-align:center;">{{$acuerdotecnico->at_largo}} {{$acuerdotecnico->at_largo ? $acuerdotecnico->largounidadmedida->nombre : ""}}</td>
							<td style="width: 5% !important;"><p>{{$acuerdotecnico->at_largodesv}}</p></td>
							<td style="width: 5% !important;"><strong>Espesor</strong></td>
							<td style="width: 5% !important;text-align:center;">{{$acuerdotecnico->at_espesor}} {{$acuerdotecnico->at_espesor ? $acuerdotecnico->espesorunidadmedida->nombre : ""}}</td>
							<td style="width: 5% !important;"><p>{{$acuerdotecnico->at_espesordesv}}</p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3" style="margin-bottom: 0px;">Impresión</span>
					<table class="datos_cliente" style="padding-top: 0px;">
						<tr class="headtarial">
							<td style="width: 15% !important;"><strong>Impreso:</strong> {{$acuerdotecnico->at_impreso == '1' ? 'Si' : 'No'}}</td>
							<td style="width: 50% !important;"><strong>Observación:</strong> {{$acuerdotecnico->at_impresoobs}}</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3" style="margin-bottom: 0px;">Forma de Embalaje</span>
					<table class="datos_cliente" style="padding-top: 0px;">
						<tr class="headtarial">
							<td style="width: 50% !important;"><strong>Unid x empaque:</strong> {{$acuerdotecnico->at_feunidxpaq}}</td>
							<td style="width: 50% !important;"><strong>Obs:</strong> {{$acuerdotecnico->at_feunidxpaqobs}}</td>
						</tr>
						<tr class="headtarial">
							<td style="width: 50% !important;"><strong>Unid x contenedor:</strong> {{$acuerdotecnico->at_feunidxcont}}</td>
							<td style="width: 50% !important;"><strong>Obs:</strong> {{$acuerdotecnico->at_feunidxcontobs}}</td>
						</tr>
						<tr class="headtarial">
							<td style="width: 50% !important;"><strong>Color contenedor:</strong> {{$acuerdotecnico->at_fecolorcont}}</td>
							<td style="width: 50% !important;"><strong>Obs:</strong> {{$acuerdotecnico->at_fecolorcontobs}}</td>
						</tr>
						<tr class="headtarial">
							<td style="width: 50% !important;"><strong>Unid x palet:</strong> {{$acuerdotecnico->at_feunitxpalet}}</td>
							<td style="width: 50% !important;"><strong>Obs:</strong> {{$acuerdotecnico->at_feunitxpaletobs}}</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="info_cliente">
				<div>
					<span class="h3" style="margin-bottom: 0px;">Etiquetado</span>
					<table class="datos_cliente" style="padding-top: 0px;">
						<tr class="headtarial">
							<td style="width: 30% !important;"><strong>Etiqueta Plastiservi:</strong> {{$acuerdotecnico->at_etiqplastiservi == '1' ? 'Si' : 'No'}}</td>
							<td style="width: 45% !important;"><strong>Obs:</strong> {{$acuerdotecnico->at_etiqplastiserviobs}}</td>
							<td style="width: 30% !important;"><strong>Etiqueta otro:</strong> {{$acuerdotecnico->at_etiqotro}}</td>
							<td style="width: 45% !important;"><strong>Obs:</strong> {{$acuerdotecnico->at_etiqotroobs}}</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>

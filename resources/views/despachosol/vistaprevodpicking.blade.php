<!--<link rel="stylesheet" href="{{asset("assets/$theme/bower_components/bootstrap/dist/css/bootstrap.min.css")}}">-->
<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<script src="{{asset("assets/$theme/bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
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
					<span class="h3">VistaPrevia OrdDesp Picking/ {{$despachosol->notaventa->sucursal->nombre}}</span>
					<p>Nro SolDesp: <strong> {{ str_pad($despachosol->id, 10, "0", STR_PAD_LEFT) }}</strong>
						@if ($despachosol->despachosolanul)
							<small class="btn btn-danger btn-xs">Anulado</small>
						@endif
					</p>
					<p>Fecha Act: {{date('d-m-Y h:i:s A')}}</p>
					<p>Vendedor: {{$despachosol->notaventa->vendedor->persona->nombre . " " . $despachosol->notaventa->vendedor->persona->apellido}} </p>
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
							<td style="width:10%"><label>Rut:</label></td><td style="width:50%"><p id="rutform" name="rutform">{{number_format( substr ( $despachosol->notaventa->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $despachosol->notaventa->cliente->rut, strlen($despachosol->notaventa->cliente->rut) -1 , 1 )}}</p></td>
							<td style="width:10%"><label>Teléfono:</label></td><td style="width:30%"><p>{{$despachosol->notaventa->cliente->telefono}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Nombre:</label></td><td style="width:50%"><p>{{$despachosol->notaventa->cliente->razonsocial}}</p></td>
							<td style="width:10%"><label>Dirección:</label></td><td style="width:30%"><p>{{$despachosol->notaventa->cliente->direccion}}</p></td>
						</tr>
						<tr class="headt">
							<td style="width:10%"><label>Contacto:</label></td><td style="width:50%"><p>{{$despachosol->notaventa->cliente->contactonombre}}</p></td>
							<td style="width:10%"><label>Comuna:</label></td><td style="width:30%"><p>{{$despachosol->notaventa->cliente->comuna->nombre}}</p></td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>
<?php
    use Illuminate\Http\Request;
    use App\Models\DespachoSolDet_InvBodegaProducto;
    use App\Models\DespachoOrd;
    use App\Models\InvBodegaProducto;
	use App\Models\Producto;
?>


	<table id="factura_detalle">
		<thead>
			<tr>
				<th>CodProd</th>
				<th class="width250">Nombre</th>
				<th>Cant</th>
				<th>OrdDesp</th>
				<th>Saldo</th>
				<th class="width130">Bodegas</th>
				<th>Esp</th>
				<th>Peso</th>
			</tr>
		</thead>
		<tbody id="detalle_productos">
			@if ($aux_sta==2 or $aux_sta==3)
				<?php $aux_nfila = 0; $i = 0;
				?>
				@foreach($detalles as $detalle)
					<?php 
						$sql = "SELECT cantdesp
								FROM vista_sumorddespdet
								WHERE despachosoldet_id=$detalle->id";
						$datasuma = DB::select($sql);
						$peso = $detalle->notaventadetalle->totalkilos/$detalle->notaventadetalle->cant;
						if(empty($datasuma)){
							$sumacantorddesp= 0;
						}else{
							$sumacantorddesp= $datasuma[0]->cantdesp;
						}

						$aux_espesor = $detalle->notaventadetalle->producto->espesor;
						$aux_espesornum = $detalle->notaventadetalle->producto->espesor;
						$aux_staAT = false;
						if ($detalle->notaventadetalle->producto->acuerdotecnico != null){
							$AcuTec = $detalle->notaventadetalle->producto->acuerdotecnico;
							$aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
							$aux_espesornum = $AcuTec->at_espesor;
							$aux_staAT = true;
						}
						$atributoProd = Producto::atributosProducto($detalle->notaventadetalle->producto_id);
						$aux_producto_nombre = $atributoProd["nombre"];


						if($detalle->cantsoldesp > $sumacantorddesp){
							$aux_totalrecABodSolDesp = 0;
							$aux_nfila++;
							$aux_saldo = $detalle->cantsoldesp - $sumacantorddesp;
							$invbodegaproductos = $detalle->notaventadetalle->producto->invbodegaproductos;
							$categoriaprod = $detalle->notaventadetalle->producto->categoriaprod;
							//CREAR REGISTRO EN TABLA invbodegaproducto (SOLO SI NO EXISTE)
							foreach ($categoriaprod->invbodegas as $invbodega){
								if($invbodega->tipo == 2){
									$invbodegaproducto = InvBodegaProducto::updateOrCreate(
										['producto_id' => $detalle->notaventadetalle->producto_id,'invbodega_id' => $invbodega->id],
										[
											'producto_id' => $detalle->notaventadetalle->producto_id,
											'invbodega_id' => $invbodega->id
										]
									);
								}
							}
							$aux_cantBodSD = 0;
							foreach ($detalle->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto) {
								if(($despachosoldet_invbodegaproducto->cant * -1) > 0){
									foreach ($despachosoldet_invbodegaproducto->invmovdet_bodsoldesps as $invmovdet_bodsoldesp){
										$aux_cantBodSD += $invmovdet_bodsoldesp->invmovdet->cant;
									}
								}
							}
							foreach ($detalle->despachoorddets as $despachoorddet){
								$DespachoOrd = DespachoOrd::findOrFail($despachoorddet->despachoord_id);
								if(!$DespachoOrd->despachoordanul ){
									foreach ($despachoorddet->despachoorddet_invbodegaproductos as $despachoorddet_invbodegaproducto){
										foreach ($despachoorddet_invbodegaproducto->invmovdet_bodorddesps as $invmovdet_bodorddesp){
											//SUMO SOLO EL MOVIMIENTO DE LA BODEGA DE SOL DESPACHO
											if($invmovdet_bodorddesp->invmovdet->invbodegaproducto->invbodega->nomabre == "SolDe"){
												$aux_cantBodSD += $invmovdet_bodorddesp->invmovdet->cant;
											}
										}
										//SI AUN NO HAY MOVIMIENTO DE INVENTARIO RESTA LOS QUE ESTA EN despachoorddet_invbodegaproducto 
										//ESTO ES POR SI ACASO HAY UNA ORDEN DE DESPACHO SIN GUARDAR EN LA PANTALLA INDEX DE ORDEN DE DESPACHO
										if (sizeof($despachoorddet_invbodegaproducto->invmovdet_bodorddesps) == 0){
											$aux_cantBodSD += $despachoorddet_invbodegaproducto->cant;
										}
									}
								}
							}
					?>
					<tr class="headt" style="height:150%;background-color: white;border-bottom: 1px solid #d3d3d3;" name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
						<td style='text-align:center;border-bottom: 1px solid #d3d3d3;' name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
							@if ($detalle->notaventadetalle->producto->acuerdotecnico)
								<a class="btn-accion-tabla btn-sm tooltipsC" title="" data-original-title="Acuerdo Técnico PDF">
									{{$detalle->notaventadetalle->producto_id}}
								</a>
							@else
								{{$detalle->notaventadetalle->producto_id}}
							@endif
						</td>
						<td style="border-bottom: 1px solid #d3d3d3;">
							{{$aux_producto_nombre}}
						</td>
						<td style="text-align:center;border-bottom: 1px solid #d3d3d3;">
							{{$detalle->cantsoldesp}}
						</td>
						<td style="text-align:center;border-bottom: 1px solid #d3d3d3;">
							{{$sumacantorddesp}}
						</td>
						<td style="text-align:center;display:none;border-bottom: 1px solid #d3d3d3;">
							{{$aux_saldo}}
						</td>
						<td style="text-align:center;border-bottom: 1px solid #d3d3d3;">
							{{$aux_saldo}}
						</td>
						<td style="text-align:center;border-bottom: 1px solid #d3d3d3;">
							<table class="table" id="tabla-bod" style="font-size:9px;table-layout: fixed;width: 130px;border-collapse: collapse;">
								<tbody>
									<?php $i=0; //dd($invbodegaproductos) ?>
									@foreach($invbodegaproductos as $invbodegaproducto)
										@if ($invbodegaproducto->invbodega->sucursal_id == $data->sucursal_id)
											<?php
												//dd($invbodegaproducto);
												$i++;
												$request = new Request();
												$request["producto_id"] = $invbodegaproducto->producto_id;
												$request["invbodega_id"] = $invbodegaproducto->invbodega_id;
												$request["tipo"] = 2;
												$existencia = $invbodegaproducto::existencia($request);
												//$existencia = $invbodegaproductoobj->consexistencia($request);
												//$aux_stock = $invbodegaproducto->invbodega->nomabre == "SolDe" ? $aux_cantBodSD  : $existencia["stock"]["cant"];
												$aux_valueStock = ""; 
												if(array_key_exists($invbodegaproducto->id . "-" . $detalle->id, $arrayBodegasPicking)){
													$aux_stock = $arrayBodegasPicking[$invbodegaproducto->id . "-" . $detalle->id]["stock"];
													$aux_valueStock = $aux_stock == 0 ? "" : $aux_stock;
												}else{
													//SI NO ESTA EN EL ARRAY DE $arrayBodegasPicking NO TIENE PICKING, ENTONCES LE ASIGNO 0
													if($invbodegaproducto->invbodega->nomabre == "SolDe"){
														$aux_stock = 0;
													}else{
														$aux_stock = $existencia["stock"]["cant"];
													}
												}
												if ($invbodegaproducto->invbodega->sucursal_id == 1) {
													$colorSuc = "#26ff00";
												}
												if ($invbodegaproducto->invbodega->sucursal_id == 2) {
													$colorSuc = "#1500ff";
												}
												if ($invbodegaproducto->invbodega->sucursal_id == 3) {
													$colorSuc = "#00c3ff";
												}
												//$stadespsinstock VARIABLE QUE ME PERMITE SABER SI EL PRODUCTO PERMITE HACER DESPACHO SIN EXISTENCIA
												$stadespsinstock = 0;
												if($invbodegaproducto->invbodega->tipo != 1 and $detalle->notaventadetalle->producto->categoriaprod->stadespsinstock == 1){
													$stadespsinstock=$detalle->notaventadetalle->producto->categoriaprod->stadespsinstock;
												}
											?>
											@if (in_array($invbodegaproducto->invbodega_id,$array_bodegasmodulo) AND ($invbodegaproducto->invbodega->activo == 1)) <!--SOLO MUESTRA LAS BODEGAS TIPO 1, LAS TIPO 2 NO LAS MUESTRA YA QUE ES BODEGA DE DESPACHO -->
												<tr style="background-color: white;">
													<td style="text-align:left;display:none;border-bottom: 1px solid #d3d3d3;">
														{{$invbodegaproducto->id}}
													</td>
													<td style="text-align:left;padding-right: 0px;padding-left: 2px;padding-top: 4px;padding-bottom: 4px;border-bottom: 1px solid #d3d3d3;">
														<div class="centrarhorizontal">
															<p style="font-size: 11px;margin-bottom: 0px">{{substr($invbodegaproducto->invbodega->nombre, 0, 4)}} {{$invbodegaproducto->invbodega->sucursal->abrev}}</p>
														</div>
													<td style="text-align:center;padding-left: 0px;padding-right: 0px;padding-top: 4px;padding-bottom: 4px;border-bottom: 1px solid #d3d3d3;">
														<div class="centrarhorizontal">
															{{$aux_stock}}
														</div>
													</td>
													<td style="text-align:center;padding-top: 4px;padding-bottom: 4px;border-bottom: 1px solid #d3d3d3;">
														{{$aux_valueStock}}
													</td>
												</tr>
											@endif
										@endif
									@endforeach
									@if ($i == 0)
										<a style="text-align:center" class='btn-sm tooltipsC' title='Producto sin Bodega Asignada y sin Stock'>
											<i class='fa fa-fw fa-question-circle text-aqua'></i>
										</a>
									@endif
								</tbody>
							</table>
						</td>
						<td style="text-align:right;border-bottom: 1px solid #d3d3d3;text-align:center">
							{{$aux_espesor}}
						</td>
						<td style="text-align:right;border-bottom: 1px solid #d3d3d3;text-align:center">
							{{$peso}}
						</td>
					</tr>
					<?php $i++;
						}
					?>
				@endforeach
			@endif
		</tbody>
	</table>
</div>

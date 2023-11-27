<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<!--<img class="anulada" src="img/anulado.png" alt="Anulada">-->
<br>
<br>
<?php 
	use App\Models\Producto;
	use App\Models\Comuna;
	use App\Models\NotaVenta;
	use App\Models\NotaVentaDetalle;
	use App\Models\InvBodega;
	use Illuminate\Http\Request;
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
					<span class="h3">Pendiente por producto</span>
					<p>Fecha: {{date("d/m/Y h:i:s A")}}</p>
					<p>Area Producción: {{$nombreAreaproduccion}}</p>
					<p>Vendedor: {{$nomvendedor}} </p>
					<p>Giro: {{$nombreGiro}} </p>
					<p>Estatus NV: {{$request->aprobstatusdesc}}</p>
					<p>Nota Venta Desde: {{$aux_fdesde}} Hasta: {{$aux_fhasta}}</p>
					<p>Plazo Entrega Desde: {{$aux_plazoentregad}} Hasta: {{$aux_plazoentregah}}</p>
				</div>
			</td>
		</tr>
	</table>

	<div class="round">
		<table id="factura_detalle">
				<thead>
					<tr>
						<th class='width30'>NV</th>
						<th class='width40'>OC</th>
						<th style="font-size: 10px;" class='width40'>Fecha</th>
						<th style="font-size: 10px;" class='width40'>Plazo<br>Entrega</th>
						<th class='width180'>Razón Social</th>
						<th class='width50'>Comuna</th>
						<th style='text-align:left' class='width10'>Cod</th>
						<th style='text-align:left' class='width90'>Descripción</th>
						<th style="font-size: 10px;" style='text-align:left' class='width30'>Clase<br>Sello</th>
						<th style='text-align:left' class='width30'>Diam<br>Ancho</th>
						<th style='text-align:left' class='width10'>L</th>
						<th style='text-align:left' class='width30'>Peso<br>Esp</th>
						<th style='text-align:left' class='width10'>TU</th>
						<th style="font-size: 10px;" style='text-align:right' class='width20'>Stock</th>
						<th style="font-size: 10px;" style='text-align:right' class='width20'>Picking</th>
						<th style="font-size: 10px;" style='text-align:right' class='width40'>Cant</th>
						<!--
						<th style='text-align:right'>Kilos</th>
						-->
						<th style='text-align:right' class='tooltipsC width30' title='Cantidad Despachada'>Cant<br>Desp</th>
						<!--
						<th style='text-align:right' class='tooltipsC' title='Kilos Despachados'>Kilos<br>Desp</th>
						-->
						<!--
						<th style='text-align:right' class='tooltipsC' title='Cantidad Solicitada'>Solid</th>
						-->
						<th style='text-align:right' class='width40'>Cant<br>Pend</th>		
						<th style='text-align:right' class='width50'>Kilos<br>Pend</th>
						<th style='text-align:right' class='width50'>Precio<br>Kg</th>
						<th style='text-align:right' class='width50'>$</th>
						<th class='width30'></th>
						<th class='width30'></th>
					</tr>
				</thead>
				<tbody id="detalle_productos">
					<?php
						$aux_totalcant = 0;
						$aux_totalcantdesp = 0;
						$aux_totalcantsol = 0;
						$aux_totalkilos = 0;
				        $aux_totalkilosdesp = 0;
						$aux_totalcantpend = 0;
						$aux_totalkilospend = 0;
						$aux_totalplata = 0;
						$aux_totalprecio = 0;
						$aux_totalpicking = 0;
						$i = 0;
					?>
					@foreach($datas as $data)
						<?php
						    $notaventadetalle = NotaVentaDetalle::findOrFail($data->id);
							$aux_picking = 0;
							$detalles = $notaventadetalle->despachosoldets()->get();
							$arrayBodegasPickings = InvBodega::llenarArrayBodegasPickingSolDesp($detalles);
							foreach ($arrayBodegasPickings as $arrayBodegasPicking) {
								$aux_picking += $arrayBodegasPicking["stock"];
							}

							//SUMA TOTAL DE SOLICITADO
							/*************************/
							$sql = "SELECT cantsoldesp
							FROM vista_sumsoldespdet
							WHERE notaventadetalle_id=$data->id";
							$datasuma = DB::select($sql);
							
							if(empty($datasuma)){
								$sumacantsoldesp= 0;
							}else{
								$sumacantsoldesp= $datasuma[0]->cantsoldesp;
							}
							/*************************/
							//SUMA TOTAL DESPACHADO
							/*************************/
							$sql = "SELECT cantdesp
								FROM vista_sumorddespxnvdetid
								WHERE notaventadetalle_id=$data->id";
							$datasumadesp = DB::select($sql);
							//dd($datasumadesp);
							if(empty($datasumadesp)){
								$sumacantdesp= 0;
							}else{
								$sumacantdesp= $datasumadesp[0]->cantdesp;
							}
							//$aux_totalkg += $data->saldokg; // ($data->totalkilos - $data->kgsoldesp);
							//$aux_totalplata += $data->saldoplata; // ($data->subtotal - $data->subtotalsoldesp);
							$aux_cantsaldo = $data->cant-$sumacantdesp;
							$comuna = Comuna::findOrFail($data->comunaentrega_id);
							$producto = Producto::findOrFail($data->producto_id);
							$aux_razonsocial = ucwords(strtolower($data->razonsocial));
							$aux_razonsocial = ucwords($aux_razonsocial,".");
							//$aux_subtotalplata = ($aux_cantsaldo * $data->peso) * $data->precioxkilo;
							$aux_subtotalplata = $aux_cantsaldo * $data->preciounit;

							$notaventa = NotaVenta::findOrFail($data->notaventa_id);
							$request = new Request();
							$request["producto_id"] = $data->producto_id;
							//dd($producto->invbodegaproductos);
							$aux_invbodega_id = "";
							foreach ($producto->invbodegaproductos as $invbodegaproducto) {
								if($invbodegaproducto->invbodega->sucursal_id == $notaventa->sucursal_id and $invbodegaproducto->invbodega->tipo == 2){
									$aux_invbodega_id = $invbodegaproducto->invbodega_id; 
								}
							}
							$request["invbodega_id"] = $aux_invbodega_id;
							$request["tipo"] = 2;
							$stock = 0;
							if(isset($invbodegaproducto)){
								$existencia =  $invbodegaproducto::existencia($request);
								$stock = $existencia["stock"]["cant"];    
							}

							$aux_producto_id = $data->producto_id;
							$aux_ancho = $producto->diametro > 0 ? $producto->diametro : "";
							$aux_espesor = $data->peso;
							$aux_largo = $data->long > 0 ? $data->long . "Mts" : "";
							$aux_cla_sello_nombre = $data->cla_nombre;
							$aux_producto_nombre = $data->nombre;
							//$aux_categoria_nombre = $data->producto->categoriaprod->nombre;
							if ($producto->acuerdotecnico != null){
								$AcuTec = $producto->acuerdotecnico;
								$aux_producto_id = "<a class='btn-accion-tabla btn-sm tooltipsC' title='' onclick='genpdfAcuTec($AcuTec->id,$data->cliente_id,1)' data-original-title='Acuerdo Técnico PDF'>
										$data->producto_id
									</a>";
								$aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
								$aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
								$aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
								$aux_espesor = $AcuTec->at_espesor;
								if($AcuTec->claseprod){
									$aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
								}else{
									$aux_cla_sello_nombre = "";
								}
							}

						?>
						<tr class='btn-accion-tabla tooltipsC'>
							<td>{{$data->notaventa_id}}</td>
							<td>{{$data->oc_id}}</td>
							<td style="font-size: 10px;">{{date('d/m/Y', strtotime($data->fechahora))}}</td>
							<td style="font-size: 10px;">{{date('d/m/Y', strtotime($data->plazoentrega))}}</td>
							<td style="font-size: 9px;">{{$aux_razonsocial}}</td>
							<td style="font-size: 9px;">{{$comuna->nombre}}</td>
							<td style='text-align:center'>{{$data->producto_id}}</td>
							<td style="font-size: 9px;">{{$data->nombre}}</td>
							<td style="font-size: 10px;text-align:center;">{{$aux_cla_sello_nombre}}</td>
							<td style='font-size: 10px;text-align:center'>{{$aux_ancho}}</td>
							<td style='font-size: 10px;text-align:center'>{{$aux_largo}}</td>
							<td style='font-size: 10px;text-align:right'>{{$aux_espesor > 0 ? number_format($aux_espesor, 4, ",", ".") : ""}}</td>
							<td style='font-size: 10px;text-align:center'>{{$data->tipounion}}</td>
							<td style='text-align:right'>{{$stock}}</td>
							<td style='text-align:right'>{{$aux_picking}}</td>
							<td style='text-align:right'>{{number_format($data->cant, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($sumacantdesp, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($aux_cantsaldo, 0, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($aux_cantsaldo * $data->peso, 2, ",", ".")}}</td>
							<td style='text-align:right'>{{number_format($data->precioxkilo, 2, ",", ".")}}</td>
							<td style='text-align:right'>&nbsp;{{number_format($aux_subtotalplata, 2, ",", ".")}}</td>
							<td></td>
						</tr>
						<?php
							$aux_totalcant += $data->cant;
							$aux_totalcantdesp += $sumacantdesp;
							$aux_totalkilos += $data->totalkilos;
            				$aux_totalkilosdesp += ($sumacantdesp * $data->peso);
							//$aux_totalcantsol += $sumacantsoldesp;
							$aux_totalcantpend += $aux_cantsaldo;
							$aux_totalkilospend += ($aux_cantsaldo * $data->peso);
							$aux_totalplata += $aux_subtotalplata;
							$aux_totalprecio += $data->precioxkilo;
							$aux_totalpicking += $aux_picking;
							$i++;
						?>
					@endforeach
					<?php
						$aux_totalkilospend = round($aux_totalkilospend,2);
						$aux_promprecioxkilo = round($aux_totalprecio/$i,2);
					?>
				</tbody>
				<tfoot id="detalle_totales">
					<tr>
						<th colspan='14' style='text-align:right'>TOTALES</th>
						<th style='text-align:right'>{{number_format($aux_totalpicking, 0, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($aux_totalcant, 0, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($aux_totalcantdesp, 0, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($aux_totalcantpend, 0, ",", ".")}}</th>
						<th style='text-align:right'>{{number_format($aux_totalkilospend, 2, ",", ".")}}</th>
						<th style='text-align:right'></th>
						<th style='text-align:right'>&nbsp;{{number_format($aux_totalplata, 2, ",", ".")}}</th>
						<th> </th>
					</tr>
					<tr>
						<th colspan='14' style='text-align:right'>PROMEDIO</th>
						<th colspan='5' style='text-align:right'></th>
						<th style='text-align:right'>{{number_format($aux_promprecioxkilo, 2, ",", ".")}}</th>
						<th style='text-align:right'>&nbsp;{{number_format($aux_totalkilospend * $aux_promprecioxkilo, 2, ",", ".")}}</th>
					</tr>	
				</tfoot>
					
		</table>
	</div>
</div>

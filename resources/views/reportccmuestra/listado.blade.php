<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">
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
            <td class="info_empresa"></td>
            <td class="info_factura">
                <div class="round">
                    <span class="h3">Reporte Muestras CC</span>
                    <p>Fecha impresión: {{date("d-m-Y H:i")}}</p>
                    @if($request->fecha_desde || $request->fecha_hasta)
                        <p>Período: {{$request->fecha_desde ?: '—'}} al {{$request->fecha_hasta ?: '—'}}</p>
                    @endif
                    @if($request->sucursal_id)
                        <p>Sucursal: {{$request->sucursal_id}}</p>
                    @endif
                    <p>Usuario: {{$usuario->nombre}}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="round">
        <table id="factura_detalle">
            <thead>
                <tr>
                    <th style="text-align:center; width:30px;">ID</th>
                    <th style="text-align:center; width:70px;">Fecha</th>
                    <th style="text-align:center; width:60px;">OP/OT</th>
                    <th style="text-align:center; width:30px;">NV</th>
                    <th style="text-align:left; width:100px;">Cliente</th>
                    <th style="text-align:left; width:120px;">Producto</th>
                    <th style="text-align:left; width:60px;">Etapa</th>
                    <th style="text-align:right; width:40px;">Kg</th>
                    <th style="text-align:left; width:60px;">Operario</th>
                    <th style="text-align:left; width:60px;">Máquina</th>
                    <th style="text-align:center; width:60px;">Status CC</th>
                    <th style="text-align:center; width:70px;">Estado envío</th>
                    <th style="text-align:center; width:30px;">Anul.</th>
                    <th style="text-align:center; width:30px;">Desb.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $statusTexto  = [1 => 'Aprobado', 2 => 'Aprobado c/obs', 3 => 'Rechazado'];
                    $staEnvTexto  = [0 => 'Sin enviar', 1 => 'Enviado', 2 => 'Aprobado sup.', 3 => 'Rechazado sup.'];
                    $statusColor  = [1 => '#00a65a', 2 => '#f39c12', 3 => '#dd4b39'];
                    $staEnvColor  = [0 => '#aaa', 1 => '#00c0ef', 2 => '#00a65a', 3 => '#dd4b39'];
                    $totalKg      = 0;
                    $totAprobados = 0; $totConObs = 0; $totRechazados = 0; $totAnulados = 0;
                ?>
                @foreach($datas as $d)
                <?php
                    $totalKg += $d->kgprod;
                    if ($d->anulado)         $totAnulados++;
                    elseif ($d->status == 1) $totAprobados++;
                    elseif ($d->status == 2) $totConObs++;
                    elseif ($d->status == 3) $totRechazados++;
                    $statusTxt = $statusTexto[$d->status] ?? '—';
                    $statusCol = $statusColor[$d->status] ?? '#aaa';
                    $staEnvTxt = $staEnvTexto[$d->sta_env ?? 0] ?? '—';
                    $staEnvCol = $staEnvColor[$d->sta_env ?? 0] ?? '#aaa';
                ?>
                <tr>
                    <td style="text-align:center;">{{$d->id}}</td>
                    <td style="text-align:center; font-size:10px;">{{substr($d->fechahora, 0, 16)}}</td>
                    <td style="text-align:center; font-size:10px;">OP{{$d->op_id}}/OT{{$d->ot_id}}</td>
                    <td style="text-align:center;">{{$d->notaventa_id ?? '—'}}</td>
                    <td style="font-size:10px;">{{$d->cliente_razonsocial ?? '—'}}</td>
                    <td style="font-size:10px;">{{$d->producto_nombre}}</td>
                    <td style="font-size:10px;">{{$d->etapaprod_nombre}}</td>
                    <td style="text-align:right;">{{number_format($d->kgprod, 2, ',', '.')}}</td>
                    <td style="font-size:10px;">{{$d->operario_nombre ?? '—'}}</td>
                    <td style="font-size:10px;">{{$d->maquina_nombre ?? '—'}}</td>
                    <td style="text-align:center;">
                        @if($d->anulado)
                            <span style="color:#777; font-size:10px;">Anulado</span>
                        @else
                            <span style="color:{{$statusCol}}; font-size:10px; font-weight:bold;">{{$statusTxt}}</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span style="color:{{$staEnvCol}}; font-size:10px;">{{$staEnvTxt}}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($d->anulado)
                            <span style="color:#dd4b39;" title="{{$d->anulacion_usuario}} — {{$d->anulacion_motivo}}">✗</span>
                        @else
                            —
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($d->desbloqueado)
                            <span style="color:#00c0ef;" title="{{$d->desbloqueo_usuario}}">✓</span>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" style="text-align:right; font-weight:bold;">TOTAL:</td>
                    <td style="text-align:right; font-weight:bold;">{{number_format($totalKg, 2, ',', '.')}}</td>
                    <td colspan="5" style="font-size:10px; color:#555;">
                        Aprobados: {{$totAprobados}} |
                        Con obs: {{$totConObs}} |
                        Rechazados: {{$totRechazados}} |
                        Anulados: {{$totAnulados}}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

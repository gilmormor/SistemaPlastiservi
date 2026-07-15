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
                    <span class="h3">Reporte Cobertura CC</span>
                    <p>Fecha impresión: {{date("d-m-Y H:i")}}</p>
                    @if($request->fecha_desde || $request->fecha_hasta)
                        <p>Período: {{$request->fecha_desde ?: '—'}} al {{$request->fecha_hasta ?: '—'}}</p>
                    @endif
                    <p>Usuario: {{$usuario->nombre}}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="round">
        <?php
            $totalKg      = 0;
            $conCobertura = 0;
            $sinCobertura = 0;
        ?>
        @foreach($datas as $d)
            <?php $totalKg += $d->kgprod; ?>
            <?php if ($d->con_muestra) $conCobertura++; else $sinCobertura++; ?>
        @endforeach
        <?php
            $total = count($datas);
            $pct   = $total > 0 ? round($conCobertura / $total * 100, 1) : 0;
        ?>

        {{-- Resumen totales --}}
        <table style="width:100%; margin-bottom:10px; font-size:11px;">
            <tr>
                <td style="width:25%; text-align:center; padding:4px; border:1px solid #ccc; background:#f9f9f9;">
                    <strong>Total lotes</strong><br>
                    <span style="font-size:16px; font-weight:bold;">{{$total}}</span>
                </td>
                <td style="width:25%; text-align:center; padding:4px; border:1px solid #ccc; background:#f9f9f9; color:#00a65a;">
                    <strong>Con cobertura</strong><br>
                    <span style="font-size:16px; font-weight:bold;">{{$conCobertura}}</span>
                </td>
                <td style="width:25%; text-align:center; padding:4px; border:1px solid #ccc; background:#f9f9f9; color:#dd4b39;">
                    <strong>Sin cobertura</strong><br>
                    <span style="font-size:16px; font-weight:bold;">{{$sinCobertura}}</span>
                </td>
                <td style="width:25%; text-align:center; padding:4px; border:1px solid #ccc; background:#f9f9f9; color:#3c8dbc;">
                    <strong>% Cobertura</strong><br>
                    <span style="font-size:16px; font-weight:bold;">{{$pct}}%</span>
                </td>
            </tr>
        </table>

        <table id="factura_detalle">
            <thead>
                <tr>
                    <th style="text-align:center; width:40px;">Lote</th>
                    <th style="text-align:center; width:75px;">Fecha aprobación</th>
                    <th style="text-align:center; width:55px;">OP/OT</th>
                    <th style="text-align:left; width:120px;">Producto</th>
                    <th style="text-align:left; width:70px;">Etapa</th>
                    <th style="text-align:right; width:45px;">Kg</th>
                    <th style="text-align:left; width:80px;">Operario</th>
                    <th style="text-align:left; width:55px;">Máquina</th>
                    <th style="text-align:center; width:35px;">N° Muestras</th>
                    <th style="text-align:center; width:60px;">Cobertura</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datas as $d)
                <tr @if(!$d->con_muestra) style="background-color:#fff3f3;" @endif>
                    <td style="text-align:center;">{{$d->id}}</td>
                    <td style="text-align:center; font-size:10px;">
                        {{$d->aprobfechahora ? substr($d->aprobfechahora, 0, 16) : '—'}}
                    </td>
                    <td style="text-align:center; font-size:10px;">OP{{$d->op_id}}/OT{{$d->ot_id}}</td>
                    <td style="font-size:10px;">{{$d->producto_nombre}}</td>
                    <td style="font-size:10px;">{{$d->etapaprod_nombre}}</td>
                    <td style="text-align:right;">{{number_format($d->kgprod, 2, ',', '.')}}</td>
                    <td style="font-size:10px;">{{$d->operario_nombre ?? '—'}}</td>
                    <td style="font-size:10px;">{{$d->maquina_nombre ?? '—'}}</td>
                    <td style="text-align:center;">
                        @if($d->total_muestras > 0)
                            <strong>{{$d->total_muestras}}</strong>
                        @else
                            <span style="color:#aaa;">0</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($d->con_muestra)
                            <span style="color:#00a65a; font-weight:bold; font-size:10px;">Con muestra</span>
                        @else
                            <span style="color:#dd4b39; font-weight:bold; font-size:10px;">Sin muestra</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right; font-weight:bold;">TOTAL Kg:</td>
                    <td style="text-align:right; font-weight:bold;">{{number_format($totalKg, 2, ',', '.')}}</td>
                    <td colspan="4" style="font-size:10px; color:#555;">
                        Con cobertura: {{$conCobertura}} |
                        Sin cobertura: {{$sinCobertura}} |
                        % Cobertura: {{$pct}}%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{--
    Fragmento reutilizable: tabla de etapas + muestras CC para un lote.
    Variables esperadas: $lote, $filas, $loteId, $sinAcuerdo
--}}
<?php
$statusLabel = [
    1 => ['txt' => 'Aprobado',                  'class' => 'label-success'],
    2 => ['txt' => 'Aprobado con observaciones', 'class' => 'label-warning'],
    3 => ['txt' => 'Rechazado',                  'class' => 'label-danger'],
    5 => ['txt' => 'Sin parámetros',             'class' => 'label-default'],
];
?>

<div style="margin-bottom:10px;">
    <table class="table table-condensed" style="width:auto;margin-bottom:4px;">
        <tr>
            <th style="font-size:11px;padding:2px 8px;">Producto:</th>
            <td style="font-size:11px;padding:2px 8px;"><strong>{{$lote->producto_codigo}} — {{$lote->producto_nombre}}</strong></td>
            <th style="font-size:11px;padding:2px 8px;">OP / OT:</th>
            <td style="font-size:11px;padding:2px 8px;">OP {{$lote->op_id}} / OT {{$lote->ot_id}}</td>
        </tr>
    </table>
    @if($sinAcuerdo)
        <div class="alert alert-info" style="font-size:11px;padding:6px 10px;margin-bottom:6px;">
            <i class="fa fa-info-circle"></i>
            Este producto no tiene etapas configuradas en acuerdo técnico. Se muestran solo los lotes registrados.
        </div>
    @endif
</div>

<div class="table-responsive">
<table class="table table-bordered table-condensed" style="font-size:11px;">
    <thead>
        <tr style="background:#f4f4f4;">
            <th style="width:30px;">#</th>
            <th>Etapa</th>
            <th style="width:50px;text-align:center;">Req. CC</th>
            <th style="width:80px;">Lote</th>
            <th style="width:70px;text-align:right;">Kg</th>
            <th style="width:80px;text-align:right;">U/S</th>
            <th>Operario / Máquina</th>
            <th style="width:60px;text-align:center;">N° Muestra</th>
            <th style="width:140px;text-align:center;">Status muestra</th>
            <th style="width:110px;text-align:center;">Estado CC</th>
        </tr>
    </thead>
    <tbody>
    @foreach($filas as $fila)
        @if($fila['sin_registro'])
            {{-- Etapa configurada pero sin lote registrado --}}
            <tr style="background:#fffde7;">
                <td style="text-align:center;color:#aaa;">{{$fila['orden']}}</td>
                <td>
                    <strong>{{$fila['etapaprod_nombre']}}</strong>
                </td>
                <td style="text-align:center;">
                    @if($fila['requiere_cc'])
                        <span class="label label-danger" style="font-size:10px;">Sí</span>
                    @else
                        <span class="label label-default" style="font-size:10px;">No</span>
                    @endif
                </td>
                <td colspan="6" style="color:#999;font-style:italic;">
                    <i class="fa fa-clock-o"></i> Sin registro de producción — etapa no procesada aún
                </td>
                <td style="text-align:center;">
                    <span class="label label-default" style="font-size:10px;">
                        <i class="fa fa-minus"></i> No iniciada
                    </span>
                </td>
            </tr>
        @else
            @foreach($fila['lotes'] as $idx => $loteRow)
            <tr style="{{$loteRow['es_lote_consultado'] ? 'background:#e8f4fd;font-weight:bold;' : ''}}">
                {{-- Orden y nombre de etapa solo en primera fila del grupo --}}
                @if($idx === 0)
                <td style="text-align:center;color:#888;" rowspan="{{count($fila['lotes'])}}">{{$fila['orden']}}</td>
                <td rowspan="{{count($fila['lotes'])}}">
                    <strong>{{$fila['etapaprod_nombre']}}</strong>
                </td>
                <td style="text-align:center;" rowspan="{{count($fila['lotes'])}}">
                    @if($fila['requiere_cc'])
                        <span class="label label-danger" style="font-size:10px;">Sí</span>
                    @else
                        <span class="label label-default" style="font-size:10px;">No</span>
                    @endif
                </td>
                @endif
                <td>
                    <a href="javascript:void(0);" onclick="verEtiquetaEtapaConPermiso({{$loteRow['id']}},'ver-etiqueta-regprod','modalCcLote')"
                       style="color:#2980b9;font-weight:600;" title="Ver etiqueta lote #{{$loteRow['id']}}">
                        <i class="fa fa-tag"></i> #{{$loteRow['id']}}
                    </a>
                    @if($loteRow['es_lote_consultado'])
                        <span class="label label-info" style="font-size:9px;">consultado</span>
                    @endif
                </td>
                <td style="text-align:right;">
                    {{number_format($loteRow['kgprod'] ?? 0, 2, ',', '.')}}
                </td>
                <td style="text-align:right;">
                    {{number_format($loteRow['cantprod'] ?? 0, 2, ',', '.')}}
                    <small style="color:#888;">{{$loteRow['unidadmedida'] ?? '—'}}</small>
                </td>
                <td>
                    <span style="color:#555;">{{$loteRow['operario'] ?? '—'}}</span>
                    @if($loteRow['maquina'])
                        <br><small style="color:#888;"><i class="fa fa-cog"></i> {{$loteRow['maquina']}}</small>
                    @endif
                </td>
                {{-- Columna: N° Muestra --}}
                <td style="text-align:center;">
                    @if(empty($loteRow['muestras']))
                        <span style="color:#aaa;">—</span>
                    @else
                        @foreach($loteRow['muestras'] as $m)
                            @if(!$m->anulado)
                                <a href="javascript:void(0);" onclick="genpdfCC({{$m->id}})"
                                   title="Ver PDF muestra #{{$m->id}}" style="font-weight:600;">#{{$m->id}}</a>
                            @else
                                <span style="color:#aaa;text-decoration:line-through;">#{{$m->id}}</span>
                            @endif
                            @if(!$loop->last)<br>@endif
                        @endforeach
                    @endif
                </td>
                {{-- Columna: Status muestra (ccregistmuestra.status) --}}
                <td style="text-align:center;">
                    @if(empty($loteRow['muestras']))
                        <span style="color:#aaa;">—</span>
                    @else
                        @foreach($loteRow['muestras'] as $m)
                            @php $sl = $statusLabel[$m->status] ?? ['txt' => '?', 'class' => 'label-default']; @endphp
                            @if($m->anulado)
                                <span class="label label-default" style="font-size:10px;opacity:0.6;">Anulada</span>
                            @else
                                <span class="label {{$sl['class']}}" style="font-size:10px;white-space:normal;">
                                    {{$sl['txt']}}
                                </span>
                            @endif
                            @if(!$loop->last)<br>@endif
                        @endforeach
                    @endif
                </td>
                <td style="text-align:center;">
                    @php $cs = $loteRow['cc_status']; @endphp
                    @if($cs === 'ok')
                        <span class="label label-success" style="font-size:10px;white-space:normal;">
                            <i class="fa fa-check"></i> Aprobado CC
                        </span>
                    @elseif($cs === 'rechazado')
                        <span class="label label-danger" style="font-size:10px;white-space:normal;">
                            <i class="fa fa-times"></i> Rechazado CC
                        </span>
                    @elseif($cs === 'sin_muestra')
                        <span class="label label-danger" style="font-size:10px;white-space:normal;">
                            <i class="fa fa-ban"></i> Falta muestra
                        </span>
                    @elseif($cs === 'pendiente_sup')
                        <span class="label label-warning" style="font-size:10px;white-space:normal;">
                            <i class="fa fa-hourglass-half"></i> Pend. supervisor
                        </span>
                    @else
                        <span class="label label-default" style="font-size:10px;white-space:normal;">
                            <i class="fa fa-minus"></i> No requiere
                        </span>
                    @endif
                </td>
            </tr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>
</div>

<div style="text-align:right;margin-top:4px;">
    <a href="javascript:void(0);"
       onclick="verPdfCcLote('/reportcclote/exportPdf?opdetregprod_id={{$loteId}}', 'modalCcLote')"
       class="btn btn-danger btn-xs tooltipsC" title="Exportar PDF">
        <i class="fa fa-file-pdf-o"></i> PDF
    </a>
</div>

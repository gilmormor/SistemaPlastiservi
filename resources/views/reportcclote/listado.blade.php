<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 10px; }
    h2   { font-size: 13px; margin: 0 0 4px 0; }
    .info-tabla { width: 100%; margin-bottom: 8px; border-collapse: collapse; }
    .info-tabla td { padding: 2px 6px; font-size: 10px; }
    table.principal { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.principal th { background: #e8e8e8; font-size: 9px; padding: 3px 4px; border: 1px solid #bbb; text-align: left; }
    table.principal td { font-size: 9px; padding: 3px 4px; border: 1px solid #ddd; vertical-align: top; }
    .bg-amarillo  { background: #fffde7; }
    .bg-consultado{ background: #e8f4fd; font-weight: bold; }
    .lbl { display: inline-block; padding: 1px 4px; border-radius: 3px; font-size: 8px; font-weight: bold; }
    .lbl-ok      { background: #00a65a; color: #fff; }
    .lbl-danger  { background: #dd4b39; color: #fff; }
    .lbl-warning { background: #f39c12; color: #fff; }
    .lbl-default { background: #aaa;    color: #fff; }
    .lbl-info    { background: #00c0ef; color: #fff; }
    .pie { font-size: 8px; color: #888; margin-top: 12px; text-align: right; }
</style>
</head>
<body>
<?php
$aprobLabels = [1 => 'Pendiente', 2 => 'Aprobado', 3 => 'Rechazado'];
$statusLabel = [
    1 => ['txt' => 'Aprobado',      'cls' => 'lbl-ok'],
    2 => ['txt' => 'Aprob. c/obs',  'cls' => 'lbl-warning'],
    3 => ['txt' => 'Rechazado',     'cls' => 'lbl-danger'],
    5 => ['txt' => 'Sin param.',    'cls' => 'lbl-default'],
];
?>

<h2><i>Reporte CC — Cobertura por Lote</i></h2>
<table class="info-tabla">
    <tr>
        <td><strong>Producto:</strong> {{$lote->producto_codigo}} — {{$lote->producto_nombre}}</td>
        <td><strong>OP:</strong> {{$lote->op_id}} &nbsp; <strong>OT:</strong> {{$lote->ot_id}}</td>
        <td><strong>Lote consultado:</strong> #{{$loteId}}</td>
        <td style="text-align:right;"><strong>Fecha:</strong> {{\Carbon\Carbon::now()->format('d/m/Y H:i')}}</td>
    </tr>
</table>

@if($sinAcuerdo)
    <p style="color:#888;font-style:italic;font-size:9px;">
        * Este producto no tiene etapas configuradas en acuerdo técnico. Se muestran solo los lotes registrados.
    </p>
@endif

<table class="principal">
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th>Etapa</th>
            <th style="width:35px;text-align:center;">Req.CC</th>
            <th style="width:50px;">Lote</th>
            <th style="width:55px;text-align:right;">Kg</th>
            <th>Operario / Máquina</th>
            <th>Muestras CC</th>
            <th style="width:80px;text-align:center;">Estado CC</th>
        </tr>
    </thead>
    <tbody>
    @foreach($filas as $fila)
        @if($fila['sin_registro'])
            <tr class="bg-amarillo">
                <td style="color:#aaa;text-align:center;">{{$fila['orden']}}</td>
                <td><strong>{{$fila['etapaprod_nombre']}}</strong></td>
                <td style="text-align:center;">
                    <span class="lbl {{$fila['requiere_cc'] ? 'lbl-danger' : 'lbl-default'}}">
                        {{$fila['requiere_cc'] ? 'Sí' : 'No'}}
                    </span>
                </td>
                <td colspan="4" style="color:#999;font-style:italic;">Sin registro de producción — no procesada aún</td>
                <td style="text-align:center;"><span class="lbl lbl-default">No iniciada</span></td>
            </tr>
        @else
            @foreach($fila['lotes'] as $idx => $loteRow)
            <tr class="{{$loteRow['es_lote_consultado'] ? 'bg-consultado' : ''}}">
                @if($idx === 0)
                <td style="text-align:center;color:#888;" rowspan="{{count($fila['lotes'])}}">{{$fila['orden']}}</td>
                <td rowspan="{{count($fila['lotes'])}}"><strong>{{$fila['etapaprod_nombre']}}</strong></td>
                <td style="text-align:center;" rowspan="{{count($fila['lotes'])}}">
                    <span class="lbl {{$fila['requiere_cc'] ? 'lbl-danger' : 'lbl-default'}}">
                        {{$fila['requiere_cc'] ? 'Sí' : 'No'}}
                    </span>
                </td>
                @endif
                <td>
                    #{{$loteRow['id']}}
                    @if($loteRow['es_lote_consultado']) <span class="lbl lbl-info">consulta</span> @endif
                </td>
                <td style="text-align:right;">
                    {{number_format($loteRow['kgprod'] ?? 0, 2, ',', '.')}}
                    <small>{{$loteRow['unidadmedida'] ?? 'kg'}}</small>
                </td>
                <td>
                    {{$loteRow['operario'] ?? '—'}}
                    @if($loteRow['maquina']) / {{$loteRow['maquina']}} @endif
                </td>
                <td>
                    @if(empty($loteRow['muestras']))
                        <span style="color:#aaa;font-style:italic;">Sin muestras</span>
                    @else
                        @foreach($loteRow['muestras'] as $m)
                            @php $sl = $statusLabel[$m->status] ?? ['txt'=>'?','cls'=>'lbl-default']; @endphp
                            <span class="lbl {{$sl['cls']}}{{$m->anulado ? ' lbl-default' : ''}}">
                                #{{$m->id}} {{$m->anulado ? 'Anulada' : $sl['txt']}}
                            </span>
                            @if(!$loop->last) &nbsp; @endif
                        @endforeach
                    @endif
                </td>
                <td style="text-align:center;">
                    @php $cs = $loteRow['cc_status']; @endphp
                    @if($cs === 'ok')
                        <span class="lbl lbl-ok">✓ Aprobado</span>
                    @elseif($cs === 'rechazado')
                        <span class="lbl lbl-danger">✗ Rechazado</span>
                    @elseif($cs === 'sin_muestra')
                        <span class="lbl lbl-danger">! Falta muestra</span>
                    @elseif($cs === 'pendiente_sup')
                        <span class="lbl lbl-warning">~ Pend. sup.</span>
                    @else
                        <span class="lbl lbl-default">— No requiere</span>
                    @endif
                </td>
            </tr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>

<div class="pie">
    Generado por: {{$usuario->nombre ?? '—'}} &nbsp;|&nbsp; {{$empresa->first()->nombre ?? ''}} &nbsp;|&nbsp; {{\Carbon\Carbon::now()->format('d/m/Y H:i:s')}}
</div>
</body>
</html>

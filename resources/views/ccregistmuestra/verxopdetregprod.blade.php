@extends("theme.$theme.layout")
@section('titulo')
CC — Muestras del Registro #{{ $reg ? $reg->id : '?' }}
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-flask"></i> Muestras CC del Registro #{{ $reg ? $reg->id : '?' }}
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{route('listaropdetregprod_ccregistmuestra')}}" class="btn btn-info btn-sm">
                        <i class="fa fa-reply-all"></i> Volver a la búsqueda
                    </a>
                </div>
            </div>
            <div class="box-body">
                {{-- Datos del registro de producción --}}
                @if($reg)
                <div class="row" style="background:#f9f9f9; padding:10px; border-radius:4px; margin-bottom:15px;">
                    <div class="col-xs-2"><strong>Reg. Prod.:</strong><br>#{{ $reg->id }}</div>
                    <div class="col-xs-2"><strong>OP/OT:</strong><br>OP {{ $reg->op_id }} / OT {{ $reg->ot_id }}</div>
                    <div class="col-xs-4"><strong>Producto:</strong><br>{{ $reg->producto_nombre }}</div>
                    <div class="col-xs-2"><strong>Etapa:</strong><br>{{ $reg->etapaprod_nombre }}</div>
                    <div class="col-xs-2"><strong>Kg prod.:</strong><br>{{ number_format($reg->kgprod, 2, ',', '.') }} kg</div>
                </div>
                @endif

                @if($muestras->isEmpty())
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No hay muestras CC registradas para este registro de producción.
                    </div>
                @else
                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>Fecha</th>
                            <th>Registrado por</th>
                            <th style="text-align:center;">Status CC</th>
                            <th style="text-align:center;">Liberado</th>
                            <th style="text-align:center;">Parámetros</th>
                            <th style="text-align:center; width:100px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($muestras as $m)
                        @php
                            $statusLabel = [1 => ['Aprobado','success'], 2 => ['Aprobado c/obs','warning'], 3 => ['Rechazado','danger']];
                            $sl = $statusLabel[$m->status] ?? ['—','default'];
                        @endphp
                        <tr>
                            <td>{{ $m->id }}</td>
                            <td>{{ $m->fechahora }}</td>
                            <td>{{ $m->usuario->nombre ?? '—' }}</td>
                            <td class="text-center">
                                <span class="label label-{{ $sl[1] }}">{{ $sl[0] }}</span>
                            </td>
                            <td class="text-center">
                                @if($m->sta_env)
                                    <span class="label label-success"><i class="fa fa-check"></i> Sí</span>
                                @else
                                    <span class="label label-default">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge">{{ $m->dets->count() }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ver_ccregistmuestra', ['id' => $m->id]) }}"
                                    class="btn btn-info btn-xs">
                                    <i class="fa fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                {{-- Botón para agregar nueva muestra --}}
                @if($reg)
                <div class="text-center" style="margin-top:15px;">
                    <a href="{{ route('crear_ccregistmuestra', ['opdetregprod_id' => $reg->id]) }}"
                        class="btn btn-warning">
                        <i class="fa fa-plus"></i> Nueva Muestra CC para este registro
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

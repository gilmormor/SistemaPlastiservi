<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Validacion de Acuerdo Técnico</title>
</head>
<body>

    <p><b>Fecha:</b> {{ date("d-m-Y h:i:s A", strtotime($msg->updated_at)) }}.</p>
    <p>{{ $cuerpo }}</p>
    
    <p>Datos usuario que generó el correo:</p>
    <ul>
        <li><b>Nombre:</b> {{session()->get('nombre_usuario') }}</li>
        <li><b>Email:</b> {{Auth::user()->email}}</li>
    </ul>
    <p><b>Datos:</b></p>
    <ul>
        <li><b>Nro. Cotización:</b> {{ $tabla->id }}</li>
        <li><b>Fecha Cotización:</b> {{date("d-m-Y h:i:s A", strtotime($tabla->fechahora))}}</li>
        <li><b>Fecha Validación:</b> {{date("d-m-Y h:i:s A", strtotime($tabla->aprobfechahora))}}</li>
        <li><b>RUT:</b> {{ $tabla->cliente->rut }}</li>
        <li><b>Razon Social:</b> {{ $tabla->cliente->razonsocial }}</li>
        <li><b>Vendedor:</b> {{ $tabla->vendedor->persona->nombre . " " . $tabla->vendedor->persona->apellido}}</li>
    </ul>
    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="{{urlRaiz()."/cotizacionaprobaracutec"}}">
            {{urlRaiz()}}
        </a>
    </p>
</body>
</html>
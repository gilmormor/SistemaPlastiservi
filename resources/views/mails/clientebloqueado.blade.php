<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Observación Financiera</title>
</head>
<body>

    <p>{{ $cuerpo }}.</p>
    <p>Fecha: {{ date("d-m-Y h:i:s A", strtotime($msg->updated_at)) }}.</p>
    <p>Datos del usuario que genero el correo:</p>
    <ul>
        <li><b>Nombre:</b> {{session()->get('nombre_usuario') }}</li>
        <li><b>Email:</b> {{Auth::user()->email}}</li>
    </ul>
    <p><b>Datos:</b></p>
    <ul>
        <li><b>Cliente Id:</b> {{ $msg->cliente->id }}</li>
        <li><b>RUT:</b> {{ $msg->cliente->rut }}</li>
        <li><b>Razón Social:</b> {{ $msg->cliente->razonsocial }}</li>
        <li><b>Descripción:</b> {{ $msg->descripcion }}</li>
        <li><b>Vendedor:</b> {{ $nombrevendedor }}</li>
    </ul>
    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="{{urlRaiz()}}">
            {{urlRaiz()}}
        </a>
    </p>
</body>
</html>
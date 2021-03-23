<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Venta Devuelta</title>
</head>
<body>

    <p>{{ $cuerpo }}, {{ date("d-m-Y h:i:s A", strtotime($msg->updated_at)) }}.</p>
    <p>Datos usuario que generó el correo:</p>
    <ul>
        <li><b>Nombre:</b> {{session()->get('nombre_usuario') }}</li>
        <li><b>Email:</b> {{Auth::user()->email}}</li>
    </ul>
    <p><b>Datos:</b></p>
    <ul>
        <li><b>Nota Venta Id:</b> {{ $msg->tabla_id }}</li>
        <li><b>Fecha:</b> {{date("d-m-Y h:i:s A", strtotime($notaventa->fechahora))}}</li>
        <li><b>RUT:</b> {{ $notaventa->cliente->rut }}</li>
        <li><b>Razon Social:</b> {{ $notaventa->cliente->razonsocial }}</li>
        <li><b>Vendedor:</b> {{ $notaventa->vendedor->persona->nombre . " " . $notaventa->vendedor->persona->apellido}}</li>
    </ul>
    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="https://www.pl.plastiservi.cl">
            https://www.pl.plastiservi.cl
        </a>
    </p>
</body>
</html>
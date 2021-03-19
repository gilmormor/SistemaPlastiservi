<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Venta Devuelta</title>
</head>
<body>

    <p>{{ $cuerpo }}, {{ $msg->updated_at }}.</p>
    <p>Datos del usuario que genero el correo:</p>
    <ul>
        <li>Nombre: {{session()->get('nombre_usuario') }}</li>
        <li>Email: {{Auth::user()->email}}</li>
    </ul>
    <p>Datos:</p>
    <ul>
        <li>Id Nota Venta: {{ $msg->tabla_id }}</li>
        <li>RUT: {{ $msg->rut }}</li>
        <li>Razon Social: {{ $msg->razonsocial }}</li>
        <li>
            <a href="https://www.pl.plastiservi.cl">
                Ingresar al Sistema: https://www.pl.plastiservi.cl
            </a>
        </li>
    </ul>
</body>
</html>
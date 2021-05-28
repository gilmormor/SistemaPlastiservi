<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$notificacion->mensaje}}</title>
</head>
<body>

    <p><b>Fecha:</b> {{ date("d-m-Y h:i:s A", strtotime($notificacion->created_at)) }}.</p>

    <p>Datos usuario que generó el correo:</p>
    <ul>
        <li><b>Nombre:</b> {{session()->get('nombre_usuario') }}</li>
        <li><b>Email:</b> {{Auth::user()->email}}</li>
    </ul>
    
    <?php
        echo $detalle;
    ?>

    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="https://www.pl.plastiservi.cl">
            https://www.pl.plastiservi.cl
        </a>
    </p>
</body>
</html>
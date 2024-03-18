<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Credito</title>
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
        <li><b>Nro. Nota Credito:</b> {{ $tabla->nrodocto }}</li>
        <li><b>Fecha NC:</b> {{date("d-m-Y h:i:s A", strtotime($tabla->fechahora))}}</li>
        <li><b>Tipo Documento DTE asociado:</b> {{ $tabla->dtedte->dter->foliocontrol->desc }}</li>
        <li><b>Nro. Documento DTE asociado:</b> {{ $tabla->dtedte->dter->nrodocto }}</li>
        <li><b>Fecha:</b> {{date("d-m-Y h:i:s A", strtotime($tabla->dtedte->dter->fechahora))}}</li>
        <li><b>RUT:</b> {{ $tabla->cliente->rut }}</li>
        <li><b>Razon Social:</b> {{ $tabla->cliente->razonsocial }}</li>
        <li><b>Vendedor:</b> {{ $tabla->vendedor->persona->nombre . " " . $tabla->vendedor->persona->apellido}}</li>
    </ul>
    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="{{urlRaiz()}}">
            {{urlRaiz()}}
        </a>
    </p>
</body>
</html>
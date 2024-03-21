<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XML Carga Documento Manager</title>
</head>
<body>

    <p><b>Fecha:</b> {{ date("d-m-Y h:i:s A") }}.</p>
    <p>DTE Nro: {{ $event->dte->nrodocto }}</p>
    

    <p>Datos usuario que generó el correo:</p>
    <ul>
        <li><b>Nombre:</b> {{session()->get('nombre_usuario') }}</li>
        <li><b>Email:</b> {{Auth::user()->email}}</li>
    </ul>

    <p><b>XML Cliente:</b></p>
    <p>{{$event->xmlcli}}</p>


    <p><b>XML DTE: {{ $event->dte->nrodocto }}</b></p>
    <p>{{$event->xml}}</p>

    <p>
        <b>Ingresar al Sistema:</b> 
        <a href="{{urlRaiz()}}">
            {{urlRaiz()}}
        </a>
    </p>
</body>
</html>
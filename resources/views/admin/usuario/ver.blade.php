<div>{{$usuario->nombre}}</div>
<div>{{$usuario->usuario}}</div>
<div>{{$usuario->email}}</div>
<div class="user-header">
    <img src="/storage/imagenes/usuario/{{$usuario->foto}}" alt="User Image" width='33%' height='33%'>
    <p>
        {{session()->get('nombre_usuario') }}
        <small>{{session()->get('rol_nombre') }}</small>
    </p>
</div>
<!--
<div><img src="/storage/imagenes/usuario/{{$usuario->foto}}" alt="User Image"></div>
-->
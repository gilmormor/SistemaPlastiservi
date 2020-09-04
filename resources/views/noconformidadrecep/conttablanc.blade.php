<tr>
    <td>{{$data->id}}</td>
    <td><i class="fa {{$recibido}}"></i></td>
    <td>
        {{date('d-m-Y', strtotime($data->fechahora))}}
        @if ($data->notirecep === null)
            <span class="pull-right-container">
                <small class="label bg-red">new</small>
            </span>                                    
        @endif
    </td>
    <td>{{$data->hallazgo}}</td>
    <?php
        $aux_btn = "btn-warning";
        $aux_icono = "glyphicon-ok";
        if(empty($data->accioninmediata)){
            $aux_btn = "btn-primary";
            $aux_icono = "glyphicon-pencil";
        }
    ?>
    <!--
    <td>
        @csrf @method("delete")
        <a id='accioninmediata{{$i}}' name='accioninmediata{{$i}}' class='btn {{$aux_btn}} btn-sm tooltipsC' title='Editar' onclick='paso2({{$data->id}},{{$i}})'>
            <span id='iconoai{{$i}}' name='iconoai{{$i}}' class='glyphicon {{$aux_icono}}' style='bottom: 0px;top: 2px;' class='tooltipsC'></span>
        </a>
    </td>
    -->
    <td>
        <a href="{{route('editar_noconformidadrecep', ['id' => $data->id, 'sta_val' => '0'])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
            <i class="fa fa-fw fa-pencil"></i>
        </a>
    </td>
    <!--
    <td>
        <a href="{{route('editar_noconformidad', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
            <i class="fa fa-fw fa-pencil"></i>
        </a>
        <form action="{{route('eliminar_noconformidad', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
            @csrf @method("delete")
            <button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro">
                <i class="fa fa-fw fa-trash text-danger"></i>
            </button>
        </form>
    </td>
    -->
</tr>
<?php
    $i++;
?>
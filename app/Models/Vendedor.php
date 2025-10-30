<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Vendedor extends Model
{
    use SoftDeletes;
    protected $table = "vendedor";
    protected $fillable = [
        'persona_id',
        'usuariodel_id',
        'sta_activo'
    ];

    /* public static function vendedores(){
        $respuesta = array();
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $vendedores = Usuario::join('sucursal_usuario', function ($join) {
                $user = Usuario::findOrFail(auth()->id());
                $sucurArray = $user->sucursales->pluck('id')->toArray();
                $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
                ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                        })
                ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
                ->join('vendedor', function ($join) {
                    $join->on('persona.id', '=', 'vendedor.persona_id')
                        ->where('vendedor.sta_activo', '=', 1);
                })
                ->select([
                    'vendedor.id',
                    'persona.nombre',
                    'persona.apellido'
                ])
                ->where('vendedor.id','=',$vendedor_id)
                ->groupBy('vendedor.id')
                ->get();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
            $vendedores = Usuario::join('sucursal_usuario', function ($join) {
                $user = Usuario::findOrFail(auth()->id());
                $sucurArray = $user->sucursales->pluck('id')->toArray();
                $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
                ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                        })
                ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
                ->join('vendedor', function ($join) {
                    $join->on('persona.id', '=', 'vendedor.persona_id')
                        ->where('vendedor.sta_activo', '=', 1);
                })
                ->select([
                    'vendedor.id',
                    'persona.nombre',
                    'persona.apellido',
                    'vendedor.sta_activo',
                    DB::raw('IF(vendedor.sta_activo = 1, "A", "I") AS vendedor_estado')
                ])
                ->groupBy('vendedor.id')
                ->get();
        }
        $respuesta['vendedor_id'] = $vendedor_id;
        $respuesta['vendedores'] = $vendedores;
        $respuesta['clientevendedorArray'] = $clientevendedorArray;
        return $respuesta;
    } */

    public static function vendedores($estado = null){
        $respuesta = [];
        $user = Usuario::findOrFail(auth()->id());

        // Verificar si el usuario actual tiene un vendedor asociado
        $sql = 'SELECT COUNT(*) AS contador
                FROM vendedor 
                INNER JOIN persona ON vendedor.persona_id = persona.id
                INNER JOIN usuario ON persona.usuario_id = usuario.id
                WHERE usuario.id = ' . auth()->id();

        $counts = DB::select($sql);
        $vendedor_id = '0';

        // Prepara la subconsulta base de vendedores
        $queryBase = Usuario::join('sucursal_usuario', function ($join) {
                $user = Usuario::findOrFail(auth()->id());
                $sucurArray = $user->sucursales->pluck('id')->toArray();
                $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
                    ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
            })
            ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
            ->join('vendedor', 'persona.id', '=', 'vendedor.persona_id')
            ->select([
                'vendedor.id',
                'persona.nombre',
                'persona.apellido',
                'vendedor.sta_activo',
                DB::raw('IF(vendedor.sta_activo = 1, "A", "I") AS vendedor_estado')
            ])
            ->groupBy('vendedor.id')
            // 👇 primero los activos, después los inactivos, y luego orden alfabético
            ->orderBy('persona.nombre', 'asc')
            ->orderBy('persona.apellido', 'asc');

        // Aplicar filtro de estado si corresponde
        if ($estado === 'A') {
            $queryBase->where('vendedor.sta_activo', '=', 1);
        } elseif ($estado === 'I') {
            $queryBase->where('vendedor.sta_activo', '=', 0);
        } elseif ($estado === 'T') {
            // No se aplica filtro (trae todos)
        } elseif (is_null($estado)) {
            // Si no se envía nada, tampoco filtra (igual que 'T')
        }

        // Si el usuario tiene un vendedor asociado
        if ($counts[0]->contador > 0) {
            $vendedor_id = $user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id', $vendedor_id)
                                    ->pluck('cliente_id')
                                    ->toArray();

            // Filtra solo su propio vendedor
            $vendedores = (clone $queryBase)
                ->where('vendedor.id', '=', $vendedor_id)
                ->get();
        } else {
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
            $vendedores = $queryBase->get();
        }

        // Retornar datos
        $respuesta['vendedor_id'] = $vendedor_id;
        $respuesta['vendedores'] = $vendedores;
        $respuesta['clientevendedorArray'] = $clientevendedorArray;

        return $respuesta;
    }

    public function clientess()
    {
        return $this->hasMany(Cliente::class);
    }
    public function sucursalclientedirecs()
    {
        return $this->hasMany(SucursalClienteDirec::class);
    }

    //Relacion inversa a Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    //RELACION UNO A MUCHOS Cotizacion
    public function cotizacions()
    {
        return $this->hasMany(Cotizacion::class);
    }

    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE cliente_vendedor
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_vendedor')->withTimestamps();
    }

    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class);
    }

    public static function selectvendedores(){
        $vendedores1 = Vendedor::vendedores();
        /* $respuesta = "
            <select name='vendedor_id' id='vendedor_id' class='selectpicker form-control vendedor_id'  data-live-search='true' multiple data-actions-box='true'>";
                foreach($vendedores1['vendedores'] as $vendedor){
                    $respuesta .= "
                        <option value='$vendedor->id'>$vendedor->nombre $vendedor->apellido</option>";
                }
        $respuesta .= "</select>"; */
        $respuesta = "
        <select name='vendedor_id' id='vendedor_id' class='selectpicker form-control vendedor_id' data-live-search='true' multiple data-actions-box='true'>";

        foreach ($vendedores1['vendedores'] as $vendedor) {
            // si el vendedor está inactivo (sta_activo == 0), color rojo
            $color = ($vendedor->sta_activo == 0) ? "color:red;" : "";
            $estado = ($vendedor->sta_activo == 1) ? "" : " (Inactivo)";
            
            $respuesta .= "
                <option value='$vendedor->id' style='$color'>
                    $vendedor->nombre $vendedor->apellido $estado
                </option>";
        }

        $respuesta .= "</select>";
        return $respuesta;
    }

    public static function selectvendedoresTodos(){
        $vendedores1 = Vendedor::vendedoresTodos();
         $respuesta = "
            <select name='vendedor_id' id='vendedor_id' class='selectpicker form-control vendedor_id'  data-live-search='true' multiple data-actions-box='true'>";
        foreach($vendedores1['vendedores'] as $vendedor){
            $respuesta .= "
                <option value='$vendedor->id'>$vendedor->nombre $vendedor->apellido</option>";
        }
        $respuesta .= "</select>";
        return $respuesta;
    }

    public static function vendedor_id(){
        $respuesta = array();
        $user = Usuario::findOrFail(auth()->id());
        //$vendedor_id=$user->persona->vendedor->id;
        $sql= 'SELECT COUNT(*) AS contador
                FROM vendedor INNER JOIN persona
                ON vendedor.persona_id=persona.id
                INNER JOIN usuario 
                ON persona.usuario_id=usuario.id
                WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id = $user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
        }
        $respuesta['vendedor_id'] = $vendedor_id;
        $respuesta['clientevendedorArray'] = $clientevendedorArray;

        
        return $respuesta;
    }
    //RELACION MUCHO A MUCHOS producto TRAVES DE producto_vendedor
    public function productos()
    {
        return $this->belongsToMany(Cliente::class, 'producto_vendedor');
    }

    public static function vendedoresTodos(){
        $respuesta = array();
        $user = Usuario::findOrFail(auth()->id());
        $sql= 'SELECT COUNT(*) AS contador
            FROM vendedor INNER JOIN persona
            ON vendedor.persona_id=persona.id
            INNER JOIN usuario 
            ON persona.usuario_id=usuario.id
            WHERE usuario.id=' . auth()->id();
        $counts = DB::select($sql);
        $vendedor_id = '0';
        if($counts[0]->contador>0){
            $vendedor_id=$user->persona->vendedor->id;
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            $vendedores = Usuario::join('sucursal_usuario', function ($join) {
                $user = Usuario::findOrFail(auth()->id());
                $sucurArray = $user->sucursales->pluck('id')->toArray();
                $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
                ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                        })
                ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
                ->join('vendedor','persona.id', '=', 'vendedor.persona_id')
                ->select([
                    'vendedor.id',
                    'persona.nombre',
                    'persona.apellido'
                ])
                ->where('vendedor.id','=',$vendedor_id)
                ->groupBy('vendedor.id')
                ->get();
        }else{
            $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
            $vendedores = Usuario::join('sucursal_usuario', function ($join) {
                $user = Usuario::findOrFail(auth()->id());
                $sucurArray = $user->sucursales->pluck('id')->toArray();
                $join->on('usuario.id', '=', 'sucursal_usuario.usuario_id')
                ->whereIn('sucursal_usuario.sucursal_id', $sucurArray);
                        })
                ->join('persona', 'usuario.id', '=', 'persona.usuario_id')
                ->join('vendedor','persona.id', '=', 'vendedor.persona_id')
                ->select([
                    'vendedor.id',
                    'persona.nombre',
                    'persona.apellido'
                ])
                ->groupBy('vendedor.id')
                ->get();
        }
        $respuesta['vendedor_id'] = $vendedor_id;
        $respuesta['vendedores'] = $vendedores;
        $respuesta['clientevendedorArray'] = $clientevendedorArray;
        return $respuesta;
    }


}

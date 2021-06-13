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

    public static function vendedores(){
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
                    'persona.apellido'
                ])
                ->groupBy('vendedor.id')
                ->get();
        }
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
        return $this->belongsToMany(Cliente::class, 'cliente_vendedor');
    }

    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class);
    }

    public static function selectvendedores(){
        $vendedores1 = Vendedor::vendedores();
         $respuesta = "
            <select name='vendedor_id' id='vendedor_id' class='selectpicker form-control vendedor_id'  data-live-search='true'>
            <option value=''>Todos</option>";
        foreach($vendedores1['vendedores'] as $vendedor){
            $respuesta .= "
                <option value='$vendedor->id'>$vendedor->nombre $vendedor->apellido</option>";
        }
        $respuesta .= "</select>";
        return $respuesta;
    }

}

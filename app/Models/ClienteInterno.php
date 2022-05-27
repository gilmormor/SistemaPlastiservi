<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ClienteInterno extends Model
{
    use SoftDeletes;
    protected $table = "clienteinterno";
    protected $fillable = [
        'rut',
        'razonsocial',
        'direccion',
        'telefono',
        'email',
        'nombrefantasia',
        'giro',
        'comunap_id',
        'formapago_id',
        'plazopago_id',
        'observaciones',
        'usuariodel_id'
    ];
    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE clienteinterno_vendedor
    public function vendedores()
    {
        return $this->belongsToMany(Vendedor::class, 'clienteinterno_vendedor','clienteinterno_id')->withTimestamps();
    }
    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE clienteinterno_sucursal
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'clienteinterno_sucursal','clienteinterno_id')->withTimestamps();
    }
    //Relacion inversa a Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class,'comunap_id');
    }
    //RELACION INVERSA FORMAPAGO
    public function formapago()
    {
        return $this->belongsTo(FormaPago::class);
    }
    //RELACION INVERSA PLAZOPAGO
    public function plazopago()
    {
        return $this->belongsTo(PlazoPago::class);
    }

    public static function clientesxUsuario($vendedor_id = '0'){
        $respuesta = array();
        $user = Usuario::findOrFail(auth()->id());
        //$vendedor_id=$user->persona->vendedor->id;
        if($vendedor_id == '0'){
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
                $clientevendedorArray = ClienteInternoVendedor::where('vendedor_id',$vendedor_id)->pluck('clienteinterno_id')->toArray();
            }else{
                $clientevendedorArray = ClienteInternoVendedor::pluck('clienteinterno_id')->toArray();
            }
        }else{
            $clientevendedorArray = ClienteInternoVendedor::where('vendedor_id',$vendedor_id)->pluck('clienteinterno_id')->toArray(); 
        }
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $clientes = ClienteInterno::select(['clienteinterno.id','clienteinterno.rut','clienteinterno.razonsocial','clienteinterno.direccion','clienteinterno.telefono'])
        ->whereIn('clienteinterno.id' , ClienteInternoSucursal::select(['clienteinterno_sucursal.clienteinterno_id'])
                                ->whereIn('clienteinterno_sucursal.sucursal_id', $sucurArray)
        ->pluck('clienteinterno_sucursal.clienteinterno_id')->toArray())
        ->whereIn('clienteinterno.id',$clientevendedorArray)
        ->get();

        $respuesta['vendedor_id'] = $vendedor_id;
        $respuesta['clientes'] = $clientes;
        $respuesta['sucurArray'] = $sucurArray;

        return $respuesta;
    }
}

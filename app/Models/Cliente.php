<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    use SoftDeletes;
    protected $table = "cliente";
    protected $fillable = [
        'rut',
        'razonsocial',
        'direccion',
        'telefono',
        'email',
        'nombrefantasia',
        'sta_temp',
        'giro_id',
        'giro',
        'regionp_id',
        'provinciap_id',
        'comunap_id',
        'ciudad_id',
        'formapago_id',
        'plazopago_id',
        'contactonombre',
        'contactoemail',
        'contactotelef',
        'mostrarguiasfacturas',
        'finanzascontacto',
        'finanzanemail',
        'finanzastelefono',
        'observaciones',
        'limitecredito'
    ];

    //RELACION DE UNO A MUCHOS Cotizacion
    public function cotizacion()
    {
        return $this->hasMany(Cotizacion::class);
    }
    
    public function clientedirecs()
    {
        return $this->hasMany(ClienteDirec::class);
    }
    //RELACION DE UNO A MUCHOS NotaVenta
    public function notaventa()
    {
        return $this->hasMany(NotaVenta::class);
    }

    //Relacion inversa a Vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    public function clientebloqueados()
    {
        return $this->hasMany(ClienteBloqueado::class);
    }
    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE cliente_vendedor
    public function vendedores()
    {
        return $this->belongsToMany(Vendedor::class, 'cliente_vendedor')->withTimestamps();
    }
    //Relacion inversa a Giros
    public function giro()
    {
        return $this->belongsTo(Giro::class);
    }
    //RELACION MUCHO A MUCHOS CON USUARIO A TRAVES DE cliente_sucursal
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'cliente_sucursal')->withTimestamps();
    }
    //Relacion inversa a Region
    public function region()
    {
        return $this->belongsTo(Region::class,'regionp_id');
    }
    //Relacion inversa a Provincia
    public function provincia()
    {
        return $this->belongsTo(Provincia::class,'provinciap_id');
    }
    //Relacion inversa a Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class,'comunap_id');
    }
    //Relacion inversa a Ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
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
    //RELACION MUCHO A MUCHOS CON PRODUCTO A TRAVES DE cliente_producto
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'cliente_producto')->withTimestamps();
    }

    //RELACION DE UNO A MUCHOS acuerdotecnico_cliente
    public function acuerdotecnico_cliente()
    {
        return $this->hasMany(AcuerdoTecnico_Cliente::class);
    }
    
    //RELACION DE UNO A MUCHOS acuerdotecnicotemp_cliente
    public function acuerdotecnicotemp_cliente()
    {
        return $this->hasMany(AcuerdoTecnicoTemp_Cliente::class);
    }
    //RELACION UNO A UNO clientedesbloqueado
    public function clientedesbloqueado()
    {
        return $this->hasOne(ClienteDesBloqueado::class);
    }
    //RELACION UNO A UNO clientebloqueado
    public function clientebloqueado()
    {
        return $this->hasOne(ClienteBloqueado::class);
    }

    //RELACION UNO A UNO clientedesbloqueado Nota de Venta
    public function clientedesbloqueadonv()
    {
        return $this->hasOne(ClienteDesBloqueadoNV::class);
    }

        //RELACION UNO A UNO datacobranza
        public function datacobranza()
        {
            return $this->hasOne(DataCobranza::class);
        }
    
    //RELACION UNO A UNO clientedesbloqueadopro
    public function clientedesbloqueadopro()
    {
        return $this->hasOne(ClienteDesbloqueadoPro::class);
    }

    
    public static function clientesxUsuario($vendedor_id = '0',$cliente_id = 0){
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
                $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
            }else{
                $clientevendedorArray = ClienteVendedor::pluck('cliente_id')->toArray();
            }
        }else{
            $clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray(); 
        }
        //* Filtro solos los clientes que esten asignados a la sucursal y asignado al vendedor logueado*/
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $clientes = Cliente::select(['cliente.id','cliente.rut','cliente.razonsocial','cliente.direccion','cliente.telefono','cliente.giro_id'])
        ->whereIn('cliente.id' , ClienteSucursal::select(['cliente_sucursal.cliente_id'])
                                ->whereIn('cliente_sucursal.sucursal_id', $sucurArray)
        ->pluck('cliente_sucursal.cliente_id')->toArray())
        ->whereIn('cliente.id',$clientevendedorArray)
        ->get();

        $respuesta['vendedor_id'] = $vendedor_id;
        $respuesta['clientes'] = $clientes;
        $respuesta['sucurArray'] = $sucurArray; //Sucursales del Usuario o vendedor

        if($cliente_id != 0){ //Sucursales que coincidan entre vendedor y cliente
            $sucurcadena = implode(",", $sucurArray);
            $sql= "SELECT sucursal.id,sucursal.nombre
            FROM cliente left JOIN cliente_sucursal
            ON cliente.id=cliente_sucursal.cliente_id and cliente_sucursal.sucursal_id in ($sucurcadena) and isnull(cliente_sucursal.deleted_at)
            INNER JOIN sucursal
            ON cliente_sucursal.sucursal_id=sucursal.id and isnull(sucursal.deleted_at)
            WHERE cliente.id=$cliente_id
            and isnull(cliente.deleted_at)
            order by cliente_sucursal.sucursal_id";
            $respuesta['sucursales'] = DB::select($sql); //Sucursales que coincidan entre vendedor y cliente
    
        }
        if(!isset($respuesta['sucursales'])){
            $sucurcadena = implode(",", $sucurArray);
            $sql= "SELECT sucursal.id,sucursal.nombre
            FROM sucursal
            where sucursal.id in ($sucurcadena) and isnull(sucursal.deleted_at)
            order by sucursal.id";
            $respuesta['sucursales'] = DB::select($sql); //Sucursales que coincidan entre vendedor y cliente

        }

        return $respuesta;
    }

    //San Bernardo //public static function clientesxUsuarioSQLTemp($vendedor_id = '0',$sucursal_id = false){ //Esta funcion es temporal mientras fusiono las 2 ramas de Santa Ester y San Bernardo
    public static function clientesxUsuarioSQL($vendedor_id = '0',$sucursal_id = false){
        $users = Usuario::findOrFail(auth()->id());
        if($sucursal_id){
            $sucurArray = [$sucursal_id];
        }else{
            $sucurArray = $users->sucursales->pluck('id')->toArray();
        }
        $sucurcadena = implode(",", $sucurArray);
        $vendedor_idCond = "true";

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
                $vendedor_id=$users->persona->vendedor->id;
                //$clientevendedorArray = ClienteVendedor::where('vendedor_id',$vendedor_id)->pluck('cliente_id')->toArray();
                $vendedor_idCond = "cliente_vendedor.vendedor_id = " . $vendedor_id;
            }
        }else{
            $vendedor_idCond = "cliente_vendedor.vendedor_id = " . $vendedor_id;
        }

        $sqlclienteVendedor = "SELECT cliente_vendedor.cliente_id
                                FROM cliente_vendedor
                                WHERE $vendedor_idCond
                                and isnull(cliente_vendedor.deleted_at)
                                GROUP BY cliente_vendedor.cliente_id";

        $sql = "SELECT cliente.id,cliente.rut,cliente.razonsocial,cliente.direccion,cliente.telefono,cliente.giro_id
                FROM cliente inner join cliente_sucursal
                on cliente.id = cliente_sucursal.cliente_id and isnull(cliente.deleted_at) and isnull(cliente_sucursal.deleted_at)
                where cliente.id in ($sqlclienteVendedor)
                and cliente_sucursal.sucursal_id in ($sucurcadena)
                GROUP BY cliente.id
                ORDER BY cliente.id;";
        $datas = DB::select($sql);
        return $datas;
    }

    public static function valBloqCliSisCob(&$cliente,$request,$aux_consultadeuda){
        //dd(count($cliente));
        if(count($cliente) > 0){
            if(isset($cliente[0]->cliente_id)){
                $aux_cliente_id = $cliente[0]->cliente_id;
            }else{
                $aux_cliente_id = $cliente[0]->id;
            }
            $staBloqueo = clienteBloqueado($aux_cliente_id,$aux_consultadeuda,$request);
            if(isset($staBloqueo["error"])){
                return $staBloqueo;
            }
            $cliente[0]->descripcion = $staBloqueo ["bloqueo"];
            $cliente[0]->TDeuda = 0;
            if(isset($staBloqueo["datacobranza"]["TDeuda"])){
                $cliente[0]->TDeuda = $staBloqueo["datacobranza"]["TDeuda"];
            }
            /* $clientebus = Cliente::findOrFail($cliente[0]->id);
            if($clientebus->clientedesbloqueado){
                $cliente[0]->descripcion = null;
            }else{
                if(is_null($cliente[0]->clientebloqueado_descripcion)){
                    $rut = isset($request->rut) ? $request->rut : null;
                    $datCobranza = Dte::deudaClienteSisCobranza($rut);
                    //dd($datCobranza);
                    if($datCobranza["TDeuda"] > 0 and $datCobranza["TDeuda"] >= $datCobranza["limitecredito"]){
                        $cliente[0]->descripcion = "Supero limite de Crédito: " . number_format($datCobranza["limitecredito"], 0, ',', '.') . "\nDeuda: " . number_format($datCobranza["TDeuda"], 0, ',', '.');
                    }else{
                        if($datCobranza["TDeudaFec"] > 0){
                            $cliente[0]->descripcion = "Facturas Vencidas: " . $datCobranza["NroFacDeu"];
                        }
                    }
                }    
            } */
        }
    }

    public static function clientesxUsuarioMejorado($request){
        //dd($request);
        $users = Usuario::findOrFail(auth()->id());
        $sucurUsuArray = $users->sucursales->pluck('id')->toArray();
        $sucurUsuCadena = implode(",", $sucurUsuArray);
        //dd($sucurUsuArray);
        $condUsuvendedor_id = "true";
        if($users->persona->vendedor){
            $vendedor_id=$users->persona->vendedor->id;
            $condUsuvendedor_id = "cliente_vendedor.vendedor_id = $vendedor_id";
        }

        if(!isset($request->id) or empty($request->id)){
            $aux_condcliente_id = " true";
        }else{
            $aux_condcliente_id = "cliente.id = $request->id";
        }

        if(!isset($request->sucursal_id) or empty($request->sucursal_id) or ($request->sucursal_id == "")){
            $aux_sucursal_idCond = "true";
        }else{
            $aux_sucursal_idCond = "cliente_sucursal.sucursal_id = $request->sucursal_id";
        }

        if(!isset($request->vendedor_id) or empty($request->vendedor_id)){
            $aux_condvendedor_id = " true";
        }else{
            $aux_vendedor_id = implode(",", $request->vendedor_id);
            $aux_condvendedor_id = "cliente_vendedor.vendedor_id in ($aux_vendedor_id)";
        }
 
        $sql = "SELECT cliente.id,cliente.rut,cliente.razonsocial,cliente.direccion,cliente.telefono,cliente.giro_id
                FROM cliente inner join cliente_sucursal
                on cliente.id = cliente_sucursal.cliente_id and isnull(cliente.deleted_at) and isnull(cliente_sucursal.deleted_at)
                LEFT JOIN cliente_vendedor
                ON cliente_vendedor.cliente_id = cliente.id and isnull(cliente_vendedor.deleted_at)
                where $aux_condcliente_id
                and cliente_sucursal.sucursal_id in ($sucurUsuCadena)
                and $aux_sucursal_idCond
                and $condUsuvendedor_id
                and $aux_condvendedor_id
                GROUP BY cliente.id
                ORDER BY cliente.id;";
        //dd($sql);
        $datas = DB::select($sql);
        return $datas;
    }
    
}

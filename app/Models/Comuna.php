<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comuna extends Model
{
    use SoftDeletes;
    protected $table = "comuna";
    protected $fillable = [
            'nombre',
            'provincia_id',
            'ciudad_id',
            'usuariodel_id'
    ];
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }
    //RELACION INVERSA PARA BUSCAR EL PADRE
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
    //Relacion de uno a Muchos con Cliente
    public function clientes()
    {
        return $this->hasMany(Cliente::class,'comunap_id','comuna_id');
    }
    //Relacion de uno a Muchos con ClienteTemp
    public function clientetemps()
    {
        return $this->hasMany(ClienteTemp::class,'comunap_id','comuna_id');
    }
    //Relacion de uno a Muchos con notaventa
    public function notaventas()
    {
        return $this->hasMany(NotaVenta::class,'comunap_id','comunaentrega_id');
    }
    //Relacion de uno a Muchos con despachoSol
    public function despachosols()
    {
        return $this->hasMany(DespachoSol::class,'comunap_id','comunaentrega_id');
    }
    //Relacion de uno a Muchos con despachoOrd
    public function despachoords()
    {
        return $this->hasMany(DespachoOrd::class,'comunap_id','comunaentrega_id');
    }

    public static function selectcomunas(){
        $comunas = Comuna::orderBy('id')->get();
        $respuesta = "
        <select name='comuna_id' id='comuna_id' multiple class='selectpicker form-control comuna_id' data-live-search='true' multiple data-actions-box='true'>";
            foreach($comunas as $comuna){
                $respuesta .= "
                    <option value='$comuna->id'>$comuna->nombre</option>";
            }
        $respuesta .= "</select>";
        return $respuesta;
    }

}

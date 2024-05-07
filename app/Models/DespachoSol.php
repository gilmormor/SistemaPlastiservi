<?php

namespace App\Models;

use App\Models\Seguridad\Usuario;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoSol extends Model
{
    use SoftDeletes;
    protected $table = "despachosol";
    protected $fillable = [
        'notaventa_id',
        'sucursal_id',
        'usuario_id',
        'sucursal_id',
        'fechahora',
        'comunaentrega_id',
        'tipoentrega_id',
        'plazoentrega',
        'lugarentrega',
        'contacto',
        'contactoemail',
        'contactotelf',
        'observacion',
        'fechaestdesp',
        'tipoguiadesp',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoSolDet
    public function despachosoldets()
    {
        return $this->hasMany(DespachoSolDet::class,'despachosol_id');
    }

    //Relacion inversa a NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }

    //RELACION DE UNO A MUCHOS DespachoSolOrd
    public function despachoords()
    {
        return $this->hasMany(DespachoOrd::class,'despachosol_id');
    }
    
    //Relacion inversa a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    //Relacion inversa a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comunaentrega()
    {
        return $this->belongsTo(Comuna::class,'comunaentrega_id');
    }
    //RELACION DE UNO A uno DespachoSolAnul
    public function despachosolanul()
    {
        return $this->hasOne(DespachoSolAnul::class,'despachosol_id');
    }

    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }
    
    //RELACION DE MUCHOS A MUCHOS CON TABLA INVMOV
    public function invmovs()
    {
        return $this->belongsToMany(InvMov::class, 'despachosol_invmov','despachosol_id','invmov_id')->withTimestamps();
    }
    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    
    //RELACION de uno a uno despachosoldte
    public function despachosoldte()
    {
        return $this->hasOne(DespachoSolDTE::class,"despachosol_id");
    }

    //RELACION de uno a uno despachosoldev
    public function despachosoldev()
    {
        return $this->hasOne(DespachoSolDev::class,"despachosol_id");
    }

    //RELACION de uno a uno DespachoSolEnvOrdDesp
    public function despachosolenvorddesp()
    {
        return $this->hasOne(DespachoSolEnvOrdDesp::class,"despachosol_id");
    }
    

}

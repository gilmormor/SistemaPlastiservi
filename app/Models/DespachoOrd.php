<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DespachoOrd extends Model
{
    use SoftDeletes;
    protected $table = "despachoord";
    protected $fillable = [
        'despachosol_id',
        'notaventa_id',
        'usuario_id',
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
        'guiadespacho',
        'guiadespachofec',
        'numfactura',
        'fechafactura',
        'numfacturafec',
        'despachoobs_id',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdDet
    public function despachoorddets()
    {
        return $this->hasMany(DespachoOrdDet::class,'despachoord_id');
    }

    //Relacion inversa a DespachoSol
    public function despachosol()
    {
        return $this->belongsTo(DespachoSol::class);
    }

    //Relacion inversa a NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
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

    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }

    //RELACION DE UNO A MUCHOS DespachoOrdAnul
    public function despachoordanul()
    {
        return $this->hasOne(DespachoOrdAnul::class,'despachoord_id');
    }

    //RELACION DE UNO A MUCHOS DespachoOrdRec
    public function despachoordrecs()
    {
        return $this->hasOne(DespachoOrdRec::class,'despachoord_id');
    }
    //RELACION DE MUCHOS A MUCHOS CON TABLA INVMOV
    public function invmovs()
    {
        return $this->belongsToMany(InvMov::class, 'despachoord_invmov','despachoord_id','invmov_id')->withTimestamps();
    }

    //Relacion uno a Muchos con guiadesp
    public function guiadesp()
    {
        return $this->hasMany(GuiaDesp::class,"despachoord_id");
    }
    
    //RELACION DE UNO A MUCHOS despachoordanulguiafact
    public function despachoordanulguiafacts()
    {
        return $this->hasMany(DespachoOrdAnulGuiaFact::class,'despachoord_id');
    }


}

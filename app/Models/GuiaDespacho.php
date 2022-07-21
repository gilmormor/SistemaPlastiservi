<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiaDespacho extends Model
{
    use SoftDeletes;
    protected $table = "guiadespacho";
    protected $fillable = [
        'despachoord_id',
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
        'obs',
        'fechaestdesp',
        'usuariodel_id'
    ];

        //RELACION DE UNO A MUCHOS DespachoOrdDet
        public function guiadespachodets()
        {
            return $this->hasMany(guiadespachoDet::class,'guiadespacho_id');
        }
    
        //Relacion inversa a DespachoOrd
        public function despachoord()
        {
            return $this->belongsTo(DespachoOrd::class);
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
/*    
        //RELACION DE UNO A MUCHOS DespachoOrdAnul
        public function despachoordanul()
        {
            return $this->hasOne(DespachoOrdAnul::class,'despachoord_id');
        }
*/
/*
        //RELACION DE UNO A MUCHOS DespachoOrdRec
        public function despachoordrecs()
        {
            return $this->hasOne(DespachoOrdRec::class,'despachoord_id');
        }
*/
}

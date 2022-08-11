<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiaDesp extends Model
{
    use SoftDeletes;
    protected $table = "guiadesp";
    protected $fillable = [
        'fechahora',
        'despachoord_id',
        'notaventa_id',
        'rut',
        'razonsocial',
        'giro',
        'clidir',
        'comuna',
        'ciudad',
        'email',
        'telefono',
        'cliente_id',
        'contacto',
        'contactoemail',
        'contactotelf',
        'obs',
        'formapago_id',
        'vendedor_id',
        'plazoentrega',
        'fechaestdesp',
        'lugarentrega',
        'plazopago_id',
        'tipoentrega_id',
        'comuna_id',
        'comunaentrega_id',
        'neto',
        'piva',
        'iva',
        'total',
        'traslado',
        'ot',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'usuario_id',
        'usuariodel_id'
    ];

        //RELACION DE UNO A MUCHOS GuiaDespDet
        public function guiadespdets()
        {
            return $this->hasMany(GuiaDespDet::class,'guiadespacho_id');
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

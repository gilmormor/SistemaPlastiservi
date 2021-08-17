<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use SplFileInfo;

class NotaVenta extends Model
{
    use SoftDeletes;
    protected $table = "notaventa";
    protected $fillable = [
        'sucursal_id',
        'cotizacion_id',
        'fechahora',
        'direccioncot',
        'email',
        'telefono',
        'cliente_id',
        'clientedirec_id',
        'contacto',
        'contactoemail',
        'contactotelf',
        'observacion',
        'formapago_id',
        'vendedor_id',
        'plazoentrega',
        'lugarentrega',
        'plazopago_id',
        'tipoentrega_id',
        'region_id',
        'provincia_id',
        'comuna_id',
        'comunaentrega_id',
        'giro_id',
        'neto',
        'piva',
        'iva',
        'total',
        'oc_id',
        'oc_file',
        'usuario_id',
        'aprobstatus',
        'aprobusu_id',
        'aprobfechahora',
        'visto',
        'usuariodel_id',
        'stadestino'
    ];

    public static function setFotonotaventa($foto,$notaventa_id,$request, $actual = false){
        //dd($foto);
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/notaventa/$actual");
            }
            $file = $request->file('oc_file');
            $nombre = $file->getClientOriginalName();
            $info = new SplFileInfo($nombre);
            $ext = strtolower($info->getExtension()); //Obtener extencion de un archivo
            //$imageName = Str::random(10) . '.jpg';
            $imageName = $notaventa_id . '.' . $ext;
            //dd($imageName);
            //      $imagen = Image::make($foto)->encode('jpg', 75);
            //$imagen->fit(530, 470); //Fit() SUpuestamente mantiene la proporcion de la imagen
            /*$imagen->resize(530, 470, function ($constraint) {
                $constraint->upsize();
            });*/
            //Storage::disk('public')->put("imagenes/notaventa/$imageName", $imagen->stream());
            //Storage::disk('public')->put("imagenes/notaventa/$imageName", $file);
            $file->move(public_path() . "/storage/imagenes/notaventa/" , $imageName);
            //$request->file('')
            return $imageName;
        } else {
            return false;
        }
    }

    //RELACION DE UNO A MUCHOS NotaVentaDetalle
    public function notaventadetalles()
    {
        return $this->hasMany(NotaVentaDetalle::class,'notaventa_id');
    }
    //Relacion inversa a Cotizacion
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    //RELACION INVERSA Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    //RELACION INVERSA ClienteDirecc
    public function clientedirec()
    {
        return $this->belongsTo(ClienteDirec::class);
    }
    //Relacion inversa a FormaPago
    public function formapago()
    {
        return $this->belongsTo(FormaPago::class);
    }
    //Relacion inversa a Vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    //Relacion inversa a PlazoPago
    public function plazopago()
    {
        return $this->belongsTo(PlazoPago::class);
    }
    //Relacion inversa a Comuna
    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }
    public function comunaentrega()
    {
        return $this->belongsTo(Comuna::class,'comunaentrega_id');
    }
    //Relacion inversa a Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
    //Relacion inversa a TipoEntrega
    public function tipoentrega()
    {
        return $this->belongsTo(TipoEntrega::class);
    }
    //RELACION DE UNO A MUCHOS NotaVentaCerrada
    public function notaventacerradas()
    {
        return $this->hasMany(NotaVentaCerrada::class,'notaventa_id');
    }
    
    
}

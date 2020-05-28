<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class Certificado extends Model
{
    use SoftDeletes;
    protected $table = "certificado";
    protected $fillable = [
        'descripcion',
        'foto'
    ];

    public static function setFotoCertificado($foto, $actual = false){
        //dd($foto);
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/certificado/$actual");
            }
            $imageName = Str::random(10) . '.jpg';
            //$imageName = $certificado . '.jpg';
            $imagen = Image::make($foto)->encode('jpg', 75);
            $imagen->resize(530, 470, function ($constraint) {
                $constraint->upsize();
            });
            Storage::disk('public')->put("imagenes/certificado/$imageName", $imagen->stream());
            //$request->file('')
            return $imageName;
        } else {
            return false;
        }
    }

    public function acuerdotectemps()
    {
        return $this->belongsToMany(Acuerdotectemp::class, 'acuerdotectemp_certificado');
    }

    //RELACION MUCHOS A MUCHOS A TRAVES DE noconformidad_certificado
    public function noconformidades()
    {
        return $this->belongsToMany(NoConformidad::class, 'noconformidad_certificado');
    }
}

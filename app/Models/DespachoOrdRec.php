<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

class DespachoOrdRec extends Model
{
    use SoftDeletes;
    protected $table = "despachoordrec";
    protected $fillable = [
        'despachoord_id',
        'usuario_id',
        'fechahora',
        'despachoordrecmotivo_id',
        'obs',
        'documento_id',
        'documento_file',
        'anulada',
        'usuariodel_id'
    ];

    //RELACION DE UNO A MUCHOS DespachoOrdRecDet
    public function despachoordrecdets()
    {
        return $this->hasMany(DespachoOrdRecDet::class,'despachoordrec_id');
    }
    //Relacion inversa a despachoord
    public function despachoord()
    {
        return $this->belongsTo(DespachoOrd::class);
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
    //Relacion inversa a DespachoOrdRecMotivo
    public function despachoordrecmotivo()
    {
        return $this->belongsTo(DespachoOrdRecMotivo::class);
    }

    public static function setFoto($foto,$id,$request, $actual = false){
        if ($foto) {
            if ($actual) {
                Storage::disk('public')->delete("imagenes/despachorechazo/$actual");
            }
            $file = $request->file('documento_file');
            //dd($file);
            $nombre = $file->getClientOriginalName();
            $info = new SplFileInfo($nombre);
            $ext = strtolower($info->getExtension()); //Obtener extencion de un archivo
            $imageName = $id . '.' . $ext;
            $file->move(public_path() . "/storage/imagenes/despachorechazo/" , $imageName);
            return $imageName;
        } else {
            return false;
        }
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MateriaPrima extends Model
{
    use SoftDeletes;
    protected $table = "materiaprima";
    protected $fillable = [
        'nombre',
        'desc',
        'descfact',
        'pe',
        // Aptitud para contacto con alimentos: la declara CC por materia prima y
        // termina impresa en las etiquetas de lote y de muestra.
        'staaptoalimento',
        'usuariodel_id'
    ];

    /** Apta para contacto con alimentos */
    const APTO_SI = 1;
    /** No apta para contacto con alimentos */
    const APTO_NO = 0;

    /**
     * "Sin definir" llega del formulario como cadena vacía, y MySQL la guardaría
     * como 0 en un tinyint, es decir "NO APTO" — afirmando algo que nadie evaluó.
     * Se fuerza a null para que conserve el estado sin definir.
     */
    public function setStaaptoalimentoAttribute($valor)
    {
        $this->attributes['staaptoalimento'] = ($valor === '' || $valor === null)
            ? null
            : (int) $valor;
    }

    /**
     * Texto que va en la etiqueta según la aptitud declarada.
     *
     * null (sin clasificar por CC) devuelve "SIN DEFINIR": nunca se afirma que un
     * material es apto o no apto sin que alguien lo haya evaluado, porque esto se
     * imprime en una etiqueta que llega al cliente.
     */
    public static function textoAptoAlimento($sta)
    {
        if ($sta === null || $sta === '') {
            return 'SIN DEFINIR';
        }
        return ((int) $sta === self::APTO_SI)
            ? 'APTO PARA CONTACTO CON ALIMENTOS'
            : 'NO APTO PARA CONTACTO CON ALIMENTOS';
    }

    /**
     * Opciones del select de la pantalla de materia prima.
     */
    public static function opcionesAptoAlimento()
    {
        return [
            ''               => 'Sin definir',
            self::APTO_SI    => 'Apto para contacto con alimentos',
            self::APTO_NO    => 'No apto para contacto con alimentos',
        ];
    }

    /**
     * Texto de aptitud para el producto de un lote: producto → acuerdo técnico →
     * materia prima. Devuelve "SIN DEFINIR" también cuando el producto no tiene
     * acuerdo técnico o el acuerdo no tiene materia prima cargada, que para el
     * caso es lo mismo: no hay respaldo para afirmar nada.
     */
    public static function textoAptoAlimentoPorProducto($producto_id)
    {
        if (!$producto_id) {
            return self::textoAptoAlimento(null);
        }

        $r = DB::selectOne("
            SELECT mp.staaptoalimento
            FROM   acuerdotecnico at
            INNER  JOIN materiaprima mp ON mp.id = at.at_materiaprima_id
                                       AND ISNULL(mp.deleted_at)
            WHERE  at.producto_id = ?
              AND  ISNULL(at.deleted_at)
            ORDER  BY at.id DESC
            LIMIT  1
        ", [$producto_id]);

        return self::textoAptoAlimento($r ? $r->staaptoalimento : null);
    }

    public function acuerdotecnicotemps()
    {
        return $this->hasMany(AcuerdoTecnicoTemp::class);
    }
    public function acuerdotecnicos()
    {
        return $this->hasMany(AcuerdoTecnico::class);
    }

}

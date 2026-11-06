<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class NotaVentaDetalle extends Model
{
    use SoftDeletes;
    protected $table = "notaventadetalle";
    protected $fillable = [
        'producto_id',
        'notaventa_id',
        'cotizaciondetalle_id',
        'cant',
        'cantgrupo',
        'cantxgrupo',
        'unidadmedida_id',
        'descuento',
        'preciounit',
        'peso',
        'precioneto',
        'iva',
        'total',
        'usuariodel_id',
        'precioxkilo',
        'precioxkiloreal',
        'totalkilos',
        'subtotal',
        'producto_nombre',
        'ancho',
        'largo',
        'espesor',
        'diametro',
        'categoriaprod_id',
        'claseprod_id',
        'grupoprod_id',
        'color_id',
        'obs',
        'usuariodel_id'
    ];
    
    //RELACION INVERSA NotaVenta
    public function notaventa()
    {
        return $this->belongsTo(NotaVenta::class);
    }
    //Relacion inversa a UnidadMedida
    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }
    //Relacion inversa a Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    //Relacion inversa a CotizacionDetalle
    public function cotizaciondetalle()
    {
        return $this->belongsTo(CotizacionDetalle::class);
    }

    //RELACION DE UNO A MUCHOS DespachoSolDet
    public function despachosoldets()
    {
        return $this->hasMany(DespachoSolDet::class,"notaventadetalle_id");
    }
    //RELACION DE UNO A MUCHOS DespachoOrdDet
    public function despachoorddets()
    {
        return $this->hasMany(DespachoOrdDet::class);
    }

    //RELACION de uno a uno acuerdotecnico
    public function acuerdotecnico()
    {
        return $this->hasOne(AcuerdoTecnico::class,"at_notaventadetalle_id");
    }

    //RELACION de uno a uno acuerdotecnicotemp
    public function acuerdotecnicotempunoauno()
    {
        return $this->hasOne(AcuerdoTecnicoTemp::class,"at_cotizaciondetalle_id","cotizaciondetalle_id");
    }
    //Relacion uno a uno con notaventadetalleext
    public function notaventadetalleext()
    {
        return $this->hasOne(NotaVentaDetalleExt::class,"notaventadetalle_id");
    }

    //Relacion inversa a CategoriaProd
    public function categoriaprod()
    {
        return $this->belongsTo(CategoriaProd::class);
    }

    public function doccompdets()
    {
        return $this->hasMany(DocCompDet::class, 'origendet_id', 'id')
            ->where('origentipo', 'NV');
    }
    public function docinsumodets()
    {
        return $this->hasMany(DocInsumoDet::class, 'origendet_id', 'id')
            ->where('origentipo', 'NV');
    }

    /**
     * Cantidades solicitadas y despachadas de varios items de nota de venta,
     * en dos consultas, para no repetirlas una vez por item.
     *
     * Replican el cuerpo de las vistas vista_sumsoldespdet y
     * vista_sumorddespxnvdetid, pero con el filtro por notaventadetalle_id
     * dentro: consultar esas vistas con un WHERE obliga a MySQL 5.7 a
     * materializarlas completas -unos 5,8 s cada vez en el caso de
     * vista_sumorddespxnvdetid, que ademas contiene otra vista anidada-,
     * mientras que con el filtro adentro responden en milisegundos.
     *
     * OJO: si se modifica alguna de esas dos vistas, hay que revisar este
     * metodo, porque repite su logica. La columna cantrec se sigue leyendo de
     * vista_sumrecxnotaventadet_id para no duplicar tambien esa.
     *
     * Devuelve dos arreglos indexados por notaventadetalle_id:
     *   ['cantsoldesp' => [id => cantidad], 'cantdesp' => [id => cantidad]]
     * Los items sin movimientos no aparecen; quien lo use debe tomarlos como 0,
     * igual que hacia el codigo anterior cuando la consulta no devolvia filas.
     */
    public static function sumasDespachoPorItem($ids)
    {
        $aux_resultado = ['cantsoldesp' => [], 'cantdesp' => []];

        // solo ids numericos, y sin repetir
        $aux_ids = [];
        foreach ((array) $ids as $aux_id) {
            if (is_numeric($aux_id)) { $aux_ids[(int) $aux_id] = true; }
        }
        if (empty($aux_ids)) { return $aux_resultado; }
        $aux_lista = implode(',', array_keys($aux_ids));

        // cantidad solicitada: cuerpo de vista_sumsoldespdet con el filtro adentro
        $aux_sql = "SELECT dsd.notaventadetalle_id,
                           SUM(dsd.cantsoldesp) AS cantsoldesp
                    FROM despachosol ds
                      INNER JOIN despachosoldet dsd ON ds.id = dsd.despachosol_id
                      INNER JOIN notaventadetalle nvd ON dsd.notaventadetalle_id = nvd.id
                      INNER JOIN producto p ON nvd.producto_id = p.id
                    WHERE ds.id NOT IN (SELECT despachosol_id FROM despachosolanul)
                      AND ISNULL(ds.deleted_at)
                      AND dsd.notaventadetalle_id IN ($aux_lista)
                    GROUP BY dsd.notaventadetalle_id, ds.notaventa_id";
        foreach (DB::select($aux_sql) as $aux_fila) {
            // se conserva la primera, como hacia el [0] del codigo anterior
            if (!array_key_exists($aux_fila->notaventadetalle_id, $aux_resultado['cantsoldesp'])) {
                $aux_resultado['cantsoldesp'][$aux_fila->notaventadetalle_id] = $aux_fila->cantsoldesp;
            }
        }

        // cantidad despachada: cuerpo de vista_sumorddespxnvdetid con el filtro adentro
        $aux_sql = "SELECT dod.notaventadetalle_id,
                           (SUM(dod.cantdesp) - COALESCE(rec.cantrec, 0)) AS cantdesp
                    FROM despachoord od
                      INNER JOIN despachoorddet dod ON dod.despachoord_id = od.id
                      INNER JOIN notaventadetalle nvd ON nvd.id = dod.notaventadetalle_id
                           AND ISNULL(nvd.deleted_at)
                      LEFT JOIN vista_sumrecxnotaventadet_id rec ON rec.id = dod.notaventadetalle_id
                      LEFT JOIN despachoordanul da ON da.despachoord_id = od.id
                    WHERE od.guiadespacho IS NOT NULL
                      AND ISNULL(od.deleted_at)
                      AND ISNULL(da.despachoord_id)
                      AND dod.notaventadetalle_id IN ($aux_lista)
                    GROUP BY dod.notaventadetalle_id";
        foreach (DB::select($aux_sql) as $aux_fila) {
            if (!array_key_exists($aux_fila->notaventadetalle_id, $aux_resultado['cantdesp'])) {
                $aux_resultado['cantdesp'][$aux_fila->notaventadetalle_id] = $aux_fila->cantdesp;
            }
        }

        return $aux_resultado;
    }
}
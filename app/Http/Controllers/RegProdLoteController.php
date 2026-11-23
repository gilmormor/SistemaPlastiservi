<?php

namespace App\Http\Controllers;

use App\Models\EtapaProd;
use App\Models\OpDet;
use App\Models\OpDetRegProd;
use App\Models\OpDetRegProdTemp;
use App\Models\OpDetRegProdTempOrigen;
use App\Models\Seguridad\Usuario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * REGISTRO DE PRODUCCION POR LOTE — entrada rapida.
 *
 * El flujo normal pide elegir la etapa, buscar el item en un listado y recien
 * ahi registrar. En las reuniones salio que son demasiados pasos para lo que el
 * operario tiene delante: un rollo con su etiqueta pegada.
 *
 * Aqui escribe el numero de lote y el sistema deduce lo demas: a que etapa
 * continua ese material, que item de la OP le corresponde y de que lote viene.
 *
 * IMPORTANTE: este controlador NO registra produccion. Solo resuelve el camino
 * y entrega el control a la pantalla de siempre (OpDetRegProdTempController),
 * que es la que tiene las validaciones de saldo, los lotes de origen, el margen
 * de exceso por maquina, los campos por etapa y el control de edicion
 * concurrente. Duplicar todo eso aqui habria significado mantener dos copias
 * que tarde o temprano dejan de comportarse igual.
 */
class RegProdLoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Pantalla con el buscador de lote. */
    public function index()
    {
        can('crear-registro-produccion');
        /* Se limpia el lote fijado de una busqueda anterior: si el operario
           vuelve aqui, empieza de cero. */
        session()->forget('regprodlote_lote_id');
        session()->forget('regprodlote_edit');

        /* Lo que el supervisor le rechazo se muestra aqui mismo, para que no
           tenga que ir a buscarlo a otra pantalla. Solo lo suyo. */
        $tablas['rechazados'] = $this->rechazadosPropios();

        return view('regprodlote.index', compact('tablas'));
    }

    /**
     * Registros rechazados por el supervisor que puede corregir ESTE usuario.
     *
     * Tres filtros, y los tres hacen falta: solo los que el creo (usuario_id),
     * solo las etapas a las que tiene acceso y solo sus sucursales. Un operario
     * no tiene por que ver -ni corregir- el rechazo de un companero.
     */
    private function rechazadosPropios()
    {
        $aux_etapas = collect(EtapaProd::etapasProdxPersona())
            ->pluck('etapaprod_id')->unique()->values()->toArray();
        $aux_sucur  = Usuario::findOrFail(auth()->id())->sucursales->pluck('id')->toArray();
        if (!count($aux_etapas) or !count($aux_sucur)) {
            return [];
        }

        return DB::table('opdetregprodtemp as t')
            ->join('opdet as d', 'd.id', '=', 't.opdet_id')
            ->join('etapaprod as e', 'e.id', '=', 't.etapaprod_id')
            ->where('t.usuario_id', auth()->id())
            ->where('t.aprobstatus', 3)
            ->whereNull('t.deleted_at')
            ->whereIn('t.etapaprod_id', $aux_etapas)
            ->whereIn('t.sucursal_id', $aux_sucur)
            ->orderBy('t.id', 'desc')
            ->select(
                't.id', 't.opdet_id', 't.kgprod', 't.kgscrap', 't.aprobobs',
                'd.op_id', 'e.nombre as etapa_nombre'
            )
            ->get();
    }

    /**
     * Abre un registro rechazado para corregirlo.
     *
     * No se enlaza directo al formulario de edicion para que nadie corrija el
     * rechazo de otro cambiando el numero en la URL: aqui se comprueba que el
     * registro sea suyo y siga rechazado antes de dejarlo entrar.
     */
    public function corregir($id)
    {
        can('editar-registro-produccion');

        $temp = OpDetRegProdTemp::find((int) $id);
        if (!$temp || $temp->deleted_at) {
            return $this->volver('El registro ' . $id . ' no existe.');
        }
        if ($temp->usuario_id != auth()->id()) {
            return $this->volver('El registro ' . $temp->id . ' lo creo otro usuario: solo quien lo registro puede corregirlo.');
        }
        if ($temp->aprobstatus != 3) {
            return $this->volver('El registro ' . $temp->id . ' ya no esta rechazado.');
        }

        /* La marca le dice a actualizar() que al guardar debe reenviarlo a
           aprobacion y devolverlo aqui, en vez de dejarlo rechazado en index01. */
        session(['regprodlote_edit' => $temp->id]);
        session()->save();

        /* Mismo formato de URL que el boton Editar del listado: id&timestamp,
           que es el control de edicion concurrente de esa pantalla. */
        return redirect('opdetregprodtemp/' . $temp->id . '&' . strtotime($temp->updated_at) . '/editar');
    }

    /**
     * Resuelve el lote y manda a la pantalla de registro.
     *
     * Todo lo que puede salir mal se responde con un mensaje que explique el
     * motivo: el operario tiene que entender por que no puede seguir, no
     * encontrarse una pantalla vacia.
     */
    public function buscar(Request $request)
    {
        can('crear-registro-produccion');

        $aux_lote_id = (int) $request->input('lote_id');
        if (!$aux_lote_id) {
            return $this->volver('Escriba el número de lote.');
        }

        $lote = OpDetRegProd::find($aux_lote_id);
        if (!$lote) {
            return $this->volver('No existe el lote ' . $aux_lote_id . '.');
        }
        if ($lote->aprobstatus != 2) {
            return $this->volver('El lote ' . $lote->id . ' todavía no está aprobado por el supervisor.');
        }
        if ($lote->es_muestra) {
            return $this->volver('El lote ' . $lote->id . ' es una muestra física y no se procesa.');
        }

        $opdetOrigen = $lote->opdet;
        if (!$opdetOrigen || !$opdetOrigen->op) {
            return $this->volver('El lote ' . $lote->id . ' no tiene una orden de producción válida.');
        }

        /* Etapa en la que continua el material: la siguiente de la OP por el
           campo orden. Mismo criterio que usan la etiqueta de lote y el modulo
           de Etapa Externa. */
        $aux_siguiente = $this->opdetSiguiente($opdetOrigen);
        if (!$aux_siguiente) {
            return $this->volver('El lote ' . $lote->id . ' ya está en la última etapa de su orden de producción ('
                . ($opdetOrigen->areaproduccionsucetapaprod->etapaprod->nombre ?? '—') . '): no hay etapa siguiente que registrar.');
        }

        $aux_aps        = $aux_siguiente->areaproduccionsucetapaprod;
        $aux_etapa_id   = $aux_aps->etapaprod_id;
        $aux_etapa_nom  = $aux_aps->etapaprod->nombre ?? '—';

        /* Acceso del usuario a esa etapa. etapasProdxPersona() ya cruza
           personaetapaprod -> persona -> usuario y ademas limita a las
           sucursales del usuario, que es justo lo que hace falta. */
        $aux_misEtapas = collect(EtapaProd::etapasProdxPersona());
        if (!$aux_misEtapas->contains('etapaprod_id', $aux_etapa_id)) {
            return $this->volver('El lote ' . $lote->id . ' continúa en ' . $aux_etapa_nom
                . ' y usted no tiene acceso a esa etapa. Consulte con el administrador.');
        }

        /* Que quede material. Un lote se procesa en varias tandas, asi que lo
           unico que se exige es que no este agotado. */
        $aux_disp = OpDetRegProdTempOrigen::lotesDisponibles($aux_siguiente)
            ->firstWhere('id', $lote->id);
        if (!$aux_disp) {
            return $this->volver('El lote ' . $lote->id . ' no está disponible para ' . $aux_etapa_nom . '.');
        }
        if ((float) $aux_disp['disponible_kg'] <= 0) {
            return $this->volver('El lote ' . $lote->id . ' ya fue procesado por completo: no le queda saldo.');
        }

        /* Se deja todo listo y se entrega a la pantalla de registro. Son las
           mismas claves de sesion que usa crearini() en el flujo normal, mas el
           lote fijado para que la pantalla muestre solo ese. */
        session([
            'etapaprod_id'           => $aux_etapa_id,
            'opdet_id'               => $aux_siguiente->id,
            'opdet_updatednum_at'    => strtotime($aux_siguiente->updated_at),
            'regprodlote_lote_id'    => $lote->id,
        ]);
        session()->save();

        return redirect()->route('crear_opdetregprodtemp');
    }

    /**
     * Datos del lote para mostrarlos antes de entrar (AJAX). Es la confirmacion
     * visual de que el numero tecleado es el rollo que tiene en la mano.
     */
    public function consultar($id)
    {
        can('crear-registro-produccion');

        $lote = OpDetRegProd::find((int) $id);
        if (!$lote) {
            return response()->json(['ok' => false, 'mensaje' => 'No existe el lote ' . $id . '.']);
        }
        if ($lote->aprobstatus != 2) {
            return response()->json(['ok' => false, 'mensaje' => 'El lote ' . $lote->id . ' todavía no está aprobado.']);
        }

        $opdetOrigen = $lote->opdet;
        $aux_siguiente = $opdetOrigen ? $this->opdetSiguiente($opdetOrigen) : null;
        $aux_prod = Producto::atributosProducto($lote->producto_id);

        $aux_etapa_nom = null;
        $aux_acceso    = false;
        if ($aux_siguiente) {
            $aux_etapa_nom = $aux_siguiente->areaproduccionsucetapaprod->etapaprod->nombre ?? '—';
            $aux_acceso = collect(EtapaProd::etapasProdxPersona())
                ->contains('etapaprod_id', $aux_siguiente->areaproduccionsucetapaprod->etapaprod_id);
        }

        return response()->json([
            'ok'          => true,
            'lote_id'     => $lote->id,
            'producto'    => $aux_prod['nombre'] ?? ('Prod. ' . $lote->producto_id),
            'kgprod'      => number_format($lote->kgprod, 2, ',', '.'),
            'etapaorigen' => $opdetOrigen->areaproduccionsucetapaprod->etapaprod->nombre ?? '—',
            'op_id'       => $opdetOrigen->op_id ?? null,
            'etapadest'   => $aux_etapa_nom,
            'acceso'      => $aux_acceso,
        ]);
    }

    /**
     * Item de la OP de la etapa siguiente a la del lote. Devuelve null cuando el
     * lote ya esta en la ultima etapa.
     */
    private function opdetSiguiente(OpDet $opdetOrigen)
    {
        $aux_orden = $opdetOrigen->areaproduccionsucetapaprod->orden;
        $aux_sig   = null;
        $aux_menor = null;

        foreach ($opdetOrigen->op->opdets as $od) {
            $aux_o = $od->areaproduccionsucetapaprod->orden;
            if ($aux_o > $aux_orden && ($aux_menor === null || $aux_o < $aux_menor)) {
                $aux_menor = $aux_o;
                $aux_sig   = $od;
            }
        }
        return $aux_sig;
    }

    private function volver($mensaje)
    {
        return redirect()->route('regprodlote')->with([
            'mensaje'    => $mensaje,
            'tipo_alert' => 'alert-error',
        ]);
    }
}

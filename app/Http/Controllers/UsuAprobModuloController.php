<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarUsuAprobModulo;
use App\Models\Seguridad\Usuario;
use App\Models\UsuAprobModulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuAprobModuloController extends Controller
{
    /**
     * Rutas (url del menu) que corresponden a pantallas de aprobacion de registros.
     * Para agregar un nuevo modulo de aprobacion a este control, solo se agrega su
     * url aqui, no se requiere crear tablas ni CRUD nuevo.
     */
    const MODULOS_APROBACION = [
        'cotizacionaprobar',
        'CotizacionAprobarCliente',
        'notaventaaprobar',
        'inventsalaprobar',
        'cotizacionaprobaracutec',
        'pesajeaprobar',
        'despachoordrecapr',
    ];

    private function obtenerModulosDisponibles()
    {
        $ph = "'" . implode("','", self::MODULOS_APROBACION) . "'";
        $menus = DB::select("SELECT nombre, url FROM menu WHERE url IN ($ph) ORDER BY nombre");
        return collect($menus)->pluck('nombre', 'url')->toArray();
    }

    // Bodegas disponibles para el select opcional de "Bodegas permitidas" (solo aplica a inventsalaprobar)
    private function obtenerBodegasDisponibles()
    {
        $sql = "SELECT ib.id, CONCAT(s.nombre, '/', ib.nombre) AS nombre
                FROM invbodega ib
                INNER JOIN sucursal s ON s.id = ib.sucursal_id
                WHERE ib.deleted_at IS NULL
                ORDER BY s.nombre, ib.nombre";
        return collect(DB::select($sql))->pluck('nombre', 'id')->toArray();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-usuaprobmodulo');
        return view('usuaprobmodulo.index');
    }

    public function usuaprobmodulopage()
    {
        $sql = "SELECT uam.id, uam.usuario_id, usu.nombre AS usuario_nombre, uam.modulo,
                       GROUP_CONCAT(DISTINCT usu2.nombre ORDER BY usu2.nombre SEPARATOR ', ') AS usuarios_permitidos,
                       GROUP_CONCAT(DISTINCT CONCAT(suc.nombre,'/',ib.nombre) ORDER BY suc.nombre, ib.nombre SEPARATOR ', ') AS bodegas_permitidas
                FROM usuaprobmodulo uam
                INNER JOIN usuario usu ON usu.id = uam.usuario_id
                LEFT JOIN usuaprobmoduledet uamd ON uamd.usuaprobmodulo_id = uam.id
                LEFT JOIN usuario usu2 ON usu2.id = uamd.usuario_creador_id
                LEFT JOIN usuaprobmodulobodega uamb ON uamb.usuaprobmodulo_id = uam.id
                LEFT JOIN invbodega ib ON ib.id = uamb.invbodega_id
                LEFT JOIN sucursal suc ON suc.id = ib.sucursal_id
                WHERE uam.deleted_at IS NULL
                GROUP BY uam.id";
        $datas = DB::select($sql);
        $modulos = $this->obtenerModulosDisponibles();
        foreach ($datas as &$d) {
            $d->modulo_nombre = $modulos[$d->modulo] ?? $d->modulo;
        }
        return datatables($datas)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-usuaprobmodulo');
        $usuarios = Usuario::orderBy('nombre')->pluck('nombre', 'id')->toArray();
        $modulos = $this->obtenerModulosDisponibles();
        $bodegas = $this->obtenerBodegasDisponibles();
        return view('usuaprobmodulo.crear', compact('usuarios', 'modulos', 'bodegas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarUsuAprobModulo $request)
    {
        can('guardar-usuaprobmodulo');

        $existe = UsuAprobModulo::where('usuario_id', $request->usuario_id)
            ->where('modulo', $request->modulo)
            ->exists();
        if ($existe) {
            return redirect()->back()->withInput()->with([
                'mensaje' => 'Ya existe una restricción configurada para ese usuario en ese módulo.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $usuAprobModulo = UsuAprobModulo::create($request->only(['usuario_id', 'modulo']));
        $usuAprobModulo->usuariosPermitidos()->sync($request->usuario_creador_id);
        $usuAprobModulo->bodegasAprobEntSalInv()->sync($request->invbodega_id ?? []);

        return redirect('usuaprobmodulo')->with('mensaje', 'Restricción creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id)
    {
        can('editar-usuaprobmodulo');
        $data = UsuAprobModulo::with(['usuariosPermitidos', 'bodegasAprobEntSalInv'])->findOrFail($id);
        $usuarios = Usuario::orderBy('nombre')->pluck('nombre', 'id')->toArray();
        $modulos = $this->obtenerModulosDisponibles();
        $bodegas = $this->obtenerBodegasDisponibles();
        return view('usuaprobmodulo.editar', compact('data', 'usuarios', 'modulos', 'bodegas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarUsuAprobModulo $request, $id)
    {
        can('editar-usuaprobmodulo');

        $existe = UsuAprobModulo::where('usuario_id', $request->usuario_id)
            ->where('modulo', $request->modulo)
            ->where('id', '!=', $id)
            ->exists();
        if ($existe) {
            return redirect()->back()->withInput()->with([
                'mensaje' => 'Ya existe una restricción configurada para ese usuario en ese módulo.',
                'tipo_alert' => 'alert-error'
            ]);
        }

        $usuAprobModulo = UsuAprobModulo::findOrFail($id);
        $usuAprobModulo->update($request->only(['usuario_id', 'modulo']));
        $usuAprobModulo->usuariosPermitidos()->sync($request->usuario_creador_id);
        $usuAprobModulo->bodegasAprobEntSalInv()->sync($request->invbodega_id ?? []);

        return redirect('usuaprobmodulo')->with('mensaje', 'Restricción actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if (can('eliminar-usuaprobmodulo', false)) {
            if ($request->ajax()) {
                if (UsuAprobModulo::destroy($request->id)) {
                    $usuAprobModulo = UsuAprobModulo::withTrashed()->findOrFail($request->id);
                    $usuAprobModulo->usuariodel_id = auth()->id();
                    $usuAprobModulo->save();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            } else {
                abort(404);
            }
        } else {
            return response()->json(['mensaje' => 'ne']);
        }
    }
}

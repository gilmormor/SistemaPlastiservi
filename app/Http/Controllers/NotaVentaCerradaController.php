<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarNotaVentaCerrada;
use App\Models\ClienteBloqueado;
use App\Models\ClienteSucursal;
use App\Models\NotaVentaCerrada;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;

class NotaVentaCerradaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-cerrar-nota-venta');
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $datas = NotaVentaCerrada::join('notaventa','notaventacerrada.notaventa_id','=','notaventa.id')
                ->select([
                    'notaventacerrada.id',
                    'notaventacerrada.notaventa_id',
                    'notaventacerrada.observacion',
                    'notaventacerrada.motcierre_id'
                    ])
                ->whereIn('notaventa.sucursal_id', $sucurArray)
                ->get();
        return view('notaventacerrar.index', compact('datas','sucursales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-nota-venta-cerrada');
        $aux_editar = 0;
        return view('notaventacerrar.crear', compact('aux_editar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-cerrar-nota-venta');
        $request->request->add(['usuario_id' => auth()->id()]);
        //dd($request);
        NotaVentaCerrada::create($request->all());
        return redirect('notaventacerrada')->with('mensaje','Creado con exito');

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
        can('editar-cerrar-nota-venta');
        $data = NotaVentaCerrada::findOrFail($id);
        $aux_editar = 1;
        return view('notaventacerrar.editar', compact('data','aux_editar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarNotaVentaCerrada $request, $id)
    {
        NotaVentaCerrada::findOrFail($id)->update($request->all());
        return redirect('notaventacerrada')->with('mensaje','Actualizado con exito');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = NotaVentaCerrada::findOrFail($id);
            $data->usuariodel_id = auth()->id();
            $data->save();
            if (NotaVentaCerrada::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}

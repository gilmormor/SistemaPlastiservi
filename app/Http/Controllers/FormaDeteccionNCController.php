<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarFormaDeteccionNC;
use App\Models\FormaDeteccionNC;
use Illuminate\Http\Request;

class FormaDeteccionNCController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-forma-deteccion-nc');
        $datas = FormaDeteccionNC::orderBy('id')->get();
        return view('formadeteccionnc.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-forma-deteccion-nc');
        return view('formadeteccionnc.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarFormaDeteccionNC $request)
    {
        can('guardar-forma-deteccion-nc');
        FormaDeteccionNC::create($request->all());
        return redirect('formadeteccionnc')->with('mensaje','Creado con exito.');
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
        can('editar-forma-deteccion-nc');
        $data = FormaDeteccionNC::findOrFail($id);
        return view('formadeteccionnc.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarFormaDeteccionNC $request, $id)
    {
        FormaDeteccionNC::findOrFail($id)->update($request->all());
        return redirect('formadeteccionnc')->with('mensaje','MActualizado con exito.');
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
            if (FormaDeteccionNC::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}

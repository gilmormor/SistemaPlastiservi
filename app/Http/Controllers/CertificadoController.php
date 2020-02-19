<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarCertificado;
use App\Models\Certificado;
use Illuminate\Http\Request;

class CertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-certificado');
        $datas = Certificado::orderBy('id')->get();
        return view('certificado.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-certificado');
        return view('certificado.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarCertificado $request)
    {
        can('guardar-certificado');

        if ($foto = Certificado::setFotoCertificado($request->foto_up)){
            $request->request->add(['foto' => $foto]);
        }

        Certificado::create($request->all());
        return redirect('certificado')->with('mensaje','Certificado creado con exito');
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
        can('editar-certificado');
        $data = Certificado::findOrFail($id);
        return view('certificado.editar', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarCertificado $request, $id)
    {
        /*
        $image = $request->file('foto_up');
        $filename = $request->usuario . '.' . $image->getClientOriginalExtension();
        $image->storeAs('public/certificado/usuario',$filename);
        //$request->file('foto_up')->storeAs('public/imagenes/usuario',$request->usuario . '.jpg');
        $request->request->add(['foto' => $filename]);
        $certificado = Certificado::findOrFail($id);
        $certificado->update(array_filter($request->all()));
        */
        
        $certificado = Certificado::findOrFail($id);
        //dd($request->foto_up);

        if ($foto = Certificado::setFotoCertificado($request->foto_up,$certificado->foto)){
            $request->request->add(['foto' => $foto]);
        }
        Certificado::findOrFail($id)->update($request->all());
        return redirect('certificado')->with('mensaje','Certificado actualizado con exito');
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
            if (Certificado::destroy($id)) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}

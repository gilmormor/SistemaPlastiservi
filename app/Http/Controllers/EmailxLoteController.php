<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarEmailxLote;
use App\Models\EmailxLote;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailxLoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-email-x-lote');
        //$datas = FormaPago::orderBy('id')->get();
        return view('emailxlote.index');
    }

    public function emailxlotepage(){    
        $sql = "SELECT emailxlote.id,emailxlote.nombre,
        GROUP_CONCAT(DISTINCT concat(' ', persona.nombre, ' ' ,persona.apellido, ' (' ,persona.email, ')') ORDER BY persona.nombre) AS persona_nombre
        from emailxlote INNER JOIN emailxlote_persona
        on emailxlote.id = emailxlote_persona.emailxlote_id 
        INNER JOIN persona
        ON emailxlote_persona.persona_id = persona.id
        where isnull(emailxlote.deleted_at) 
        GROUP BY emailxlote.id;";
        $datas = DB::select($sql);
        return datatables($datas)->toJson();

        return datatables()
            ->eloquent(EmailxLote::query())
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crear()
    {
        can('crear-email-x-lote');
        $sql = 'SELECT persona.*
        FROM persona
        WHERE persona.activo=1;';
        $personas = DB::select($sql);
        return view('emailxlote.crear',compact('personas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarEmailxLote $request)
    {
        can('guardar-email-x-lote');
        $emailxlote = EmailxLote::create($request->all());
        $emailxlote->personas()->sync($request->persona_id);
        return redirect('emailxlote')->with('mensaje','EmailxLote creado con exito');
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
        can('editar-email-x-lote');
        $data = EmailxLote::findOrFail($id);
        $sql = 'SELECT persona.*
        FROM persona
        WHERE persona.activo=1;';
        $personas = DB::select($sql);

        return view('emailxlote.editar', compact('data','personas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarEmailxLote $request, $id)
    {
        //EmailxLote::findOrFail($id)->update($request->all());
        $emailxlote = EmailxLote::findOrFail($id);
        $emailxlote->update($request->all());
        $emailxlote->personas()->sync($request->persona_id);
        return redirect('emailxlote')->with('mensaje','EmailxLote actualizado con exito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eliminar(Request $request, $id)
    {
        if(can('eliminar-email-x-lote',false)){
            if ($request->ajax()) {
                $emailxlote = EmailxLote::findOrFail($request->id);
                $aux_regAso = false;
                $aux_tabla = [];
                if(count($emailxlote->comunas) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Comuna";
                }
                if(count($emailxlote->clientes) > 0){
                    $aux_regAso = true;
                    $aux_tabla[] = "Cliente";
                }
                if(count($emailxlote->dtes) >0){
                    $aux_regAso = true;
                    $aux_tabla[] = "DTE";
                }
                if($aux_regAso){
                    return response()->json([
                        'id' => 1,
                        'mensaje' => "No se puede eliminar, tiene registros asociados a la tabla: " . implode(", ", $aux_tabla) . ".",
                        'tipo_alert' => "error"
                    ]);
                }
                if (EmailxLote::destroy($request->id)) {
                    //dd('entro');
                    //Despues de eliminar actualizo el campo usuariodel_id=usuario que elimino el registro
                    $emailxlote = EmailxLote::withTrashed()->findOrFail($request->id);
                    $emailxlote->usuariodel_id = auth()->id();
                    $emailxlote->save();
                    return response()->json(['mensaje' => 'ok']);
                } else {
                    return response()->json(['mensaje' => 'ng']);
                }
            } else {
                abort(404);
            }
        }else{
            return response()->json(['mensaje' => 'ne']);
        }
    }
}
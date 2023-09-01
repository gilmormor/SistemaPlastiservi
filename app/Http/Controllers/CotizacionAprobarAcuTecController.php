<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionAprobarAcuTecController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-aprobar-acuerdo-tecnico-cotizacion');
        session(['aux_aprocot' => '5']);
        return view('cotizacionaprobaracutec.index');
    }

    public function cotizacionaprobaracutecpage(){
        $user = Usuario::findOrFail(auth()->id());
        $sucurArray = $user->sucursales->pluck('id')->toArray();
        $sucurcadena = implode(",", $sucurArray);

        session(['aux_aprocot' => '5']);
        $sql = "SELECT cotizacion.id,DATE_FORMAT(cotizacion.fechahora,'%d/%m/%Y %h:%i %p') as fechahora,
                    if(isnull(cliente.razonsocial),clientetemp.razonsocial,cliente.razonsocial) as razonsocial,
                    concat(persona.nombre, ' ' ,persona.apellido) as vendedor_nombre,
                    aprobstatus,'1' as pdfcot
                FROM cotizacion left join cliente
                on cotizacion.cliente_id = cliente.id
                left join clientetemp
                on cotizacion.clientetemp_id = clientetemp.id
                INNER JOIN vendedor
                ON cotizacion.vendedor_id = vendedor.id
                INNER JOIN persona
                ON vendedor.persona_id = persona.id
                where aprobstatus=5
                and cotizacion.deleted_at is null
                AND cotizacion.sucursal_id in ($sucurcadena);";
        //where usuario_id='.auth()->id();
        $datas = DB::select($sql);
        return datatables($datas)->toJson();  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function productobuscarpageid(Request $request){
        $datas = Producto::productosxCliente($request);
        return datatables($datas)->toJson();
    }

    public function clientebuscarpageid($id){
        $datas = Cliente::clientesxUsuarioSQL();
        return datatables($datas)->toJson();
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        session(['editaracutec' => '0']);
        session(['aux_aprocot' => '1']);
        $objeto = new CotizacionController();
        return $objeto->editaraat($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

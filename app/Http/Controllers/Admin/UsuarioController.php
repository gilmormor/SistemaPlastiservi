<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidarUsuario;
use App\Http\Requests\ValidarUsuarioBasicos;
use App\Http\Requests\ValidarUsuarioClave;
use App\Models\Admin\Rol;
use App\Models\Seguridad\Usuario;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = Usuario::with('roles:id,nombre')->orderBy('id')->get();
        //dd($usuarios);
        return view('admin.usuario.index', compact('usuarios')); //Se usa compact() para evitar la sintaxis anterior 

    }

    public function usuariopage(){
        $sql = "SELECT usuario.*,'' as rutausuario, GROUP_CONCAT(DISTINCT rol.nombre) AS rol_nombre
        FROM usuario
        LEFT JOIN usuario_rol
        ON usuario_rol.usuario_id = usuario.id
        LEFT JOIN rol
        ON rol.id = usuario_rol.rol_id
        WHERE isnull(usuario.deleted_at)
        GROUP BY usuario.id;";
        $datas = DB::select($sql);
        foreach($datas as &$data){
            $data->rutausuario = route('ver_usuario', ['id' => $data->id]);
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
        $rols = Rol::orderBy('id')->pluck('nombre', 'id')->toArray();
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        return view('admin.usuario.crear', compact('rols','sucursales'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(ValidarUsuario $request)
    {
        //dd($request->foto_up);
        if ($foto = Usuario::setFotoUsuario($request->foto_up,$request->usuario)){
            $request->request->add(['foto' => $foto]);
        }
        $usuario = Usuario::create($request->all());
        //$usuario->roles()->attach($request->rol_id);
        $usuario->roles()->sync($request->rol_id);
        $usuario->sucursales()->sync($request->sucursal_id);
        return redirect('admin/usuario')->with('mensaje','Usuario creado con exito!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mostar($id)
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
        $rols = Rol::orderBy('id')->pluck('nombre', 'id')->toArray();
        $data = Usuario::with('roles')->findOrFail($id);
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        return view('admin.usuario.editar', compact('data','rols','sucursales'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidarUsuario $request, $id)
    {
        //dd($request->file('foto_up'));
        //$usuario = Usuario::findOrFail($id)->update(array_filter($request->all()));
        /*
        if ($foto = Usuario::setFotoUsuario($request->foto_up,$request->usuario)){
            $request->request->add(['foto' => $foto]);
        }
        */
        /*
        $image = $request->file('foto_up');
        $filename = $request->usuario . '.' . $image->getClientOriginalExtension();
        $location = public_path('images\_') . $filename;
        Image::make($image)->resize(530, 470)->save($location);
        $request->request->add(['foto' => "_".$filename]);
        */
        
        //Storage::disk('public')->put($location, $imagen1->stream());
        if(!is_null($request->file('foto_up'))){
            $image = $request->file('foto_up');
            $filename = $request->usuario . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/imagenes/usuario',$filename);
            $request->request->add(['foto' => $filename]);
        }
        //$request->file('foto_up')->storeAs('public/imagenes/usuario',$request->usuario . '.jpg');
        
        $usuario = Usuario::findOrFail($id);
        $usuario->update(array_filter($request->all()));
        $usuario->roles()->sync($request->rol_id);
        $usuario->sucursales()->sync($request->sucursal_id);
        return redirect('admin/usuario')->with('mensaje','Usuario actualizado con exito');
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
            $usuario = Usuario::findOrFail($id);
            $usuario->roles()->detach();
            Usuario::destroy($id);
            //$usuario->delete();
            return response()->json(['mensaje' => 'ok']);
         } else {
            abort(404);
        }
    }

    public function ver($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('admin.usuario.ver', compact('usuario'));
        //return view('admin.usuario.ver', compact('usuario'));
    }
    public function cambclave()
    {
        //dd(auth()->id());
        $id = auth()->id();
        $rols = Rol::orderBy('id')->pluck('nombre', 'id')->toArray();
        $data = Usuario::with('roles')->findOrFail($id);
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        return view('admin.usuario.cambclave', compact('data','rols','sucursales'));
    }

    public function actualizarclave(ValidarUsuarioClave $request){
        $id = auth()->id();
        $usuario = Usuario::findOrFail($id);
        if(Hash::check($request['passwordant'], $usuario->password)){
            $usuario->update(array_filter($request->all()));
            return redirect('/')->with('mensaje','Usuario actualizado con exito');    
        }else{
            return redirect('/')->with('mensaje','Clave No se actualizo, la clave anterior no coincide.');    
        }

    }
    
    public function datosbasicos()
    {
        $id = auth()->id();
        $rols = Rol::orderBy('id')->pluck('nombre', 'id')->toArray();
        $data = Usuario::with('roles')->findOrFail($id);
        $sucursales = Sucursal::orderBy('id')->pluck('nombre', 'id')->toArray();
        //dd($data);
        return view('admin.usuario.editarbas', compact('data','rols','sucursales'));
    }

    public function actualizarbasicos(ValidarUsuarioBasicos $request){
        //dd($request);
        if(!is_null($request->file('foto_up'))){
            $image = $request->file('foto_up');
            $filename = $request->usuario . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/imagenes/usuario',$filename);
            //$request->file('foto_up')->storeAs('public/imagenes/usuario',$request->usuario . '.jpg');
            $request->request->add(['foto' => $filename]);
        }
        $id = auth()->id();
        $usuario = Usuario::findOrFail($id);
        $usuario->update(array_filter($request->all()));
        return redirect('/')->with('mensaje','Usuario actualizado con exito');    

    }

}

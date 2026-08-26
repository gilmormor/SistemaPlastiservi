<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Menu;
use App\Models\Admin\Rol;
use Illuminate\Support\Facades\DB;

class MenuRolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-admin-menu-rol');
        // Solo pasa los roles para el select; el árbol se carga por AJAX
        $rols = Rol::orderBy('nombre')->get(['id', 'nombre']);
        return view('admin.menu-rol.index', compact('rols'));
    }

    /**
     * Devuelve el árbol de menú completo con el estado (checked/unchecked)
     * de cada ítem para los roles seleccionados. Usado por AJAX al consultar.
     */
    public function menuRolData(Request $request)
    {
        $rolIds = $request->input('rol_id', []);
        if (!is_array($rolIds)) {
            $rolIds = [$rolIds];
        }
        $rolIds = array_values(array_filter(array_map('intval', $rolIds)));

        $roles = Rol::whereIn('id', $rolIds)->orderBy('nombre')->get(['id', 'nombre']);

        // Construir mapa [menu_id][rol_id] = true
        $rows = DB::table('menu_rol')->whereIn('rol_id', $rolIds)->get(['menu_id', 'rol_id']);
        $menuRolMap = [];
        foreach ($rows as $row) {
            $menuRolMap[$row->menu_id][$row->rol_id] = true;
        }

        $menus = Menu::getMenu(false); // Todos los menús sin filtrar por rol
        $tree  = $this->attachRolesToTree($menus, $rolIds, $menuRolMap);

        return response()->json(['roles' => $roles, 'menu' => $tree]);
    }

    /**
     * Agrega el estado de cada rol a cada nodo del árbol de menú recursivamente.
     */
    private function attachRolesToTree(array $menus, array $rolIds, array $menuRolMap): array
    {
        $result = [];
        foreach ($menus as $menu) {
            $rolesState = [];
            foreach ($rolIds as $rolId) {
                $rolesState[$rolId] = isset($menuRolMap[$menu['id']][$rolId]);
            }
            $result[] = [
                'id'      => $menu['id'],
                'nombre'  => $menu['nombre'],
                'icono'   => $menu['icono'] ?? 'fa-circle-o',
                'url'     => $menu['url'],
                'roles'   => $rolesState,
                'submenu' => !empty($menu['submenu'])
                    ? $this->attachRolesToTree($menu['submenu'], $rolIds, $menuRolMap)
                    : [],
            ];
        }
        return $result;
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        can('guardar-admin-menu-rol');
        if ($request->ajax()) {
            $menus = new Menu();
            if ($request->input('estado') == 1) {
                $menus->find($request->input('menu_id'))->roles()->attach($request->input('rol_id'));
                return response()->json(['respuesta' => 'El rol se asigno correctamente']);
            } else {
                $menus->find($request->input('menu_id'))->roles()->detach($request->input('rol_id'));
                return response()->json(['respuesta' => 'El rol se elimino correctamente']);
            }
        } else {
            abort(404);
        }
    }

    
}

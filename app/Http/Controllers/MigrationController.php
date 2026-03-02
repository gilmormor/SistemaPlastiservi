<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MigrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-migration');
        //$datas = FormaPago::orderBy('id')->get();
        return view('migration.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
    public function guardar(Request $request)
    {
        can('guardar-migration');
        //dd($request);
        $output = '';
        $action = $request->action; // request('action');
        $steps = $request->steps; // request('steps', 1);
        $batch = $request->batch; // request('batch');
        
        if ($action) {
            try {
                switch ($action) {
                    case 'migrate':
                        Artisan::call('migrate', ['--force' => true]);
                        break;
                    case 'rollback':
                        $params = ['--force' => true];
                        if ($steps) $params['--step'] = $steps;
                        if ($batch) $params['--batch'] = $batch;
                        Artisan::call('migrate:rollback', $params);
                        break;
                    /* case 'reset':
                        Artisan::call('migrate:reset', ['--force' => true]);
                        break;
                    case 'refresh':
                        Artisan::call('migrate:refresh', ['--force' => true]);
                        break;
                    case 'fresh':
                        Artisan::call('migrate:fresh', ['--force' => true]);
                        break; */
                }
                $output = Artisan::output();
            } catch (\Exception $e) {
                $output = "Error: " . $e->getMessage();
            }
        }
        
        return view('migration.index', compact('output', 'action', 'steps', 'batch'));
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
    public function edit($id)
    {
        //
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

<?php

namespace App\Console\Commands;

use App\Models\DataCobranza;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class RunLlenarDataCobranza3001a3500 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datacobranza:llenardatacobranza3001a3500';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llena la tabla DataCobranza';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $request = new Request();
        $request->merge(['clienteid_ini' => 3001]);
        $request->request->set('clienteid_ini', 3001);
        $request->merge(['clienteid_fin' => 3500]);
        $request->request->set('clienteid_fin', 3500);
        $respuesta = DataCobranza::llenartabla($request);
    }
}

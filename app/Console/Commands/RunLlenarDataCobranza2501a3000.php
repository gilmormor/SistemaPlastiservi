<?php

namespace App\Console\Commands;

use App\Models\DataCobranza;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class RunLlenarDataCobranza2501a3000 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datacobranza:llenardatacobranza2501a3000';

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
        $request->merge(['clienteid_ini' => 2501]);
        $request->request->set('clienteid_ini', 2501);
        $request->merge(['clienteid_fin' => 3000]);
        $request->request->set('clienteid_fin', 3000);
        $respuesta = DataCobranza::llenartabla($request);
    }
}

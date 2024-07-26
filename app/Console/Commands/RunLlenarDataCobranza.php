<?php

namespace App\Console\Commands;

use App\Models\DataCobranza;
use Illuminate\Console\Command;
use Illuminate\Http\Request;


class RunLlenarDataCobranza extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datacobranza:llenardatacobranza  {clienteid_ini} {clienteid_fin}';

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
        $clienteid_ini = $this->argument('clienteid_ini');
        $clienteid_fin = $this->argument('clienteid_fin');
        
        $request = new Request();
        $request->merge(['clienteid_ini' => $clienteid_ini]);
        $request->merge(['clienteid_fin' => $clienteid_fin]);
        
        $respuesta = DataCobranza::llenartabla($request);
    }
}

<?php

namespace App\Console\Commands;

use App\Events\EnviarEmailFactxVencer;
use App\Models\DataCobranza;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class RunEnviarEmailFactxVencer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datacobranza:EnviarEmailFactxVencer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar por email las facturas por vencer';

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
        $respuesta = DataCobranza::EnviarEmailFactxVencer($request);
        Event(new EnviarEmailFactxVencer($respuesta));
        //dd($respuesta);

    }
}

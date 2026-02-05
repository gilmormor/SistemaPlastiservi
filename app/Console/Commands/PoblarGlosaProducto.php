<?php

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;

class PoblarGlosaProducto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'producto:poblar-glosa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pobla el campo glosa a partir de atributosProducto';

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
        Producto::chunk(100, function ($productos) {
            foreach ($productos as $producto) {

                if (empty($producto->glosa)) {
                    $atributos = $producto->atributosProducto($producto->id);
                    $producto->glosa = $atributos['nombre'];
                    if($producto->tipoprod == 1){
                        $producto->glosaaut = 0;
                    }
                    if(!isset($producto->glosa)){
                        $producto->glosa = $producto->nombre;
                    }
                    $producto->save();
                }
            }
        });

        $this->info('Campo glosa poblado correctamente.');
    }
}

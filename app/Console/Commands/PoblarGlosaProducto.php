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
        /* Producto::chunk(100, function ($productos) {
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

        $this->info('Campo glosa poblado correctamente.'); */
        Producto::select('id','glosa','nombre','tipoprod')
            ->whereNull('glosa')
            ->chunkById(500, function ($productos) {

                foreach ($productos as $producto) {

                    $atributos = $producto->atributosProducto($producto->id);

                    $glosa = $atributos['nombre'] ?? $producto->nombre;
                    $aux_color_id = $atributos['color_id'] ?? $producto->color_id;
                    $aux_claseprod_id = $atributos['claseprod_id'] ?? $producto->claseprod_id;

                    $data = [
                        'glosa' => $glosa,
                        'color_id' => $aux_color_id,
                        'claseprod_id' => $aux_claseprod_id,
                        'costobloqueado' => 1
                    ];

                    if ($producto->tipoprod == 1) {
                        $data['glosaaut'] = 0;
                    }

                    Producto::where('id', $producto->id)->update($data);

                }

                // liberar memoria del chunk
                unset($productos);

            });

        $this->info('Campo glosa poblado correctamente.');
    }
}

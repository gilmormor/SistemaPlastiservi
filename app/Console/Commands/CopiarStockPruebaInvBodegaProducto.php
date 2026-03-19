<?php

namespace App\Console\Commands;

use App\Models\InvBodegaProducto;
use Illuminate\Console\Command;

class CopiarStockPruebaInvBodegaProducto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:copiar-prueba';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copia los valores de stock y stockkg a los campos de prueba en la tabla invbodegaproducto';

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
        $this->info('Iniciando copia de stock a campos de prueba...');
        
        $totalRegistros = InvBodegaProducto::count();
        $this->info("Total registros a procesar: {$totalRegistros}");
        
        $procesados = 0;
        $errores = 0;
        
        // Procesar en chunks para no sobrecargar la memoria
        InvBodegaProducto::chunk(100, function ($registros) use (&$procesados, &$errores) {
            foreach ($registros as $registro) {
                try {
                    $registro->stockprueba = $registro->stock;
                    $registro->stockkgprueba = $registro->stockkg;
                    $registro->save();
                    
                    $procesados++;
                    
                    /* if ($procesados % 100 == 0) {
                        $this->info("Procesados: {$procesados} registros");
                    } */
                } catch (\Exception $e) {
                    $errores++;
                    $this->error("Error en registro ID {$registro->id}: {$e->getMessage()}");
                }
            }
        });
        
        $this->info("Proceso completado. Registros actualizados: {$procesados}, Errores: {$errores}");
    }
}

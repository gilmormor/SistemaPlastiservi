<?php

namespace App\Console\Commands;

use App\Models\NotaVentaDetalle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalcularTotalKilosNotaVentaDetalle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notaventa:recalcular-totalkilos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula totalkilos en notaventadetalle donde vale 0, usando acuerdotecnico.at_peso. Idempotente: se puede ejecutar las veces que sea necesario.';

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
        // Misma lógica que la migración 2025_02_05_111144_add_at_peso_to_acuerdotecnico_table,
        // extraída a un comando para poder re-ejecutarla después del merge de ramas,
        // ya que producción sigue generando registros con totalkilos = 0.
        $sql = "SELECT id
        FROM notaventadetalle
        WHERE totalkilos = 0
        AND isnull(notaventadetalle.deleted_at)";
        $nvdets = DB::select($sql);

        $actualizados = 0;
        $omitidos = 0;
        foreach ($nvdets as $nvdet) {
            $nvdetr = NotaVentaDetalle::findOrFail($nvdet->id);
            if(isset($nvdetr->producto) and isset($nvdetr->producto->acuerdotecnico) and $nvdetr->producto->acuerdotecnico->at_peso > 0){
                $nvdetr->totalkilos = $nvdetr->cant * $nvdetr->producto->acuerdotecnico->at_peso;
                $nvdetr->save();
                $actualizados++;
            }else{
                // Sin producto, sin acuerdo técnico o at_peso en 0: no se puede calcular
                $omitidos++;
            }
        }

        $this->info("Registros encontrados con totalkilos=0: " . count($nvdets));
        $this->info("Actualizados: $actualizados");
        $this->info("Omitidos (sin AT o at_peso=0): $omitidos");
    }
}

<?php

namespace App\Console\Commands;

use App\Models\AcuerdoTecnico;
use App\Models\AcuerdoTecnicoTemp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PoblarAtPesoAcuerdoTecnico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acuerdotecnico:poblar-atpeso';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pobla at_peso en acuerdotecnico y acuerdotecnicotemp donde está NULL o 0, usando pesounitat/pesounitattemp. Idempotente: se puede re-ejecutar; procesa por lotes para no agotar memoria.';

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
        // Rescate de la migración 2025_02_05_111144 cuando muere a mitad del poblado
        // (ej: proceso matado por límites del hosting). Solo toca registros sin peso.

        // --- acuerdotecnico ---
        $ok = 0; $err = 0;
        AcuerdoTecnico::whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('at_peso')->orWhere('at_peso', 0);
            })
            ->chunkById(200, function ($ats) use (&$ok, &$err) {
                foreach ($ats as $at) {
                    try {
                        $at->at_peso = pesounitat($at);
                        $at->save();
                        $ok++;
                    } catch (\Exception $e) {
                        $err++;
                        $this->error("acuerdotecnico id={$at->id}: " . $e->getMessage());
                    }
                }
            });
        $this->info("acuerdotecnico: actualizados=$ok, errores=$err");

        // --- acuerdotecnicotemp ---
        $ok2 = 0; $err2 = 0;
        AcuerdoTecnicoTemp::whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('at_peso')->orWhere('at_peso', 0);
            })
            ->chunkById(200, function ($ats) use (&$ok2, &$err2) {
                foreach ($ats as $at) {
                    try {
                        $at->at_peso = pesounitattemp($at);
                        $at->save();
                        $ok2++;
                    } catch (\Exception $e) {
                        $err2++;
                        $this->error("acuerdotecnicotemp id={$at->id}: " . $e->getMessage());
                    }
                }
            });
        $this->info("acuerdotecnicotemp: actualizados=$ok2, errores=$err2");
        $this->info('Listo.');
    }
}

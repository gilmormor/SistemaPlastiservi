<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Bodegas creadas durante el desarrollo del Modulo Produccion (2026) y su
 * configuracion asociada. Sin estas filas el modulo falla en produccion con
 * errores del tipo "No existe Bodega Picking/Pesaje en modulo invmovmodulo",
 * porque el codigo resuelve la bodega destino consultando invmovmodulobodent /
 * invmovmodulobodsal por modulo y sucursal.
 *
 * Ejecutar: php artisan db:seed --class=BodegasModuloProduccionSeeder --force
 *
 * Idempotente: cada fila se verifica por id antes de insertar, asi que puede
 * correrse varias veces sin duplicar. Preserva los ids originales, necesarios
 * porque otras maestras (apsucetapaprod_bodega en
 * TablasMaestrasModuloProduccionSeeder) los referencian.
 *
 * ORDEN: correr DESPUES de TablasMaestrasModuloProduccionSeeder no es
 * obligatorio, pero si antes de operar el modulo.
 *
 * Contenido:
 *  - invbodega: 7 bodegas (5 de etapas de produccion, Pesaje SE y el rack R01U02)
 *  - invmovmodulobodent / invmovmodulobodsal: su asignacion a los modulos de inventario
 *  - categoriaprod_invbodega: que categoria de producto opera en cada bodega
 */
class BodegasModuloProduccionSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $now = Carbon::now();

            // ── Bodegas ───────────────────────────────────────────────────────
            // tipo: 1=Picking, 2=Almacenamiento, 3=Despacho, 4=Scrap, 5=Produccion, 6=Pesaje
            $bodegas = [
                ['id' => 24, 'nombre' => 'Pesaje SE',          'nomabre' => 'PesSE', 'desc' => 'Pesaje SE',                       'sucursal_id' => 1, 'activo' => 1, 'tipo' => 6, 'orden' => 6, 'usuario_id' => 1],
                ['id' => 25, 'nombre' => 'Prod Materia Prima', 'nomabre' => 'MatPr', 'desc' => 'Bodega Produccion Materia Prima', 'sucursal_id' => 1, 'activo' => 1, 'tipo' => 5, 'orden' => 1, 'usuario_id' => 1],
                ['id' => 26, 'nombre' => 'Prod Mezclado',      'nomabre' => 'Mezcl', 'desc' => 'Produccion Mezclado',             'sucursal_id' => 1, 'activo' => 1, 'tipo' => 5, 'orden' => 2, 'usuario_id' => 1],
                ['id' => 27, 'nombre' => 'Prod Extrusion',     'nomabre' => 'Extru', 'desc' => 'Produccion Extrusion',            'sucursal_id' => 1, 'activo' => 1, 'tipo' => 5, 'orden' => 3, 'usuario_id' => 1],
                ['id' => 28, 'nombre' => 'Prod Impresion',     'nomabre' => 'Impre', 'desc' => 'Produccion Impresion',            'sucursal_id' => 1, 'activo' => 1, 'tipo' => 5, 'orden' => 4, 'usuario_id' => 1],
                ['id' => 29, 'nombre' => 'Prod Sellado',       'nomabre' => 'Sella', 'desc' => 'Produccion Sellado',              'sucursal_id' => 1, 'activo' => 1, 'tipo' => 5, 'orden' => 5, 'usuario_id' => 1],
                ['id' => 30, 'nombre' => 'R01U02',             'nomabre' => '0102',  'desc' => 'Rack 01 Ubicacion 02',            'sucursal_id' => 1, 'activo' => 1, 'tipo' => 2, 'orden' => 6, 'usuario_id' => 1],
            ];
            $nBod = 0;
            foreach ($bodegas as $b) {
                if (DB::table('invbodega')->where('id', $b['id'])->exists()) continue;
                DB::table('invbodega')->insert($b + ['created_at' => $now, 'updated_at' => $now]);
                $nBod++;
            }

            // ── Bodegas de ENTRADA por modulo de inventario ───────────────────
            // invmovmodulo_id: 1=ENTSALINV, 2=ORDDESP, 5=SOLDESP, 7=PESAJE
            $ent = [
                ['id' => 33, 'invmovmodulo_id' => 7, 'invbodega_id' => 24], // PESAJE    -> Pesaje SE
                ['id' => 34, 'invmovmodulo_id' => 1, 'invbodega_id' => 30], // ENTSALINV -> R01U02
            ];
            $nEnt = 0;
            foreach ($ent as $e) {
                if (DB::table('invmovmodulobodent')->where('id', $e['id'])->exists()) continue;
                DB::table('invmovmodulobodent')->insert($e + ['created_at' => $now, 'updated_at' => $now]);
                $nEnt++;
            }

            // ── Bodegas de SALIDA por modulo de inventario ────────────────────
            $sal = [
                ['id' => 60, 'invmovmodulo_id' => 7, 'invbodega_id' => 24], // PESAJE    -> Pesaje SE
                ['id' => 61, 'invmovmodulo_id' => 1, 'invbodega_id' => 30], // ENTSALINV -> R01U02
                ['id' => 62, 'invmovmodulo_id' => 2, 'invbodega_id' => 30], // ORDDESP   -> R01U02
                ['id' => 63, 'invmovmodulo_id' => 5, 'invbodega_id' => 30], // SOLDESP   -> R01U02
            ];
            $nSal = 0;
            foreach ($sal as $s) {
                if (DB::table('invmovmodulobodsal')->where('id', $s['id'])->exists()) continue;
                DB::table('invmovmodulobodsal')->insert($s + ['created_at' => $now, 'updated_at' => $now]);
                $nSal++;
            }

            // ── Categoria de producto por bodega ──────────────────────────────
            // Nota: la bodega 27 (Prod Extrusion) no tiene fila en el origen.
            $cat = [
                ['id' => 372, 'categoriaprod_id' => 10, 'invbodega_id' => 24],
                ['id' => 373, 'categoriaprod_id' => 10, 'invbodega_id' => 25],
                ['id' => 374, 'categoriaprod_id' => 10, 'invbodega_id' => 26],
                ['id' => 375, 'categoriaprod_id' => 10, 'invbodega_id' => 28],
                ['id' => 376, 'categoriaprod_id' => 10, 'invbodega_id' => 29],
                ['id' => 377, 'categoriaprod_id' => 10, 'invbodega_id' => 30],
            ];
            $nCat = 0;
            foreach ($cat as $c) {
                if (DB::table('categoriaprod_invbodega')->where('id', $c['id'])->exists()) continue;
                DB::table('categoriaprod_invbodega')->insert($c + ['created_at' => $now, 'updated_at' => $now]);
                $nCat++;
            }

            $this->command->info("invbodega: $nBod insertadas (total " . DB::table('invbodega')->count() . ")");
            $this->command->info("invmovmodulobodent: $nEnt insertadas");
            $this->command->info("invmovmodulobodsal: $nSal insertadas");
            $this->command->info("categoriaprod_invbodega: $nCat insertadas");
        });

        $this->command->info('Bodegas Modulo Produccion: pobladas correctamente.');
    }
}

<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Puebla las tablas maestras del Modulo Produccion (configuracion: maquinas,
 * etapas, atributos, operarios, etc.) a partir del dump generado 2026-07-27
 * desde la BD de desarrollo `biblioteca`. Reemplaza al script SQL suelto
 * `database/seeders/sql/seed_tablas_maestras_modulo_produccion.sql` (mismo
 * contenido, ejecutado por artisan en vez de mysql client).
 *
 * Ejecutar: php artisan db:seed --class=TablasMaestrasModuloProduccionSeeder --force
 *
 * Idempotente: convierte cada INSERT INTO del dump a INSERT IGNORE INTO, asi
 * que si una fila (por id) ya existe en el destino, se omite sin error y sin
 * duplicar; solo se insertan las filas que faltan. Preserva los ids
 * originales de `biblioteca`.
 *
 * Tablas incluidas (13, en orden de dependencias): maquinagrupo, etapaprod,
 * atributo, operario, maquina, maquinaetapaprod, areaproduccionsucetapaprod,
 * operario_areaproduccionsucep, personaetapaprod, etapaprod_campo, ccparam,
 * ccparam_apsucetapaprod, apsucetapaprod_bodega.
 *
 * Excluida a proposito: acuerdotecnicoapsucetapaprod (depende de
 * acuerdotecnico_id, que es transaccional y diverge entre biblioteca y
 * cualquier copia real de produccion). Configurar manualmente desde la UI
 * en el ambiente destino.
 *
 * IMPORTANTE: antes de correr, verificar que las tablas core de las que
 * dependen estas maestras (sucursal, usuario, unidadmedida, invbodega,
 * persona, areaproduccionsuc) tengan los mismos ids en el destino que en
 * biblioteca — si no coinciden, el INSERT igual entra (FK checks
 * desactivados durante la carga) pero quedan relaciones apuntando a
 * registros equivocados o inexistentes.
 */
class TablasMaestrasModuloProduccionSeeder extends Seeder
{
    public function run()
    {
        $ruta = database_path('seeders/sql/seed_tablas_maestras_modulo_produccion.sql');
        if (!file_exists($ruta)) {
            $this->command->error("No se encontro el archivo: $ruta");
            return;
        }

        $sql = file_get_contents($ruta);

        // Idempotencia: INSERT INTO -> INSERT IGNORE INTO (omite filas cuyo id ya exista, no duplica).
        $sql = preg_replace('/^INSERT INTO/m', 'INSERT IGNORE INTO', $sql);

        DB::unprepared($sql);

        $tablas = [
            'maquinagrupo', 'etapaprod', 'atributo', 'operario', 'maquina',
            'maquinaetapaprod', 'areaproduccionsucetapaprod', 'operario_areaproduccionsucep',
            'personaetapaprod', 'etapaprod_campo', 'ccparam', 'ccparam_apsucetapaprod',
            'apsucetapaprod_bodega',
        ];
        foreach ($tablas as $tabla) {
            $cont = DB::table($tabla)->count();
            $this->command->info("$tabla: $cont filas");
        }

        $this->command->info('Tablas maestras Modulo Produccion: pobladas correctamente.');
    }
}

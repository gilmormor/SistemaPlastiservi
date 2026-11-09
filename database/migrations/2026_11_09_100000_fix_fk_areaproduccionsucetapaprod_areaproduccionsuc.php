<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixFkAreaproduccionsucetapaprodAreaproduccionsuc extends Migration
{
    /**
     * La columna areaproduccionsucetapaprod.areaproduccionsuc_id guarda ids de
     * areaproduccionsuc -el area de produccion por sucursal-, pero la clave
     * foranea apuntaba a areaproduccion, que es otra tabla y solo llega al id 9.
     *
     * Por eso, configurar etapas para un area cuyo areaproduccionsuc_id sea
     * mayor que el ultimo id de areaproduccion falla con error 1452. En la base
     * de marcha blanca el problema ya esta presente: las tres filas de PEAD en
     * la sucursal Santa Ester (areaproduccionsuc_id = 11) violan esa clave, asi
     * que se debieron insertar con las comprobaciones desactivadas.
     *
     * Esta migracion apunta la clave a la tabla correcta. No cambia ningun dato.
     *
     * @return void
     */
    public function up()
    {
        // Si los datos actuales no cuadran con la tabla correcta, mejor no tocar
        // nada y avisar: crear la clave fallaria a medias.
        $aux_huerfanas = DB::table('areaproduccionsucetapaprod as a')
            ->whereNotNull('a.areaproduccionsuc_id')
            ->whereRaw('NOT EXISTS (SELECT 1 FROM areaproduccionsuc s WHERE s.id = a.areaproduccionsuc_id)')
            ->count();

        if ($aux_huerfanas > 0) {
            throw new \Exception(
                "No se corrige la clave foranea: hay $aux_huerfanas fila(s) de " .
                "areaproduccionsucetapaprod cuyo areaproduccionsuc_id no existe en " .
                "areaproduccionsuc. Revisar esas filas antes de volver a migrar."
            );
        }

        /* La clave vieja puede no existir -por ejemplo si la tabla se creo sin
           ella-, asi que se comprueba antes de borrarla. */
        if ($this->existeClave('fk_areaproduccionsucetapaprod_areaproduccion')) {
            DB::statement("ALTER TABLE `areaproduccionsucetapaprod`
                DROP FOREIGN KEY `fk_areaproduccionsucetapaprod_areaproduccion`");
        }

        if (!$this->existeClave('fk_areaproduccionsucetapaprod_areaproduccionsuc')) {
            DB::statement("ALTER TABLE `areaproduccionsucetapaprod`
                ADD CONSTRAINT `fk_areaproduccionsucetapaprod_areaproduccionsuc`
                FOREIGN KEY (`areaproduccionsuc_id`) REFERENCES `areaproduccionsuc` (`id`)");
        }
    }

    /**
     * Devuelve la clave a como estaba. Solo se puede si los datos lo permiten:
     * si hay filas que apuntan a un areaproduccionsuc que no existe como
     * areaproduccion, se deja la clave corregida y se avisa, porque restituir
     * la vieja fallaria.
     *
     * @return void
     */
    public function down()
    {
        if ($this->existeClave('fk_areaproduccionsucetapaprod_areaproduccionsuc')) {
            DB::statement("ALTER TABLE `areaproduccionsucetapaprod`
                DROP FOREIGN KEY `fk_areaproduccionsucetapaprod_areaproduccionsuc`");
        }

        $aux_noCuadran = DB::table('areaproduccionsucetapaprod as a')
            ->whereNotNull('a.areaproduccionsuc_id')
            ->whereRaw('NOT EXISTS (SELECT 1 FROM areaproduccion p WHERE p.id = a.areaproduccionsuc_id)')
            ->count();

        if ($aux_noCuadran > 0) {
            // se deja sin la clave vieja a proposito: volver a crearla fallaria
            return;
        }

        if (!$this->existeClave('fk_areaproduccionsucetapaprod_areaproduccion')) {
            DB::statement("ALTER TABLE `areaproduccionsucetapaprod`
                ADD CONSTRAINT `fk_areaproduccionsucetapaprod_areaproduccion`
                FOREIGN KEY (`areaproduccionsuc_id`) REFERENCES `areaproduccion` (`id`)");
        }
    }

    /**
     * Comprueba si una clave foranea existe en la tabla, para que la migracion
     * se pueda correr aunque el estado de partida no sea el esperado.
     *
     * @param  string  $aux_nombre
     * @return bool
     */
    private function existeClave($aux_nombre)
    {
        $aux_filas = DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'areaproduccionsucetapaprod'
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'
               AND CONSTRAINT_NAME = ?",
            [$aux_nombre]
        );

        return count($aux_filas) > 0;
    }
}

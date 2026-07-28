<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCantenvprogKgenvprogTableOtdet extends Migration
{
    /**
     * Columnas usadas por OtItemEnvProgProdController.php (fix del envío parcial
     * de OtItem a Programación) y Ot.php, pero cuya migración nunca se había
     * creado — solo existían en la BD de desarrollo `biblioteca`, no en el repo.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otdet', function (Blueprint $table) {
            $table->float('cantenvprog', 18, 2)
                ->comment('Cantidad total enviada al módulo de programación')
                ->default(0)
                ->after('cantprog');
            $table->float('kgenvprog', 18, 2)
                ->comment('Kg totales enviados al módulo de programación')
                ->default(0)
                ->after('cantenvprog');
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::table('otdet', function (Blueprint $table) {
            $table->dropColumn(['cantenvprog', 'kgenvprog']);
        });
    }
}

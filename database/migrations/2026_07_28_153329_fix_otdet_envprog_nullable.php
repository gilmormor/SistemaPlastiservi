<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixOtdetEnvprogNullable extends Migration
{
    /**
     * envprogfecha y envprogusu_id solo se llenan al enviar el ítem OT a
     * programación (OtItemEnvProgProdController.php), no al crear la OT.
     * Al quedar NOT NULL sin default en la migración original, cualquier
     * OT nueva fallaba al guardarse (error 1452 / "Field doesn't have a
     * default value" según el modo de MySQL).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otdet', function (Blueprint $table) {
            $table->dateTime('envprogfecha')->comment('Fecha de envio a programacion')->nullable()->change();
            $table->unsignedBigInteger('envprogusu_id')->comment('Usuario que envio a programacion')->nullable()->change();
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::table('otdet', function (Blueprint $table) {
            $table->dateTime('envprogfecha')->comment('Fecha de envio a programacion')->nullable(false)->change();
            $table->unsignedBigInteger('envprogusu_id')->comment('Usuario que envio a programacion')->nullable(false)->change();
        });
    }
}

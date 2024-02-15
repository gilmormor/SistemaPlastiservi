<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdenGiro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('giro', function (Blueprint $table) {
            $table->tinyInteger('orden')->comment('Orden de registros.')->default(0)->after('descripcion');
            $table->unsignedBigInteger('usuario_id')->default(1)->comment('Id Usuario quien creo el registro')->after('orden');
            $table->foreign('usuario_id','fk_giro_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('giro', function (Blueprint $table) {
            $table->dropForeign('fk_giro_usuario');
            $table->dropColumn('usuario_id');
            $table->dropColumn('orden');
        });
    }
}

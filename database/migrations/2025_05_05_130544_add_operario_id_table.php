<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperarioIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opdetprod', function (Blueprint $table) {
            $table->unsignedBigInteger('operario_id')->nullable()->after('opdet_id')->comment('Id operario.');
            $table->foreign('operario_id','fk_opdetprod_operario')->references('id')->on('operario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('maquina_id')->after('operario_id')->comment('Id maquina.');
            $table->foreign('maquina_id','fk_opdetprod_maquina')->references('id')->on('maquina')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->after('kg')->comment('Id usuario.');
            $table->foreign('usuario_id','fk_opdetprod_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opdetprod', function (Blueprint $table) {
            $table->dropForeign('fk_opdetprod_operario');
            $table->dropColumn('operario_id');
            $table->dropForeign('fk_opdetprod_maquina');
            $table->dropColumn('maquina_id');
            $table->dropForeign('fk_opdetprod_usuario');
            $table->dropColumn('usuario_id');
        });
    }
}

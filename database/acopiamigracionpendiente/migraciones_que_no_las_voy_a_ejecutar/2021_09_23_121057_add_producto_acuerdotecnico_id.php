<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductoAcuerdotecnicoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->unsignedBigInteger('acuerdotecnico_id')->comment('Id Acuerdo tecnico. Puede quedar en blanco ya que no todos los productos tienen acuerdo tecnico.')->nullable()->after('tipoprod');
            $table->foreign('acuerdotecnico_id','fk_producto_acuerdotecnico')->references('id')->on('acuerdotecnico')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('acuerdotecnico_id');
        });
    }
}

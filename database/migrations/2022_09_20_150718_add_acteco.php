<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActeco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->string('razonsocial',100)->comment('Razon Social')->default('INDUSTRIAS E INVERSIONES PLASTISERVI S.A.')->after('nombre');
            $table->string('acteco',6)->comment('Código de Actividad económica empresa emisora. SII')->default('252090')->after('iva');
            $table->string('giro',80)->comment('Giro del negocio')->default('FABRICACION DE OTROS ARTICULOS DE PLASTICO')->after('rut');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn('razonsocial');
            $table->dropColumn('acteco');
            $table->dropColumn('giro');
        });
    }
}

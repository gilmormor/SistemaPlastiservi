<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTablaJefaturaSucursalArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jefatura_sucursal_area', function (Blueprint $table) {
            //jefatura_id
            $table->unsignedBigInteger('persona_id')->comment("Id persona. Jefe de Jefatura o Departamento")->nullable()->after('jefatura_id');
            $table->foreign('persona_id','fk_jefatura_sucursal_area_persona')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jefatura_sucursal_area', function (Blueprint $table) {
            //
        });
    }
}

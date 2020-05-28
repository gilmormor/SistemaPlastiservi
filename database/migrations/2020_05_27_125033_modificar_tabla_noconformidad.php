<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTablaNoconformidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            $table->unsignedBigInteger('rechazonc_id')->comment('Id Rechazo No Conformidad.')->after('fechaaprobado');
            $table->foreign('rechazonc_id','fk_noconformidad_rechazonc')->references('id')->on('rechazonc')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            //
        });
    }
}

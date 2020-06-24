<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableNoconformidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            $table->integer('stavalai')->comment('Estatus validacion Accion inmediata no conformidad. Validar si la NC es o no una NC real. 1=Aplica para no conformidad 2=no aplica para no conformidad.')->after('accioninmediatafec')->nullable();
            $table->string('obsvalai',250)->comment('Observacion validacion Accion inmediata no conformidad.')->after('stavalai')->nullable();
            $table->dateTime('fechavalai')->comment('Fecha de validacion Accion inmediata no conformidad.')->after('obsvalai')->nullable();
            $table->unsignedBigInteger('usuario_idvalai')->comment('Usuario que valido la accion inmediata. Ya sea aceptacion o rechazo.')->after('fechavalai')->nullable();
            $table->foreign('usuario_idvalai','fk_noconformidad_usuariovalai')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipodoctoFoliocontrol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('foliocontrol', function (Blueprint $table) {
            $table->tinyInteger('tipodocto')->nullable()->comment('Tipo documento. 33: Factura Electrónica, 34: Factura No Afecta o Exenta Electrónica, 56: Nota de Débito Electrónica, 61: Nota de Crédito Electrónica')->default(0)->after('desc');
            $table->string('nombrepdf',12)->nullable()->comment('Inicio Nombre del PDF para Generar el archivo DTE')->default("")->after('tipodocto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('foliocontrol', function (Blueprint $table) {
            $table->dropColumn('tipodocto');
            $table->dropColumn('nombrepdf');
        });
    }
}

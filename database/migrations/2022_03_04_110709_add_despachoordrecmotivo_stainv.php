<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDespachoordrecmotivoStainv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despachoordrecmotivo', function (Blueprint $table) {
            $table->tinyInteger('tipomovinv')->after('desc')->comment('Tipo de movimiento para identificar si el motivo de rechazo hace que los productos involucrados entra o no al inventario. 0=No entra al inventario, 1=entra al inv, 2=entra y sale por defecto etc, 3=NO entra al inv');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('despachoordrecmotivo', function (Blueprint $table) {
            $table->dropColumn('tipomovinv');
        });
    }
}

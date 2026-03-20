<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostototalDocinsumodet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('docinsumodet', function (Blueprint $table) {
            $table->float('canttotal',12,4)->comment('Cantidad total insumos')->after('cant');
            $table->float('unidadesproducto',12,2)->comment('Costo total')->after('canttotal');
            $table->float('costototal',12,2)->comment('Costo total')->after('costounitario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('docinsumodet', function (Blueprint $table) {
            $table->dropColumn('canttotal');
            $table->dropColumn('unidadesproducto');
            $table->dropColumn('costototal');
        });
    }
}

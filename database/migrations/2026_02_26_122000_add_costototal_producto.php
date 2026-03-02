<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostototalProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->decimal('costototal',12,4)->comment('Costo total del producto calculado a partir de los insumos')->after('stockmax')->default(0);
            $table->boolean('costobloqueado')->comment('Si es true No recalcula el costototal, true Producto con costo manual')->after('costototal')->nullable();
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
            $table->dropColumn('costototal');
            $table->dropColumn('costobloqueado');
        });
    }
}

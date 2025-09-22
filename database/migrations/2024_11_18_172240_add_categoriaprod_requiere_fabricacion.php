<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriaprodRequiereFabricacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->boolean('requiere_fabricacion')->comment('Status para identificar si el producto requiere fabricacion previa, 1=producto se fabrica contra pedido 0=producto se fabrica para stock.')->default(0)->after('stadespsinstock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->dropColumn('requiere_fabricacion');
        });
    }
}

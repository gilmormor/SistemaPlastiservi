<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSkuProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->string('sku', 50)->nullable()->after('id');
        });
        // 2️⃣ Copiar id → sku
        DB::statement('UPDATE producto SET sku = CAST(id AS CHAR)');

        // 3️⃣ Hacer el campo NOT NULL + UNIQUE
        Schema::table('producto', function (Blueprint $table) {
            $table->string('sku', 50)->nullable(false)->change();
            $table->unique('sku', 'producto_sku_unique');
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
            $table->dropUnique('producto_sku_unique');
            $table->dropColumn('sku');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriaprodgrupoId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->unsignedBigInteger('categoriaprodgrupo_id')->comment('Sucursal de despacho')->nullable()->after('mostunimed');
            $table->foreign('categoriaprodgrupo_id','fk_categoriaprod_categoriaprodgrupo')->references('id')->on('categoriaprodgrupo')->onDelete('restrict')->onUpdate('restrict');

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
            $table->dropForeign('fk_categoriaprod_categoriaprodgrupo');
            $table->dropColumn('categoriaprodgrupo_id');
        });
    }
}

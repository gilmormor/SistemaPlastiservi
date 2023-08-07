<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComisionventasCategoriaprodgrupo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprodgrupo', function (Blueprint $table) {
            $table->float('comisionventas',8,2)->comment('% Comision ventas por grupo categoria.')->nullable()->after('desc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categoriaprodgrupo', function (Blueprint $table) {
            $table->dropColumn('comisionventas');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaUsuarioIdProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id')->nullable()->after('stockmax')->comment('Id de usuario que creo el registro.');
            $table->foreign('usuario_id','fk_producto_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');            
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
            $table->dropForeign('fk_producto_usuario');
            $table->dropColumn('usuario_id');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGrupocatpromcategoriaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupocatpromcategoriaprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('grupocatprom_id');
            $table->foreign('grupocatprom_id','fk_grupocatpromcategoriaprod_grupocatprom')->references('id')->on('grupocatprom')->onDelete('cascade')->onUpdate('restrict');
            $table->unsignedBigInteger('categoriaprod_id');
            $table->foreign('categoriaprod_id','fk_grupocatpromcategoriaprod_categoriaprod')->references('id')->on('categoriaprod')->onDelete('cascade')->onUpdate('restrict');
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupocatpromcategoriaprod');
    }
}

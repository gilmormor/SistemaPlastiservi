<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDatacobranzadet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datacobranzadet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('datacobranza_id')->comment('Relacion con DataCobranza');
            $table->foreign('datacobranza_id','fk_datacobranzadet_datacobranza')->references('id')->on('datacobranza')->onDelete('cascade')->onUpdate('restrict');
            $table->unsignedBigInteger('nrofav')->comment('Numero de Documento Folio')->nullable();
            $table->date('fecfact')->comment('Fecha Factura.')->nullable();
            $table->date('fecvenc')->comment('Fecha Factura.')->nullable();
            $table->double('mnttot',18)->comment('Monto Factura.');
            $table->double('deuda',18)->comment('Monto Deuda.');
            $table->tinyInteger('stavencida')->comment('No=0, Si=1');
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
        Schema::dropIfExists('datacobranzadet');
    }
}

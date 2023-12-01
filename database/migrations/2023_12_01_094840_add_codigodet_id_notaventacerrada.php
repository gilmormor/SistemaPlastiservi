<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodigodetIdNotaventacerrada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventacerrada', function (Blueprint $table) {
            $table->unsignedBigInteger('codigodet_id')->comment('Id codigodet')->nullable()->after('motcierre_id')->nullable();
            $table->foreign('codigodet_id','fk_notaventacerrada_codigodet')->references('id')->on('codigodet')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notaventacerrada', function (Blueprint $table) {
            $table->dropForeign('fk_notaventacerrada_codigodet');
            $table->dropColumn('codigodet_id');
        });
    }
}

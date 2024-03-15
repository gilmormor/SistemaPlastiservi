<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStabloxdeusiscobEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->boolean('stabloxdeusiscob')->comment('Status bloqueo por deuda y limite de credito sistema Externo de cobranza 0=No, 1=Si')->default(0)->after('actsiscob');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn('stabloxdeusiscob');
        });
    }
}

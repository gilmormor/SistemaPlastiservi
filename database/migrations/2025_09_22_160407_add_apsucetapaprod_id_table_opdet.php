<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApsucetapaprodIdTableOpdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opdet', function (Blueprint $table) {
            $table->unsignedBigInteger('apsucetapaprod_id')->nullable()->after('op_id')->comment('Id apsucetapaprod.');
            $table->foreign('apsucetapaprod_id','fk_opdet_apsucetapaprod')->references('id')->on('areaproduccionsucetapaprod')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opdet', function (Blueprint $table) {
            $table->dropForeign('fk_opdet_apsucetapaprod');
            $table->dropColumn('apsucetapaprod_id');
        });
    }
}

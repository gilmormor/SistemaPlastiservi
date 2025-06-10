<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEmailxlotePersona extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emailxlote_persona', function (Blueprint $table) {
            $table->foreign('emailxlote_id', 'fk_emailxlote_persona_emailxlote')
                ->references('id')
                ->on('emailxlote')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('persona_id', 'fk_emailxlote_persona_persona')
                ->references('id')
                ->on('persona')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emailxlote_persona', function (Blueprint $table) {
            $table->dropForeign('fk_emailxlote_persona_emailxlote');
            $table->dropForeign('fk_emailxlote_persona_persona');
        });
    }
}

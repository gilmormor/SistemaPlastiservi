<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModificarTablePreciounitNotaventadetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventadetalle', function (Blueprint $table) {
            DB::statement("ALTER TABLE notaventadetalle MODIFY COLUMN preciounit DOUBLE(12,4)");
            DB::statement("ALTER TABLE notaventadetalle MODIFY COLUMN precioxkilo DOUBLE(12,4)");
            DB::statement("ALTER TABLE notaventadetalle MODIFY COLUMN precioxkiloreal DOUBLE(12,4)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notaventadetalle', function (Blueprint $table) {
            DB::statement("ALTER TABLE notaventadetalle MODIFY COLUMN preciounit DOUBLE(10,2)");
            DB::statement("ALTER TABLE notaventadetalle MODIFY COLUMN precioxkilo DOUBLE(10,2)");
            DB::statement("ALTER TABLE notaventadetalle MODIFY COLUMN precioxkiloreal DOUBLE(10,2)");
        });
    }
}

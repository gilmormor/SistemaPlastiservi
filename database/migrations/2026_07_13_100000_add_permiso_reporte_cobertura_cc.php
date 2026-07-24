<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddPermisoReporteCoberturaCc extends Migration
{
    public function up()
    {
        // Permiso 520: Reporte de Cobertura CC
        DB::table('permiso')->insert([
            'id'     => 520,
            'nombre' => 'Reporte Cobertura CC',
            'slug'   => 'reporte-cobertura-cc',
        ]);
    }

    public function down()
    {
        DB::table('permiso')->where('id', 520)->delete();
    }
}

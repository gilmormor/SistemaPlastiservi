<?php

use App\Models\PlazoPago;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TablaPlazoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rols = [
            'CONTADO',
            '15 DIAS',
            '30 DIAS',
            '45 DIAS',
            '60 DIAS',
            '75 DIAS',
            '90 DIAS',
            '120 DIAS'
        ];
        foreach($rols as  $key => $value){
            PlazoPago::create([
                'descripcion' => $value,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}

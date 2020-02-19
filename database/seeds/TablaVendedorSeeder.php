<?php

use App\Models\Vendedor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TablaVendedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rols = [
            '2',
            '3'
        ];
        foreach($rols as  $key => $value){
            Vendedor::create([
                'persona_id' => $value,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}

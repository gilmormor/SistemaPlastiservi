<?php

use App\Models\FormaPago;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TablaFormapagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rols = [
            'EFECTIVO',
            'CHEQUE',
            'TRANSFERENCIA'
        ];
        foreach($rols as  $key => $value){
            FormaPago::create([
                'descripcion' => $value,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}

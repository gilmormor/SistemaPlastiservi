<?php

use App\Models\JefaturaSucursalAreaPersona;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TablaJefatura_sucursal_area_personaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['jefatura_sucursal_area_id' => '7',
            'persona_id' => '2',
            ],
            ['jefatura_sucursal_area_id' => '15',
            'persona_id' => '2',
            ],
            ['jefatura_sucursal_area_id' => '5',
            'persona_id' => '4',
            ],
            ['jefatura_sucursal_area_id' => '11',
            'persona_id' => '4',
            ],
            ['jefatura_sucursal_area_id' => '7',
            'persona_id' => '3'
            ]
        ];
        foreach($datas as $data){
            JefaturaSucursalAreaPersona::create([
                'jefatura_sucursal_area_id' => $data['jefatura_sucursal_area_id'],
                'persona_id' => $data['persona_id'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}

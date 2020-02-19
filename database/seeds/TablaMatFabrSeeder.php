<?php

use App\Models\MatFabr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TablaMatFabrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['nombre' => 'BLANCO PEBD',
            'descripcion' => 'Alta'
            ],
            ['nombre' => 'PEAD',
            'descripcion' => 'Alta'
            ],
            ['nombre' => 'PEAD-BD Mezcla',
            'descripcion' => 'Mezcla'
            ],
            ['nombre' => 'PEBD',
            'descripcion' => 'Baja Virjen'
            ],
            ['nombre' => 'PEBD BINS',
            'descripcion' => 'Mezcla'
            ],
            ['nombre' => 'RECUPERADO NEGRO',
            'descripcion' => 'Baja - Rigido'
            ],
            ['nombre' => 'RECUPERADO COLOR',
            'descripcion' => 'Baja - Rigido'
            ],
            ['nombre' => 'TRANSPARENTE 1',
            'descripcion' => 'Baja - Rigido'
            ],
            ['nombre' => 'TRANSPARENTE 2',
            'descripcion' => 'Baja - Rigido'
            ]
        ];
        foreach($datas as $data){
            MatFabr::create([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}

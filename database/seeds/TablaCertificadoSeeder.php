<?php

use App\Models\Certificado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TablaCertificadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['descripcion' => 'ISO 9001',
            'foto' => '',
            ],
            ['descripcion' => 'ISO 14001',
            'foto' => '',
            ],
            ['descripcion' => 'ISO 22000',
            'foto' => '',
            ],
            ['descripcion' => 'ISO32000',
            'foto' => 'pcQG8YqnL5.jpg',
            ],
            ['descripcion' => 'ISO 56000',
            'foto' => 'ISO 56000.jpg',
            ],
            ['descripcion' => 'ISO 82000',
            'foto' => 'mJSgdCwwF3.jpg',
            ]
        ];
        foreach($datas as $data){
            Certificado::create([
                'descripcion' => $data['descripcion'],
                'foto' => $data['foto'],
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}

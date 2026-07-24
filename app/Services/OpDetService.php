<?php

namespace App\Services;

use App\Models\Op;
use App\Models\OpDet;

class OpDetService
{
    public function guardarDesdeRequest(Op $op, array $data): void
    {
        $this->guardarSiAplica($op, $data, 'extrusora', 'obsext');
        $this->guardarSiAplica($op, $data, 'impresora', 'obsimp');
        $this->guardarSiAplica($op, $data, 'selladora', 'obssell');
    }

    private function guardarSiAplica(Op $op, array $data, string $campoMaquina, string $campoObs): void
    {
        if (!isset($data[$campoMaquina]) || $data[$campoMaquina] === 'X') return;

        $opdet = $op->opdets()->create([
            'otdet_id' => $data['id'],
            'obs' => $data[$campoObs] ?? null,
        ]);

        $opdet->opdetmaquinas()->create([
            'opdet_id' => $opdet->id,
            'maquina_id' => $data[$campoMaquina],
        ]);
    }
}
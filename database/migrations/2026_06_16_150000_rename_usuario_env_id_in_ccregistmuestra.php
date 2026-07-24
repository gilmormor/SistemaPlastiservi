<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class RenameUsuarioEnvIdInCcregistmuestra extends Migration
{
    public function up()
    {
        // Renombra usuario_env_id → usuariostaenv_id y recrea la FK con nuevo nombre
        DB::statement("ALTER TABLE ccregistmuestra
            DROP FOREIGN KEY fk_ccregistmuestra_usuarioenv,
            CHANGE usuario_env_id usuariostaenv_id BIGINT UNSIGNED NULL
                COMMENT 'Usuario que aprobó el registro de muestra (sta_env=1)',
            ADD CONSTRAINT fk_ccregistmuestra_usuariostaenv
                FOREIGN KEY (usuariostaenv_id) REFERENCES usuario(id)
                ON DELETE RESTRICT ON UPDATE RESTRICT");
    }

    public function down()
    {
        DB::statement("ALTER TABLE ccregistmuestra
            DROP FOREIGN KEY fk_ccregistmuestra_usuariostaenv,
            CHANGE usuariostaenv_id usuario_env_id BIGINT UNSIGNED NULL
                COMMENT 'Usuario que liberó/envió al siguiente módulo',
            ADD CONSTRAINT fk_ccregistmuestra_usuarioenv
                FOREIGN KEY (usuario_env_id) REFERENCES usuario(id)
                ON DELETE RESTRICT ON UPDATE RESTRICT");
    }
}

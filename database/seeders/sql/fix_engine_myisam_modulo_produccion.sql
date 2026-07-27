-- Fix: 4 tablas del Módulo Producción quedaron creadas como MyISAM en producción
-- (maquinagrupo, atributo, maquina, maquinaatributo), porque sus migraciones no fijan
-- $table->engine='InnoDB' explícitamente y el motor por defecto del servidor no es InnoDB.
-- MyISAM ignora silenciosamente la sintaxis de FOREIGN KEY al crear la tabla (sin error),
-- por eso quedaron como simples KEY (índice) sin CONSTRAINT real. Esto recién se detectó
-- al migrar opdetmaquina (que sí fija engine=InnoDB explícito) y MySQL rechazó agregarle
-- la FK hacia maquina (MyISAM) con el error 1215.
--
-- Confirmado 2026-07-27: las 4 tablas + opdetmaquina tienen 0 filas en producción,
-- así que no hay riesgo de violar integridad referencial al agregar las constraints.
--
-- Orden: 1) convertir motor a InnoDB, 2) restaurar las FK que MyISAM había ignorado,
-- 3) completar la FK que le falta a opdetmaquina.

-- ============ 1. Convertir a InnoDB ============
ALTER TABLE maquinagrupo ENGINE=InnoDB;
ALTER TABLE atributo ENGINE=InnoDB;
ALTER TABLE maquina ENGINE=InnoDB;
ALTER TABLE maquinaatributo ENGINE=InnoDB;

-- ============ 2. Restaurar FKs que MyISAM ignoró silenciosamente ============

-- maquinagrupo
ALTER TABLE maquinagrupo ADD CONSTRAINT fk_maquinagrupo_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- atributo
ALTER TABLE atributo ADD CONSTRAINT fk_atributo_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- maquina (depende de maquinagrupo, ya convertida arriba)
ALTER TABLE maquina ADD CONSTRAINT fk_maquina_sucursal
    FOREIGN KEY (sucursal_id) REFERENCES sucursal(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE maquina ADD CONSTRAINT fk_maquina_maquinagrupo
    FOREIGN KEY (maquinagrupo_id) REFERENCES maquinagrupo(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE maquina ADD CONSTRAINT fk_maquina_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- maquinaatributo (depende de maquina y atributo, ya convertidas arriba)
-- Nota: la columna atributo_id referencia la tabla `producto`, tal como está definido
-- en la migración original 2025_03_20_115143_create_table_maquinaatributo.php (no es un error de este fix).
ALTER TABLE maquinaatributo ADD CONSTRAINT fk_maquinaatributo_maquina
    FOREIGN KEY (maquina_id) REFERENCES maquina(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE maquinaatributo ADD CONSTRAINT fk_maquinaatributo_atributo
    FOREIGN KEY (atributo_id) REFERENCES producto(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE maquinaatributo ADD CONSTRAINT fk_maquinaatributo_unidadmedida
    FOREIGN KEY (unidadmedida_id) REFERENCES unidadmedida(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE maquinaatributo ADD CONSTRAINT fk_maquinaatributo_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- ============ 3. Completar la FK que faltaba en opdetmaquina ============
-- (la tabla y fk_opdetmaquina_opdet ya existen; solo falta esta)
ALTER TABLE opdetmaquina ADD CONSTRAINT fk_opdetmaquina_maquina
    FOREIGN KEY (maquina_id) REFERENCES maquina(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

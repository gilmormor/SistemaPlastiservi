-- Fix: bin y operario_areaproduccionsucep quedaron creadas como MyISAM en producción
-- (mismo patrón que maquinagrupo/atributo/maquina/maquinaatributo, ver
-- fix_engine_myisam_modulo_produccion.sql): sus migraciones no fijaban
-- $table->engine='InnoDB' explícitamente, y MyISAM ignora silenciosamente la sintaxis
-- de FOREIGN KEY al crear la tabla (sin error), dejando las FKs como simple KEY (índice).
--
-- Se detectó al migrar 2025_11_10_113828_create_table_opdetregprodtempmezclado
-- (que sí fija engine=InnoDB) y MySQL rechazó agregarle la FK hacia bin (MyISAM),
-- error 1215. operario_areaproduccionsucep no ha fallado aún (nada la referenció
-- todavía con una tabla InnoDB), pero tiene el mismo problema latente.
--
-- Confirmado 2026-07-27: ambas tablas tienen 0 filas en producción, sin riesgo de
-- violar integridad referencial al agregar las constraints.
--
-- Las migraciones fuente ya se corrigieron (agregado $table->engine='InnoDB') para
-- que instalaciones nuevas/limpias no repitan este problema.

-- ============ 1. Convertir a InnoDB ============
ALTER TABLE bin ENGINE=InnoDB;
ALTER TABLE operario_areaproduccionsucep ENGINE=InnoDB;

-- ============ 2. Restaurar FKs que MyISAM ignoró silenciosamente ============

-- bin
ALTER TABLE bin ADD CONSTRAINT fk_bin_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- operario_areaproduccionsucep (depende de operario y areaproduccionsucetapaprod, ya InnoDB)
ALTER TABLE operario_areaproduccionsucep ADD CONSTRAINT fk_operario_areaproduccionsucep_areaproduccion
    FOREIGN KEY (operario_id) REFERENCES operario(id) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE operario_areaproduccionsucep ADD CONSTRAINT fk_operario_areaproduccionsucep_areaproduccionsucep
    FOREIGN KEY (areaproduccionsucep_id) REFERENCES areaproduccionsucetapaprod(id) ON DELETE RESTRICT ON UPDATE RESTRICT;

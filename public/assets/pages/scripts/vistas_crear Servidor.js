/*
Nombre: vista_despordxdespsoltotales
SQL: 
select `despachoord`.`despachosol_id` AS `despachosol_id`,sum(`despachoorddet`.`cantdesp`) AS `cantdesp`,sum(`notaventadetalle`.`totalkilos`) AS `nvtotalkg`,sum((`notaventadetalle`.`totalkilos` / `notaventadetalle`.`cant`)) AS `pesoxunit`,sum(((`notaventadetalle`.`totalkilos` / `notaventadetalle`.`cant`) * `despachoorddet`.`cantdesp`)) AS `totalkilos` from ((`despachoord` join `despachoorddet` on((`despachoord`.`id` = `despachoorddet`.`despachoord_id`))) join `notaventadetalle` on((`despachoorddet`.`notaventadetalle_id` = `notaventadetalle`.`id`))) where ((not(`despachoord`.`id` in (select `despachoordanul`.`despachoord_id` from `despachoordanul`))) and isnull(`despachoord`.`deleted_at`)) group by `despachoord`.`despachosol_id`


********************************

Nombre: vista_despsoltotales
SQL: 
select `despachosol`.`id` AS `id`,sum(`despachosoldet`.`cantsoldesp`) AS `cantsoldesp`,sum(((`notaventadetalle`.`totalkilos` / `notaventadetalle`.`cant`) * `despachosoldet`.`cantsoldesp`)) AS `totalkilos`,sum(((`notaventadetalle`.`subtotal` / `notaventadetalle`.`cant`) * `despachosoldet`.`cantsoldesp`)) AS `subtotalsoldesp` from ((`despachosol` join `despachosoldet` on((`despachosol`.`id` = `despachosoldet`.`despachosol_id`))) join `notaventadetalle` on((`despachosoldet`.`notaventadetalle_id` = `notaventadetalle`.`id`))) where ((not(`despachosol`.`id` in (select `despachosolanul`.`despachosol_id` from `despachosolanul`))) and isnull(`despachosol`.`deleted_at`)) group by `despachosol`.`id`  


*************************************

Nombre: vista_notaventatotales

select `notaventa`.`id` AS `id`,sum(`notaventadetalle`.`cant`) AS `cant`,sum(`notaventadetalle`.`precioxkilo`) AS `precioxkilo`,sum(`notaventadetalle`.`totalkilos`) AS `totalkilos`,sum(`notaventadetalle`.`subtotal`) AS `subtotal` from (`notaventa` join `notaventadetalle` on(((`notaventa`.`id` = `notaventadetalle`.`notaventa_id`) and isnull(`notaventa`.`anulada`) and isnull(`notaventa`.`deleted_at`) and isnull(`notaventadetalle`.`deleted_at`)))) group by `notaventa`.`id` 


***************************************

Nombre: vista_sumorddespdet

select `despachoorddet`.`despachosoldet_id` AS `despachosoldet_id`,sum(`despachoorddet`.`cantdesp`) AS `cantdesp` from (`despachoord` join `despachoorddet` on((`despachoord`.`id` = `despachoorddet`.`despachoord_id`))) where ((not(`despachoord`.`id` in (select `despachoordanul`.`despachoord_id` from `despachoordanul`))) and isnull(`despachoord`.`deleted_at`)) group by `despachoorddet`.`despachosoldet_id`


***************************************

Nombre: vista_sumsoldespdet

select `despachosoldet`.`notaventadetalle_id` AS `notaventadetalle_id`,`despachosol`.`notaventa_id` AS `notaventa_id`,sum(`despachosoldet`.`cantsoldesp`) AS `cantsoldesp`,`producto`.`peso` AS `peso`,(sum(`despachosoldet`.`cantsoldesp`) * `producto`.`peso`) AS `kgsoldesp`,sum(((`notaventadetalle`.`subtotal` / `notaventadetalle`.`cant`) * `despachosoldet`.`cantsoldesp`)) AS `subtotalsoldesp` from (((`despachosol` join `despachosoldet` on((`despachosol`.`id` = `despachosoldet`.`despachosol_id`))) join `notaventadetalle` on((`despachosoldet`.`notaventadetalle_id` = `notaventadetalle`.`id`))) join `producto` on((`notaventadetalle`.`producto_id` = `producto`.`id`))) where ((not(`despachosol`.`id` in (select `despachosolanul`.`despachosol_id` from `despachosolanul`))) and isnull(`despachosol`.`deleted_at`)) group by `despachosoldet`.`notaventadetalle_id`,`despachosol`.`notaventa_id` 

*/
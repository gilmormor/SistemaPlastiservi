/*
Nombre: vista_sumorddespdet
SQL: 
select despachosoldet_id,sum(despachoorddet.cantdesp) AS cantdesp 
from (despachoord join despachoorddet 
on((despachoord.id = despachoorddet.despachoord_id))) 
where ((not(despachoord.id in (select despachoordanul.despachoord_id from despachoordanul))) 
and isnull(despachoord.deleted_at)) 
group by despachoorddet.despachosoldet_id 

********************************

Nombre: vista_sumsoldespdet
SQL: 
select notaventadetalle_id,sum(despachosoldet.cantsoldesp) AS cantsoldesp 
from (despachosol join despachosoldet 
on((despachosol.id = despachosoldet.despachosol_id))) 
where ((not(despachosol.id in (select despachosolanul.despachosol_id from despachosolanul))) 
and isnull(despachosol.deleted_at)) group by despachosoldet.notaventadetalle_id

Modificada: 20/11/2020
select notaventadetalle_id,despachosol.notaventa_id,sum(despachosoldet.cantsoldesp) AS cantsoldesp,
producto.peso, sum(despachosoldet.cantsoldesp)*producto.peso AS kgsoldesp,
sum((notaventadetalle.subtotal/notaventadetalle.cant)*despachosoldet.cantsoldesp) AS subtotalsoldesp
from (despachosol join despachosoldet 
on((despachosol.id = despachosoldet.despachosol_id))) 
INNER JOIN notaventadetalle
ON despachosoldet.notaventadetalle_id=notaventadetalle.id
INNER JOIN producto
ON notaventadetalle.producto_id=producto.id
where ((not(despachosol.id in (select despachosolanul.despachosol_id from despachosolanul))) 
and isnull(despachosol.deleted_at)) 
group by despachosoldet.notaventadetalle_id,despachosol.notaventa_id 


************************************

Nombre: vista_notaventatotales

SELECT notaventa.id,
sum(notaventadetalle.cant) AS cant,
sum(notaventadetalle.precioxkilo) AS precioxkilo,
sum(notaventadetalle.totalkilos) AS totalkilos,
sum(notaventadetalle.subtotal) AS subtotal
FROM notaventa INNER JOIN notaventadetalle
ON notaventa.id=notaventadetalle.notaventa_id
and notaventa.anulada is null
and notaventa.deleted_at is null and notaventadetalle.deleted_at is null
group by notaventa.id 




*/
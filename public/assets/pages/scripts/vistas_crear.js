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

*/
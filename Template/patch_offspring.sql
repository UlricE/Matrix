update @TABLE_PREFIX@_register set offspring = 0;
update @TABLE_PREFIX@_register k1,
(select far_id, count(*) n
from @TABLE_PREFIX@_register
group by far_id) k2
set k1.offspring = k2.n
where k1.id = k2.far_id;
update @TABLE_PREFIX@_register k1,
(select mor_id, count(*) n
from @TABLE_PREFIX@_register
group by mor_id) k2
set k1.offspring = k1.offspring+k2.n
where k1.id = k2.mor_id;

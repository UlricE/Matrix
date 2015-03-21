
select '' 'Letar efter hanar som är mödrar';

select distinct k1.nummer, k1.namn, k1.mor_nr, k1.mor, k1.far_nr, k1.far
from @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
where k1.mor_id = k2.id
and k2.kon = 'hane';

select '' 'Letar efter honor som är fäder';

select distinct k1.nummer, k1.namn, k1.mor_nr, k1.mor, k1.far_nr, k1.far
from @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
where k1.far_id = k2.id
and k2.kon = 'hona';

select '' 'Letar efter djur utan regnr';

select namn
from @TABLE_PREFIX@_register
where nummer = '';

select '' 'Letar efter regnr som finns på flera djur';

select nummer, count(*) n
from @TABLE_PREFIX@_register
where nummer != ''
group by nummer
having n > 1;

select '' 'Letar efter djur som har oidentifierad mor';

select nummer, namn, mor, mor_nr
from @TABLE_PREFIX@_register
where mor_nr != ''
and mor_id is null;

select '' 'Letar efter djur som har oidentifierad far';

select nummer, namn, far, far_nr
from @TABLE_PREFIX@_register
where far_nr != ''
and far_id is null;

select '' 'Letar efter djur utan födelsedatum';
select nummer, namn
from @TABLE_PREFIX@_register
where fodd = '0000-00-00';

select '' 'Letar efter djur som är födda före sin mor';
select k1.nummer, k1.namn, k1.fodd, k1.mor_nr, k1.mor, k2.fodd
from @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
where k1.mor_id = k2.id
and k1.fodd <= k2.fodd;

select '' 'Letar efter djur som är födda före sin far';
select k1.nummer, k1.namn, k1.fodd, k1.far_nr, k1.far, k2.fodd
from @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
where k1.far_id = k2.id
and k1.fodd <= k2.fodd;

select '' 'Letar efter mismatch i mödrars namn';
select k1.nummer, k1.namn, k1.mor_nr, k1.mor, k2.namn
from @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
where k1.mor_id = k2.id
and k1.mor != k2.namn;

select '' 'Letar efter mismatch i fäders namn';
select k1.nummer, k1.namn, k1.far_nr, k1.far, k2.namn
from @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
where k1.far_id = k2.id
and k1.far != k2.namn;

select '' 'Letar efter djur med far men utan far_id';
select nummer, namn, far, far_nr
from @TABLE_PREFIX@_register
where far_id is null
and far != '';

select '' 'Letar efter djur med mor men utan mor_id';
select nummer, namn, mor, mor_nr
from @TABLE_PREFIX@_register
where mor_id is null
and mor != '';


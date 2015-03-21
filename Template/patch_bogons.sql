select '' 'Markera djur med otillförlitlig härstamning';
update @TABLE_PREFIX@_register set bogon = null;
update @TABLE_PREFIX@_register set bogon = 1 where mor_id is null and far_id is null and fodd > '2000-01-01';
update @TABLE_PREFIX@_register set bogon = null where nummer = '7-211G' and namn = 'Sture';

update @TABLE_PREFIX@_register r1, @TABLE_PREFIX@_register r2
set r1.bogon = 2
where r1.far_id = r2.id
and r1.bogon is null
and r2.bogon is not null;

update @TABLE_PREFIX@_register r1, @TABLE_PREFIX@_register r2
set r1.bogon = 3
where r1.mor_id = r2.id
and r1.bogon is null
and r2.bogon is not null;

update @TABLE_PREFIX@_register r1, @TABLE_PREFIX@_register r2
set r1.bogon = 4
where r1.far_id = r2.id
and r1.bogon is null
and r2.bogon is not null;

update @TABLE_PREFIX@_register r1, @TABLE_PREFIX@_register r2
set r1.bogon = 5
where r1.mor_id = r2.id
and r1.bogon is null
and r2.bogon is not null;

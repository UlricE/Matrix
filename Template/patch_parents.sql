-- Update mor_id and far_id fields
-- Some animals have duplicate numbers, which means that both number and name must be used to identify the parents.
-- Founders do not have parents (mor_nr/far_nr is empty)

select '' 'Pass 0, ta bort föräldra-id';

update @TABLE_PREFIX@_register
set mor_id = null, far_id = null;

select '' 'Pass 1, matcha både regnr och namn';

update @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
set k1.mor_id = k2.id
where k1.mor_id is null
and k1.mor_nr = k2.nummer
and k1.mor_nr != ''
and k1.mor = k2.namn;

update @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
set k1.far_id = k2.id
where k1.far_id is null
and k1.far_nr = k2.nummer
and k1.far_nr != ''
and k1.far = k2.namn;

select '' 'Pass 2, matcha bara regnr för djur som saknar förälder';

update @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
set k1.mor_id = k2.id
where k1.mor_id is null
and k1.mor_nr = k2.nummer
and k1.mor_nr != '';

update @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
set k1.far_id = k2.id
where k1.far_id is null
and k1.far_nr = k2.nummer
and k1.far_nr != '';

select '' 'Pass 3, matcha bara namn för djur som saknar förälder';

update @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
set k1.mor_id = k2.id
where k1.mor_id is null
and k1.mor != ''
and k1.mor = k2.namn;

update @TABLE_PREFIX@_register k1, @TABLE_PREFIX@_register k2
set k1.far_id = k2.id
where k1.far_id is null
and k1.far != ''
and k1.far = k2.namn;


#!/bin/sh

. Lib/config.sh
. Lib/common.sh

load_config "$1"

fas()
{
	wright -u $URL \
		-t ${table_prefix}_register -i id -b fodd -d mor_id -s far_id \
		-c ic \
		-l 100 \
		-f "g$1 != '' and bogon is null"
}

cat > ${table_prefix}_fas.sql << END
drop table if exists ${table_prefix}_fas;
create table ${table_prefix}_fas (
	year int unique not null,
	fas float not null
);
END

for year in `seq $first_year $last_year`; do
	fas=`fas $year|cut -f 2 -d :`
	if test ! -z "$fas"; then
		echo "insert into ${table_prefix}_fas (year, fas) values ($year, $fas);"
	else
		echo "-- No data for $year"
	fi
done >> ${table_prefix}_fas.sql

echo "Output in ${table_prefix}_fas.sql"


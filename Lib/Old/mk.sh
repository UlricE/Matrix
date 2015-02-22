#!/bin/sh

. Lib/config.sh

echo "truncate table ${table_prefix}_average_mk;"
for n in `seq $first_year $last_year`; do
	echo "insert into ${table_prefix}_average_mk (year, amk)
		select $n, avg(mk$n) from ${table_prefix}_register;"
done

if test $this_year -gt $last_year; then
	echo "insert into ${table_prefix}_average_mk (year, amk)
		select $this_year, avg(mk$this_year) from ${table_prefix}_register;"
fi

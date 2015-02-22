#!/bin/sh

usage()
{
        echo "Usage: $0 config.cfg"
        exit
}

load_config()
{
	if test -z "$1" -o ! -f "$1"; then
        	usage
	fi

	cfg=`realpath "$1"`
	cfg_prefix=`basename "$cfg" .cfg`

	. "$cfg"

	# Make sure we have what we need to continue
	if test -z "$table_prefix"; then
        	fail "$cfg: no table_prefix"
	fi
	if test -z "$first_year"; then
        	fail "$cfg: no first_year"
	fi
	if test -z "$last_year"; then
        	fail "$cfg: no last_year"
	fi
	if test -z "$this_year"; then
        	fail "$cfg: no this_year"
	fi
	if test -z "$register_dir"; then
        	fail "$cfg: no register_dir"
	fi
	if test -z "$register_csv"; then
        	fail "$cfg: no register_csv"
	fi
	if test "$cfg_prefix" != "$table_prefix"; then
        	fail "$cfg: $cfg_prefix != $table_prefix"
	fi
	if test -z "$do_bogons"; then
        	fail "$cfg: no do_bogons"
	fi
	if test ! -f "$register_dir/$register_csv"; then
        	fail "Register file $register_dir/$register_csv missing or empty"
	fi

	export table_prefix first_year last_year this_year register_dir

	# Make sure we can access mysql
	two=`echo "select 1+1"|$MYSQL`
	if test "$two" != 2; then
        	fail "Can't access Mysql"
	fi

	# Change directory to the project home
	cd "$PROJECT_HOME"
}

fail()
{
	echo "Fail: $1"
	exit 1
}

templify()
{
	sed -e "s,@TABLE_PREFIX@,$table_prefix,g" \
		-e "s,@FIRST_YEAR@,$first_year,g" \
		-e "s,@LAST_YEAR@,$last_year,g" \
		-e "s,@THIS_YEAR@,$this_year,g"
}

sanity()
{
	if test ! -s Tmp/${table_prefix}_sanity.sql; then
		templify < Template/sanity.sql > Tmp/${table_prefix}_sanity.sql
	fi
	$MYSQL < Tmp/${table_prefix}_sanity.sql
}

create_register_php()
{
	template=Template/register.php

	if test -z "$table_prefix"; then
        	echo "table_prefix empty" 1>&2
        	exit 1
	fi

	if test -z "$first_year"; then
        	echo "first_year empty" 1>&2
        	exit 1
	fi

	if test -z "$last_year"; then
        	echo "last_year empty" 1>&2
        	exit 1
	fi

	templify < $template
}

create_schema()
{
	if test -z "$table_prefix"; then
		echo "table_prefix empty" 1>&2
		exit 1
	fi
	if test -z "$first_year"; then
		echo "first_year missing" 1>&2
		exit 1
	fi
	if test -z "$last_year"; then
		echo "last_year missing" 1>&2
		exit 1
	fi

	cat << EOF
--
-- Table structure for table ${table_prefix}_average_mk
--

DROP TABLE IF EXISTS ${table_prefix}_average_mk;
CREATE TABLE ${table_prefix}_average_mk (
  year int(11) NOT NULL,
  amk float NOT NULL,
  sd float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table ${table_prefix}_founders
--

DROP TABLE IF EXISTS ${table_prefix}_founders;
CREATE TABLE ${table_prefix}_founders (
  id int(11) DEFAULT NULL,
  founder int(11) DEFAULT NULL,
  factor double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table ${table_prefix}_kinship
--

DROP TABLE IF EXISTS ${table_prefix}_kinship;
CREATE TABLE ${table_prefix}_kinship (
  id1 int(11) NOT NULL,
  id2 int(11) NOT NULL,
  kinship float NOT NULL,
  UNIQUE KEY ${table_prefix}_kinship_unique (id1,id2)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table ${table_prefix}_register
--

DROP TABLE IF EXISTS ${table_prefix}_register;
CREATE TABLE ${table_prefix}_register (
  id int(11) NOT NULL AUTO_INCREMENT,
  namn varchar(100) DEFAULT NULL,
  genb int(11) DEFAULT NULL,
  intyg int(11) DEFAULT NULL,
  nummer varchar(20) DEFAULT NULL,
  kon varchar(10) DEFAULT NULL,
  ar int(11) DEFAULT NULL,
  fodd datetime DEFAULT NULL,
  mor varchar(50) DEFAULT NULL,
  mor_nr varchar(10) DEFAULT NULL,
  mor_id int(11) DEFAULT NULL,
  far varchar(50) DEFAULT NULL,
  far_nr varchar(10) DEFAULT NULL,
  far_id int(11) DEFAULT NULL,
  fargnr int(11) DEFAULT NULL,
  farg varchar(50) DEFAULT NULL,
  ny_g int(11) DEFAULT NULL,
  dod varchar(50) DEFAULT NULL,
  vikt float DEFAULT NULL,
  kull int(11) DEFAULT NULL,
  ic float DEFAULT NULL,
  offspring int(11) NOT NULL DEFAULT '0',
  ovrigt varchar(255) DEFAULT NULL,
  bogon int(11) DEFAULT NULL,
  dmin int(11) DEFAULT NULL,
  dmax int(11) DEFAULT NULL,
EOF

	for y in `seq $first_year $last_year`; do
		echo "g$y varchar(5) DEFAULT NULL,"
		echo "mk$y float DEFAULT NULL,"
	done

	if test $this_year -gt $last_year; then
		echo "g$this_year varchar(5) DEFAULT NULL,"
		echo "mk$this_year float DEFAULT NULL,"
	fi

	cat << EOF
  UNIQUE KEY id (id),
  UNIQUE KEY ${table_prefix}_unique (nummer,namn,intyg),
  KEY ${table_prefix}_nummer_index (nummer)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOF

}

mk()
{
	echo "truncate table ${table_prefix}_average_mk;"
	for n in `seq $first_year $last_year`; do
		echo "insert into ${table_prefix}_average_mk (year, amk, sd)
			select $n, avg(mk$n), stddev(mk$n) from ${table_prefix}_register;"
	done

	if test $this_year -gt $last_year; then
		echo "insert into ${table_prefix}_average_mk (year, amk)
			select $this_year, avg(mk$this_year) from ${table_prefix}_register;"
	fi
}

patch()
{
	if test -z "$1"; then
		fail "Patchnamn saknas"
	fi

	if test -z "$table_prefix"; then
		fail "table_prefix inte satt"
	fi

	template=Template/patch_$1.sql
	if test ! -f $template; then
		fail "Mall $template saknas"
	fi
	patch=Tmp/patch_${table_prefix}_$1.sql
	rm -f $patch
	echo "Skapar patchscript $patch från mall $template"
	templify < $template > $patch
	if test $? != 0; then
		fail "Kan inte skapa patchscript"
	fi
	if test ! -s $patch; then
		fail "Patchscript $patch tomt"
	fi

	echo "Patchar $1, resultat i Log/patch_$1.log, kontroll i Log/sanity_$1.diff"
	sanity > Log/sanity_before.log
	$MYSQL < $patch > Log/patch_$1.log

	if test $? != 0; then
		fail "Patchscriptet $patch misslyckades"
	fi

	sanity > Log/sanity_after.log
	diff Log/sanity_before.log Log/sanity_after.log > Log/sanity_$1.diff
	if test $? = 0; then
		echo "Sanity oförändrad"
	else
		echo "Sanity förändrad"
	fi
}

do_wright()
{
	TMPFILE=Tmp/wright.sh
	LOGFILE=Log/wright.log
	SQLFILE=Tmp/wright.sql

	# Populate the matrix and calculate inbreeding and generation depth
	cat << EOF > $TMPFILE
wright -u $URL \\
	-v -t ${table_prefix}_register -i id -b fodd -d mor_id -s far_id \
	-c ic -D \\
EOF

	# Yearly mean kinship values
	for n in `seq $first_year $last_year`; do
		echo "$n_arg -n \"g${n} != '' and bogon is null\" -k mk${n} \\" >> $TMPFILE
	done

	# Mean kinship for this year
	# Handled specially because both last_year and this_year are included
	echo "$n_arg -n \"(g${last_year} != '' or g${this_year} != '') and bogon is null\" -k mk${this_year} \\" >> $TMPFILE

	# Save parts of kinship matrix for this_year and last_year
	# Save founder representation for this_year and last_year
	cat << EOF >> $TMPFILE
	-m "select r1.id, r2.id from ${table_prefix}_register r1 join ${table_prefix}_register r2 where r1.id < r2.id and r1.kon != r2.kon and (r1.g${last_year} != '' or r1.g${this_year} != '') and (r2.g${last_year} != '' or r2.g${this_year} != '')" \\
	-g "select r.id, f.id from ${table_prefix}_register r join ${table_prefix}_register f where (r.g${last_year} != '' or r.g${this_year} != '') and f.mor_id is null"
EOF

	# Run wright, output into database and logfile
	. $TMPFILE > $LOGFILE

	# Store parts of kinship matrix into sql script
	grep "^Kinship:" $LOGFILE |
	while read a b c d; do
		echo "insert into ${table_prefix}_kinship values ($b, $c, $d);"
	done > $SQLFILE

	# Store founder representation into sql acript
	grep "^Founders:" $LOGFILE |
	while read a b c d; do
		echo "insert into ${table_prefix}_founders values ($b, $c, $d);"
	done >> $SQLFILE

	# Store minimum and maximum distance from founders
	grep "^Depth:" $LOGFILE |
	while read a b c d; do
		echo "update ${table_prefix}_register set dmin = $c, dmax = $d where id = $b;"
	done >> $SQLFILE

	# Save kinship matrix and founder representation into database
	$MYSQL < $SQLFILE
}


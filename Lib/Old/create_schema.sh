#!/bin/sh

. Lib/config.sh

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
  amk float NOT NULL
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


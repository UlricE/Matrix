#!/bin/sh

. Lib/config.sh
. Lib/common.sh

load_config "$1"

# Ta bort gamla script etc som skapas från mallar.
rm -f Tmp/*${table_prefix}*

create_schema > Tmp/$table_prefix.sql
if test $? != 0; then
	fail "Kan inte skapa schema."
fi
echo "Schema skapat i Tmp/$table_prefix.sql"

$MYSQL < Tmp/$table_prefix.sql

if test $? != 0; then
	fail "Kan inte importera schema."
fi

# Hoppa över rubrikraden
tail -n +2 "$register_dir/$register_csv" | php "$register_dir/import_$table_prefix.php" > Tmp/register_$table_prefix.sql
if test $? != 0; then
	fail "Kan inte skapa registerscript."
fi
echo "Registerscript skapat"

$MYSQL < Tmp/register_$table_prefix.sql

if test $? != 0; then
	fail "Kan inte importera register."
fi
echo "Register importerat"

echo "delete from ${table_prefix}_register where nummer = '' and namn = '';" | $MYSQL
echo "select count(*) from ${table_prefix}_register;" | $MYSQL

do_patch()
{
	patch $1
	if test $? != 0; then
		fail "Patch $1 misslyckades"
	fi
}

#echo "Sök efter hanar som är mödrar"
# Det är inget vi skall göra; ändra uppströms istället så det blir rätt
#do_patch male_dam

#echo "Sök efter honor som är fäder"
# Samma sak här, ändra uppströms
#do_patch female_sire

#echo "Sök efter mödrar födda efter sina ungar"
# Samma sak här, ändra uppströms
#do_patch young_dam

#echo "Sök efter fäder födda efter sina ungar"
# Samma sak här, ändra uppströms
#do_patch young_sire

echo "Sök efter djur utan födelsedatum"
# Detta gör vi, det tar hand om djur som har årtal men inte datum
do_patch unborn

echo "Uppdatera fälten mor_id och far_id"
do_patch parents

echo "Totalkontroll, resultat i Log/sanity.log"
sanity > Log/sanity.log

echo "Beräkna antal ungar till respektive avelsdjur"
do_patch offspring

if test "$do_bogons" = "yes"; then
	echo "Sök efter falska founders"
	do_patch bogons
else
	echo "Hoppar över att söka efter falska founders; gör detta manuellt!"
fi

echo "Beräkna släktskap och inavel"
do_wright
if test $? != 0; then
	fail "Släktskapsberäkning misslyckades"
fi

echo "Beräkna genomsnittligt medelsläktskap år för år"
mk > Tmp/mk.sql
$MYSQL < Tmp/mk.sql > Log/mk.log

create_register_php > $register_dir/register_$table_prefix.php
if test $? != 0; then
	echo "Kan inte skapa applikation." 1>&2
	exit 1
fi
echo "Applikation skapad i $register_dir/register_$table_prefix.php"

cp $register_dir/register_$table_prefix.php /var/www
if test $? != 0; then
	echo "Kan inte kopiera applikationen till webben."
	exit 1
fi
echo "Applikation kopierad till webben."


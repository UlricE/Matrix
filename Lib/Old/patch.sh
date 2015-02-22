#!/bin/sh

. Lib/config.sh

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
exit 0


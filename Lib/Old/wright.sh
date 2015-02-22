#!/bin/sh

. Lib/config.sh

TMPFILE=Tmp/wright.sh
LOGFILE=Log/wright.log
SQLFILE=Tmp/wright.sql

# Populate the matrix and calculate inbreeding
cat << EOF > $TMPFILE
wright -u $URL \\
	-v -t ${table_prefix}_register -i id -b fodd -d mor_id -s far_id \
	-c ic \\
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

#wright -u "$URL" \
#	-v -t ${table_prefix}_register -i id -b fodd -d mor_id -s far_id \
#	-c ic \
#	$n_arg \
#	-m "select r1.id, r2.id from ${table_prefix}_register r1 join ${table_prefix}_register r2 where r1.id < r2.id and r1.kon != r2.kon and r1.g${last_year} != '' and r2.g${last_year} != ''" \
#	> Log/wright.log

# Store parts of kinship matrix into sql script
grep "^Kinship: " $LOGFILE |
while read a b c d; do
	echo "insert into ${table_prefix}_kinship values ($b, $c, $d);"
done > $SQLFILE

# Store founder representation into sql acript
grep "^Founders: " $LOGFILE |
while read a b c d; do
	echo "insert into ${table_prefix}_founders values ($b, $c, $d);"
done >> $SQLFILE

# Save kinship matrix and founder representation into database
$MYSQL < $SQLFILE

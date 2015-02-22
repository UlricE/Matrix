#!/bin/sh

. Lib/config.sh
. Lib/common.sh

load_config "$1"

DUMPFILE="${table_prefix}_dump.sql"

mysqldump -u $DB_UID -p$DB_PWD $DB_DB ${table_prefix}_average_mk ${table_prefix}_founders ${table_prefix}_kinship ${table_prefix}_register > "$DUMPFILE"

echo "Export in $DUMPFILE"

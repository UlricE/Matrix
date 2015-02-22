# Change these variables according to local customs.

DB_HOST=localhost
DB_UID=gotlandskaninen
DB_PWD=kanjaglaga
DB_DB=gotlandskaninen

MYSQL="mysql -h $DB_HOST -u $DB_UID -p$DB_PWD $DB_DB -N"

# Connect URL for libsdb
URL="mysql:host=$DB_HOST:uid=$DB_UID:pwd=$DB_PWD:db=$DB_DB"

PROJECT_HOME=/home/ulric/Djur/Kanin/Register

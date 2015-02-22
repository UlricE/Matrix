#!/bin/sh

. Lib/config.sh

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

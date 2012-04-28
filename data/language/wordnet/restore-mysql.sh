#!/bin/bash
# bbou@ac-toulouse;fr
# 21.02.2009 

db='wordnet30'
dbtype=mysql
dbuser=root
if [ "$1" == "-d" ]; then
dbdelete=true
fi
modules="wn legacy vn xwn bnc sumo"

function process()
{
	if [ ! -e "$1" ];then
		return
	fi
	echo "$2"
	mysql -u ${dbuser} --password=${password} $4 $3 < $1
}

function dbexists()
{
	mysql -u ${dbuser} --password=${password} -e "\q" ${db} > /dev/null 2> /dev/null
	return $? 
}

function deletedb()
{
	echo "delete ${db}"
	mysql -u ${dbuser} --password=${password} -e "DROP DATABASE ${db};"
}

function createdb()
{
	echo "create ${db}"
	mysql -u ${dbuser} --password=${password} -e "CREATE DATABASE ${db} DEFAULT CHARACTER SET UTF8;"
}

function getpassword()
{
	read -s -p "enter ${dbuser}'s password: " password
	echo
}

echo "restoring ${db}"
getpassword

#database
if [ ! -z ${dbdelete} ]; then
	deletedb
fi
if ! dbexists; then
	createdb
fi

# module tables
for m in ${modules}; do
	sql=${dbtype}-${m}-schema.sql
	process ${sql} schema ${db}
done
for m in ${modules}; do
	sql=${dbtype}-${m}-data.sql
	process ${sql} data ${db}
done
for m in ${modules}; do
	sql=${dbtype}-${m}-constraints.sql
	process ${sql} constraints ${db} --force
done
for m in ${modules}; do
	sql=${dbtype}-${m}-views.sql
	process ${sql} views ${db}
done


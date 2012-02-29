#!/bin/bash
if ! [ -d temp ]; then
    mkdir --verbose -m 777 temp;
fi

cd temp;

echo "Copiando base de dados de produção para 'mysql_sugarcrm_principal.sql'...";

SQLFILE=mysql_sugarcrm_principal.sql;

PASSWD=t31bra#322;

DUMP="mysqldump -u telbrax -p$PASSWD --single-transaction --skip-add-locks sugarcrm";

ssh telbrax@crm.telbrax.com.br $DUMP > $SQLFILE;

echo "Copiando arquivos do sugarcrm...";

FILE=sugarcrm-$(date +"%Y-%m-%d");

ORIGINPATH=/home/telbrax/sugarcrm/bkp/$FILE.zip;

scp telbrax@crm.telbrax.com.br:$ORIGINPATH .;

TEMP=$(pwd);


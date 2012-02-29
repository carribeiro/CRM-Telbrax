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

cd /var/www;

if ! [ -d bkp ]; then
    mkdir --verbose -m 777 bkp;
fi;

echo "Gerando backup dos arquivos do crm local...";

TIMESTAMP=$(date +"%Y%m%d%H%M%S");

mv --verbose sugarcrm bkp/sugarcrm_$TIMESTAMP;

echo "Restaurando copia da produção para local..."

CRM=sugarcrm;

unzip -q $TEMP/$FILE.zip -d $CRM;

chmod -R 777 $CRM;

#chown --verbose -R www-data:www-data $CRM;

echo "Gerando backup do banco de dados local...";

$DUMP > bkp/mysql_bkp_$TIMESTAMP.sql;

echo "Restaurando banco de dados...";

mysql -u telbrax -p$PASSWD sugarcrm < $TEMP/$SQLFILE;

echo "Importação concluida com sucesso!"

cd $TEMP/..;


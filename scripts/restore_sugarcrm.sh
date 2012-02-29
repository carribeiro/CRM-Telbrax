#!/bin/bash
if [ $# -ne 2 ]; then
   echo "usage: $(basename $0) backup_path database_file";
   exit 1;
fi

BACKUP=$1;
DATABASE=$2;

if [ ! -d $BACKUP ]; then
   echo "Diretorio de backup inválido ou inexistente: '$BACKUP'!";
   exit 1;
fi

if [ ! -f $DATABASE ]; then
   echo "Arquivo com a base de dados inválido ou inexistente: '$DATABASE'!";
   exit 1;
fi

WWWPATH=/var/www;

echo "Restaurando arquivos...";

mv -v $WWWPATH/sugarcrm $WWWPATH/bkp/last;
mv -v $BACKUP $WWWPATH/sugarcrm;

echo "Restaurando base de dados...";

mysql -utelbrax -pt31bra#322 sugarcrm < $DATABASE;

echo "Restauração concluida.";


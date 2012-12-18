#!/bin/sh

# Изменение владельца и группы файлов и каталогов проекта
echo "Введите имя пользователя [www-data]:";
read user;
if test -z $user; then
   user='www-data';
fi;
chown -R $user:$user .;

# Изменение прав доступа на файлы и каталоги проекта
echo "Введите HEX представление прав доступа к каталогам [755]:";
read dirAccess;
if test -z $dirAccess; then
  dirAccess='755';
fi;
chmod -R  $dirAccess .;

echo "Введите HEX представление прав доступа к файлам [744]:";
read dirFile;
if test -z $dirFile; then
  dirFile='744';
fi;
find . -type f -exec chmod $dirFile {} \;

# Запрет просмотра защищенных файлов
chmod 740 'services/configuration/conf.ini';
#files='model/modules model/modules/Console/state.ini model/modules/Console/file$

echo 'Complete';

#/bin/bash
echo "Определите тип взаимодействия ([1] - использование, 2 - разработка):";
read type;
if test -z $type; then 
  type='1';
fi;
if test $type = '2'; then
  # Разработка
  chmod -R 777 .;
else
  # Использование
  echo "Введите имя пользователя [www-data]:";
  read user;
  if test -z $user; then 
    user='www-data';
  fi;
  chown -R $user:$user .;

  echo "Введите HEX представление прав доступа к каталогам [755]:";
  read dirAccess;
  if test -z $dirAccess; then 
    dirAccess='755'; 
  fi;
  find -type d -exec chmod $dirAccess {} \;

  echo "Введите HEX представление прав доступа к файлам [744]:";
  read fileAccess;
  if test -z $fileAccess; then
    fileAccess='744';
  fi;
  find -type f -exec chmod $fileAccess {} \;

  echo "Введите HEX представление прав доступа к скриптам [500]:";
  read scriptAccess;
  if test -z $scriptAccess; then
    scriptAccess='500';
  fi;
  find -name "*.php" -exec chmod $scriptAccess {} \;

  echo "Введите HEX представление прав доступа к файлам данных [700]:";
  read dataAccess;
  if test -z $datatAccess; then
    dataAccess='700';
  fi;
  find -name "*.ini" -exec chmod $dataAccess {} \;
fi;

chmod 111 preInstall.sh;
echo 'Complete';

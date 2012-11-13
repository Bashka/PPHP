<?php
/**
* Файл выполняет первоначальную настройку системы.
*/
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules/Console/state.ini', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules/Console/files', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules/SystemPackages/state.ini', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules/SystemPackages/tmp', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules/SystemPackages/InstallerModules/state.ini', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/services/configuration/conf.ini', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/services/log/log.txt', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/tools/classes/standard/fileSystem/loadingFiles/temp', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/view/screens', 0777);
chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/view/screens/Console/browse/state.ini', 0777);
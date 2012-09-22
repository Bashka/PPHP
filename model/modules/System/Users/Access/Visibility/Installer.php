<?php
namespace PPHP\model\modules\System\Users\Access\Visibility;

class Installer extends \PPHP\model\classes\Installer{
  public function install(){
    $PDO = \PPHP\services\database\ConnectionManager::getInstance()->getPDO();
    $PDO->multiQuery('
      CREATE TABLE `Visibility_Shade` (
      `OID` INT NOT NULL PRIMARY KEY,
      `module` VARCHAR(80) NOT NULL,
      `screen` VARCHAR(80) NOT NULL
      );

      CREATE TABLE `Visibility_RoleShade` (
      `OID` INT NOT NULL PRIMARY KEY,
      `role` VARCHAR(100) NOT NULL,
      `shade` VARCHAR(100) NOT NULL
      );',
    ';');
  }

  public function uninstall(){
    $PDO = \PPHP\services\database\ConnectionManager::getInstance()->getPDO();
    $PDO->multiQuery('
      DROP TABLE `Visibility_Shade`;
      DROP TABLE `Visibility_RoleShade`;
      ',
    ';');
  }
}
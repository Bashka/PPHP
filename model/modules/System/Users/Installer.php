<?php
namespace PPHP\model\modules\System\Users;

class Installer extends \PPHP\model\classes\Installer{
  public function install(){
    $PDO = \PPHP\services\database\ConnectionManager::getInstance()->getPDO();
    $PDO->query('CREATE TABLE `Users_Users` (
                 `OID` INT NOT NULL PRIMARY KEY,
                 `ip` VARCHAR(15) NULL )');
  }

  public function uninstall(){
    $PDO = \PPHP\services\database\ConnectionManager::getInstance()->getPDO();
    $PDO->query('DROP TABLE `Users_Users`');
  }
}